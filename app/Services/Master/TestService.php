<?php

namespace App\Services\Master;

use Throwable;
use AutoIdGenerate;
use DepartmentTypeData;
use App\Enums\ServiceType;
use App\Models\Master\Test;
use Illuminate\Http\Request;
use App\Traits\TestValidation;
use App\Contracts\CRUDContract;
use App\Services\CheckValidation;
use App\Contracts\FilterContract;
// use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TestService implements CRUDContract, FilterContract
{
    use TestValidation;

    private $columns;
    private $checkValidationService;

    /**
     * Summary of __construct
     * @param \App\Services\CheckValidation $checkValidationService
     */
    public function __construct(CheckValidation $checkValidationService)
    {
        $this->columns = Test::$columns;
        $this->checkValidationService = $checkValidationService;
    }


    /**
     * Summary of get
     * @param string $id
     * @return Test
     */
    public function get(string $id): Test
    {
        $test = Test::findOrFail($id);
        // if (!$test) {
        //     throw new NotFoundHttpException('Data not found.');
        // }
        return $test;
    }

    /**
     * Summary of all
     * @param mixed $request
     * @return mixed
     */
    public function all(?Request $request = null): mixed
    {
        $test = Test::query();
        if ($request->has('search')) {
            $searchValue = $request->search;
            $test = $this->search($searchValue, $test);
        }
        if ($request->has('sort_by')) {
            $sortBy = $request->sort_by ?? 'test_name';
            $sortOrder = $request->sort_order ?? 'desc';
            $test = $test->orderBy($sortBy, $sortOrder);
        }
        return $test->select($this->columns)->paginate(env('PAGINATION', 25));
    }

    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void
    {
        $this->checkValidationService->checkValidation($this->validate($request));
        $test = ['test_number' => AutoIdGenerate::generateId(ServiceType::Test)];
        Test::create(array_merge($request->all(), $test));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function update(Request $request, ?string $id): void
    {
        $this->checkValidationService->checkValidation($this->validate($request, true, $id));
        $test = Test::findOrFail($id);
        // if (!$test) {
        //     throw new NotFoundHttpException('Data not found.');
        // }
        $test->update($request->all());
    }

    /**
     * Summary of delete
     * @param string $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return void
     */
    public function delete(string $id): void
    {
        $test = Test::findOrFail($id);
        // if (!$test) {
        //     throw new NotFoundHttpException('Data not found.');
        // }
        $test->delete();
    }


    /**
     * Summary of testList
     * @param mixed $departmentValue
     * @return \Illuminate\Database\Eloquent\Collection<int, Test>
     */
    public function testList(?string $departmentValue)
    {
        // $departmentType = DepartmentTypeData::normalizeDepartmentType($departmentValue);
        // return Test::where('department_type', $departmentType)->select('id', 'test_name', 'test_description')->get();
        return Test::select('id', 'test_name', 'test_description')->get();
    }

    /**
     * @deprecated partialUpdate is not in use
     */
    public function partialUpdate(Request $request, string|null $id): void
    {
        //code here
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
        // $data->where('test_name', 'like', '%' . $searchText . '%')->orWhere('test_number', 'like', '%' . $searchText . '%')->orWhere('test_price', 'like', '%' . $searchText . '%')->orWhere('tax_price', 'like', '%' . $searchText . '%');
        return $data;
    }
    /**
     * @deprecated message
     */
    public function filterByDateRange(string $searchText, $data)
    {
    }
    /**
     * @deprecated message
     */
    public function sortData(string $searchText, $data)
    {
    }
}