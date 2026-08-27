<?php
namespace App\Services;

use App\Models\IPD;
use App\Models\IPDPreliminaryNotes;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class IPDPreliminaryNotesService
{
    private $checkValidationService;

    public function __construct(CheckValidation $checkValidationService)
    {
        $this->checkValidationService = $checkValidationService;
    }

    /**
     * Create preliminary notes for an IPD
     */
    public function create(Request $request, string $ipdId)
    {
        // Verify IPD exists
        $ipd = IPD::findOrFail($ipdId);

        // Check if preliminary notes already exist for this IPD
        $existingNotes = IPDPreliminaryNotes::where('ipd_id', $ipdId)->first();
        if ($existingNotes) {
            throw new Exception('Preliminary notes already exist for this IPD. Only one preliminary note is allowed per IPD.');
        }

        $notesData = [
            'ipd_id'                     => $ipdId,
            'chief_complaint'            => $request->chief_complaint ?? null,
            'associated_complaint'       => $request->associated_complaint ?? null,
            'previous_treatment_history' => $request->previous_treatment_history ?? null,
            'medical_history'            => $request->medical_history ?? null,
            'family_history'             => $request->family_history ?? null,
            'personal_history'           => $request->personal_history ?? null,
            'allergy'                    => $request->allergy ?? null,
            'bp'                         => $request->bp ?? null,
            'pulse'                      => $request->pulse ?? null,
            'temperature'                => $request->temperature ?? null,
            'spo2'                       => $request->spo2 ?? null,
            'weight'                     => $request->weight ?? null,
            'height'                     => $request->height ?? null,
            'cvs'                        => $request->cvs ?? null,
            'rs'                         => $request->rs ?? null,
            'per_abdomen'                => $request->per_abdomen ?? null,
            'local_examination'          => $request->local_examination ?? null,
            'pr'                         => $request->pr ?? null,
            'dre'                        => $request->dre ?? null,
            'proctoscopy'                => $request->proctoscopy ?? null,
            'examination_comments'       => $request->examination_comments ?? null,
            'investigation'              => $request->investigation ?? null,
            'hb'                         => $request->hb ?? null,
            'tc'                         => $request->tc ?? null,
            'esr'                        => $request->esr ?? null,
            'rbs'                        => $request->rbs ?? null,
            'bt'                         => $request->bt ?? null,
            'ct'                         => $request->ct ?? null,
            'blood_urea'                 => $request->blood_urea ?? null,
            'hiv'                        => $request->hiv ?? null,
            'hbsag'                      => $request->hbsag ?? null,
            'line_of_treatment'          => $request->line_of_treatment ?? null,
            'provisional_diagnosis'      => $request->provisional_diagnosis ?? null,
            'final_diagnosis'            => $request->final_diagnosis ?? null,
            'treatment_advised'          => $request->treatment_advised ?? null,
            'treatment_given'            => $request->treatment_given ?? null,
            'preoperative_instruction'   => $request->preoperative_instruction ?? null,
        ];

        return IPDPreliminaryNotes::create($notesData);
    }

    /**
     * Get all preliminary notes with pagination
     */
    public function all(?Request $request)
    {
        $query = IPDPreliminaryNotes::with('ipd')->orderBy('created_at', 'desc');

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('chief_complaint', 'like', "%{$search}%")
                    ->orWhere('provisional_diagnosis', 'like', "%{$search}%")
                    ->orWhere('final_diagnosis', 'like', "%{$search}%")
                    ->orWhereHas('ipd', function ($subQuery) use ($search) {
                        $subQuery->where('ipd_number', 'like', "%{$search}%")
                            ->orWhere('patient_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('ipd_id') && ! empty($request->ipd_id)) {
            $query->where('ipd_id', $request->ipd_id);
        }

        if ($request->has('sort_by') && ! empty($request->sort_by)) {
            $sortOrder = $request->has('sort_order') && $request->sort_order === 'asc' ? 'asc' : 'desc';
            $query->orderBy($request->sort_by, $sortOrder);
        }

        $perPage = $request->has('per_page') ? (int) $request->per_page : 10;
        $page    = $request->has('page') ? (int) $request->page : 1;

        Paginator::useBootstrap();
        $notes = $query->paginate($perPage, ['*'], 'page', $page);

        return [
            'data'       => $notes->items(),
            'pagination' => [
                'total'        => $notes->total(),
                'count'        => $notes->count(),
                'per_page'     => $notes->perPage(),
                'current_page' => $notes->currentPage(),
                'total_pages'  => $notes->lastPage(),
                'links'        => [
                    'next' => $notes->nextPageUrl(),
                ],
            ],
        ];
    }

    /**
     * Get a single preliminary notes record by ID
     */
    public function get(string $id)
    {
        return IPDPreliminaryNotes::where('ipd_id', $id)->with('ipd')->firstOrFail();
    }

    /**
     * Update preliminary notes
     */
    public function update(Request $request, string $id)
    {
        $notes = IPDPreliminaryNotes::findOrFail($id);

        $updateData = [];

        if ($request->has('chief_complaint')) {
            $updateData['chief_complaint'] = $request->chief_complaint;
        }
        if ($request->has('associated_complaint')) {
            $updateData['associated_complaint'] = $request->associated_complaint;
        }
        if ($request->has('previous_treatment_history')) {
            $updateData['previous_treatment_history'] = $request->previous_treatment_history;
        }
        if ($request->has('medical_history')) {
            $updateData['medical_history'] = $request->medical_history;
        }
        if ($request->has('family_history')) {
            $updateData['family_history'] = $request->family_history;
        }
        if ($request->has('personal_history')) {
            $updateData['personal_history'] = $request->personal_history;
        }
        if ($request->has('allergy')) {
            $updateData['allergy'] = $request->allergy;
        }
        if ($request->has('bp')) {
            $updateData['bp'] = $request->bp;
        }
        if ($request->has('pulse')) {
            $updateData['pulse'] = $request->pulse;
        }
        if ($request->has('temperature')) {
            $updateData['temperature'] = $request->temperature;
        }
        if ($request->has('spo2')) {
            $updateData['spo2'] = $request->spo2;
        }
        if ($request->has('weight')) {
            $updateData['weight'] = $request->weight;
        }
        if ($request->has('height')) {
            $updateData['height'] = $request->height;
        }
        if ($request->has('cvs')) {
            $updateData['cvs'] = $request->cvs;
        }
        if ($request->has('rs')) {
            $updateData['rs'] = $request->rs;
        }
        if ($request->has('per_abdomen')) {
            $updateData['per_abdomen'] = $request->per_abdomen;
        }
        if ($request->has('local_examination')) {
            $updateData['local_examination'] = $request->local_examination;
        }
        if ($request->has('pr')) {
            $updateData['pr'] = $request->pr;
        }
        if ($request->has('dre')) {
            $updateData['dre'] = $request->dre;
        }
        if ($request->has('proctoscopy')) {
            $updateData['proctoscopy'] = $request->proctoscopy;
        }
        if ($request->has('investigation')) {
            $updateData['investigation'] = $request->investigation;
        }
        if ($request->has('hb')) {
            $updateData['hb'] = $request->hb;
        }
        if ($request->has('tc')) {
            $updateData['tc'] = $request->tc;
        }
        if ($request->has('esr')) {
            $updateData['esr'] = $request->esr;
        }
        if ($request->has('rbs')) {
            $updateData['rbs'] = $request->rbs;
        }
        if ($request->has('bt')) {
            $updateData['bt'] = $request->bt;
        }
        if ($request->has('ct')) {
            $updateData['ct'] = $request->ct;
        }
        if ($request->has('blood_urea')) {
            $updateData['blood_urea'] = $request->blood_urea;
        }
        if ($request->has('hiv')) {
            $updateData['hiv'] = $request->hiv;
        }
        if ($request->has('hbsag')) {
            $updateData['hbsag'] = $request->hbsag;
        }
        if ($request->has('line_of_treatment')) {
            $updateData['line_of_treatment'] = $request->line_of_treatment;
        }
        if ($request->has('provisional_diagnosis')) {
            $updateData['provisional_diagnosis'] = $request->provisional_diagnosis;
        }
        if ($request->has('final_diagnosis')) {
            $updateData['final_diagnosis'] = $request->final_diagnosis;
        }
        if ($request->has('treatment_advised')) {
            $updateData['treatment_advised'] = $request->treatment_advised;
        }
        if ($request->has('treatment_given')) {
            $updateData['treatment_given'] = $request->treatment_given;
        }
        if ($request->has('preoperative_instruction')) {
            $updateData['preoperative_instruction'] = $request->preoperative_instruction;
        }

        if (! empty($updateData)) {
            $notes->update($updateData);
        }

        return $notes->load('ipd');
    }

    /**
     * Delete preliminary notes
     */
    public function delete(string $id)
    {
        $notes = IPDPreliminaryNotes::findOrFail($id);
        $notes->delete();
    }
}
