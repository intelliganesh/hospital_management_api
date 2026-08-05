<?php

namespace App\Services\Master;


use App\Models\Master\Rooms;
use App\Models\Ward;
use App\Services\WardService;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use App\Contracts\CRUDContract;
use App\Traits\FieldValuesTrait;
use App\Attributes\Transactional;
use App\Services\CheckValidation;
use App\Services\GetImageService;
use App\Contracts\FilterContract;
use App\Traits\RoomsValidationTrait;
use App\Facades\AutoIdGenerateFacade;
use App\Enums\ServiceType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class RoomService extends GetImageService implements CRUDContract, FilterContract
{

    use ResponseTrait;
    use FieldValuesTrait;
    use RoomsValidationTrait;

    private $column;
    private $wardService;
    private $checkValidationService;


    /**
     * Summary of __construct
     * @param \App\Services\WardService $wardService
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService, WardService $wardService)
    {
        $this->column = Rooms::$column;
        $this->wardService = $wardService;
        $this->checkValidationService = $checkValidationService;
    }


    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Create rooms record within a secure transaction')]
    public function create(Request $request): void
    {
        $validator = $this->validate($request);
        $this->checkValidationService->checkValidation($validator);
        Rooms::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return bool
     */
    #[Transactional(secure: true, requiredRole: null, description: 'Update rooms record within a secure transaction')]
    public function update(Request $request, string|null $id): void
    {
        $validator = $this->validate($request, true, $id);
        $this->checkValidationService->checkValidation($validator);
        $rooms = Rooms::findOrFail($id);
        // if (!$rooms) {
        //     throw new NotFoundHttpException('Room data not found.');
        // }
        $rooms->update($request->all());
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        // write code here.
    }

    /**
     * Summary of delete
     * @param string $id
     * @return bool
     */
    public function delete(string $id): void
    {
        $rooms = Rooms::findOrFail($id);
        // if (!$rooms) {
        //     throw new NotFoundHttpException('Room data not found.');
        // }
        $rooms->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Rooms|null
     */
    public function get(string $id): mixed
    {
        $rooms = Rooms::with(['ward:id,name,ward_number'])->findOrFail($id);
        // if (!$rooms) {
        //     throw new NotFoundHttpException('Room data not found.');
        // }
        return $rooms;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return \Illuminate\Database\Eloquent\Collection<int, Rooms>
     */
    public function all(?Request $request): mixed
    {
        $rooms = Rooms::query();
        if ($request->has('search')) {
            $rooms = $this->search($request->input('search'), $rooms);
        }
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            $rooms = $rooms->orderBy($sortBy, $sortOrder);
        }
        return $rooms->select($this->column)->with(['ward:id,name,ward_number'])->paginate(env('PAGINATION', 25));
    }



    /**
     * Summary of getRoomsList
     * @param string|array $columnNames
     * @param int $wardId
     * @return \Illuminate\Database\Eloquent\Collection<int, Rooms>
     */
    public function getRoomsList(string|array $columnNames,int $wardId): mixed
    {
        $query = Rooms::select($columnNames);
        if ($wardId !== 0) {
            $query->where('ward_id', $wardId);
        }
        return $query->get();
    }


    /**
     * Summary of search
     * @param string $search
     * @param mixed $user
     */
    public function search(string $searchText, $data)
    {

        foreach ($this->column as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;

        // return $data->where('name', 'like', '%' . $searchText . '%')
        //     ->orWhere('type', 'like', '%' . $searchText . '%')
        //     ->orWhere('floor', 'like', '%' . $searchText . '%')
        //     ->orWhere('status', 'like', '%' . $searchText . '%')
        //     ->orWhere('capacity', 'like', '%' . $searchText . '%')
        //     ->orWhere('ward_type', 'like', '%' . $searchText . '%')
        //     ->orWhere('ward_name', 'like', '%' . $searchText . '%');
    }

    /**
     * @deprecated This method is not used
     */
    public function filterByDateRange(string $searchText, $data)
    {
        // write code here
    }

    /**
     * @deprecated This method is not used.
     * 
     */
    public function sortData(string $searchText, $data)
    {
        // write code here
    }

}