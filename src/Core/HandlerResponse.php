<?php

namespace Slicer\Core;

use Slicer\Exceptions\MappedExceptions;

use Slicer\Exceptions\SlicingDiceException;
use Slicer\Exceptions\SlicingDiceHTTPException;
use Slicer\Exceptions\DemoUnavailableException;
use Slicer\Exceptions\RequestRateLimitException;
use Slicer\Exceptions\RequestBodySizeExceededException;
use Slicer\Exceptions\IndexEntitiesLimitException;
use Slicer\Exceptions\IndexColumnsLimitException;

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
            $exception = new $mappedExceptions[$status]($error);
        } else {
            $exception = new SlicingDiceException($error);
        }
        throw $exception;
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