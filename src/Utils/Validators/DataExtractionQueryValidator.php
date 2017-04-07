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
        if (array_key_exists("fields", $this->queryData)) {
            if (count($this->queryData["fields"]) > 10) {
                throw new InvalidQueryException(
                    "The key 'fields' in data extraction result must " . 
                    "have up to 10 fields.");
            }
        }
        return true;
    }
}
?>