<?php

namespace Slicer\Tests;

date_default_timezone_set('America/Sao_Paulo');
$filename = str_replace("tests_and_examples", "vendor/autoload.php", __DIR__);
require_once $filename;

use Slicer\SlicingDice;

class SlicingDiceTester {

    private $client;
    private $fieldTranslation;
    private $sleepTime;
    private $path;
    private $extension;
    public $numSuccess;
    public $numFails;
    public $failedTests;
    private $verbose;

    function __construct($apiKey, $verboseOption=false) {
        $this->client = new SlicingDice(array(
            "masterKey" => $apiKey
        ));

        // Translation table for fields with timestamp
        $this->fieldTranslation = array();

        $this->sleepTime = 5; // seconds
        $this->path = "/examples/"; // Directory containing examples to test
        $this->extension = ".json"; // Examples file format

        $this->numSuccess = 0;
        $this->numFails = 0;
        $this->failedTests = array();

        $this->verbose = $verboseOption;
        $this->updateResults();
    }

    public function runTests($queryType){
        $testData = $this->loadTestData($queryType);
        $numTests = count($testData);

        $counter = 0;
        foreach($testData as $test){
            $this->emptyFieldTranslation();

            $counter += 1;
            $name =  $test["name"];
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

    private function emptyFieldTranslation(){
        $this->fieldTranslation = array();
    }

    private function loadTestData($queryType){
        $filename = __DIR__ . $this->path . $queryType . $this->extension;
        $content = file_get_contents($filename);
        return json_decode($content, true);
    }

    private function createFields($test){
        $isSingular = $this->numFails == 1;
        $fieldOrFields = null;
        if ($isSingular){
            $fieldOrFields = "field";
        } else {
            $fieldOrFields = "fields";
        }
        print "  Creating " . count($test["fields"]) . " " . $fieldOrFields . "\n";

        foreach ($test["fields"] as $field) {
            $newField = $this->appendTimestampToFieldName($field);
            $this->client->createField($newField, true);

            if ($this->verbose){
                echo "    - " . $newField['api-name'] . "\n";
            }
        }
    }

    private function appendTimestampToFieldName($field){
        $oldName = '"' . $field['api-name'] . '"';

        $timestamp = $this->getTimestamp();
        $field['name'] = $field['name'] . $timestamp;
        $field['api-name'] = $field['api-name'] . $timestamp;
        $newName = '"' . $field['api-name'] . '"';

        $this->fieldTranslation[$oldName] = $newName;
        return $field;
    }

    private function getTimestamp(){
        $date = new \DateTime();
        return strval($date->getTimestamp());
    }

    private function indexData($test){
        $isSingular = $this->numFails == 1;
        $entityOrEntities = null;
        if ($isSingular){
            $entityOrEntities = "entity";
        } else {
            $entityOrEntities = "entities";
        }
        print "  Indexing " . count($test["index"]) . " " . $entityOrEntities . "\n";

        $indexDataArray = $this->translateFieldNames($test["index"]);

        if ($this->verbose) {
            print_r($indexDataArray);
        }

        $this->client->index($indexDataArray, null, true);

        sleep($this->sleepTime);
    }

    private function translateFieldNames($jsonData){
        $dataString = json_encode($jsonData);

        foreach ($this->fieldTranslation as $oldName => $newName) {
            $dataString = str_replace($oldName, $newName, $dataString);
        }

        return json_decode($dataString, true);
    }

    private function executeQuery($queryType, $test){
        $queryData = $this->translateFieldNames($test["query"]);
        $result = null;
        echo "  Querying\n";

        if ($this->verbose){
            print_r($queryData);
        }

        if ($queryType == "count_entity"){
            $result = $this->client->countEntity($queryData, true);
        } else if ($queryType == "count_event"){
            $result = $this->client->countEvent($queryData, true);
        } else if ($queryType == "top_values"){
            $result = $this->client->topValues($queryData, true);
        } else if ($queryType == "aggregation"){
            $result = $this->client->aggregation($queryData, true);
        } else if ($queryType == "result"){
            $result = $this->client->result($queryData, true);
        } else if ($queryType == "score"){
            $result = $this->client->score($queryData, true);
        }



        return $result;
    }

    private function compareResult($test, $result){
        $expected = $this->translateFieldNames($test["expected"]);

        foreach ($test["expected"] as $key => $value) {
            if($value == "ignore"){
                continue;
            }

            if (array_diff_key($expected[$key], $result[$key])){
                $this->numFails += 1;
                array_push($this->failedTests, $test["name"]);

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

function showResult(){
    echo file_get_contents("testerResult.tmp");
    unlink("testerResult.tmp");
}

function signal_handler(){
    showResult();
    exit(1);
}

function main(){

    if (PHP_OS == "Linux"){
        declare(ticks = 1);
        pcntl_signal(SIGINT, "Slicer\Tests\signal_handler");
        pcntl_signal(SIGTSTP, "Slicer\Tests\signal_handler");
    }

    $queryTypes = array(
        'count_entity',
        'count_event',
        'top_values',
        'aggregation',
        'result',
        'score'
    );

    $sdTester = new SlicingDiceTester(
        'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJfX3NhbHQiOiJkZW1vNDFtIiwicGVybWlzc2lvbl9sZXZlbCI6MywicHJvamVjdF9pZCI6MjAyLCJjbGllbnRfaWQiOjEwfQ.ncguKQpOLBE97Y8-ODSnpMjWNjQ7nx7ruyTSS4OXL-A');

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
