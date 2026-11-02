<?php

namespace App\Services;

use App\Models\IPD;
use App\Models\IpdDocument;
use DomainException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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
                        'document_source' => IpdDocument::SOURCE_UPLOADED,
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
        $this->assertUploaded($document);
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
        $this->assertUploaded($document);
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

    private function assertUploaded(IpdDocument $document): void
    {
        if ($document->document_source !== IpdDocument::SOURCE_UPLOADED) {
            throw new DomainException('Generated IPD documents cannot be updated or deleted.');
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
            'document_source' => $document->document_source ?? IpdDocument::SOURCE_GENERATED,
            'file_name' => basename($document->document_path),
            'document_path' => $document->document_path,
            'document_url' => asset('storage/'.ltrim($document->document_path, '/')),
            'created_at' => $document->created_at?->toISOString(),
            'updated_at' => $document->updated_at?->toISOString(),
        ];
    }
}
