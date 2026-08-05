<?php
namespace App\Services;

use App\Attributes\Transactional;
use App\Contracts\CRUDContract;
// use Barryvdh\DomPDF\Facade\Pdf;
use App\Contracts\FilterContract;
use App\Models\PostSurgeryDetails;
use App\Models\PostSurgeryFollowUp;
use App\Services\CheckValidation;
use App\Traits\PostSurgeryFollowUpValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Rap2hpoutre\FastExcel\FastExcel;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Arr;
use App\Models\Consultations;

class PostSurgeryFollowUpService implements CRUDContract, FilterContract
{
    use PostSurgeryFollowUpValidation;

    private $filter;
    private $columns;
    private $checkValidationService;
    private $postSurgeryDetailsColumns;
    private $postSurgeryFollowUpService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     * @param \App\Models\PostSurgeryFollowUp $postSurgeryFollowUpService
     */
    public function __construct(CheckValidation $checkValidationService, PostSurgeryFollowUp $postSurgeryFollowUpService)
    {
        $this->filter                     = PostSurgeryFollowUp::$filter;
        $this->columns                    = PostSurgeryFollowUp::$columns;
        $this->checkValidationService     = $checkValidationService;
        $this->postSurgeryFollowUpService = $postSurgeryFollowUpService;
        $this->postSurgeryDetailsColumns  = PostSurgeryDetails::$postSurgeryDetailsColumns;

    }

    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        foreach ($this->columns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
    }

    /**
     * Summary of filterMultipleFields
     * @param mixed $request
     * @param mixed $data
     */
    public function filterMultipleFields($request, $data)
    {
        foreach ($this->filter as $column) {
            if (! empty($request[$column])) {
                $data->where($column, $request[$column]);
            }
        }
        return $data;
    }

    /**
     * @deprecated this function is not in use
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }

    /**
     * @deprecated this function is not in use
     */
    public function sortData(string $searchText, $data)
    {
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create  postSurgeryFollowUp  record within a secure transaction')]
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        if($request->appointment_number){
            $consultation=Consultations::where('appointment_number', $request->appointment_number)->first();
            if($consultation){
                $request->merge(['consultation_id' => $consultation->id]);
            }
        }
        PostSurgeryFollowUp::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update  postSurgeryFollowUp  record within a secure transaction')]
    public function update(Request $request, string | null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        if($request->appointment_number){
            $consultation=Consultations::where('appointment_number', $request->appointment_number)->first();
            if($consultation){
                $request->merge(['consultation_id' => $consultation->id]);
            }
        }
        PostSurgeryFollowUp::findOrFail($id)->update($request->all());
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string | null $id): void
    {
        //code here
    }

    /**
     * Summary of delete
     * @param string $id
     * @return void
     */
    public function delete(string $id): void
    {
        PostSurgeryFollowUp::findOrFail($id)->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return PostSurgeryFollowUp
     */
    public function get(string $id): PostSurgeryFollowUp
    {
        return PostSurgeryFollowUp::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $postSurgeryFollowUp = PostSurgeryFollowUp::query();
        if ($request?->has('search')) {
            $searchValue         = $request->search;
            $postSurgeryFollowUp = $this->search($searchValue, $postSurgeryFollowUp);
        }

        if ($request?->has('sort_by')) {
            $sortBy              = $request->sort_by ?? '';
            $sortOrder           = $request->sort_order ?? 'desc';
            $postSurgeryFollowUp = $postSurgeryFollowUp->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $postSurgeryFollowUp = $this->filterMultipleFields($request->multiple_filter, $postSurgeryFollowUp);
        }
        $postSurgeryFollowUp=$postSurgeryFollowUp->where('post_surgery_details_id', $request->post_surgery_id);
        if ($request->consultation_id){
            $postSurgeryFollowUp=$postSurgeryFollowUp->where('consultation_id', $request->consultation_id);
        }
        // return $postSurgeryFollowUp->where('consultation_id', $request->consultation_id)->where('post_surgery_details_id', $request->post_surgery_id)->select($this->columns)->paginate(env('PAGINATION', 25));
        return $postSurgeryFollowUp->select($this->columns)->paginate(env('PAGINATION', 25));
        // return $postSurgeryFollowUp->where('patient_id', $request->consultation_id)->where('post_surgery_details_id', $request->post_surgery_id)->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of followUpDownload
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function followUpDownload(Request $request)
    {
        $postSurgeryFollowUps = PostSurgeryFollowUp::with('postSurgeryDetails.patient')
            ->where('post_surgery_details_id', $request->post_surgery_details_id)
            ->get();

        $fileName = 'post_surgery_follow_up_' . time() . '.xlsx';
        $path     = storage_path('app/public/' . $fileName);

        (new FastExcel($postSurgeryFollowUps))->export($path, function ($item) {
            $patient = $item->postSurgeryDetails->patient->name ?? null;
            $age     = $item->postSurgeryDetails->patient->age ?? null;

            return [

                "Appointment Number"    => $item->appointment_number,
                'Date'                  => \Carbon\Carbon::parse($item->date)->format('d-m-Y'),
                'Patient Name'          => $patient ? $patient : 'N/A',
                'Patient Age'           => $age ? $age : 'N/A',
                'KS Changed'            => $item->ks_changed,
                'Dressing'              => $item->dressing,
                'Partial Lay open'      => $item->partial_lay_open,
                'Follow up examination' => $item->follow_up_examination,
                'New abscess threading' => $item->new_abscess_threading,
                'Cut Through/any other' => $item->cut_through,
            ];
        });

        return response()->download($path)->deleteFileAfterSend(true);
    }

    /**
     * Summary of getPostSurgeryFollowUpDownload
     * @param string $id
     * @return string
     */
    public function getPostSurgeryFollowUpDownload(string $id)
    {
        $postSurgeryFollowUps = PostSurgeryFollowUp::with('postSurgeryDetails.patient')
            ->where('post_surgery_details_id', $id)
            ->get();

        $html = view('templates.downloads.post-surgery-follow-up', [
            'postSurgeryFollowUps' => $postSurgeryFollowUps,
        ]);

        $fileName = 'post_surgery_follow_up_' . $id . '_' . time() . '.pdf';
        $filePath = storage_path("app/public/pdfs/{$fileName}");

        if (! Storage::disk('public')->exists('pdfs')) {
            Storage::disk('public')->makeDirectory('pdfs');
        }

        Browsershot::html($html)
            ->format('A4')
            ->margins(5, 5, 5, 5)
            ->noSandbox()
            ->waitUntilNetworkIdle()
            ->setOption('printBackground', true)
            ->savePdf($filePath);

        return asset("storage/pdfs/{$fileName}");
    }

    /**
     * Summary of postSurgeryDetails
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return void
     */
    public function postSurgeryDetails(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'date'              => 'required',
            // 'consultation_id'   => 'required',
            'post_surgery_name' => 'required',
        ]);

        if ($validate->fails()) {
            throw new ValidationException($validate);
        }

        PostSurgeryDetails::create($request->all());
    }

    /**
     * Summary of postSurgeryDetailsList
     * @param \Illuminate\Http\Request $request
     */
    public function postSurgeryDetailsList(Request $request)
    {
        return PostSurgeryDetails::where('patient_id', $request->patient_id)->select($this->postSurgeryDetailsColumns)->get();
    }

    public function updateOrCreatePostSurgeryDetails($consultation_id,$data)
    {
        $array = json_decode(json_encode($data), true);
        $postData= Arr::except($array, ['id']);
        // $postSurgeryDetails = PostSurgeryDetails::updateOrCreate(['id' => $consultation_id], $postData['post_surgery_details']);
        $postSurgeryFollowUp = PostSurgeryFollowUp::updateOrCreate(['consultation_id'=>$consultation_id], $postData);

        return $postSurgeryFollowUp;
    }

    public function getByDynamicColumn($id,$columnName)
    {
        if($columnName=="consultation_id"){
            $postSurgeryFollowUp=PostSurgeryFollowUp::where('consultation_id', $id)->first();
            if(!is_null($postSurgeryFollowUp)){
                $postSurgeryDetails=PostSurgeryDetails::find($postSurgeryFollowUp->post_surgery_details_id);
                 $postSurgeryFollowUp=$postSurgeryFollowUp->toArray();
                $postSurgeryFollowUp['post_surgery_details']=$postSurgeryDetails->toArray();
            }
        }else{
            $postSurgeryDetails=PostSurgeryDetails::where($columnName, $id)->first();
            $postSurgeryFollowUp=null;
            if(!is_null($postSurgeryDetails)){
                $postSurgeryFollowUp=PostSurgeryFollowUp::where('post_surgery_details_id', $postSurgeryDetails->id)->first();
                if(!is_null($postSurgeryFollowUp)){
                    $postSurgeryFollowUp=$postSurgeryFollowUp->toArray();
                    $postSurgeryFollowUp['post_surgery_details']=$postSurgeryDetails->toArray();
                }
            }
        }

        return $postSurgeryFollowUp;
    }

}
