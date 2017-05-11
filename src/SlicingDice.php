<?php

namespace Slicer;

use Slicer\Core\Requester;
use Slicer\Utils\Validators\DataExtractionQueryValidator;
use Slicer\Utils\Validators\ColumnValidator;
use Slicer\Utils\Validators\QueryCountValidator;
use Slicer\Utils\Validators\QueryTopValuesValidator;
use Slicer\Utils\Validators\SavedQueryValidator;
use Slicer\Exceptions\SlicingDiceException;
use Slicer\Utils\URLResources;

class SlicingDice {

    /**
    * Valid api key
    *
    * @var array
    */
    private $apiKey;
    /**
    * Requester object
    *
    * @var Requester
    */
    private $requester;
    /**
    * API url base
    *
    * @var string
    */
    private $baseURL;
    /**
    * Identify if client will use tests endpoint
    *
    * @var boolean
    */
    private $usesTestEndpoint;

    private $header;
    private $timeout;

    function __construct($apiKeys, $usesTestEndpoint=false, $timeout=60) {
        $this->apiKey = $apiKeys;
        $timeout = $timeout;
        $header = $this->getHeader();
        $this->baseURL = $this->getBaseURL();
        $this->usesTestEndpoint = $usesTestEndpoint;
    }

    /**
    * Get SlicingDice API base url, if SD_API_ADDRESS isn't set
    * https://api.slicingdice.com/v1 will be used
    */
    private function getBaseURL(){
        $sdAddressAPI = getenv("SD_API_ADDRESS");
        if (empty($sdAddressAPI)){
            return "https://api.slicingdice.com/v1";
        }
        return $sdAddressAPI;
    }

    /**
    * Get header to make request
    */
    private function getHeader() {
        return array(
            "Content-Type" => "application/json",
            "Authorization" => $this->apiKey);
    }

    private function getUserKeyLevel(){
        if (gettype($this->apiKey) !== "array") {
            throw new SlicingDiceException("Keys need be placed in array.");
        }
        if (array_key_exists("masterKey", $this->apiKey)) {
            return array($this->apiKey["masterKey"], 2);
        } else if(array_key_exists("customKey", $this->apiKey)){
            return array($this->apiKey["customKey"], 2);
        } else if(array_key_exists("writeKey", $this->apiKey)){
            return array($this->apiKey["writeKey"], 1);
        } else if(array_key_exists("readKey", $this->apiKey)){
            return array($this->apiKey["readKey"], 0);
        }

        throw new SlicingDiceException("You need to put a key.");
    }

    /**
    * Get api key with a desired minimum level
    *
    * @param integer $levelKey Define the min level of the desired key
    */
    private function getAPIKey($levelKey){
        $currentKey = $this->getUserKeyLevel();

        if ($currentKey[1] < $levelKey) {
            throw new SlicingDiceException("The key inserted is not allowed to perform this operation.");
        } else {
            return $currentKey[0];            
        }
    }

    /**
    * Make effective and identify method request
    *
    * @param string $url Url to make request
    * @param string $reqType Request method type
    * @param integer $levelKey Define the min level to do the query
    * @param array $query A SlicingDice query
    */
    private function makeRequest($url, $reqType, $levelKey, $query=null) {
        $requester = new Requester($this->header, $this->timeout);
        $key = $this->getAPIKey($levelKey);
        if ($reqType == "GET") {
            return $requester->get($url, $key);
        }
        if ($reqType == "POST") {
            return $requester->data($url, $key, $query, false);
        }
        if ($reqType == "PUT") {
            return $requester->data($url, $key, $query, true);
        }
        if ($reqType == "DELETE") {
            return $requester->delete($url, $key);
        }
    }

    /**
    * Make count event and count entity query
    *
    * @param string $url Url to make request
    * @param array $query A SlicingDice query
    */
    private function countQueryWrapper($url, $query) {
        $sdValidator = new QueryCountValidator($query);
        if ($sdValidator->validator()) {
            return $this->makeRequest($url, "POST", 0, $query);
        }
    }

    /**
    * Make score and result query
    *
    * @param string $url Url to make request
    * @param array $query A SlicingDice query
    */
    private function dataExtractionWrapper($url, $query) {
        $sdValidator = new DataExtractionQueryValidator($query);
        if ($sdValidator->validator()) {
            return $this->makeRequest($url, "POST", 0, $query);
        }
    }

    /**
    * Define base url to make a request
    */
    private function testWrapper() {
        if ($this->usesTestEndpoint){
            return $this->baseURL . "/test";
        }
        return $this->baseURL;
    }

    /**
    * Get information about current database in SlicingDice
    */
    public function getDatabase(){
        $url = $this->testWrapper() . URLResources::DATABASE;
        return $this->makeRequest($url, "GET", 2);
    }

    /**
    * Get all columns in SlicingDice API
    */
    public function getColumns(){
        $url = $this->testWrapper() . URLResources::COLUMN;
        return $this->makeRequest($url, "GET", 2);
    }

    /**
    * Get a saved query in SlicingDice
    *
    * @param string $savedQueryName Saved query name to recover
    */
    public function getSavedQuery($savedQueryName){
        $url = $this->testWrapper() . URLResources::QUERY_SAVED . $savedQueryName;
        return $this->makeRequest($url, "GET", 0);
    }

    /**
    * Get all saved queries in SlicingDice
    */
    public function getSavedQueries(){
        $url = $this->testWrapper() . URLResources::QUERY_SAVED;
        return $this->makeRequest($url, "GET", 2);
    }

    /**
    * Delete a saved query in SlicingDice
    *
    * @param string $savedQueryName Saved query name to recover
    */
    public function deleteSavedQuery($savedQueryName){
        $url = $this->testWrapper() . URLResources::QUERY_SAVED . $savedQueryName;
        return $this->makeRequest($url, "DELETE", 2);
    }
    /**
    * Create a column in SlicingDice
    *
    * @param array $column An array with all column characteristics
    */
    public function createColumn($column){
        $url = $this->testWrapper() . URLResources::COLUMN;
        $sdValidator = new ColumnValidator($column);
        if ($sdValidator->validator()) {
            return $this->makeRequest($url, "POST", 1, $column);
        }
    }

    /**
    * Insert data in SlicingDice
    *
    * @param array $data Data you want to insert
    */
    public function insert($data){
        $url = $this->testWrapper() . URLResources::INSERT;
        return $this->makeRequest($url, "POST", 1, $data);
    }

    /**
    * Get count entity total queries
    * @param array $tables An array containing the tables in which
    *                      the total query will be performed
    */
    public function countEntityTotal($tables=null) {
        if($tables == null){
            $tables = array(
                    "tables" => array()
                );
        }
        $url = $this->testWrapper() . URLResources::QUERY_COUNT_ENTITY_TOTAL;
        return $this->makeRequest($url, "POST", 0, $tables);
    }

    /**
    * Make a count entity query in SlicingDice
    *
    * @param array $query A count entity query
    */
    public function countEntity($query) {
        $url = $this->testWrapper() . URLResources::QUERY_COUNT_ENTITY;
        return $this->countQueryWrapper($url, $query);
    }

    /**
    * Make a count event query in SlicingDice
    *
    * @param array $query A count event query
    */
    public function countEvent($query) {
        $url = $this->testWrapper() . URLResources::QUERY_COUNT_EVENT;
        return $this->countQueryWrapper($url, $query);
    }

    /**
    * Get if exists entities SlicingDice
    *
    * @param array $ids A list of ids
    */
    public function existsEntity($ids) {
        $url = $this->testWrapper() . URLResources::QUERY_EXISTS_ENTITY;
        $query = array('ids' => $ids, );
        return $this->makeRequest($url, "POST", 0, $query);
    }

    /**
    * Make a aggregation query in SlicingDice
    *
    * @param array $query A aggregation query
    */
    public function aggregation($query) {
        $url = $this->testWrapper() . URLResources::QUERY_AGGREGATION;
        return $this->makeRequest($url, "POST", 0, $query);
    }

    /**
    * Make a top values query in SlicingDice
    *
    * @param array $query A top values query
    */
    public function topValues($query) {
        $url = $this->testWrapper() . URLResources::QUERY_TOP_VALUES;
        $sdValidator = new QueryTopValuesValidator($query);
        if ($sdValidator->validator()) {
            return $this->makeRequest($url, "POST", 0, $query);
        }
    }

    /**
    * Make a create saved query in SlicingDice
    *
    * @param array $query A saved query
    */
    public function createSavedQuery($query) {
        $url = $this->testWrapper() . URLResources::QUERY_SAVED;
        $sdValidator = new SavedQueryValidator($query);
        if ($sdValidator->validator()) {
            return $this->makeRequest($url, "POST", 2, $query);
        }
    }

    /**
    * Update a saved query in SlicingDice
    *
    * @param string $savedQueryName A saved query name
    * @param array $query A saved query
    */
    public function updateSavedQuery($savedQueryName, $query) {
        $url = $this->testWrapper() . URLResources::QUERY_SAVED . $savedQueryName;
        return $this->makeRequest($url, "PUT", 2, $query);
    }

    /**
    * Make a data extraction result query in SlicingDice
    *
    * @param array $query A result query
    */
    public function result($query) {
        $url = $this->testWrapper() . URLResources::QUERY_DATA_EXTRACTION_RESULT;
        return $this->dataExtractionWrapper($url, $query);
    }

    /**
    * Make a data extraction score query in SlicingDice
    *
    * @param array $query A score query
    */
    public function score($query) {
        $url = $this->testWrapper() . URLResources::QUERY_DATA_EXTRACTION_SCORE;
        return $this->dataExtractionWrapper($url, $query);
    }
}
?>