<?php

namespace Slicer\Core;

use Slicer\Core\HandlerResponse;
use Slicer\Exceptions\SlicingDiceHTTPException;

class Requester {

    /**
    * A $curl object request
    */
    private $curl;
    /**
    * A timeout request
    *
    * @var int Timeout
    */
    private $timeoutReq;
    private $response;
    private $header = array("Content-Type: application/json");
    private $headerSQL = array("Content-Type: application/sql");

    private $isSQL;

    function __construct($timeout, $sql=false) {
        $this->curl = curl_init();
        $this->timeoutReq = $timeout;
        $this->setRequestSettings();
        $this->isSQL = $sql;
    }

    /**
    * Configure curl settings before request
    */
    private function setRequestSettings(){
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeoutReq);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($this->curl, CURLOPT_VERBOSE, true);
    }

    /**
    * Make a get request
    *
    * @param string $url A url to request
    * @param string $apiKey A apiKey to access API
    */
    public function get($url, $apiKey) {
        array_push($this->header, "Authorization: " . $apiKey);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
        return $this->wrapperRequests();
    }

    /**
    * Make a delete request
    *
    * @param string $url A url to request
    * @param string $apiKey A apiKey to access API
    */
    public function delete($url, $apiKey) {
        array_push($this->header, "Authorization: " . $apiKey);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->header);
        curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "DELETE");
        return $this->wrapperRequests();
    }
    /**
    * Make a POST and PUT request
    *
    * @param string $url A url to request
    * @param string $apiKey A apiKey to access API
    * @param array $query A query to send in request
    * @param bool $update Define if request is PUT or POST
    */
    public function data($url, $apiKey, $query, $update=false) {
        if ($this->isSQL) {
            $dataToSend = $query;
        } else {
            $dataToSend = json_encode($query);
        }
        $header_request = $this->header;

        if ($this->isSQL) {
            $header_request = $this->headerSQL;
        }

        array_push($header_request, "Authorization: " . $apiKey);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header_request);
        // curl_setopt($this->curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $dataToSend);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        if ($update) {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "PUT");
        }
        else {
            curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, "POST");
        }
        return $this->wrapperRequests();
    }

    /**
    * Handler response and raise errors
    */
    private function wrapperRequests() {
        $this->response = json_decode(curl_exec($this->curl), true);
        $info = curl_getinfo($this->curl);
        $httpStatus = $info["http_code"];
        $handlerResponse = new HandlerResponse($this->response);

        if ($handlerResponse->requestSuccessful()) {
            if ($httpStatus >= 400 && $httpStatus < 600) {
                throw new SlicingDiceHTTPException("HTTP Error: " . $httpStatus);
            }
            return $this->response;
        } else {
            print_r($this->response);
        }
    }
}
?>