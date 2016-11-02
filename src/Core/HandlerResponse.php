<?php

namespace Slicer\Core;

use Slicer\Exceptions\MappedExceptions;
use Slicer\Exceptions\Field\FieldAlreadyExistsException;

$exceptions = array(
    "src/Exceptions/Account",
    "src/Exceptions/Auth",
    "src/Exceptions/Field",
    "src/Exceptions/Request",
    "src/Exceptions/Query",
    "src/Exceptions/Index",
);

$generalExceptions = array(
    "src/Exceptions/SlicingDiceException.php",
    "src/Exceptions/InternalServerException.php",
    "src/Exceptions/SlicingDiceHTTPException.php",
    "src/Exceptions/FieldCreateInternalException.php"
);

function loadExceptions($ownExceptions, $ownGeneralExceptions){
    foreach ($ownGeneralExceptions as $exceptionPathName) {
        require_once $exceptionPathName;
    }
    foreach ($ownExceptions as $exceptionFolder) {
        foreach (glob("{$exceptionFolder}/*.php") as $filename)
        {
            require_once $filename;
        }
    }
}

if (file_exists('../src/Exceptions/SlicingDiceException.php')){
    $ownExceptions = array();
    $ownGeneralExceptions = array();
    foreach ($exceptions as $exceptionFolder) {
        array_push($ownExceptions, "../" . $exceptionFolder);
    }
    foreach ($generalExceptions as $exceptionPath) {
        array_push($ownGeneralExceptions, "../" . $exceptionPath);
    }
    loadExceptions($ownExceptions, $ownGeneralExceptions);
} else {
    loadExceptions($exceptions, $generalExceptions);
}


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