<?php

namespace Slicer\Utils\Validators;

use Slicer\Exceptions\Client\MaxLimitException;

class QueryTopValuesValidator {

    private $queryData;

    function __construct($query){
        $this->queryData = $query;
    }

    private function exceedsQueriesLimit() {
        if (count($this->queryData) > 5) {
            throw new MaxLimitException(
                "Validator query has a limit of 5 queries by request.");
        }
        return false;
    }

    private function exceedsFieldsLimit() {
        foreach ($this->queryData as $key => $value) {
            if (count($value) > 6) {
                throw new MaxLimitException(
                    "The query '{$value}' exceeds the limit of fields per ".
                    "query in request");
            }
        }
        return false;
    }

    private function exceedsValuesContainsLimit() {
        foreach ($this->queryData as $key => $value) {
            if (array_key_exists("contains", $value)) {
                $valueContains = $value["contains"];
                if (count($valueContains) > 5) {
                    throw new MaxLimitException(
                        "The query '{$key}' exceeds the limit of contains per " .
                        "query in request");
                }
            }
        }
        return false;
    }

    public function validator() {
        if (!$this->exceedsQueriesLimit() && !$this->exceedsFieldsLimit() && 
            !$this->exceedsValuesContainsLimit()) {
            return true;
        }
        return false;
    }
}
?>