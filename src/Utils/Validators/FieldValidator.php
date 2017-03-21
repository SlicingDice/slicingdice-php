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

    /**
    * Validate field name
    */
    private function validateName($query) {
        if (!array_key_exists("name", $query)) {
            throw new InvalidFieldException("The field should have a name.");
        }
        else {
            $name = $query["name"];
            if (strlen($name) > 80){
                throw new InvalidFieldNameException(
                    "The field's name have a very big content. (Max: 80 chars)");
            }
        }
    }

    /**
    * Validate field description
    */
    private function validateDescription($query) {
        $description = $query["description"];
        if (strlen($description) > 300){
            throw new InvalidFieldDescriptionException(
                "The field's description have a very big content. (Max: 300chars)");
        }
    }

    /**
    * Validate field type
    */
    private function validateFieldType($query) {
        if (!array_key_exists("type", $query)) {
            throw new InvalidFieldException("The field should have a type.");
        }
        $fieldType = $query["type"];
        if (!in_array($fieldType, $this->validTypesFields)){
            throw new InvalidFieldException("This field have a invalid type.");
        }
    }

    /**
    * Verify if decimal has a valid type
    */
    private function validateDecimalType($query) {
        $decimalTypes = array("decimal", "decimal-time-series");
        $fieldType = $query["type"];
        if (!in_array($fieldType, $decimalTypes)){
            throw new InvalidFieldException(
                "This field is only accepted on type 'decimal' or " .
                "'decimal-time-series'");
        }
    }

    /**
    * Check cardinality property on string fields
    */
    private function checkStringIntegrity($query) {
        if (!array_key_exists("cardinality", $query)) {
            throw new InvalidFieldException(
                "The field with type string should have 'cardinality' key.");
        }
        $cardinalityTypes = array("high", "low");
        $cardinality = $query["cardinality"];
        if (!in_array($cardinality, $cardinalityTypes)) {
            throw new InvalidFieldException(
                "The field 'cardinality' has invalid value.");
        }
    }

    /**
    * Validate enumerate field
    */
    private function validateEnumerate($query) {
        if(!array_key_exists("range", $query)) {
            throw new InvalidFieldException(
                "The 'enumerate' type needs of the 'range' parameter.");
        }
    }

    /** 
    * Validate a field
    *
    * @return true if field is valid
    */
    public function validator() {
        if(isset($this->queryData[0]) && is_array($this->queryData[0])) {
            foreach ($this->queryData as $query) {
                $this->validateField($query);
            }
        } else {
            $this->validateField($this->queryData);
        }

        return true;
    }

    private function validateField($query) {
        $this->validateName($query);
        $this->validateFieldType($query);
        $fieldType = $query["type"];
        if ($fieldType == "string") {
            $this->checkStringIntegrity($query);
        }
        if ($fieldType == "enumerated") { 
            $this->validateEnumerate($query);
        }
        if (array_key_exists("description", $query)) {
            $this->validateDescription($query);
        }
        if (array_key_exists("decimal-place", $query)) {
            $this->validateDecimalType($query);
        }
    }
}
?> 