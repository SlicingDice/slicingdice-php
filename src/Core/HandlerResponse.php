<?php

namespace Slicer\Core;

use Slicer\Exceptions\MappedExceptions;
use Slicer\Exceptions\Field\FieldAlreadyExistsException;
use Slicer\Exceptions\Account;
use Slicer\Exceptions\Auth;
use Slicer\Exceptions\Field;
use Slicer\Exceptions\Request;
use Slicer\Exceptions\Query;
use Slicer\Exceptions\Index;
use Slicer\Exceptions\SlicingDiceException;
use Slicer\Exceptions\InternalServerException;
use Slicer\Exceptions\SlicingDiceHTTPException;
use Slicer\Exceptions\FieldCreateInternalException;

class HandlerResponse {

    /**
    * A requester response
    *
    * @var array $requestResponse
    */
    private $requestResponse;

    function __construct($result) {
        $this->requestResponse = $result;
    }

    /**
    * Raise api errors
    *
    * @param array $error A array with message and code error
    */
    private function raiseException($error) {
        $status = $error["code"];
        $mappedExceptions = MappedExceptions::all();
        if (array_key_exists($status, $mappedExceptions)) {
            throw new $mappedExceptions[$status]($error["message"]);
        } else{
            throw new \Exception($error["message"]);
        }
    }

    /**
    * Check if request was successful
    */
    public function requestSuccessful() {
        if (is_array($this->requestResponse)){
            if (array_key_exists("errors", $this->requestResponse)) {
                $error = $this->requestResponse["errors"][0];
                $this->raiseException($error);
            }
        }
        return true;
    }
}
?>