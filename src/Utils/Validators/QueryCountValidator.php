<?php

namespace Slicer\Utils\Validators;

use Slicer\Exceptions\Client\MaxLimitException;

class QueryCountValidator {

    private $queryData;

    function __construct($query){
        $this->queryData = $query;
    }

    public function validator() {
        if (count($this->queryData) > 10) {
            throw new MaxLimitException(
                "The query count entity has a limit of 10 queries by request.");
        }
        return true;
    }
}
?>