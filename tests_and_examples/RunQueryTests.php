<?php

namespace Slicer\Tests;

date_default_timezone_set('America/Sao_Paulo');
$filename = str_replace("tests_and_examples", "vendor/autoload.php", __DIR__);
require_once $filename;

use Slicer\SlicingDice;

class SlicingDiceTester {

    // The Slicing Dice API client
    private $client;
    // Array for field translation
    private $fieldTranslation;
    // Sleep time in seconds
    private $sleepTime;
    // Examples path
    private $path;
    // Examples file extension
    private $extension;
    public $numSuccess;
    public $numFails;
    public $failedTests;
    private $verbose;

    function __construct($apiKey, $verboseOption=false) {
        $this->client = new SlicingDice(array(
            "masterKey" => $apiKey
        ), true);

        // Translation table for fields with timestamp
        $this->fieldTranslation = array();

        $this->sleepTime = 10;
        $this->path = "/examples/"; 
        $this->extension = ".json"; 

        $this->numSuccess = 0;
        $this->numFails = 0;
        $this->failedTests = array();

        $this->verbose = $verboseOption;
        $this->updateResults();
    }

    /**
    * Run tests 
    *
    * @param string $queryType the type of the query
    */
    public function runTests($queryType){
        $testData = $this->loadTestData($queryType);
        $numTests = count($testData);

        $counter = 0;
        foreach($testData as $test){
            $this->emptyFieldTranslation();

            $counter += 1;
            $name = $test["name"];
            print "($counter/$numTests) Executing test $name\n";

            if (array_key_exists('description', $test)){
                print '  Description: ' . $test["description"];
            }

            print "\n  Query type: $queryType \n";
            try{
                $this->createFields($test);
                $this->indexData($test);
                $result = $this->executeQuery($queryType, $test);
            } catch (\Exception $e){
                $result = array("result" => array(
                        "error" => $e->getMessage()
                    ));
            }

            $this->compareResult($test, $result);
            echo "\n";
        }
    }

    /**
    * Reset field translation
    */
    private function emptyFieldTranslation(){
        $this->fieldTranslation = array();
    }

    /**
    * Load test data from example files
    *
    * @param string $queryType the type of the query
    */
    private function loadTestData($queryType) {
        $filename = __DIR__ . $this->path . $queryType . $this->extension;
        $content = file_get_contents($filename);
        return json_decode($content, true);
    }

    /**
    * Create fields on Slicing Dice API
    *
    * @param array $fieldObject the field object to create
    */
    private function createFields($fieldObject) {
        $isSingular = $this->numFails == 1;
        $fieldOrFields = null;
        if ($isSingular){
            $fieldOrFields = "field";
        } else {
            $fieldOrFields = "fields";
        }
        print "  Creating " . count($fieldObject["fields"]) . " " . $fieldOrFields . "\n";

        foreach ($fieldObject["fields"] as $field) {
            $newField = $this->appendTimestampToFieldName($field);
            $this->client->createField($newField);

            if ($this->verbose){
                echo "    - " . $newField['api-name'] . "\n";
            }
        }
    }

    /**
    * Put timestamp to the end of the field name
    * 
    * @param array $field the field to append timestamp
    */
    private function appendTimestampToFieldName($field){
        $oldName = '"' . $field['api-name'] . '"';

        $timestamp = $this->getTimestamp();
        $field['name'] = $field['name'] . $timestamp;
        $field['api-name'] = $field['api-name'] . $timestamp;
        $newName = '"' . $field['api-name'] . '"';

        $this->fieldTranslation[$oldName] = $newName;
        return $field;
    }

    /**
    * Get actual timestamp
    *
    * @return string with the timestamp
    */
    private function getTimestamp(){
        $date = new \DateTime();
        return strval($date->getTimestamp());
    }

    /**
    * Index data 
    *
    * @param array $data the data to index
    */
    private function indexData($data){
        $isSingular = $this->numFails == 1;
        $entityOrEntities = null;
        if ($isSingular){
            $entityOrEntities = "entity";
        } else {
            $entityOrEntities = "entities";
        }
        print "  Indexing " . count($data["index"]) . " " . $entityOrEntities . "\n";

        $indexDataArray = $this->translateFieldNames($data["index"]);

        if ($this->verbose) {
            print_r($indexDataArray);
        }

        $this->client->index($indexDataArray, null);

        sleep($this->sleepTime);
    }

    /**
    * Tranlate field name to use timestamp
    *
    * @param array $jsonData the json data to translate fields
    */
    private function translateFieldNames($jsonData){
        $dataString = json_encode($jsonData);

        foreach ($this->fieldTranslation as $oldName => $newName) {
            $dataString = str_replace($oldName, $newName, $dataString);
        }

        return json_decode($dataString, true);
    }

    /**
    * Execute query on Slicing Dice API
    *
    * @param string $queryType the query type
    * @param array $data the query array
    */
    private function executeQuery($queryType, $data){
        $queryData = $this->translateFieldNames($data["query"]);
        $result = null;
        echo "  Querying\n";

        if ($this->verbose){
            print_r($queryData);
        }

        if ($queryType == "count_entity"){
            $result = $this->client->countEntity($queryData);
        } else if ($queryType == "count_event"){
            $result = $this->client->countEvent($queryData);
        } else if ($queryType == "top_values"){
            $result = $this->client->topValues($queryData);
        } else if ($queryType == "aggregation"){
            $result = $this->client->aggregation($queryData);
        } else if ($queryType == "result"){
            $result = $this->client->result($queryData);
        } else if ($queryType == "score"){
            $result = $this->client->score($queryData);
        }

        return $result;
    }

    /**
    * Compare received result with expected
    *
    * @param array $expectedArray the expected array
    * @param array $result the result array received
    */
    private function compareResult($expectedArray, $result){
        $expected = $this->translateFieldNames($expectedArray["expected"]);

        foreach ($expectedArray["expected"] as $key => $value) {
            if($value == "ignore"){
                continue;
            }

            if (array_diff_key($expected[$key], $result[$key])){
                $this->numFails += 1;
                array_push($this->failedTests, $expectedArray["name"]);

                print_r('  Expected: "' . $key . '": ' . json_encode($expected[$key]) . "\n");
                print_r('  Result: "' . $key . '": ' . json_encode($result[$key]) .  "\n");
                echo "  Status: Failed\n";
                $this->updateResults();
                return;
            }
        }

        $this->numSuccess += 1;
        print "  Status: Passed\n";
        $this->updateResults();
    }

    /**
    * Update tests result on tests result file
    */
    private function updateResults() {
        if (PHP_OS != "Linux") return;
        $finalMessage = null;
        $failedTestsStr = null;

        foreach ($this->failedTests as $item) {
            $failedTestsStr .= "    - " . $item . "\n";
        }
        if ($this->numFails > 0){
            $isSingular = $this->numFails == 1;
            $testOrTests = null;
            if ($isSingular){
                $testOrTests = "test has";
            } else {
                $testOrTests = "tests have";
            }

            $finalMessage = "FAIL: $this->numFails $testOrTests failed";
        } else {
            $finalMessage = "SUCCESS: All tests passed";
        }

        $content = "\nResults:\n" .
            "  Successes: $this->numSuccess \n" .
            "  Fails: $this->numFails \n" .
            $failedTestsStr . "\n" .
            $finalMessage . "\n";
        $fp = fopen('testerResult.tmp', 'w');
        fwrite($fp, $content);
        fclose($fp);
    }
}

/**
* Print the content of tests result
*/
function showResult(){
    echo file_get_contents("testerResult.tmp");
    unlink("testerResult.tmp");
}

function signal_handler(){
    showResult();
    exit(1);
}

function main(){

    $queryTypes = array(
        'count_entity',
        'count_event',
        'top_values',
        'aggregation',
        'result',
        'score'
    );

    // Use SlicingDiceTester with demo api key
    // To get another demo api key visit: http://panel.slicingdice.com/docs/#api-details-api-connection-api-keys-demo-key
    $sdTester = new SlicingDiceTester(
        'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfX3NhbHQiOiJkZW1vNDFtIiwicGVybWlzc2lvbl9sZXZlbCI6MywicHJvamVjdF9pZCI6MjAyLCJjbGllbnRfaWQiOjEwfQ.ncguKQpOLBE97Y8-ODSnpMjWNjQ7nx7ruyTSS4OXL-A');

    // run tests for each query type
    try{
        foreach($queryTypes as $item){
            $sdTester->runTests($item);
        }
    } catch(\Exception $e){
    }

    showResult();

    if ($sdTester->numFails > 0) {
        exit(1);
    }
}

main();
?>
