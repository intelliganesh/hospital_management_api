<?php

namespace App\Services;

use App\Models\IPD;
use App\Models\IpdDocument;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class IPDDocumentService
{
    private const DOCUMENT_TYPE_PATTERN = '/^[a-z0-9]+(?:_[a-z0-9]+)*$/';

    private const FILE_RULES = 'file|mimes:jpeg,png,jpg,gif,svg,webp,pdf,doc,docx|max:15360';

    public function create(Request $request): array
    {
        $this->validateCreate($request);
        $ipd = IPD::findOrFail($request->input('ipd_id'));
        $storedPaths = [];

        try {
            $documents = DB::transaction(function () use ($request, $ipd, &$storedPaths) {
                $documents = [];

                foreach ($request->file('documents') as $file) {
                    $path = $this->storeFile($ipd, $file);
                    $storedPaths[] = $path;

                    $documents[] = IpdDocument::create([
                        'ipd_id' => $ipd->id,
                        'ipd_surgery_id' => null,
                        'document_type' => $request->input('document_type'),
                        'document_path' => $path,
                    ]);
                }

                return $documents;
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            throw $exception;
        }

        return array_map(fn (IpdDocument $document) => $this->formatDocument($document), $documents);
    }

    // ---------------------------------------------------------------------------------------------
    // General uploads / scanned records (external test reports, previous hospital records, ...)
    // Stored in ipd_documents with document_type = 'general_upload' + category/title/description columns.
    // ---------------------------------------------------------------------------------------------

    public const GENERAL_TYPE = 'general_upload';

    public const GENERAL_CATEGORIES = [
        'external_test_report'     => 'External test report',
        'previous_hospital_records' => 'Previous hospital records',
        'other'                    => 'Other',
    ];

    /** real (content-sniffed) mime => stored extension */
    private const GENERAL_MIME_EXT = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
    ];

    private const GENERAL_ALLOWED_CLIENT_EXT = ['pdf', 'jpg', 'jpeg', 'png'];

    private const GENERAL_DELETE_ROLES = ['Admin', 'Super Admin', 'Front Desk User'];

    public function createGeneral(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'ipd_id'      => 'required|uuid|exists:ipd,id',
            'category'    => ['required', 'string', 'in:' . implode(',', array_keys(self::GENERAL_CATEGORIES))],
            'title'       => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'files'       => 'required|array|min:1|max:10',
            'files.*'     => 'required|file|mimes:pdf,jpg,jpeg,png|mimetypes:application/pdf,image/jpeg,image/png|max:10240',
        ]);
        $validator->after(function ($validator) use ($request) {
            foreach ((array) $request->file('files') as $i => $file) {
                if ($file instanceof UploadedFile && ! in_array(strtolower($file->getClientOriginalExtension()), self::GENERAL_ALLOWED_CLIENT_EXT, true)) {
                    $validator->errors()->add("files.$i", 'Only PDF, JPG, JPEG and PNG files are allowed.');
                }
            }
        });
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $ipd         = IPD::findOrFail($request->input('ipd_id'));
        $user        = $request->user();
        $storedPaths = [];

        try {
            $rows = DB::transaction(function () use ($request, $ipd, $user, &$storedPaths) {
                $rows = [];
                foreach ($request->file('files') as $file) {
                    $mime      = $file->getMimeType(); // content based (finfo), not the client header
                    $ext       = self::GENERAL_MIME_EXT[$mime] ?? null;
                    if (! $ext) {
                        throw ValidationException::withMessages(['files' => ['Only PDF, JPG, JPEG and PNG files are allowed.']]);
                    }
                    $original  = $file->getClientOriginalName();
                    $title     = trim((string) $request->input('title')) ?: pathinfo($original, PATHINFO_FILENAME);
                    $slug      = Str::limit(Str::slug($title, '_') ?: 'file', 60, '');
                    $path      = $file->storeAs("pdfs/ipd/{$ipd->ipd_number}/general_uploads", Str::uuid() . '_' . $slug . '.' . $ext, 'public');
                    if (! $path) {
                        throw new RuntimeException('Unable to store the uploaded file.');
                    }
                    $storedPaths[] = $path;

                    $rows[] = IpdDocument::create([
                        'ipd_id'           => $ipd->id,
                        'ipd_surgery_id'   => null,
                        'document_type'    => self::GENERAL_TYPE,
                        'document_path'    => $path,
                        'category'         => $request->input('category'),
                        'title'            => Str::limit($title, 255, ''),
                        'description'      => $request->input('description'),
                        'original_name'    => Str::limit($original, 255, ''),
                        'mime_type'        => $mime,
                        'file_size'        => $file->getSize(),
                        'uploaded_by'      => $user?->id,
                        'uploaded_by_name' => $user?->name,
                    ]);
                }

                return $rows;
            });
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($storedPaths);
            throw $e;
        }

        return array_map(fn (IpdDocument $d) => $this->formatGeneral($d), $rows);
    }

    public function listGeneral(string $ipdId, Request $request): array
    {
        IPD::findOrFail($ipdId);
        $category = $request->query('category');
        if ($category !== null && $category !== '' && ! array_key_exists($category, self::GENERAL_CATEGORIES)) {
            throw ValidationException::withMessages(['category' => ['Invalid category.']]);
        }

        return IpdDocument::query()
            ->where('ipd_id', $ipdId)
            ->where('document_type', self::GENERAL_TYPE)
            ->when($category, fn ($q) => $q->where('category', $category))
            ->latest()
            ->get()
            ->map(fn (IpdDocument $d) => $this->formatGeneral($d))
            ->values()
            ->all();
    }

    /** @return array{path: string, name: string, mime: string} */
    public function generalFile(string $id): array
    {
        $doc = IpdDocument::where('document_type', self::GENERAL_TYPE)->findOrFail($id);
        if (! Storage::disk('public')->exists($doc->document_path)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
        }
        $ext  = pathinfo($doc->document_path, PATHINFO_EXTENSION);
        $name = Str::slug(pathinfo((string) ($doc->original_name ?: $doc->title ?: 'document'), PATHINFO_FILENAME), '_') ?: 'document';

        return [
            'path' => Storage::disk('public')->path($doc->document_path),
            'name' => $name . '.' . $ext,
            'mime' => $doc->mime_type ?: 'application/octet-stream',
        ];
    }

    public function deleteGeneral(string $id, Request $request): void
    {
        $doc  = IpdDocument::where('document_type', self::GENERAL_TYPE)->findOrFail($id);
        $user = $request->user();
        $allowed = $user && ($user->hasAnyRole(self::GENERAL_DELETE_ROLES) || ((int) $doc->uploaded_by === (int) $user->id));
        if (! $allowed) {
            throw new AuthorizationException('You are not allowed to delete this document.');
        }

        $path = $doc->document_path;
        DB::transaction(fn () => $doc->delete());
        Storage::disk('public')->delete($path);
    }

    private function formatGeneral(IpdDocument $d): array
    {
        return [
            'id'               => $d->id,
            'ipd_id'           => $d->ipd_id,
            'category'         => $d->category,
            'category_label'   => self::GENERAL_CATEGORIES[$d->category] ?? $d->category,
            'title'            => $d->title,
            'description'      => $d->description,
            'original_name'    => $d->original_name,
            'mime_type'        => $d->mime_type,
            'file_size'        => $d->file_size,
            'uploaded_by'      => $d->uploaded_by,
            'uploaded_by_name' => $d->uploaded_by_name,
            'document_path'    => $d->document_path,
            'document_url'     => asset('storage/' . ltrim($d->document_path, '/')),
            'created_at'       => $d->created_at?->toISOString(),
        ];
    }

    public function all(string $ipdId, Request $request): array
    {
        IPD::findOrFail($ipdId);
        $this->validateDocumentType($request->query('document_type'));

        return IpdDocument::query()
            ->where('ipd_id', $ipdId)
            ->when(
                $request->filled('document_type'),
                fn ($query) => $query->where('document_type', $request->query('document_type'))
            )
            ->latest()
            ->get()
            ->map(fn (IpdDocument $document) => $this->formatDocument($document))
            ->values()
            ->all();
    }

    public function get(string $id): array
    {
        return $this->formatDocument(IpdDocument::findOrFail($id));
    }

    public function update(string $id, Request $request): array
    {
        $document = IpdDocument::findOrFail($id);
        $this->validateUpdate($request);

        $ipd = IPD::findOrFail($document->ipd_id);
        $oldPath = $document->document_path;
        $newPath = null;

        try {
            if ($request->hasFile('document')) {
                $newPath = $this->storeFile($ipd, $request->file('document'));
            }

            DB::transaction(function () use ($document, $request, $newPath) {
                if ($request->filled('document_type')) {
                    $document->document_type = $request->input('document_type');
                }

                if ($newPath) {
                    $document->document_path = $newPath;
                }

                $document->save();
            });
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }
            throw $exception;
        }

        if ($newPath && $oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return $this->formatDocument($document->fresh());
    }

    public function delete(string $id): void
    {
        $document = IpdDocument::findOrFail($id);
        $path = $document->document_path;

        DB::transaction(fn () => $document->delete());
        Storage::disk('public')->delete($path);
    }

    private function validateCreate(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'ipd_id' => 'required|uuid|exists:ipd,id',
            'document_type' => ['required', 'string', 'max:100', 'regex:'.self::DOCUMENT_TYPE_PATTERN],
            'documents' => 'required|array|min:1',
            'documents.*' => 'required|'.self::FILE_RULES,
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateUpdate(Request $request): void
    {
        $validator = Validator::make($request->all(), [
            'document_type' => ['nullable', 'string', 'max:100', 'regex:'.self::DOCUMENT_TYPE_PATTERN],
            'document' => 'nullable|'.self::FILE_RULES,
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        if (! $request->filled('document_type') && ! $request->hasFile('document')) {
            throw ValidationException::withMessages([
                'document' => ['A document type or replacement document is required.'],
            ]);
        }
    }

    private function validateDocumentType(?string $documentType): void
    {
        if (is_null($documentType) || $documentType === '') {
            return;
        }

        $validator = Validator::make(
            ['document_type' => $documentType],
            ['document_type' => ['string', 'max:100', 'regex:'.self::DOCUMENT_TYPE_PATTERN]]
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function storeFile(IPD $ipd, UploadedFile $file): string
    {
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), '_');
        $baseName = Str::limit($baseName ?: 'document', 80, '');
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = Str::uuid().'_'.$baseName.'.'.$extension;
        $directory = "pdfs/ipd/{$ipd->ipd_number}/others";
        $path = $file->storeAs($directory, $fileName, 'public');

        if (! $path) {
            throw new RuntimeException('Unable to store the IPD document.');
        }

        return $path;
    }

    private function formatDocument(IpdDocument $document): array
    {
        return [
            'id' => $document->id,
            'ipd_id' => $document->ipd_id,
            'ipd_surgery_id' => $document->ipd_surgery_id,
            'document_type' => $document->document_type,
            'file_name' => basename($document->document_path),
            'document_path' => $document->document_path,
            'document_url' => asset('storage/'.ltrim($document->document_path, '/')),
            'created_at' => $document->created_at?->toISOString(),
            'updated_at' => $document->updated_at?->toISOString(),
        ];
    }
}
