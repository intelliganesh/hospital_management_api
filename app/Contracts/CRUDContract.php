<?php

namespace App\Contracts;

use Illuminate\Http\Request;
interface CRUDContract
{
    /**
     * Summary of create
     * @param \Illuminate\Http\Request $request
     * @return void
     */
    public function create(Request $request): void;
    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function update(Request $request, string|null $id): void;

    /**
     * Summary of partialUpdate
     * @param \Illuminate\Http\Request $request
     * @param string|null $id
     * @return void
     */
    public function partialUpdate(Request $request, string|null $id): void;

    /**
     * Summary of delete
     * @param string $id
     * @return void
     */
    public function delete(string $id): void;

    /**
     * Summary of get
     * @param string $id
     * @return void
     */
    public function get(string $id): mixed;

    /**
     * Summary of all
     * @param mixed $request
     * @return void
     */
    public function all(?Request $request): mixed;


}

interface SoftDeleteContract
{
    /**
     * Summary of softDelete
     * @param string $id
     * @return void
     */
    public function softDelete(string $id): bool;
}