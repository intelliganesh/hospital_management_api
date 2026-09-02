<?php
namespace App\Services\Reports;

use App\Models\IPD;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class IPDReportService
{
    /**
     * Get basic IPD report with filters.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function all(Request $request)
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $request);
        $this->applySorting($query, $request);

        $result = $query->paginate(env('PAGINATION', 25));
        $result->getCollection()->transform(function ($row) {
            return $this->appendReportFields($row);
        });

        return $result;
    }

    /**
     * Download basic IPD report as Excel.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadExcel(Request $request)
    {
        $query = $this->baseQuery();

        $this->applyFilters($query, $request);
        $this->applySorting($query, $request);

        $data = $query->get()->map(function ($row) {
            $row = $this->appendReportFields($row);

            return [
                'Admission Date'          => $this->formatDateTime($row->admission_date_time),
                'Discharge Date'          => $this->formatDateTime($row->discharge_date_time),
                'IPD Number'              => $row->ipd_number ?? '',
                'Patient Name'            => $row->patient_name ?? '',
                'Patient Number'          => $row->patient_number ?? '',
                'Patient Age'             => $row->patient_age ?? '',
                'Patient Phone'           => $row->patient_phone ?? '',
                'Patient Email'           => $row->patient_email ?? '',
                'Patient Address'         => $row->patient_address ?? '',
                'Attendant Name'          => $row->patient_attendant_name ?? '',
                'Attendant Phone'         => $row->patient_attendant_phone ?? '',
                'Doctor Name'             => $row->doctor_name ?? '',
                'Doctor Phone'            => $row->doctor_phone ?? '',
                'Ward Number'             => $row->ward_number ?? '',
                'Ward Type'               => $row->ward_type ?? '',
                'Room Number'             => $row->room_number ?? '',
                'Room Type'               => $row->room_type ?? '',
                'Bed Number'              => $row->bed_number ?? '',
                'Status'                  => $row->status ?? '',
                'Discharge Summary Type'  => $row->report_summary_type ?? '',
                'Surgery Count'           => $row->surgery_count ?? 0,
            ];
        });

        $fileName = 'ipd_report_' . time() . '.xlsx';
        $tempPath = storage_path('app/public/' . $fileName);

        (new FastExcel($data))->export($tempPath);

        return response()->download($tempPath)->deleteFileAfterSend(true);
    }

    private function baseQuery()
    {
        return IPD::query()
            ->withCount('surgery')
            ->leftJoin('ipd_discharge_summary', 'ipd.id', '=', 'ipd_discharge_summary.ipd_id')
            ->select(
                'ipd.*',
                'ipd_discharge_summary.summary_type as discharge_summary_type'
            );
    }

    private function applyFilters($query, Request $request): void
    {
        $filters = $request->input('multiple_filter', []);

        $equalsFilters = [
            'doctor_id'  => 'ipd.doctor_id',
            'patient_id' => 'ipd.patient_id',
            'status'     => 'ipd.status',
            'ward_id'    => 'ipd.ward_id',
            'room_id'    => 'ipd.room_id',
            'bed_id'     => 'ipd.bed_id',
        ];

        foreach ($equalsFilters as $filter => $column) {
            if (! empty($filters[$filter])) {
                $query->where($column, $filters[$filter]);
            }
        }

        if (! empty($filters['summary_type'])) {
            $summaryType = $filters['summary_type'];

            if ($summaryType === 'surgical') {
                $query->where(function ($q) {
                    $q->whereHas('surgery')
                        ->orWhere('ipd_discharge_summary.summary_type', 'surgical');
                });
            }

            if ($summaryType === 'non_surgical') {
                $query->where(function ($q) {
                    $q->whereDoesntHave('surgery')
                        ->orWhere('ipd_discharge_summary.summary_type', 'non_surgical');
                });
            }
        }

        $fromDate = $request->input('from_date', $filters['from_date'] ?? null);
        $toDate = $request->input('to_date', $filters['to_date'] ?? null);

        if (! empty($fromDate) && ! empty($toDate)) {
            $query->whereBetween('ipd.admission_date_time', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);
        }

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ipd.patient_name', 'like', '%' . $search . '%')
                    ->orWhere('ipd.patient_number', 'like', '%' . $search . '%')
                    ->orWhere('ipd.patient_phone', 'like', '%' . $search . '%')
                    ->orWhere('ipd.ipd_number', 'like', '%' . $search . '%')
                    ->orWhere('ipd.doctor_name', 'like', '%' . $search . '%')
                    ->orWhere('ipd.ward_number', 'like', '%' . $search . '%')
                    ->orWhere('ipd.room_number', 'like', '%' . $search . '%')
                    ->orWhere('ipd.bed_number', 'like', '%' . $search . '%')
                    ->orWhere('ipd.status', 'like', '%' . $search . '%');
            });
        }
    }

    private function applySorting($query, Request $request): void
    {
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        switch ($request->input('sort_by', 'admission_date_time')) {
            case 'discharge_date_time':
                $query->orderBy('ipd.discharge_date_time', $sortOrder);
                break;
            case 'ipd_number':
                $query->orderBy('ipd.ipd_number', $sortOrder);
                break;
            case 'patient_name':
                $query->orderBy('ipd.patient_name', $sortOrder);
                break;
            case 'doctor_name':
                $query->orderBy('ipd.doctor_name', $sortOrder);
                break;
            case 'status':
                $query->orderBy('ipd.status', $sortOrder);
                break;
            case 'created_at':
                $query->orderBy('ipd.created_at', $sortOrder);
                break;
            default:
                $query->orderBy('ipd.admission_date_time', $sortOrder);
                break;
        }
    }

    private function appendReportFields($row)
    {
        $row->report_summary_type = $row->discharge_summary_type
            ?: (($row->surgery_count ?? 0) > 0 ? 'surgical' : 'non_surgical');

        return $row;
    }

    private function formatDateTime($dateTime): string
    {
        if (empty($dateTime)) {
            return '';
        }

        return Carbon::parse($dateTime)->format('d/m/Y h:i A');
    }
}
