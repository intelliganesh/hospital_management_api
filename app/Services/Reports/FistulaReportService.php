<?php
namespace App\Services\Reports;

use App\Models\Consultations;
use App\Models\PatientFistula;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class FistulaReportService
{
    /**
     * Replace empty strings with '-' in array values
     *
     * @param string $value
     * @param string $delimiter
     * @param string $suffix
     * @return string
     */
    private function formatArrayValues($value, $delimiter = ', ', $suffix = '')
    {
        if (empty($value)) {
            return '';
        }
        $items = array_map(fn($v) => $v === '' ? '-' : $v, explode('#', $value));
        $result = implode($delimiter, $items);
        return ($result === '-') ? $result : $result . ' ' . $suffix;   
    }

    /**
     * Get fistula report with filters
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(Request $request)
    {
        // Query consultations with proctology data
        $consultationQuery = Consultations::query()
            ->join('proctology', 'consultations.id', '=', 'proctology.consultation_id')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->join('users as doctors', 'consultations.doctor_id', '=', 'doctors.id')
            ->select(
                'consultations.*',
                'proctology.previous_scar',
                'proctology.previous_scar_position',
                'proctology.abscess',
                'proctology.abscess_position',
                'proctology.type_of_fistula_position',
                'proctology.type_of_fistula_sphincter',
                'proctology.no_of_tracks_in_one_fistula',
                'proctology.no_of_fistula',
                'proctology.internal_opening_position',
                'proctology.internal_opening_distance',
                'proctology.secondary_anal_valve',
                'proctology.posterior_fistulous_angle',
                'proctology.sonologist',
                'proctology.managements',
                'proctology.fistula_recurrence',
                'proctology.fistula_recurrence_surgery_count',
                'proctology.fistula_remark',
                'proctology.external_opening_position',
                'proctology.no_of_external_opening_position',
                'proctology.any_other',
                'proctology.no_of_secondary_opening_position',
                'proctology.type_of_crypt',
                'proctology.crypt_cause',
                'proctology.basis_of_high_low_riding',
                'proctology.distant_visceral_communication',
                'proctology.sono_fistula_gram',
                'proctology.mri_fistula_gram',
                'proctology.sonologist_findings',
                'proctology.other_investigation',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.phone_no as patient_phone',
                'patients.email as patient_email',
                'doctors.name as doctor_name'
            );

        // Query patient_fistula data
        $patientFistulaQuery = PatientFistula::query()
            ->join('patients', 'patient_fistula.patient_id', '=', 'patients.id')
            ->select(
                'patient_fistula.*',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.phone_no as patient_phone',
                'patients.email as patient_email'
            );

        // Get multiple filters array
        $filters = $request->input('multiple_filter', []);

        // Apply filters to consultation query
        if (isset($filters['doctor_id']) && ! empty($filters['doctor_id'])) {
            $consultationQuery->where('consultations.doctor_id', $filters['doctor_id']);
        }
        if (isset($filters['patient_id']) && ! empty($filters['patient_id'])) {
            $consultationQuery->where('consultations.patient_id', $filters['patient_id']);
            $patientFistulaQuery->where('patient_fistula.patient_id', $filters['patient_id']);
        }
        if (isset($request->from_date) && isset($request->to_date)) {
            $consultationQuery->whereBetween('consultations.created_at', [
                \Carbon\Carbon::parse($request->from_date)->startOfDay(),
                \Carbon\Carbon::parse($request->to_date)->endOfDay(),
            ]);
            $patientFistulaQuery->whereBetween('patient_fistula.created_at', [
                \Carbon\Carbon::parse($request->from_date)->startOfDay(),
                \Carbon\Carbon::parse($request->to_date)->endOfDay(),
            ]);
        }

        // Apply fistula-related filters to both queries
        if (isset($filters['type_of_fistula_position']) && ! empty($filters['type_of_fistula_position'])) {
            $consultationQuery->where('proctology.type_of_fistula_position', 'like', '%' . $filters['type_of_fistula_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.type_of_fistula_position', 'like', '%' . $filters['type_of_fistula_position'] . '%');
        }
        if (isset($filters['type_of_fistula_sphincter']) && ! empty($filters['type_of_fistula_sphincter'])) {
            $consultationQuery->where('proctology.type_of_fistula_sphincter', 'like', '%' . $filters['type_of_fistula_sphincter'] . '%');
            $patientFistulaQuery->where('patient_fistula.type_of_fistula_sphincter', 'like', '%' . $filters['type_of_fistula_sphincter'] . '%');
        }
        if (isset($filters['no_of_tracks_in_one_fistula']) && ! empty($filters['no_of_tracks_in_one_fistula'])) {
            $consultationQuery->where('proctology.no_of_tracks_in_one_fistula', $filters['no_of_tracks_in_one_fistula']);
            $patientFistulaQuery->where('patient_fistula.no_of_tracks_in_one_fistula', $filters['no_of_tracks_in_one_fistula']);
        }
        if (isset($filters['no_of_fistula']) && ! empty($filters['no_of_fistula'])) {
            $consultationQuery->where('proctology.no_of_fistula', $filters['no_of_fistula']);
            $patientFistulaQuery->where('patient_fistula.no_of_fistula', $filters['no_of_fistula']);
        }
        if (isset($filters['internal_opening_position']) && ! empty($filters['internal_opening_position'])) {
            $consultationQuery->where('proctology.internal_opening_position', 'like', '%' . $filters['internal_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.internal_opening_position', 'like', '%' . $filters['internal_opening_position'] . '%');
        }
        if (isset($filters['secondary_anal_valve']) && ! empty($filters['secondary_anal_valve'])) {
            $consultationQuery->where('proctology.secondary_anal_valve', 'like', '%' . $filters['secondary_anal_valve'] . '%');
            $patientFistulaQuery->where('patient_fistula.secondary_anal_valve', 'like', '%' . $filters['secondary_anal_valve'] . '%');
        }
        if (isset($filters['posterior_fistulous_angle']) && ! empty($filters['posterior_fistulous_angle'])) {
            $consultationQuery->where('proctology.posterior_fistulous_angle', 'like', '%' . $filters['posterior_fistulous_angle'] . '%');
            $patientFistulaQuery->where('patient_fistula.posterior_fistulous_angle', 'like', '%' . $filters['posterior_fistulous_angle'] . '%');
        }
        if (isset($filters['type_of_crypt']) && ! empty($filters['type_of_crypt'])) {
            $consultationQuery->where('proctology.type_of_crypt', 'like', '%' . $filters['type_of_crypt'] . '%');
            $patientFistulaQuery->where('patient_fistula.type_of_crypt', 'like', '%' . $filters['type_of_crypt'] . '%');
        }
        if (isset($filters['crypt_cause']) && ! empty($filters['crypt_cause'])) {
            $consultationQuery->where('proctology.crypt_cause', 'like', '%' . $filters['crypt_cause'] . '%');
            $patientFistulaQuery->where('patient_fistula.crypt_cause', 'like', '%' . $filters['crypt_cause'] . '%');
        }
        if (isset($filters['basis_of_high_low_riding']) && ! empty($filters['basis_of_high_low_riding'])) {
            $consultationQuery->where('proctology.basis_of_high_low_riding', 'like', '%' . $filters['basis_of_high_low_riding'] . '%');
            $patientFistulaQuery->where('patient_fistula.basis_of_high_low_riding', 'like', '%' . $filters['basis_of_high_low_riding'] . '%');
        }
        if (isset($filters['distant_visceral_communication']) && ! empty($filters['distant_visceral_communication'])) {
            $consultationQuery->where('proctology.distant_visceral_communication', 'like', '%' . $filters['distant_visceral_communication'] . '%');
            $patientFistulaQuery->where('patient_fistula.distant_visceral_communication', 'like', '%' . $filters['distant_visceral_communication'] . '%');
        }
        if (isset($filters['sono_fistula_gram']) && ! empty($filters['sono_fistula_gram'])) {
            $consultationQuery->where('proctology.sono_fistula_gram', 'like', '%' . $filters['sono_fistula_gram'] . '%');
            $patientFistulaQuery->where('patient_fistula.sono_fistula_gram', 'like', '%' . $filters['sono_fistula_gram'] . '%');
        }
        if (isset($filters['mri_fistula_gram']) && ! empty($filters['mri_fistula_gram'])) {
            $consultationQuery->where('proctology.mri_fistula_gram', 'like', '%' . $filters['mri_fistula_gram'] . '%');
            $patientFistulaQuery->where('patient_fistula.mri_fistula_gram', 'like', '%' . $filters['mri_fistula_gram'] . '%');
        }
        if (isset($filters['sonologist']) && ! empty($filters['sonologist'])) {
            $consultationQuery->where('proctology.sonologist', 'like', '%' . $filters['sonologist'] . '%');
            $patientFistulaQuery->where('patient_fistula.sonologist', 'like', '%' . $filters['sonologist'] . '%');
        }
        if (isset($filters['managements']) && ! empty($filters['managements'])) {
            $consultationQuery->where('proctology.managements', 'like', '%' . $filters['managements'] . '%');
        }
        if (isset($filters['external_opening_position']) && ! empty($filters['external_opening_position'])) {
            $consultationQuery->where('proctology.external_opening_position', 'like', '%' . $filters['external_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.external_opening_position', 'like', '%' . $filters['external_opening_position'] . '%');
        }
        if (isset($filters['no_of_external_opening_position']) && ! empty($filters['no_of_external_opening_position'])) {
            $consultationQuery->where('proctology.no_of_external_opening_position', 'like', '%' . $filters['no_of_external_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.no_of_external_opening_position', 'like', '%' . $filters['no_of_external_opening_position'] . '%');
        }
        if (isset($filters['any_other']) && ! empty($filters['any_other'])) {
            $consultationQuery->where('proctology.any_other', 'like', '%' . $filters['any_other'] . '%');
            $patientFistulaQuery->where('patient_fistula.any_other', 'like', '%' . $filters['any_other'] . '%');
        }
        if (isset($filters['no_of_secondary_opening_position']) && ! empty($filters['no_of_secondary_opening_position'])) {
            $consultationQuery->where('proctology.no_of_secondary_opening_position', 'like', '%' . $filters['no_of_secondary_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.no_of_secondary_opening_position', 'like', '%' . $filters['no_of_secondary_opening_position'] . '%');
        }
        if (isset($filters['sonologist_findings']) && ! empty($filters['sonologist_findings'])) {
            $consultationQuery->where('proctology.sonologist_findings', 'like', '%' . $filters['sonologist_findings'] . '%');
            $patientFistulaQuery->where('patient_fistula.sonologist_findings', 'like', '%' . $filters['sonologist_findings'] . '%');
        }
        if (isset($filters['other_investigation']) && ! empty($filters['other_investigation'])) {
            $consultationQuery->where('proctology.other_investigation', 'like', '%' . $filters['other_investigation'] . '%');
            $patientFistulaQuery->where('patient_fistula.other_investigation', 'like', '%' . $filters['other_investigation'] . '%');
        }
        if (isset($filters['fistula_recurrence']) && ! empty($filters['fistula_recurrence'])) {
            $consultationQuery->where('proctology.fistula_recurrence', $filters['fistula_recurrence']);
            $patientFistulaQuery->where('patient_fistula.fistula_recurrence', $filters['fistula_recurrence']);
        }

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $consultationQuery->where(function ($q) use ($search) {
                $q->where('patients.first_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.last_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.phone_no', 'like', '%' . $search . '%')
                    ->orWhere('doctors.name', 'like', '%' . $search . '%');
            });
            $patientFistulaQuery->where(function ($q) use ($search) {
                $q->where('patients.first_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.last_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.phone_no', 'like', '%' . $search . '%')
                    ->orWhere('patient_fistula.patient_name', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sortBy    = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Map sort_by to actual column names
        switch ($sortBy) {
            case 'consultation_date':
                $consultationQuery->orderBy('consultations.created_at', $sortOrder);
                $patientFistulaQuery->orderBy('patient_fistula.created_at', $sortOrder);
                break;
            case 'appointment_id':
                $consultationQuery->orderBy('consultations.appointment_id', $sortOrder);
                break;
            case 'patient_name':
                $consultationQuery->orderBy('patients.first_name', $sortOrder)
                    ->orderBy('patients.last_name', $sortOrder);
                $patientFistulaQuery->orderBy('patients.first_name', $sortOrder)
                    ->orderBy('patients.last_name', $sortOrder);
                break;
            case 'doctor_name':
                $consultationQuery->orderBy('doctors.name', $sortOrder);
                break;
            default:
                $consultationQuery->orderBy('consultations.created_at', $sortOrder);
                $patientFistulaQuery->orderBy('patient_fistula.created_at', $sortOrder);
                break;
        }

        // Get consultation results
        $consultationResults = $consultationQuery->get();
        $patientFistulaResults = $patientFistulaQuery->get();

        // Merge results
        $mergedResults = $consultationResults->concat($patientFistulaResults);

        // Sort merged results
        if ($sortBy === 'patient_name') {
            $mergedResults = $mergedResults->sortBy(function ($item) {
                return $item->patient_first_name . ' ' . $item->patient_last_name;
            });
            if ($sortOrder === 'desc') {
                $mergedResults = $mergedResults->reverse();
            }
        } else {
            $mergedResults = $mergedResults->sortBy(function ($item) {
                return $item->created_at ?? $item->updated_at;
            });
            if ($sortOrder === 'desc') {
                $mergedResults = $mergedResults->reverse();
            }
        }

        // Calculate analytics
        $analytics = [];
        $analytics['total_cases'] = $mergedResults->count();
        $analytics['new_cases'] = $mergedResults->filter(function ($item) {
            return ($item->fistula_recurrence ?? null) === 'new_case';
        })->count();
        $analytics['recurrence_cases'] = $mergedResults->filter(function ($item) {
            return ($item->fistula_recurrence ?? null) === 'recurrence';
        })->count();

        // Paginate merged results
        $page = $request->input('page', 1);
        $perPage = env('PAGINATION', 25);
        $paginated = $mergedResults->forPage($page, $perPage);

        $result = new \Illuminate\Pagination\Paginator(
            $paginated->values(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return array_merge($result->toArray(), ['analytics' => $analytics]);
    }

    /**
     * Download fistula report as Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExcel(Request $request)
    {
        // Query consultations with proctology data
        $consultationQuery = Consultations::query()
            ->join('proctology', 'consultations.id', '=', 'proctology.consultation_id')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->join('users as doctors', 'consultations.doctor_id', '=', 'doctors.id')
            ->select(
                'consultations.*',
                'proctology.type_of_fistula_position',
                'proctology.type_of_fistula_sphincter',
                'proctology.no_of_tracks_in_one_fistula',
                'proctology.no_of_fistula',
                'proctology.internal_opening_position',
                'proctology.internal_opening_distance',
                'proctology.secondary_anal_valve',
                'proctology.posterior_fistulous_angle',
                'proctology.sonologist',
                'proctology.external_opening_position',
                'proctology.previous_scar',
                'proctology.previous_scar_position',
                'proctology.abscess',
                'proctology.abscess_position',
                'proctology.sonologist_findings',
                'proctology.secondary_opening_position',
                'proctology.other_investigation',
                'proctology.managements',
                'proctology.fistula_recurrence',
                'proctology.fistula_recurrence_surgery_count',
                'proctology.fistula_remark',
                'proctology.no_of_external_opening_position',
                'proctology.any_other',
                'proctology.no_of_secondary_opening_position',
                'proctology.type_of_crypt',
                'proctology.crypt_cause',
                'proctology.basis_of_high_low_riding',
                'proctology.distant_visceral_communication',
                'proctology.sono_fistula_gram',
                'proctology.mri_fistula_gram',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.phone_no as patient_phone',
                'patients.email as patient_email',
                'patients.patient_number',
                'doctors.name as doctor_name'
            );

        // Query patient_fistula data
        $patientFistulaQuery = PatientFistula::query()
            ->join('patients', 'patient_fistula.patient_id', '=', 'patients.id')
            ->select(
                'patient_fistula.*',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.phone_no as patient_phone',
                'patients.email as patient_email'
            );

        // Get multiple filters array
        $filters = $request->input('multiple_filter', []);

        // Apply filters to consultation query
        if (isset($filters['doctor_id']) && ! empty($filters['doctor_id'])) {
            $consultationQuery->where('consultations.doctor_id', $filters['doctor_id']);
        }
        if (isset($filters['patient_id']) && ! empty($filters['patient_id'])) {
            $consultationQuery->where('consultations.patient_id', $filters['patient_id']);
            $patientFistulaQuery->where('patient_fistula.patient_id', $filters['patient_id']);
        }
        if (isset($request->from_date) && isset($request->to_date)) {
            $consultationQuery->whereBetween('consultations.created_at', [
                \Carbon\Carbon::parse($request->from_date)->startOfDay(),
                \Carbon\Carbon::parse($request->to_date)->endOfDay(),
            ]);
            $patientFistulaQuery->whereBetween('patient_fistula.created_at', [
                \Carbon\Carbon::parse($request->from_date)->startOfDay(),
                \Carbon\Carbon::parse($request->to_date)->endOfDay(),
            ]);
        }

        // Apply fistula-related filters to both queries
        if (isset($filters['type_of_fistula_position']) && ! empty($filters['type_of_fistula_position'])) {
            $consultationQuery->where('proctology.type_of_fistula_position', 'like', '%' . $filters['type_of_fistula_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.type_of_fistula_position', 'like', '%' . $filters['type_of_fistula_position'] . '%');
        }
        if (isset($filters['type_of_fistula_sphincter']) && ! empty($filters['type_of_fistula_sphincter'])) {
            $consultationQuery->where('proctology.type_of_fistula_sphincter', 'like', '%' . $filters['type_of_fistula_sphincter'] . '%');
            $patientFistulaQuery->where('patient_fistula.type_of_fistula_sphincter', 'like', '%' . $filters['type_of_fistula_sphincter'] . '%');
        }
        if (isset($filters['no_of_tracks_in_one_fistula']) && ! empty($filters['no_of_tracks_in_one_fistula'])) {
            $consultationQuery->where('proctology.no_of_tracks_in_one_fistula', $filters['no_of_tracks_in_one_fistula']);
            $patientFistulaQuery->where('patient_fistula.no_of_tracks_in_one_fistula', $filters['no_of_tracks_in_one_fistula']);
        }
        if (isset($filters['no_of_fistula']) && ! empty($filters['no_of_fistula'])) {
            $consultationQuery->where('proctology.no_of_fistula', $filters['no_of_fistula']);
            $patientFistulaQuery->where('patient_fistula.no_of_fistula', $filters['no_of_fistula']);
        }
        if (isset($filters['internal_opening_position']) && ! empty($filters['internal_opening_position'])) {
            $consultationQuery->where('proctology.internal_opening_position', 'like', '%' . $filters['internal_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.internal_opening_position', 'like', '%' . $filters['internal_opening_position'] . '%');
        }
        if (isset($filters['external_opening_position']) && ! empty($filters['external_opening_position'])) {
            $consultationQuery->where('proctology.external_opening_position', 'like', '%' . $filters['external_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.external_opening_position', 'like', '%' . $filters['external_opening_position'] . '%');
        }
        if (isset($filters['secondary_opening_position']) && ! empty($filters['secondary_opening_position'])) {
            $consultationQuery->where('proctology.secondary_opening_position', 'like', '%' . $filters['secondary_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.secondary_opening_position', 'like', '%' . $filters['secondary_opening_position'] . '%');
        }
        if (isset($filters['secondary_anal_valve']) && ! empty($filters['secondary_anal_valve'])) {
            $consultationQuery->where('proctology.secondary_anal_valve', 'like', '%' . $filters['secondary_anal_valve'] . '%');
            $patientFistulaQuery->where('patient_fistula.secondary_anal_valve', 'like', '%' . $filters['secondary_anal_valve'] . '%');
        }
        if (isset($filters['posterior_fistulous_angle']) && ! empty($filters['posterior_fistulous_angle'])) {
            $consultationQuery->where('proctology.posterior_fistulous_angle', 'like', '%' . $filters['posterior_fistulous_angle'] . '%');
            $patientFistulaQuery->where('patient_fistula.posterior_fistulous_angle', 'like', '%' . $filters['posterior_fistulous_angle'] . '%');
        }
        if (isset($filters['type_of_crypt']) && ! empty($filters['type_of_crypt'])) {
            $consultationQuery->where('proctology.type_of_crypt', 'like', '%' . $filters['type_of_crypt'] . '%');
            $patientFistulaQuery->where('patient_fistula.type_of_crypt', 'like', '%' . $filters['type_of_crypt'] . '%');
        }
        if (isset($filters['crypt_cause']) && ! empty($filters['crypt_cause'])) {
            $consultationQuery->where('proctology.crypt_cause', 'like', '%' . $filters['crypt_cause'] . '%');
            $patientFistulaQuery->where('patient_fistula.crypt_cause', 'like', '%' . $filters['crypt_cause'] . '%');
        }
        if (isset($filters['basis_of_high_low_riding']) && ! empty($filters['basis_of_high_low_riding'])) {
            $consultationQuery->where('proctology.basis_of_high_low_riding', 'like', '%' . $filters['basis_of_high_low_riding'] . '%');
            $patientFistulaQuery->where('patient_fistula.basis_of_high_low_riding', 'like', '%' . $filters['basis_of_high_low_riding'] . '%');
        }
        if (isset($filters['distant_visceral_communication']) && ! empty($filters['distant_visceral_communication'])) {
            $consultationQuery->where('proctology.distant_visceral_communication', 'like', '%' . $filters['distant_visceral_communication'] . '%');
            $patientFistulaQuery->where('patient_fistula.distant_visceral_communication', 'like', '%' . $filters['distant_visceral_communication'] . '%');
        }
        if (isset($filters['sono_fistula_gram']) && ! empty($filters['sono_fistula_gram'])) {
            $consultationQuery->where('proctology.sono_fistula_gram', 'like', '%' . $filters['sono_fistula_gram'] . '%');
            $patientFistulaQuery->where('patient_fistula.sono_fistula_gram', 'like', '%' . $filters['sono_fistula_gram'] . '%');
        }
        if (isset($filters['mri_fistula_gram']) && ! empty($filters['mri_fistula_gram'])) {
            $consultationQuery->where('proctology.mri_fistula_gram', 'like', '%' . $filters['mri_fistula_gram'] . '%');
            $patientFistulaQuery->where('patient_fistula.mri_fistula_gram', 'like', '%' . $filters['mri_fistula_gram'] . '%');
        }
        if (isset($filters['sonologist']) && ! empty($filters['sonologist'])) {
            $consultationQuery->where('proctology.sonologist', 'like', '%' . $filters['sonologist'] . '%');
            $patientFistulaQuery->where('patient_fistula.sonologist', 'like', '%' . $filters['sonologist'] . '%');
        }
        if (isset($filters['managements']) && ! empty($filters['managements'])) {
            $consultationQuery->where('proctology.managements', 'like', '%' . $filters['managements'] . '%');
        }
        if (isset($filters['no_of_external_opening_position']) && ! empty($filters['no_of_external_opening_position'])) {
            $consultationQuery->where('proctology.no_of_external_opening_position', 'like', '%' . $filters['no_of_external_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.no_of_external_opening_position', 'like', '%' . $filters['no_of_external_opening_position'] . '%');
        }
        if (isset($filters['any_other']) && ! empty($filters['any_other'])) {
            $consultationQuery->where('proctology.any_other', 'like', '%' . $filters['any_other'] . '%');
            $patientFistulaQuery->where('patient_fistula.any_other', 'like', '%' . $filters['any_other'] . '%');
        }
        if (isset($filters['no_of_secondary_opening_position']) && ! empty($filters['no_of_secondary_opening_position'])) {
            $consultationQuery->where('proctology.no_of_secondary_opening_position', 'like', '%' . $filters['no_of_secondary_opening_position'] . '%');
            $patientFistulaQuery->where('patient_fistula.no_of_secondary_opening_position', 'like', '%' . $filters['no_of_secondary_opening_position'] . '%');
        }
        if (isset($filters['sonologist_findings']) && ! empty($filters['sonologist_findings'])) {
            $consultationQuery->where('proctology.sonologist_findings', 'like', '%' . $filters['sonologist_findings'] . '%');
            $patientFistulaQuery->where('patient_fistula.sonologist_findings', 'like', '%' . $filters['sonologist_findings'] . '%');
        }
        if (isset($filters['other_investigation']) && ! empty($filters['other_investigation'])) {
            $consultationQuery->where('proctology.other_investigation', 'like', '%' . $filters['other_investigation'] . '%');
            $patientFistulaQuery->where('patient_fistula.other_investigation', 'like', '%' . $filters['other_investigation'] . '%');
        }
        if (isset($filters['fistula_recurrence']) && ! empty($filters['fistula_recurrence'])) {
            $consultationQuery->where('proctology.fistula_recurrence', $filters['fistula_recurrence']);
            $patientFistulaQuery->where('patient_fistula.fistula_recurrence', $filters['fistula_recurrence']);
        }

        // Sorting
        $sortBy    = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Map sort_by to actual column names
        switch ($sortBy) {
            case 'consultation_date':
                $consultationQuery->orderBy('consultations.created_at', $sortOrder);
                $patientFistulaQuery->orderBy('patient_fistula.created_at', $sortOrder);
                break;
            case 'appointment_id':
                $consultationQuery->orderBy('consultations.appointment_id', $sortOrder);
                break;
            case 'patient_name':
                $consultationQuery->orderBy('patients.first_name', $sortOrder)
                    ->orderBy('patients.last_name', $sortOrder);
                $patientFistulaQuery->orderBy('patients.first_name', $sortOrder)
                    ->orderBy('patients.last_name', $sortOrder);
                break;
            case 'doctor_name':
                $consultationQuery->orderBy('doctors.name', $sortOrder);
                break;
            default:
                $consultationQuery->orderBy('consultations.created_at', $sortOrder);
                $patientFistulaQuery->orderBy('patient_fistula.created_at', $sortOrder);
                break;
        }

        // Get results from both queries
        $consultationResults = $consultationQuery->get();
        $patientFistulaResults = $patientFistulaQuery->get();

        // Merge results
        $data = $consultationResults->concat($patientFistulaResults);

        $fileName = 'fistula_report_' . time() . '.xlsx';
        $tempPath = storage_path('app/public/' . $fileName);

        $exportData = $data->map(function ($row) {
            // Extract management labels from JSON array
            $managementNames = '';
            if (! empty($row->managements)) {
                $managementArray = json_decode($row->managements, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($managementArray)) {
                    $labels = [];
                    foreach ($managementArray as $management) {
                        if (is_array($management) && isset($management['label'])) {
                            $labels[] = $management['label'];
                        } elseif (is_array($management) && isset($management['value'])) {
                            $labels[] = $management['value'];
                        }
                    }
                    $managementNames = implode(', ', $labels);
                } else {
                    $managementNames = $row->managements;
                }
            }

            $internalOpeningPositionLevel = '';
            if (! empty($row->internal_opening_position)) {
                $items = explode('#', $row->internal_opening_position);
                $formatted = [];
                for ($i = 0; $i < count($items); $i += 2) {
                    if (isset($items[$i + 1])) {
                        $formatted[] = $items[$i] .' '. $items[$i + 1];
                    } else {
                        $formatted[] = $items[$i];
                    }
                }
                $internalOpeningPositionLevel = implode(', ', $formatted);
            }

            return [
                'Date'                             => \Carbon\Carbon::parse($row->created_at)->format('d/m/Y'),
                'Patient Name'                     => $row->patient_first_name . ' ' . $row->patient_last_name,
                'Patient Number'                   => $row->patient_number ?? '',
                'Patient Phone'                    => $row->patient_phone ?? '',
                'Patient Email'                    => $row->patient_email ?? '',
                'Doctor Name'                      => $row->doctor_name ?? '',
          
                'No of Fistula'                    => $row->no_of_fistula ?? '',
                'No of Tracks in one Fistula'      => $this->formatArrayValues($row->no_of_tracks_in_one_fistula),

                'No of External Opening'           => $this->formatArrayValues($row->no_of_external_opening_position),
                'Position of External Opening'     => $this->formatArrayValues($row->external_opening_position, "o'clock, ", "o'clock"),
                

                'Type of Fistula'                  => $this->formatArrayValues($row->type_of_fistula_position),
                'Type of Fistula Sphincter'        => $this->formatArrayValues($row->type_of_fistula_sphincter),
                
                'No of Secondary Opening Position' => $this->formatArrayValues($row->no_of_secondary_opening_position),
                'Position of Secondary Opening'    => $this->formatArrayValues($row->secondary_anal_valve, "o'clock, ", "o'clock"),
                
                'Internal Opening Position(level)' => $internalOpeningPositionLevel ?? '',
                'Internal Opening Distance'        =>$this->formatArrayValues($row->internal_position_distance),
                'Posterior Fistulous Angle'        => ($row->posterior_fistulous_angle!='') ? $row->posterior_fistulous_angle : '',
                
                'Any Other'                        => $this->formatArrayValues($row->any_other),
                
                'On the Basis of Crypt'            => $this->formatArrayValues($row->type_of_crypt),
                'If Any Cause'                     => $this->formatArrayValues($row->crypt_cause),
                'On the Basis of High/Low Riding'  => $this->formatArrayValues($row->basis_of_high_low_riding),
                'Any Other Distant/Visceral Communication'   => $this->formatArrayValues($row->distant_visceral_communication),
                'Sonologist/Radiologist'           => $row->sonologist ?? '',
                'Sonologist/Radiologist Findings'  => $row->sonologist_findings ?? '',
                'Other Investigation'              => $row->other_investigation ?? '',
                'Management'                       => $managementNames,
                'Fistula Recurrence'               => ucwords(str_replace('_', ' ', $row->fistula_recurrence)) ?? '',
                'Fistula Recurrence Surgery Count' => $row->fistula_recurrence_surgery_count ?? '',
                'Fistula Remark'                   => $row->fistula_remark ?? '',
            ];
        });

        (new FastExcel($exportData))->export($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}
