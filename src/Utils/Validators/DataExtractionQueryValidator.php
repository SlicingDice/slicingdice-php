<?php

namespace Slicer\Utils\Validators;
use Slicer\Exceptions\Client\InvalidQueryException;


class DataExtractionQueryValidator {
    private $queryData;

    function __construct($query) {
        $this->queryData = $query;
    }

    /** 
    * Validate a data extraction query
    *
    * @return true if query is valid 
    */
    public function validator() {
        if (array_key_exists("columns", $this->queryData)) {
            if (count($this->queryData["columns"]) > 10) {
                throw new InvalidQueryException(
                    "The key 'columns' in data extraction result must " . 
                    "have up to 10 columns.");
            }
        }
        return true;
    }
}
?>