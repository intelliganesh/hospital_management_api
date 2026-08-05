<?php
namespace App\Services\Reports;

use App\Models\Consultations;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class ConsultationReportService
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
     * Get consultation report with filters
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(Request $request)
    {
        $query = Consultations::query()
            ->with('invoiceNumber:id,consultation_id,currency')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->join('users as doctors', 'consultations.doctor_id', '=', 'doctors.id')
            ->leftJoin('proctology', 'consultations.id', '=', 'proctology.consultation_id')
            ->leftJoin('non_proctology', 'consultations.id', '=', 'non_proctology.consultation_id')
            ->select(
                'consultations.*',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.phone_no as patient_phone',
                'patients.email as patient_email',
                'patients.patient_number',
                'patients.age as patient_age',
                'patients.gender as patient_gender',
                'doctors.name as doctor_name',

                // Proctology fields
                'proctology.abscess',
                'proctology.abscess_position',
                'proctology.any_other',
                'proctology.basis_of_high_low_riding',
                'proctology.chief_complaints as proc_chief_complaints',
                'proctology.co_morbidities as proc_co_morbidities',
                'proctology.co_morbidities_description as proc_co_morbidities_description',
                'proctology.crypt_cause',
                'proctology.diagnosis_summary as proc_diagnosis',
                'proctology.diet_plan as proc_diet_plan',
                'proctology.distant_visceral_communication',
                'proctology.dre as proc_dre',
                'proctology.examination_overview as proc_examination_overview',
                'proctology.external_opening_position',
                'proctology.external_opening_position',
                'proctology.fistula_recurrence',
                'proctology.fistula_recurrence_surgery_count',
                'proctology.fistula_remark',
                'proctology.internal_opening_position',
                'proctology.internal_opening_distance',
                'proctology.managements as proc_managements',
                'proctology.mri_fistula_gram',
                'proctology.no_of_external_opening_position',
                'proctology.no_of_fistula',
                'proctology.no_of_secondary_opening_position',
                'proctology.no_of_tracks_in_one_fistula',
                'proctology.on_examination as proc_on_examination',
                'proctology.other_investigation',
                'proctology.other_investigation',
                'proctology.posterior_fistulous_angle',
                'proctology.preliminary_diagnostic as proc_preliminary_diagnostic',
                'proctology.previous_scar',
                'proctology.previous_scar_position',
                'proctology.proctoscopy as proc_proctoscopy',
                'proctology.secondary_anal_valve',
                'proctology.secondary_opening_position',
                'proctology.sono_fistula_gram',
                'proctology.sonologist',
                'proctology.sonologist_findings',
                'proctology.surgical_history as proc_surgical_history',
                'proctology.tests as proc_tests',
                'proctology.treatment_plan as proc_treatment_plan',
                'proctology.type_of_crypt',
                'proctology.type_of_fistula_position',
                'proctology.type_of_fistula_sphincter',

                // Non-Proctology fields
                'non_proctology.chief_complaints as non_proc_chief_complaints',
                'non_proctology.surgical_history as non_proc_surgical_history',
                'non_proctology.co_morbidities as non_proc_co_morbidities',
                'non_proctology.co_morbidities_description as non_proc_co_morbidities_description',
                'non_proctology.on_examination as non_proc_on_examination',
                'non_proctology.treatment_plan as non_proc_treatment_plan',
                'non_proctology.tests as non_proc_tests',
                'non_proctology.diet_plan as non_proc_diet_plan',
                'non_proctology.food_advice as non_proc_food_advice',
                'non_proctology.yoga_asana as non_proc_yoga_asana',
                'non_proctology.prakriti as non_proc_prakriti',
                'non_proctology.vikruti as non_proc_vikruti',
                'non_proctology.agni as non_proc_agni',
                'non_proctology.koshta as non_proc_koshta',
                'non_proctology.avastha as non_proc_avastha'
            );

        // Get multiple filters array
        $filters = $request->input('multiple_filter', []);

        // Filter by doctor
        if (isset($filters['doctor_id']) && ! empty($filters['doctor_id'])) {
            $query->where('consultations.doctor_id', $filters['doctor_id']);
        }

        // Filter by patient
        if (isset($filters['patient_id']) && ! empty($filters['patient_id'])) {
            $query->where('consultations.patient_id', $filters['patient_id']);
        }

        // Filter by department
        if (isset($filters['department']) && ! empty($filters['department'])) {
            $query->where('consultations.type', $filters['department']);
        }

        // Filter by date range
        if (isset($request->from_date) && isset($request->to_date)) {
            $query->whereBetween('consultations.created_at', [
                \Carbon\Carbon::parse($request->from_date)->startOfDay(),
                \Carbon\Carbon::parse($request->to_date)->endOfDay(),
            ]);
        }

        // Conditional Filters based on Department
        $department = $filters['department'] ?? null;

        // Proctology Filters (apply if department is Proctology or not specified)
        if (empty($department) || strtolower($department) === 'proctology') {
            if (isset($filters['proc_chief_complaints']) && ! empty($filters['proc_chief_complaints'])) {
                $query->where('proctology.chief_complaints', 'like', '%' . $filters['proc_chief_complaints'] . '%');
            }

            if (isset($filters['proc_surgical_history']) && ! empty($filters['proc_surgical_history'])) {
                $query->where('proctology.surgical_history', 'like', '%' . $filters['proc_surgical_history'] . '%');
            }

            if (isset($filters['proc_co_morbidities']) && ! empty($filters['proc_co_morbidities'])) {
                $query->where('proctology.co_morbidities', 'like', '%' . $filters['proc_co_morbidities'] . '%');
            }

            if (isset($filters['proc_on_examination']) && ! empty($filters['proc_on_examination'])) {
                $query->where('proctology.on_examination', 'like', '%' . $filters['proc_on_examination'] . '%');
            }

            if (isset($filters['proc_dre']) && ! empty($filters['proc_dre'])) {
                $query->where('proctology.dre', 'like', '%' . $filters['proc_dre'] . '%');
            }

            if (isset($filters['proc_proctoscopy']) && ! empty($filters['proc_proctoscopy'])) {
                $query->where('proctology.proctoscopy', 'like', '%' . $filters['proc_proctoscopy'] . '%');
            }
        }

        // Non-Proctology Filters (apply if department is not Proctology or not specified)
        if (empty($department) || strtolower($department) !== 'proctology') {
            if (isset($filters['non_proc_chief_complaints']) && ! empty($filters['non_proc_chief_complaints'])) {
                $query->where('non_proctology.chief_complaints', 'like', '%' . $filters['non_proc_chief_complaints'] . '%');
            }

            if (isset($filters['non_proc_surgical_history']) && ! empty($filters['non_proc_surgical_history'])) {
                $query->where('non_proctology.surgical_history', 'like', '%' . $filters['non_proc_surgical_history'] . '%');
            }

            if (isset($filters['non_proc_co_morbidities']) && ! empty($filters['non_proc_co_morbidities'])) {
                $query->where('non_proctology.co_morbidities', 'like', '%' . $filters['non_proc_co_morbidities'] . '%');
            }

            if (isset($filters['non_proc_on_examination']) && ! empty($filters['non_proc_on_examination'])) {
                $query->where('non_proctology.on_examination', 'like', '%' . $filters['non_proc_on_examination'] . '%');
            }

            if (isset($filters['non_proc_prakriti']) && ! empty($filters['non_proc_prakriti'])) {
                $query->where('non_proctology.prakriti', 'like', '%' . $filters['non_proc_prakriti'] . '%');
            }

            if (isset($filters['non_proc_vikruti']) && ! empty($filters['non_proc_vikruti'])) {
                $query->where('non_proctology.vikruti', 'like', '%' . $filters['non_proc_vikruti'] . '%');
            }

            if (isset($filters['non_proc_agni']) && ! empty($filters['non_proc_agni'])) {
                $query->where('non_proctology.agni', 'like', '%' . $filters['non_proc_agni'] . '%');
            }

            if (isset($filters['non_proc_koshta']) && ! empty($filters['non_proc_koshta'])) {
                $query->where('non_proctology.koshta', 'like', '%' . $filters['non_proc_koshta'] . '%');
            }

            if (isset($filters['non_proc_avastha']) && ! empty($filters['non_proc_avastha'])) {
                $query->where('non_proctology.avastha', 'like', '%' . $filters['non_proc_avastha'] . '%');
            }
        }

        // Search functionality
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patients.first_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.last_name', 'like', '%' . $search . '%')
                    ->orWhere('patients.phone_no', 'like', '%' . $search . '%')
                    ->orWhere('patients.patient_number', 'like', '%' . $search . '%')
                    ->orWhere('doctors.name', 'like', '%' . $search . '%');
            });
        }

        // Sorting
        $sortBy    = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Map sort_by to actual column names
        switch ($sortBy) {
            case 'consultation_date':
                $query->orderBy('consultations.created_at', $sortOrder);
                break;
            case 'appointment_id':
                $query->orderBy('consultations.appointment_id', $sortOrder);
                break;
            case 'patient_name':
                $query->orderBy('patients.first_name', $sortOrder)
                    ->orderBy('patients.last_name', $sortOrder);
                break;
            case 'doctor_name':
                $query->orderBy('doctors.name', $sortOrder);
                break;
            default:
                $query->orderBy('consultations.created_at', $sortOrder);
                break;
        }

       $result = $query->paginate(env('PAGINATION', 25));
       $result->getCollection()->transform(function ($row) {
            $row->currency = $row->invoice->currency ?? '₹';
            return $row;
        });
        return $result; 
    }

    /**
     * Download consultation report as Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExcel(Request $request)
    {
        $query = Consultations::query()
            ->with('invoiceNumber:id,consultation_id,currency')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->join('users as doctors', 'consultations.doctor_id', '=', 'doctors.id')
            ->leftJoin('proctology', 'consultations.id', '=', 'proctology.consultation_id')
            ->leftJoin('non_proctology', 'consultations.id', '=', 'non_proctology.consultation_id')
            ->select(
                'consultations.*',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.phone_no as patient_phone',
                'patients.email as patient_email',
                'patients.patient_number',
                'patients.age as patient_age',
                'patients.gender as patient_gender',
                'doctors.name as doctor_name',

                // Proctology fields
                'proctology.abscess',
                'proctology.abscess_position',
                'proctology.any_other',
                'proctology.basis_of_high_low_riding',
                'proctology.chief_complaints as proc_chief_complaints',
                'proctology.co_morbidities as proc_co_morbidities',
                'proctology.co_morbidities_description as proc_co_morbidities_description',
                'proctology.crypt_cause',
                'proctology.diagnosis_summary as proc_diagnosis',
                'proctology.diet_plan as proc_diet_plan',
                'proctology.distant_visceral_communication',
                'proctology.dre as proc_dre',
                'proctology.examination_overview as proc_examination_overview',
                'proctology.external_opening_position',
                'proctology.internal_opening_distance',
                'proctology.fistula_recurrence',
                'proctology.fistula_recurrence_surgery_count',
                'proctology.fistula_remark',
                'proctology.internal_opening_position',
                'proctology.managements as proc_managements',
                'proctology.mri_fistula_gram',
                'proctology.no_of_external_opening_position',
                'proctology.no_of_fistula',
                'proctology.no_of_secondary_opening_position',
                'proctology.no_of_tracks_in_one_fistula',
                'proctology.on_examination as proc_on_examination',
                'proctology.other_investigation',
                'proctology.other_investigation',
                'proctology.posterior_fistulous_angle',
                'proctology.preliminary_diagnostic as proc_preliminary_diagnostic',
                'proctology.previous_scar',
                'proctology.previous_scar_position',
                'proctology.proctoscopy as proc_proctoscopy',
                'proctology.secondary_anal_valve',
                'proctology.secondary_opening_position',
                'proctology.sono_fistula_gram',
                'proctology.sonologist',
                'proctology.sonologist_findings',
                'proctology.surgical_history as proc_surgical_history',
                'proctology.tests as proc_tests',
                'proctology.treatment_plan as proc_treatment_plan',
                'proctology.type_of_crypt',
                'proctology.type_of_fistula_position',
                'proctology.type_of_fistula_sphincter',

                // Non-Proctology fields
                'non_proctology.chief_complaints as non_proc_chief_complaints',
                'non_proctology.surgical_history as non_proc_surgical_history',
                'non_proctology.co_morbidities as non_proc_co_morbidities',
                'non_proctology.co_morbidities_description as non_proc_co_morbidities_description',
                'non_proctology.on_examination as non_proc_on_examination',
                'non_proctology.treatment_plan as non_proc_treatment_plan',
                'non_proctology.tests as non_proc_tests',
                'non_proctology.diet_plan as non_proc_diet_plan',
                'non_proctology.food_advice as non_proc_food_advice',
                'non_proctology.yoga_asana as non_proc_yoga_asana',
                'non_proctology.prakriti as non_proc_prakriti',
                'non_proctology.vikruti as non_proc_vikruti',
                'non_proctology.agni as non_proc_agni',
                'non_proctology.koshta as non_proc_koshta',
                'non_proctology.avastha as non_proc_avastha'
            );

        // Get multiple filters array
        $filters = $request->input('multiple_filter', []);

        // Apply same filters
        if (isset($filters['doctor_id']) && ! empty($filters['doctor_id'])) {
            $query->where('consultations.doctor_id', $filters['doctor_id']);
        }

        if (isset($filters['patient_id']) && ! empty($filters['patient_id'])) {
            $query->where('consultations.patient_id', $filters['patient_id']);
        }

        if (isset($filters['department']) && ! empty($filters['department'])) {
            $query->where('consultations.type', $filters['department']);
        }

        if (isset($filters['from_date']) && isset($filters['to_date'])) {
            $query->whereBetween('consultations.created_at', [
                \Carbon\Carbon::parse($filters['from_date'])->startOfDay(),
                \Carbon\Carbon::parse($filters['to_date'])->endOfDay(),
            ]);
        }

        // Conditional Filters based on Department
        $department = $filters['department'] ?? null;

        // Proctology Filters (apply if department is Proctology or not specified)
        if (empty($department) || strtolower($department) === 'proctology') {
            if (isset($filters['proc_chief_complaints']) && ! empty($filters['proc_chief_complaints'])) {
                $query->where('proctology.chief_complaints', 'like', '%' . $filters['proc_chief_complaints'] . '%');
            }

            if (isset($filters['proc_surgical_history']) && ! empty($filters['proc_surgical_history'])) {
                $query->where('proctology.surgical_history', 'like', '%' . $filters['proc_surgical_history'] . '%');
            }

            if (isset($filters['proc_co_morbidities']) && ! empty($filters['proc_co_morbidities'])) {
                $query->where('proctology.co_morbidities', 'like', '%' . $filters['proc_co_morbidities'] . '%');
            }

            if (isset($filters['proc_on_examination']) && ! empty($filters['proc_on_examination'])) {
                $query->where('proctology.on_examination', 'like', '%' . $filters['proc_on_examination'] . '%');
            }

            if (isset($filters['proc_dre']) && ! empty($filters['proc_dre'])) {
                $query->where('proctology.dre', 'like', '%' . $filters['proc_dre'] . '%');
            }

            if (isset($filters['proc_proctoscopy']) && ! empty($filters['proc_proctoscopy'])) {
                $query->where('proctology.proctoscopy', 'like', '%' . $filters['proc_proctoscopy'] . '%');
            }
        }

        // Non-Proctology Filters (apply if department is not Proctology or not specified)
        if (empty($department) || strtolower($department) !== 'proctology') {
            if (isset($filters['non_proc_chief_complaints']) && ! empty($filters['non_proc_chief_complaints'])) {
                $query->where('non_proctology.chief_complaints', 'like', '%' . $filters['non_proc_chief_complaints'] . '%');
            }

            if (isset($filters['non_proc_surgical_history']) && ! empty($filters['non_proc_surgical_history'])) {
                $query->where('non_proctology.surgical_history', 'like', '%' . $filters['non_proc_surgical_history'] . '%');
            }

            if (isset($filters['non_proc_co_morbidities']) && ! empty($filters['non_proc_co_morbidities'])) {
                $query->where('non_proctology.co_morbidities', 'like', '%' . $filters['non_proc_co_morbidities'] . '%');
            }

            if (isset($filters['non_proc_on_examination']) && ! empty($filters['non_proc_on_examination'])) {
                $query->where('non_proctology.on_examination', 'like', '%' . $filters['non_proc_on_examination'] . '%');
            }

            if (isset($filters['non_proc_prakriti']) && ! empty($filters['non_proc_prakriti'])) {
                $query->where('non_proctology.prakriti', 'like', '%' . $filters['non_proc_prakriti'] . '%');
            }

            if (isset($filters['non_proc_vikruti']) && ! empty($filters['non_proc_vikruti'])) {
                $query->where('non_proctology.vikruti', 'like', '%' . $filters['non_proc_vikruti'] . '%');
            }

            if (isset($filters['non_proc_agni']) && ! empty($filters['non_proc_agni'])) {
                $query->where('non_proctology.agni', 'like', '%' . $filters['non_proc_agni'] . '%');
            }

            if (isset($filters['non_proc_koshta']) && ! empty($filters['non_proc_koshta'])) {
                $query->where('non_proctology.koshta', 'like', '%' . $filters['non_proc_koshta'] . '%');
            }

            if (isset($filters['non_proc_avastha']) && ! empty($filters['non_proc_avastha'])) {
                $query->where('non_proctology.avastha', 'like', '%' . $filters['non_proc_avastha'] . '%');
            }
        }

        // Sorting
        $sortBy    = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Map sort_by to actual column names
        switch ($sortBy) {
            case 'consultation_date':
                $query->orderBy('consultations.created_at', $sortOrder);
                break;
            case 'appointment_id':
                $query->orderBy('consultations.appointment_id', $sortOrder);
                break;
            case 'patient_name':
                $query->orderBy('patients.first_name', $sortOrder)
                    ->orderBy('patients.last_name', $sortOrder);
                break;
            case 'doctor_name':
                $query->orderBy('doctors.name', $sortOrder);
                break;
            default:
                $query->orderBy('consultations.created_at', $sortOrder);
                break;
        }

        $data = $query->get();

       $data->getCollection()->transform(function ($row) {
            $row->currency = $row->invoice->currency ?? '₹';
            return $row;
        });

        $fileName = 'consultation_report_' . time() . '.xlsx';
        $tempPath = storage_path('app/public/' . $fileName);

        $exportData = $data->map(function ($row) {
            // Helper function to extract labels from JSON array
            $extractLabels = function ($jsonData) {
                if (empty($jsonData)) {
                    return '';
                }

                $decodedArray = json_decode($jsonData, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedArray)) {
                    $labels = [];
                    foreach ($decodedArray as $item) {
                        if (is_array($item) && isset($item['label'])) {
                            $labels[] = $item['label'];
                        } elseif (is_array($item) && isset($item['value'])) {
                            $labels[] = $item['value'];
                        }
                    }
                    return implode(', ', $labels);
                }

                return $jsonData;
            };

            // Helper function to strip HTML tags
            $stripHtml = function ($text) {
                if (empty($text)) {
                    return '';
                }
                return strip_tags($text);
            };

            // Extract labels for all JSON fields
            $managementNames = $extractLabels($row->proc_managements);
            $chiefComplaints = $extractLabels($row->proc_chief_complaints);
            $surgicalHistory = $extractLabels($row->proc_surgical_history);
            $coMorbidities = $extractLabels($row->proc_co_morbidities);
            $onExamination = $extractLabels($row->proc_on_examination);
            $dre = $extractLabels($row->proc_dre);
            $proctoscopy = $extractLabels($row->proc_proctoscopy);
            $tests = $extractLabels($row->proc_tests);
            $dietPlan = $extractLabels($row->proc_diet_plan);

            // Non-Proctology fields
            $nonProcChiefComplaints = $extractLabels($row->non_proc_chief_complaints);
            $nonProcSurgicalHistory = $extractLabels($row->non_proc_surgical_history);
            $nonProcCoMorbidities = $extractLabels($row->non_proc_co_morbidities);
            $nonProcOnExamination = $extractLabels($row->non_proc_on_examination);
            $nonProcTests = $extractLabels($row->non_proc_tests);
            $nonProcDietPlan = $extractLabels($row->non_proc_diet_plan);

            $internalOpeningPositionLevel = '';
            if (! empty($row->internal_opening_position)) {
                $items = explode('#', $row->internal_opening_position);
                $formatted = [];
                for ($i = 0; $i < count($items); $i += 2) {
                    if (isset($items[$i + 1])) {
                        $formatted[] = $items[$i] .' '. $items[$i + 1]. ', ' ;
                    } else {
                        $formatted[] = $items[$i];
                    }
                }
                $internalOpeningPositionLevel = implode(', ', $formatted);
            }


            return [
                'Date'                                => \Carbon\Carbon::parse($row->created_at)->format('d/m/Y'),
                'Patient Name'                        => $row->patient_first_name . ' ' . $row->patient_last_name,
                'Patient Number'                      => $row->patient_number ?? '',
                'Patient Age'                         => $row->patient_age ?? '',
                'Patient Gender'                      => $row->patient_gender ?? '',
                'Patient Phone'                       => $row->patient_phone ?? '',
                'Patient Email'                       => $row->patient_email ?? '',
                'Doctor Name'                         => $row->doctor_name ?? '',
                'Department'                          => $row->type ?? '',

                // Proctology Fields
                'Proctology Chief Complaints'               => $chiefComplaints,
                'Proctology Surgical History'               => $surgicalHistory,
                'Proctology Co-morbidities'                 => $coMorbidities,
                'Proctology Co-morbidities Description'     => $row->proc_co_morbidities_description ?? '',
                'Proctology On Examination'                 => $onExamination,
                'Proctology DRE'                            => $dre,
                'Proctology Proctoscopy'                    => $proctoscopy,
                'Proctology Diagnosis'                      => $row->proc_diagnosis ?? '',
                'Proctology Examination Overview'           => $row->proc_examination_overview ?? '',
                'Proctology Preliminary Diagnostic'         => $row->proc_preliminary_diagnostic ?? '',
                'Proctology Treatment Plan'                 => $stripHtml($row->proc_treatment_plan ?? ''),
                'Proctology Tests'                          => $tests,
                'Proctology Diet Plan'                      => $dietPlan,
                'Proctology Management'                     => $managementNames,
                'Abscess?'                         => $row->abscess ?? '',
                'Abscess Position'                 => $row->abscess_position ?? '',
                'Previous Scar'                      => $row->previous_scar ?? '',
                'Previous Scar Position'               => $row->previous_scar_position ?? '',
                'No of Fistula'                    => $row->no_of_fistula ?? '',
                'No of Tracks in one Fistula'      => $this->formatArrayValues($row->no_of_tracks_in_one_fistula),

                'No of External Opening'           => $this->formatArrayValues($row->no_of_external_opening_position),
                'Position of External Opening'     => $this->formatArrayValues($row->external_opening_position, "o'clock, ", "o'clock"),
                

                'Type of Fistula'                  => $this->formatArrayValues($row->type_of_fistula_position),
                'Type of Fistula Sphincter'        => $this->formatArrayValues($row->type_of_fistula_sphincter),
                
                'No of Secondary Opening Position' => $this->formatArrayValues($row->no_of_secondary_opening_position),
                'Position of Secondary Opening'    => $this->formatArrayValues($row->secondary_anal_valve, "o'clock, ", "o'clock"),
                
                'Internal Opening Position(level)' => $internalOpeningPositionLevel ?? '',
                'Internal Opening Distance'        => $this->formatArrayValues($row->internal_opening_distance),
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

                // 'Type of Fistula'                  => $row->type_of_fistula_position ?? '',
                // 'Type of Fistula Sphincter'        => $row->type_of_fistula_sphincter ?? '',
                // 'No of Tracks in one Fistula'      => $row->no_of_tracks_in_one_fistula ?? '',
                // 'No of Fistula'                    => $row->no_of_fistula ?? '',
                // 'Internal Opening Position'        => ($row->secondary_opening_position!='') ? implode("o'clock, ", explode("#",$row->secondary_opening_position))."o'clock" : '',
                // 'External Opening Position'        => ($row->external_opening_position!='') ? implode("o'clock, ", explode("#",$row->external_opening_position))."o'clock" : '',
                // 'Secondary Anal Valve'             => ($row->secondary_anal_valve!='') ? implode("o'clock, ", explode("#",$row->secondary_anal_valve))."o'clock" : '',
                // 'Internal Opening Position(level)' => $internalOpeningPositionLevel ?? '',
                // 'Posterior Fistulous Angle'        => ($row->posterior_fistulous_angle!='') ? $row->posterior_fistulous_angle.' degree' : '',
              
                // 'Sonologist'                       => $row->sonologist ?? '',
                // 'Sonologist Findings'              => $row->sonologist_findings ?? '',
                // 'Other Investigation'              => $row->other_investigation ?? '',
                // 'Management'                       => $managementNames,
                // 'Fistula Recurrence'               => ucwords(str_replace('_', ' ', $row->fistula_recurrence)) ?? '',
                // 'Fistula Recurrence Surgery Count' => $row->fistula_recurrence_surgery_count ?? '',

                // Non-Proctology Fields
                'Non-Proctology Chief Complaints'           => $nonProcChiefComplaints,
                'Non-Proctology Surgical History'           => $nonProcSurgicalHistory,
                'Non-Proctology Co-morbidities'             => $nonProcCoMorbidities,
                'Non-Proctology Co-morbidities Description' => $row->non_proc_co_morbidities_description ?? '',
                'Non-Proctology On Examination'             => $nonProcOnExamination,
                'Non-Proctology Treatment Plan'             => $stripHtml($row->non_proc_treatment_plan ?? ''),
                'Non-Proctology Tests'                      => $nonProcTests,
                'Non-Proctology Diet Plan'                  => $nonProcDietPlan,
                'Non-Proctology Food Advice'                => $row->non_proc_food_advice ?? '',
                'Non-Proctology Yoga Asana'                 => $row->non_proc_yoga_asana ?? '',
                'Non-Proctology Prakriti'                   => $row->non_proc_prakriti ?? '',
                'Non-Proctology Vikruti'                    => $row->non_proc_vikruti ?? '',
                'Non-Proctology Agni'                       => $row->non_proc_agni ?? '',
                'Non-Proctology Koshta'                     => $row->non_proc_koshta ?? '',
                'Non-Proctology Avastha'                    => $row->non_proc_avastha ?? '',

                // Consultation Fields
                'Total Amount'                        => $row->currency.(float) ($row->total_amount ?? 0),
                'Payment Status'                      => $row->payment_status ?? '',
            ];
        });

        (new FastExcel($exportData))->export($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}