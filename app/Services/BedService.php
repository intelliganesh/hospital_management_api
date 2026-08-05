<?php

namespace App\Services;

use App\Models\Bed;
use Illuminate\Http\Request;
use App\Traits\BedValidation;
use App\Contracts\CRUDContract;
use App\Contracts\FilterContract;
use App\Facades\AutoIdGenerateFacade;
use App\Enums\ServiceType;
use App\Models\IPD;

class BedService implements CRUDContract, FilterContract
{

    use BedValidation;

    private $columns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns = Bed::$columns;
        $this->checkValidationService = $checkValidationService;
    }


    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Bed::create($request->all());
    }


    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $bed = Bed::findOrFail($id);
        $bed->update($request->all());
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        // write code here
    }

    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $bed = Bed::findOrFail($id);
        $bed->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Bed
     */
    public function get(string $id): mixed
    {
        $bed = Bed::with(['room:id,name,room_number'])->findOrFail($id);
        return $bed;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $bed = Bed::query();
        if ($request->has('search')) {
            $bed = $this->search($request->input('search'), $bed);
        }
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'name';
            $sortOrder = $request->sort_order ?? 'desc';
            $bed = $bed->orderBy($sortBy, $sortOrder);
        }
        return $bed->select($this->columns)->with(['room:id,name,room_number'])->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of getBedsList
     * @param string|array $columnNames
     * @param int $roomId
     * @return \Illuminate\Database\Eloquent\Collection<int, Bed>
     */
    public function getBedsList(string|array $columnNames,int $roomId): mixed
    {
        $query =$query = Bed::select($columnNames)
        ->where('status', 'Available')
            ->whereNotIn('id', function ($query) {
                $query->select('bed_id')
                    ->from('ipd')
                    ->where('status', 'Admitted')
                    ->where('bed_id', '!=', null);
            });
        if ($roomId !== 0) {
            $query->where('room_id', $roomId);
        }
        return $query->get();
    }

    /**
     * Summary of search
     * @param string $searchText
     * @param  $data
     */
    public function search(string $searchText, $data)
    {
        foreach ($this->columns as $column) {
            $data->orWhere($column, 'like', '%' . $searchText . '%');
        }
        return $data;
    }

    /**
     * @deprecated this function is not in use
     */
    public function filterByDateRange(string $searchText, $data)
    {
        //code here
    }


    /**
     * @deprecated this function is not in use
     */
    public function sortData(string $searchText, $data)
    {
        //code here
    }
}