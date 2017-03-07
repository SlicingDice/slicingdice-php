<?php

namespace Slicer\Utils\Validators;

use Slicer\Exceptions\Client\InvalidQueryTypeException;

class SavedQueryValidator {

    private $queryData;
    private $listQueryTypes;

    function __construct($query){
        $this->queryData = $query;
        $this->listQueryTypes = array(
            "count/entity", "count/event", "count/entity/total",
            "aggregation", "top_values");
    }

    /**
    * Check if saved query has valid type
    *
    * @return true if has valid type
    */
    private function hasValidType() {
        $queryType = $this->queryData["type"];
        if (!in_array($queryType, $this->listQueryTypes)) {
            throw new InvalidQueryTypeException(
                "This dictionary don't have query type valid.");
        }
        return true;
    }

    /** 
    * Validate a data extraction query
    *
    * @return true if query is valid and false otherwise
    */
    public function validator() {
        return $this->hasValidType();
    }
}
?>