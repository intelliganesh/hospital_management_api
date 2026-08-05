<?php

namespace App\Contracts;


interface FilterContract
{
    /**
     * Summary of search
     * @param string $searchText
     * @param  $data
     */
    public function search(string $searchText, $data);
    /**
     * Summary of filterByDateRange
     * @param string $searchText
     * @param  $data
     */
    public function filterByDateRange(string $searchText, $data);
    /**
     * Summary of sortData
     * @param string $searchText
     * @param  $data
     */
    public function sortData(string $searchText, $data);
}