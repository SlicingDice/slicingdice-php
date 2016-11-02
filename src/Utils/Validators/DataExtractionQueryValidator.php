<?php

namespace Slicer\Utils\Validators;
use Slicer\Exceptions\Client\InvalidQueryException;


class DataExtractionQueryValidator {
    private $queryData;

    function __construct($query) {
        $this->queryData = $query;
    }

    public function validator() {
        if (array_key_exists("limit", $this->queryData)) {
            if ($this->queryData["limit"] > 100) {
                throw new InvalidQueryException(
                    "The field 'limit' has a value max of 100.");
            }
        }
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