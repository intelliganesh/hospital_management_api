<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:trait {name}', function ($name) {
    $path = app_path("Traits/{$name}.php");

    if (file_exists($path)) {
        $this->error('Trait already exists!');
        return;
    }

    if (! is_dir(app_path('Traits'))) {
        mkdir(app_path('Traits'), 0755, true);
    }

    file_put_contents(
        $path,
        <<<EOT
                <?php

                namespace App\Traits;

                use Illuminate\Http\Request;

                trait {$name}
                {
                    use CustomValidatorTrait;

                    public function validate(Request|array \$request, ?bool \$edit = false, ?string \$id = ''){
                        \$rules = [];

                        // For PATCH or PUT requests, apply 'sometimes' rule
                        // if (\$edit) {
                        //     foreach (\$rules as \$field => \$rule) {
                        //         \$rules[\$field] = 'sometimes|' . \$rule;
                        //     }
                        // }

                        return \$this->validator(\$request, \$rules,\$edit);
                    }
                }
                EOT
    );

    $this->info("Trait {$name} created successfully!");
})->purpose('Create a new trait');

Artisan::command('make:dto {dtoPath}', function (string $dtoPath) {
    $parts = explode('/', $dtoPath);
    if (count($parts) !== 2) {
        $this->error('Invalid DTO path format. Use: folderName/DtoName');
        return;
    }
    [$folderName, $name] = $parts;
    $folderPath          = app_path("DTO/{$folderName}");
    $path                = "{$folderPath}/{$name}.php";

    if (file_exists($path)) {
        $this->error('DTO already exists!');
        return;
    }

    if (! is_dir($folderPath)) {
        mkdir($folderPath, 0755, true);
    }

    file_put_contents(
        $path,
        <<<EOT
                    <?php
                        namespace App\DTO\\{$folderName};
                        class {$name}
                        {
                             public function __construct(){
                             }
                        }
                EOT
    );

    $this->info("DTO {$folderName}/{$name} created successfully!");
})->purpose('Create a new DTO');

Artisan::command('make:service {servicePath} {--model=} {--trait=}', function ($servicePath) {
    $model       = $this->option('model');
    $trait       = $this->option('trait');
    $servicePath = str_replace('\\', '/', $servicePath);
    $path        = app_path("Services/{$servicePath}.php");

    if (file_exists($path)) {
        $this->error('Service already exists!');
        return;
    }

    $directory = dirname($path);
    $className = class_basename($servicePath);

    $traitUsage  = $trait ? "    use {$trait};\n" : '';
    $traitImport = $trait ? "use App\\Traits\\{$trait};\n" : '';

    $modelImport = $model ? "use App\\Models\\{$model};\n" : '';

    $modelVar         = '';
    $modelParam       = '';
    $modelProperty    = '';
    $modelConstructor = '';

    if ($model) {
        $modelVar         = lcfirst($model);
        $modelProperty    = "    private \${$modelVar}Service;\n";
        $modelParam       = ", {$model} \${$modelVar}Service";
        $modelConstructor = "  \$this->{$modelVar}Service = \${$modelVar}Service;\n";
    }

    // if (!is_dir(app_path('Services'))) {
    //     mkdir(app_path('Services'), 0755, true);
    // }
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    file_put_contents(
        $path,
        <<<EOT
           <?php

           namespace App\Services;

           use Illuminate\Http\Request;
           use App\Contracts\CRUDContract;
           use App\Attributes\Transactional;
           use App\Services\CheckValidation;
           use App\Contracts\FilterContract;
           {$modelImport}
           {$traitImport}

           class {$className} implements CRUDContract, FilterContract
           {
                {$traitUsage}
                private \$filter;
                private \$columns;
                private \$checkValidationService;
                {$modelProperty}

                /**
                 * Summary of __construct
                 * @param \App\Services\CheckValidation \$checkValidationService
                 */
                public function __construct(CheckValidation \$checkValidationService{$modelParam})
                {
                    \$this->filter = {$model}::\$filter;
                    \$this->columns = {$model}::\$columns;
                    \$this->checkValidationService = \$checkValidationService;
                    {$modelConstructor}
                }

                /**
                 * Summary of search
                 * @param string \$searchText
                 * @param mixed \$data
                 */
                public function search(string \$searchText, \$data){
                   foreach (\$this->columns as \$column) {
                        \$data->orWhere(\$column, 'like', '%' . \$searchText . '%');
                    }
                    return \$data;
                }

                /**
                 * Summary of filterMultipleFields
                 * @param mixed \$request
                 * @param mixed \$data
                 */
                public function filterMultipleFields(\$request, \$data){
                    foreach (\$this->filter as \$column) {
                        if (!empty(\$request[\$column])) {
                            \$data->where(\$column, \$request[\$column]);
                        }
                    }
                    return \$data;
                }

                /**
                 * @deprecated this function is not in use
                 */
                public function filterByDateRange(string \$searchText, \$data)
                {
                }

                /**
                 * @deprecated this function is not in use
                 */
                public function sortData(string \$searchText, \$data)
                {
                }

                /**
                 * Summary of create
                 * @param \Illuminate\Http\Request \$request
                 * @return void
                 */
                #[Transactional(secure: true, requiredRole: null, description: 'Create  {$modelVar}  record within a secure transaction')]
                public function create(Request \$request): void
                {
                    \$this->checkValidationService->checkValidation(\$this->validate(\$request));
                    {$model}::create(\$request->all());
                }

                /**
                 * Summary of update
                 * @param \Illuminate\Http\Request \$request
                 * @param string|null \$id
                 * @return void
                 */
                #[Transactional(secure: true, requiredRole: null, description: 'Update  {$modelVar}  record within a secure transaction')]
                public function update(Request \$request, string|null \$id): void
                {
                    \$this->checkValidationService->checkValidation(\$this->validate(\$request, true,\$id));
                    {$model}::findOrFail(\$id)->update(\$request->all());
                }

                /**
                 * @deprecated this function is not in use
                 */
                public function partialUpdate(Request \$request, string|null \$id): void
                {
                    //code here
                }

                /**
                 * Summary of delete
                 * @param string \$id
                 * @return void
                 */
                public function delete(string \$id): void
                {
                    {$model}::findOrFail(\$id)->delete();
                }

                /**
                 * Summary of get
                 * @param string \$id
                 * @return {$model}
                 */
                public function get(string \$id): {$model}
                {
                    return {$model}::findOrFail(\$id);
                }

                public function all(?Request \$request): mixed
                {
                    \${$modelVar} = {$model}::query();
                    if (\$request?->has('search')) {
                        \$searchValue = \$request->search;
                        \${$modelVar} = \$this->search(\$searchValue, \${$modelVar});
                    }

                    if (\$request?->has('sort_by')) {
                            \$sortBy = \$request->sort_by ?? '';
                            \$sortOrder = \$request->sort_order ?? 'desc';
                            \${$modelVar} = \${$modelVar}->orderBy(\$sortBy, \$sortOrder);
                    }

                    if (\$request->has('multiple_filter')) {
                        \${$modelVar} = \$this->filterMultipleFields(\$request->multiple_filter, \${$modelVar});
                    }

                    return \${$modelVar}->select(\$this->columns)->paginate(env('PAGINATION', 25));
                }

           }
           EOT
    );
    $this->info("Service {$servicePath} created successfully!");
})->purpose('Create a new service');

Artisan::command('make:controller {controllerPath} {--service=}', function ($controllerPath) {
    $service        = $this->option('service');
    $controllerPath = str_replace('\\', '/', $controllerPath);
    $path           = app_path("Http/Controllers/{$controllerPath}.php");
    $baseName       = preg_replace('/Controller$/', '', $controllerPath);

    if (file_exists($path)) {
        $this->error('Controller already exists!');
        return;
    }

    $directory = dirname($path);
    $className = class_basename($controllerPath);

    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }

    $relativeNamespace = str_replace('/', '\\', dirname($controllerPath));
    $namespace         = 'App\\Http\\Controllers' . ($relativeNamespace !== '.' ? "\\{$relativeNamespace}" : '');

    $modelVar         = '';
    $modelParam       = '';
    $modelImport      = '';
    $modelService     = '';
    $modelConstructor = '';

    if ($service) {
        $modelVar         = lcfirst($service);
        $routeVar         = lcfirst(preg_replace('/Service$/', '', $modelVar));
        $routeUrlVar      = Str::snake($routeVar);
        $modelImport      = "use App\\Services\\{$service};\n";
        $modelService     = "private \${$modelVar};\n";
        $modelParam       = "{$service} \${$modelVar}";
        $modelConstructor = "\$this->{$modelVar} = \${$modelVar};\n";
    }

    file_put_contents(
        $path,
        <<<EOT
                <?php

                namespace {$namespace};

                use Exception;
                use Illuminate\Http\Request;
                use App\Traits\ResponseTrait;
                use Illuminate\Validation\ValidationException;
                use Illuminate\Database\Eloquent\ModelNotFoundException;
                use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
                {$modelImport}

                class {$className} extends Controller{

                    use ResponseTrait;

                    {$modelService}

                    /**
                     * Summary of __construct
                     * @param
                     */
                    public function __construct({$modelParam})
                    {
                        {$modelConstructor}
                    }

                    /**
                        * @OA\Get(
                        *     path="/api/{$routeUrlVar}_list",
                        *     summary="Get all {$routeVar}s",
                        *     description="Retrieve a list of all {$routeVar}s in the system",
                        *     tags={"Master"},
                        *     security={{"bearerAuth": {}}},
                        *       @OA\Parameter(
                        *          name="search",
                        *          in="query",
                        *          required=false,
                        *          description="Search keyword",
                        *         @OA\Schema(
                        *             type="string",
                        *             example=""
                        *         )
                        *      ),
                        *     @OA\Parameter(
                        *          name="sort_by",
                        *          in="query",
                        *          required=false,
                        *          description="Field to sort by",
                        *         @OA\Schema(
                        *             type="string",
                        *             example=""
                        *         )
                        *      ),
                        *     @OA\Response(
                        *         response=200,
                        *         description="A list of {$routeVar}s",
                        *         @OA\JsonContent(
                        *             type="array",
                        *             @OA\Items(
                        *             )
                        *         )
                        *     ),
                        *     @OA\Response(
                        *         response=401,
                        *         description="Unauthenticated"
                        *     ),
                        *     @OA\Response(
                        *         response=404,
                        *         ref="#/components/responses/NotFound"
                        *     ),
                        *     @OA\Response(
                        *         response=500,
                        *         ref="#/components/responses/ServerErrorResponse"
                        *     )
                        * )
                        */
                    public function all(?Request \$request)
                    {
                        try {
                            return \$this->successResponse(\$this->{$modelVar}->all(\$request));
                        } catch (Exception \$e) {
                            return \$this->exceptionResponse(\$e);
                        }
                    }


                    /**
                     * @OA\Get(
                     *     path="/api/{$routeUrlVar}_details/{id}",
                     *     summary="Get complete {$routeVar} details",
                     *     tags={"Master"},
                     *     description="Get complete {$routeVar} details",
                     *     security={{"bearerAuth":{}}},
                     *     @OA\Parameter(
                     *          name="id",
                     *          in="path",
                     *          required=true,
                     *          description="ID of the {$routeVar} to get {$routeVar} details",
                     *          @OA\Schema(type="integer", example=1)
                     *     ),
                     *     @OA\Response(
                     *         response=200,
                     *         description="Successful {$routeVar} details retrieval",
                     *         @OA\JsonContent(
                     *             @OA\Property(property="status", type="string", example="success"),
                     *             @OA\Property(property="message", type="string", example="{$routeVar}s details successfully fetched."),
                     *             @OA\Property(
                     *                 property="data",
                     *                 type="object",
                     *             )
                     *         )
                     *     ),
                     *     @OA\Response(
                     *         response=401,
                     *         description="Unauthenticated"
                     *     ),
                     *     @OA\Response(
                     *         response=404,
                     *         ref="#/components/responses/NotFound"
                     *     ),
                     *     @OA\Response(
                     *         response=500,
                     *         ref="#/components/responses/ServerErrorResponse"
                     *     )
                     * )
                     */

                    public function get(string \$id)
                    {
                        try {
                             return \$this->successResponse(\$this->{$modelVar}->get(\$id));
                        } catch (ModelNotFoundException \$e) {
                            throw new NotFoundHttpException('{$baseName} data not found.');
                        } catch (NotFoundHttpException \$notFound) {
                            return \$this->notFoundResponse(\$notFound);
                        } catch (Exception \$e) {
                            return \$this->exceptionResponse(\$e);
                        }
                    }


                    /**
                     * @OA\Post(
                     *     path="/api/{$routeUrlVar}_add",
                     *     summary="{$routeVar} add",
                     *     tags={"Master"},
                     *     description="Add a new {$routeVar} details",
                     *     security={{"bearerAuth": {}}},
                     *     @OA\RequestBody(
                     *         required=true,
                     *         description="Add a new {$routeVar} details",
                     *         @OA\MediaType(
                     *             mediaType="application/json",
                     *             @OA\Schema(
                     *                 type="object",
                     *                 required={},
                     *                 @OA\Property(property="example", type="string", example="example"),
                     *             )
                     *         )
                     *     ),
                     *     @OA\Response(
                     *         response=200,
                     *         description="Successfully {$routeVar} add",
                     *         @OA\JsonContent(
                     *             @OA\Property(property="status", type="string", example="success"),
                     *             @OA\Property(property="message", type="string", example="Successfully received")
                     *         )
                     *     ),
                     *     @OA\Response(
                     *         response=400,
                     *         description="Validation error",
                     *         @OA\JsonContent(
                     *             type="object",
                     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The name field is required."}),
                     *         )
                     *     ),
                     *     @OA\Response(
                     *         response=401,
                     *         description="Unauthenticated"
                     *     ),
                     *     @OA\Response(
                     *         response=404,
                     *         ref="#/components/responses/NotFound"
                     *     ),
                     *     @OA\Response(
                     *         response=500,
                     *         ref="#/components/responses/ServerErrorResponse"
                     *     )
                     * )
                     */

                    public function create(Request \$request)
                    {
                        try {
                            \$this->{$modelVar}->create(\$request);
                            return \$this->successResponse();
                        } catch (ValidationException \$ve) {
                            return \$this->validationResponse(\$ve);
                        } catch (Exception \$e) {
                            return \$this->exceptionResponse(\$e);
                        }
                    }


                    /**
                     * @OA\Put(
                     *     path="/api/{$routeUrlVar}_update/{id}",
                     *     summary="Update {$routeVar}",
                     *     tags={"Master"},
                     *     description="Update {$routeVar} details",
                     *     security={{"bearerAuth": {}}},
                     *     @OA\Parameter(
                     *         name="id",
                     *         in="path",
                     *         description="Update by Id for {$routeVar}",
                     *         required=true,
                     *         @OA\Schema(
                     *             type="integer"
                     *         )
                     *     ),
                     *     @OA\RequestBody(
                     *         required=true,
                     *         description="Update {$routeVar} details",
                     *         @OA\MediaType(
                     *             mediaType="application/json",
                     *             @OA\Schema(
                     *                 type="object",
                     *                 required={},
                     *                 @OA\Property(property="example", type="string", example="Some example value"),
                     *             )
                     *         )
                     *     ),
                     *     @OA\Response(
                     *         response=200,
                     *         description="Successful {$routeVar} update",
                     *         @OA\JsonContent(
                     *             @OA\Property(property="status", type="string", example="success"),
                     *             @OA\Property(property="message", type="string", example="{$routeVar} updated successfully")
                     *         )
                     *     ),
                     *     @OA\Response(
                     *         response=400,
                     *         description="Validation error",
                     *         @OA\JsonContent(
                     *             type="object",
                     *             @OA\Property(property="example", type="array", @OA\Items(type="string"), example={"The name field is required."}),
                     *         )
                     *     ),
                     *     @OA\Response(
                     *         response=401,
                     *         description="Unauthenticated"
                     *     ),
                     *     @OA\Response(
                     *         response=404,
                     *         ref="#/components/responses/NotFound"
                     *     ),
                     *     @OA\Response(
                     *         response=500,
                     *         ref="#/components/responses/ServerErrorResponse"
                     *     )
                     * )
                     */

                    public function update(Request \$request, string \$id)
                    {
                        try {
                            \$this->{$modelVar}->update(\$request,\$id);
                            return \$this->successResponse();
                        } catch (ModelNotFoundException \$e) {
                            throw new NotFoundHttpException('{$baseName} data not found.');
                        } catch (NotFoundHttpException \$notFound) {
                            return \$this->notFoundResponse(\$notFound);
                        } catch (ValidationException \$ve) {
                            return \$this->validationResponse(\$ve);
                        } catch (Exception \$e) {
                            return \$this->exceptionResponse(\$e);
                        }
                    }


                    /**
                     * @OA\Delete(
                     *     path="/api/{$routeUrlVar}_delete/{id}",
                     *     summary="Delete a {$routeVar}",
                     *     tags={"Master"},
                     *     description="Deletes a {$routeVar} by ID",
                     *     security={{"bearerAuth": {}}},
                     *     @OA\Parameter(
                     *         name="id",
                     *         in="path",
                     *         required=true,
                     *         description="ID of the {$routeVar} to be deleted",
                     *         @OA\Schema(type="integer", example=1)
                     *     ),
                     *     @OA\Response(
                     *         response=200,
                     *         description="{$routeVar} successfully deleted",
                     *         @OA\JsonContent(
                     *             @OA\Property(
                     *                 property="status",
                     *                 type="string",
                     *                 example="success"
                     *             ),
                     *             @OA\Property(
                     *                 property="message",
                     *                 type="string",
                     *                 example="{$routeVar} deleted successfully."
                     *             )
                     *         )
                     *     ),
                     *     @OA\Response(
                     *         response=401,
                     *         description="Unauthenticated"
                     *     ),
                     *     @OA\Response(
                     *         response=404,
                     *         ref="#/components/responses/NotFound"
                     *     ),
                     *     @OA\Response(
                     *         response=500,
                     *         ref="#/components/responses/ServerErrorResponse"
                     *     )
                     * )
                     */

                    public function delete(string \$id)
                    {
                         try {
                            \$this->{$modelVar}->delete(\$id);
                            return \$this->successResponse();
                        } catch (ModelNotFoundException \$e) {
                            throw new NotFoundHttpException('{$baseName} data not found.');
                        } catch (NotFoundHttpException \$notFound) {
                            return \$this->notFoundResponse(\$notFound);
                        } catch (Exception \$e) {
                            return \$this->exceptionResponse(\$e);
                        }
                    }

                }
                EOT
    );
    $this->info("Controller {$controllerPath} created successfully!");
})->purpose('Create a new controller');
