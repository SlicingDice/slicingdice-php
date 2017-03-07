<?php

namespace Slicer;

use Slicer\Core\Requester;
use Slicer\Utils\Validators\DataExtractionQueryValidator;
use Slicer\Utils\Validators\FieldValidator;
use Slicer\Utils\Validators\QueryCountValidator;
use Slicer\Utils\Validators\QueryTopValuesValidator;
use Slicer\Utils\Validators\SavedQueryValidator;
use Slicer\Exceptions\SlicingDiceException;
use Slicer\Utils\URLResources;

class SlicingDice {

    /**
    * Valid api key
    *
    * @var string
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

    private $header;
    private $timeout;

    function __construct($keys, $timeout=60) {
        $this->apiKey = $keys;
        $timeout = $timeout;
        $header = $this->getHeader();
        $this->baseURL = $this->getBaseURL();
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
        } else if(array_key_exists("writeKey", $this->apiKey)){
            return array($this->apiKey["writeKey"], 1);
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
    * Define base url to make test
    *
    * @param bool $test if true the base url will be on tests end-point
    */
    private function testWrapper($test) {
        if ($test){
            return $this->baseURL . "/test";
        }
        return $this->baseURL;
    }

    /**
    * Get all projects in SlicingDice
    *
    ** @param bool $test if true will use tests end-point
    */
    public function getProjects($test=False){
        $url = $this->testWrapper($test) . URLResources::PROJECT;
        return $this->makeRequest($url, "GET", 2);
    }

    /**
    * Get errors in SlicingDice
    */
    public function getErrors($test=False){
        $url = "https://localhost:5000/error/";
        return $this->makeRequest($url, "GET", 2);
    }

    /**
    * Get all fields in SlicingDice API
    *
    * @param bool $test if true will use tests end-point
    */
    public function getFields($test=False){
        $url = $this->testWrapper($test) . URLResources::FIELD;
        return $this->makeRequest($url, "GET", 2);
    }

    /**
    * Get a saved query in SlicingDice
    *
    * @param string $savedQueryName Saved query name to recover
    * @param bool $test if true will use tests end-point
    */
    public function getSavedQuery($savedQueryName, $test=False){
        $url = $this->testWrapper($test) . URLResources::QUERY_SAVED . $savedQueryName;
        return $this->makeRequest($url, "GET", 0);
    }

    /**
    * Get all saved queries in SlicingDice
    *
    * @param bool $test if true will use tests end-point
    */
    public function getSavedQueries($test=False){
        $url = $this->testWrapper($test) . URLResources::QUERY_SAVED;
        return $this->makeRequest($url, "GET", 2);
    }

    /**
    * Delete a saved query in SlicingDice
    *
    * @param string $savedQueryName Saved query name to recover
    * @param bool $test if true will use tests end-point
    */
    public function deleteSavedQuery($savedQueryName, $test=False){
        $url = $this->testWrapper($test) . URLResources::QUERY_SAVED . $savedQueryName;
        return $this->makeRequest($url, "DELETE", 2);
    }
    /**
    * Create a field in SlicingDice
    *
    * @param array $field An array with all field characteristics
    * @param bool $test if true will use tests end-point
    */
    public function createField($field, $test=False){
        $url = $this->testWrapper($test) . URLResources::FIELD;
        $sdValidator = new FieldValidator($field);
        if ($sdValidator->validator()) {
            return $this->makeRequest($url, "POST", 1, $field);
        }
    }

    /**
    * Index a query in SlicingDice
    *
    * @param array $query A index query
    * @param bool $autoCreateFields if true SlicingDice API will automatically create
    * nonexistent fields
    * @param bool $test if true will use tests end-point
    */
    public function index($query, $autoCreateFields=False, $test=False){
        if ($autoCreateFields) {
            $query["auto-create-fields"] = true;
        }
        $url = $this->testWrapper($test) . URLResources::INDEX;
        return $this->makeRequest($url, "POST", 1, $query);
    }

    /**
    * Get count entity total queries
    *
    * @param bool $test if true will use tests end-point
    */
    public function countEntityTotal($test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_COUNT_ENTITY_TOTAL;
        return $this->makeRequest($url, "GET", 0);
    }

    /**
    * Make a count entity query in SlicingDice
    *
    * @param array $query A count entity query
    * @param bool $test if true will use tests end-point
    */
    public function countEntity($query, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_COUNT_ENTITY;
        return $this->countQueryWrapper($url, $query);
    }

    /**
    * Make a count event query in SlicingDice
    *
    * @param array $query A count event query
    * @param bool $test if true will use tests end-point
    */
    public function countEvent($query, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_COUNT_EVENT;
        return $this->countQueryWrapper($url, $query);
    }

    /**
    * Get if exists entities SlicingDice
    *
    * @param array $ids A list of ids
    * @param bool $test if true will use tests end-point
    */
    public function existsEntity($ids, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_EXISTS_ENTITY;
        $query = array('ids' => $ids, );
        return $this->makeRequest($url, "POST", 0, $query);
    }

    /**
    * Make a aggregation query in SlicingDice
    *
    * @param array $query A aggregation query
    * @param bool $test if true will use tests end-point
    */
    public function aggregation($query, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_AGGREGATION;
        return $this->makeRequest($url, "POST", 0, $query);
    }

    /**
    * Make a top values query in SlicingDice
    *
    * @param array $query A top values query
    * @param bool $test if true will use tests end-point
    */
    public function topValues($query, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_TOP_VALUES;
        $sdValidator = new QueryTopValuesValidator($query);
        if ($sdValidator->validator()) {
            return $this->makeRequest($url, "POST", 0, $query);
        }
    }

    /**
    * Make a create saved query in SlicingDice
    *
    * @param array $query A saved query
    * @param bool $test if true will use tests end-point
    */
    public function createSavedQuery($query, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_SAVED;
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
    * @param bool $test if true will use tests end-point
    */
    public function updateSavedQuery($savedQueryName, $query, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_SAVED . $savedQueryName;
        return $this->makeRequest($url, "PUT", 2, $query);
    }

    /**
    * Make a data extraction result query in SlicingDice
    *
    * @param array $query A result query
    * @param bool $test if true will use tests end-point
    */
    public function result($query, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_DATA_EXTRACTION_RESULT;
        return $this->dataExtractionWrapper($url, $query);
    }

    /**
    * Make a data extraction score query in SlicingDice
    *
    * @param array $query A score query
    * @param bool $test if true will use tests end-point
    */
    public function score($query, $test=False) {
        $url = $this->testWrapper($test) . URLResources::QUERY_DATA_EXTRACTION_SCORE;
        return $this->dataExtractionWrapper($url, $query);
    }
}
?>