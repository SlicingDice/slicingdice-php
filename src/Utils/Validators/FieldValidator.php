<?php

namespace Slicer\Utils\Validators;

use Slicer\Exceptions\Client\InvalidFieldDescriptionException;
use Slicer\Exceptions\Client\InvalidFieldNameException;
use Slicer\Exceptions\Client\InvalidFieldException;

class FieldValidator {

    private $queryData;
    private $validTypesFields;

    function __construct($query) {
        $this->queryData = $query;
        $this->validTypesFields = array(
            "unique-id", "boolean", "string", "integer", "decimal",
            "enumerated", "date", "integer-time-series",
            "decimal-time-series", "string-time-series");
    }

    private function validateName() {
        if (!array_key_exists("name", $this->queryData)) {
            throw new InvalidFieldException("The field should have a name.");
        }
        else {
            $name = $this->queryData["name"];
            if (strlen($name) > 80){
                throw new InvalidFieldNameException(
                    "The field's name have a very big name.(Max: 80 chars)");
            }
        }
    }

    private function validateDescription() {
        $description = $this->queryData["description"];
        if (strlen($description) > 300){
            throw new InvalidFieldDescriptionException(
                "The field's description have a very big name.(Max: 300chars)");
        }
    }

    private function validateFieldType() {
        if (!array_key_exists("type", $this->queryData)) {
            throw new InvalidFieldException("The field should have a type.");
        }
        $fieldType = $this->queryData["type"];
        if (!in_array($fieldType, $this->validTypesFields)){
            throw new InvalidFieldException("This field have a invalid type.");
        }
    }

    private function validateDecimalPlaces() {
        $decimalTypes = array("decimal", "decimal-time-series");
        $fieldType = $this->queryData["type"];
        if (!in_array($fieldType, $decimalTypes)){
            throw new InvalidFieldException(
                "This field is only accepted on type 'decimal' or " .
                "'decimal-time-series'");
        }
    }

    private function checkStringIntegrity() {
        if (!array_key_exists("cardinality", $this->queryData)) {
            throw new InvalidFieldException(
                "The field with type string should have 'cardinality' key.");
        }
        $cardinalityTypes = array("high", "low");
        $cardinality = $this->queryData["cardinality"];
        if (!in_array($cardinality, $cardinalityTypes)) {
            throw new InvalidFieldException(
                "The field 'cardinality' has invalid value.");
        }
    }

    private function validateEnumerate() {
        if(!array_key_exists("range", $this->queryData)) {
            throw new InvalidFieldException(
                "The 'enumerate' type needs of the 'range' parameter.");
        }
    }

    public function validator() {
        $this->validateName();
        $this->validateFieldType();
        $fieldType = $this->queryData["type"];
        if ($fieldType == "string") {
            $this->checkStringIntegrity();
        }
        if ($fieldType == "enumerated") { 
            $this->validateEnumerate();
        }
        if (array_key_exists("description", $this->queryData)) {
            $this->validateDescription();
        }
        if (array_key_exists("decimal-place", $this->queryData)) {
            $this->validateDecimalPlaces();
        }
        return true;
    }
}
?>