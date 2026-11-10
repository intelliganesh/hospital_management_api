<?php

namespace App\Services\Master;

use Illuminate\Http\Request;
use App\Contracts\CRUDContract;
use App\Models\Master\Shloka;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
use App\Traits\ShlokaValidation;

class ShlokaService implements CRUDContract, FilterContract
{

    use ShlokaValidation;

    private $columns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns = Shloka::$columns;
        $this->checkValidationService = $checkValidationService;
    }


    /**
     * Summary of search
     * @param string $searchText
     * @param mixed $data
     */
    public function search(string $searchText, $data)
    {
        return $data->where(function ($q) use ($searchText) {
            $q->where('title', 'like', '%' . $searchText . '%')
                ->orWhere('verse_text', 'like', '%' . $searchText . '%')
                ->orWhere('status', 'like', '%' . $searchText . '%');
        });
    }

    public function filterMultipleFields($request, $data)
    {
        if (isset($request['title']) && $request['title'] != null) {
            $data = $data->where('title', 'like', '%' . $request['title'] . '%');
        }
        if (isset($request['status']) && $request['status'] != null) {
            $data = $data->where('status', $request['status']);
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
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        Shloka::create($request->all());
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function update(Request $request, string|null $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true));
        $shloka = Shloka::findOrFail($id);
        $shloka->update($request->all());
    }

    /**
     * @deprecated this function is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
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
        $shloka = Shloka::findOrFail($id);
        $shloka->delete();
    }

    /**
     * Summary of get
     * @param string $id
     * @return Shloka
     */
    public function get(string $id): Shloka
    {
        return Shloka::findOrFail($id);
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request): mixed
    {
        $shloka = Shloka::query();

        if ($request->has('search')) {
            $searchValue = $request->search;
            $shloka = $this->search($searchValue, $shloka);
        }

        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'title';
            $sortOrder = $request->sort_order ?? 'desc';
            $shloka = $shloka->orderBy($sortBy, $sortOrder);
        }

        if ($request->has('multiple_filter')) {
            $shloka = $this->filterMultipleFields($request->multiple_filter, $shloka);
        }

        return $shloka->select($this->columns)->paginate(env('PAGINATION', 25));

    }


    /**
     * Summary of optionsList
     * @return \Illuminate\Database\Eloquent\Collection<int, Shloka>
     */
    public function optionsList()
    {
        return Shloka::where('status', 'Active')->orderBy('title')->get();
    }

}