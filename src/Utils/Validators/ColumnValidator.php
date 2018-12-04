<?php

namespace Slicer\Utils\Validators;

use Slicer\Exceptions\Client\InvalidColumnDescriptionException;
use Slicer\Exceptions\Client\InvalidColumnNameException;
use Slicer\Exceptions\Client\InvalidColumnException;

class ColumnValidator {

    private $queryData;
    private $validTypesColumns;

    function __construct($query) {
        $this->queryData = $query;
        $this->validTypesColumns = array(
            "unique-id", "boolean", "string", "integer", "decimal",
            "date", "integer-event",
            "decimal-event", "string-event", "datetime");
    }

    /**
    * Validate column name
    */
    private function validateName($query) {
        if (!array_key_exists("name", $query)) {
            throw new InvalidColumnException("The column should have a name.");
        }
        else {
            $name = $query["name"];
            if (strlen($name) > 80){
                throw new InvalidColumnNameException(
                    "The column's name have a very big content. (Max: 80 chars)");
            }
        }
    }

    /**
    * Validate column description
    */
    private function validateDescription($query) {
        $description = $query["description"];
        if (strlen($description) > 300){
            throw new InvalidColumnDescriptionException(
                "The column's description have a very big content. (Max: 300chars)");
        }
    }

    /**
    * Validate column type
    */
    private function validateColumnType($query) {
        if (!array_key_exists("type", $query)) {
            throw new InvalidColumnException("The column should have a type.");
        }
        $columnType = $query["type"];
        if (!in_array($columnType, $this->validTypesColumns)){
            throw new InvalidColumnException("This column have a invalid type.");
        }
    }

    /**
    * Verify if decimal has a valid type
    */
    private function validateDecimalType($query) {
        $decimalTypes = array("decimal", "decimal-event");
        $columnType = $query["type"];
        if (!in_array($columnType, $decimalTypes)){
            throw new InvalidColumnException(
                "This column is only accepted on type 'decimal' or " .
                "'decimal-event'");
        }
    }

    /**
    * Check cardinality property on string columns
    */
    private function checkStringIntegrity($query) {
        if (!array_key_exists("cardinality", $query)) {
            throw new InvalidColumnException(
                "The column with type string should have 'cardinality' key.");
        }
        $cardinalityTypes = array("high", "low");
        $cardinality = $query["cardinality"];
        if (!in_array($cardinality, $cardinalityTypes)) {
            throw new InvalidColumnException(
                "The column 'cardinality' has invalid value.");
        }
    }

    /**
    * Validate enumerate column
    */
    private function validateEnumerate($query) {
        if(!array_key_exists("range", $query)) {
            throw new InvalidColumnException(
                "The 'enumerate' type needs of the 'range' parameter.");
        }
    }

    /** 
    * Validate a column
    *
    * @return true if column is valid
    */
    public function validator() {
        if(isset($this->queryData[0]) && is_array($this->queryData[0])) {
            foreach ($this->queryData as $query) {
                $this->validateColumn($query);
            }
        } else {
            $this->validateColumn($this->queryData);
        }

        return true;
    }

    private function validateColumn($query) {
        $this->validateName($query);
        $this->validateColumnType($query);
        $columnType = $query["type"];
        if ($columnType == "string") {
            $this->checkStringIntegrity($query);
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