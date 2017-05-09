<?php

namespace Slicer\Core;

use Slicer\Exceptions\MappedExceptions;

use Slicer\Exceptions\Auth\AuthMissingHeaderException;
use Slicer\Exceptions\Auth\AuthAPIKeyException;
use Slicer\Exceptions\Auth\AuthInvalidAPIKeyException;
use Slicer\Exceptions\Auth\AuthInvalidRemoteException;
use Slicer\Exceptions\Auth\CustomKeyInvalidColumnCreationException;
use Slicer\Exceptions\Auth\CustomKeyInvalidPermissionForColumnException;
use Slicer\Exceptions\Auth\CustomKeyInvalidOperationException;
use Slicer\Exceptions\Auth\CustomKeyNotPermittedException;
use Slicer\Exceptions\Auth\CustomKeyRouteNotPermittedException;
use Slicer\Exceptions\Auth\DemoApiInvalidEndpointException;

use Slicer\Exceptions\Request\RequestMissingContentTypeException;
use Slicer\Exceptions\Request\RequestIncorrectContentTypeValueException;
use Slicer\Exceptions\Request\RequestRateLimitException;
use Slicer\Exceptions\Request\RequestInvalidJsonException;
use Slicer\Exceptions\Request\RequestInvalidHttpMethodException;
use Slicer\Exceptions\Request\RequestInvalidEndpointException;
use Slicer\Exceptions\Request\RequestIncorrectHttpException;
use Slicer\Exceptions\Request\RequestExceededLimitException;

use Slicer\Exceptions\Account\AccountMissingPaymentMethodException;
use Slicer\Exceptions\Account\AccountPaymentRequiredException;
use Slicer\Exceptions\Account\AccountBannedException;
use Slicer\Exceptions\Account\AccountDisabledException;

use Slicer\Exceptions\Column\ColumnMissingParamException;
use Slicer\Exceptions\Column\ColumnTypeException;
use Slicer\Exceptions\Column\ColumnIntegerValuesException;
use Slicer\Exceptions\Column\ColumnAlreadyExistsException;
use Slicer\Exceptions\Column\ColumnLimitException;
use Slicer\Exceptions\Column\ColumnTimeSeriesLimitException;
use Slicer\Exceptions\Column\ColumnTimeSeriesSystemLimitException;
use Slicer\Exceptions\Column\ColumnDecimalTypeException;
use Slicer\Exceptions\Column\ColumnStorageValueException;
use Slicer\Exceptions\Column\ColumnInvalidApiName;
use Slicer\Exceptions\Column\ColumnInvalidNameException;
use Slicer\Exceptions\Column\ColumnInvalidDescriptionException;
use Slicer\Exceptions\Column\ColumnExceededDescriptionlengthException;
use Slicer\Exceptions\Column\ColumnInvalidCardinalityException;
use Slicer\Exceptions\Column\ColumnDecimalLimitException;
use Slicer\Exceptions\Column\ColumnRangeLimitException;
use Slicer\Exceptions\Column\ColumnExceededMaxNameLenghtException;
use Slicer\Exceptions\Column\ColumnExceededMaxApiNameLenghtException;
use Slicer\Exceptions\Column\ColumnEmptyEntityIdException;
use Slicer\Exceptions\Column\ColumnExceededPermitedValueException;

use Slicer\Exceptions\Insert\InsertInvalidDecimalPlacesException;
use Slicer\Exceptions\Insert\InsertEntityValueTypeException;
use Slicer\Exceptions\Insert\InsertColumnNameTypeException;
use Slicer\Exceptions\Insert\InsertColumnTypeException;
use Slicer\Exceptions\Insert\InsertEntityNameTooBigException;
use Slicer\Exceptions\Insert\InsertColumnValueTooBigException;
use Slicer\Exceptions\Insert\InsertColumnNotActiveException;
use Slicer\Exceptions\Insert\InsertIdLimitException;
use Slicer\Exceptions\Insert\InsertColumnLimitException;
use Slicer\Exceptions\Insert\InsertDateFormatException;
use Slicer\Exceptions\Insert\InsertColumnStringEmptyValueException;
use Slicer\Exceptions\Insert\InsertColumnTimeSeriesInvalidParameterException;
use Slicer\Exceptions\Insert\InsertColumnNumericInvalidValueException;
use Slicer\Exceptions\Insert\InsertColumnTimeSeriesMissingValueException;
use Slicer\Exceptions\Insert\QueryTimeSeriesInvalidPrecisionSecondsException;
use Slicer\Exceptions\Insert\QueryTimeSeriesInvalidPrecisionMinutesException;
use Slicer\Exceptions\Insert\QueryTimeSeriesInvalidPrecisionHoursException;
use Slicer\Exceptions\Insert\QueryDateFormatException;
use Slicer\Exceptions\Insert\QueryRelativeIntervalException;

use Slicer\Exceptions\Query\QueryMissingQueryException;
use Slicer\Exceptions\Query\QueryInvalidTypeException;
use Slicer\Exceptions\Query\QueryMissingTypeParamException;
use Slicer\Exceptions\Query\QueryInvalidOperatorException;
use Slicer\Exceptions\Query\QueryIncorrectOperatorUsageException;
use Slicer\Exceptions\Query\QueryColumnNotActiveException;
use Slicer\Exceptions\Query\QueryMissingOperatorException;
use Slicer\Exceptions\Query\QueryIncompleteException;
use Slicer\Exceptions\Query\QueryEventCountQueryException;
use Slicer\Exceptions\Query\QueryInvalidMetricException;
use Slicer\Exceptions\Query\QueryColumnLimitException;
use Slicer\Exceptions\Query\QueryLevelLimitException;
use Slicer\Exceptions\Query\QueryBadAggsFormationException;
use Slicer\Exceptions\Query\QueryInvalidAggFilterException;
use Slicer\Exceptions\Query\QueryMetricsLevelException;
use Slicer\Exceptions\Query\QueryTimeSeriesException;
use Slicer\Exceptions\Query\QueryMetricsTypeException;
use Slicer\Exceptions\Query\QueryContainsNumericException;
use Slicer\Exceptions\Query\QueryExistsEntityLimitException;
use Slicer\Exceptions\Query\QueryMultipleFiltersException;
use Slicer\Exceptions\Query\QueryMissingNameParamException;
use Slicer\Exceptions\Query\QuerySavedAlreadyExistsException;
use Slicer\Exceptions\Query\QuerySavedNotExistsException;
use Slicer\Exceptions\Query\QuerySavedInvalidTypeException;
use Slicer\Exceptions\Query\MethodNotAllowedException;
use Slicer\Exceptions\Query\QueryExistsMissingIdsException;
use Slicer\Exceptions\Query\QueryInvalidFormatException;
use Slicer\Exceptions\Query\QueryTopValuesParameterEmptyException;
use Slicer\Exceptions\Query\QueryDataExtractionLimitValueException;
use Slicer\Exceptions\Query\QueryDataExtractionLimitValueTooBigException;
use Slicer\Exceptions\Query\QueryDataExtractionLimitAndPageTokenValueException;
use Slicer\Exceptions\Query\QueryDataExtractionPageTokenValueException;
use Slicer\Exceptions\Query\QueryDataExtractionColumnLimitException;
use Slicer\Exceptions\Query\QueryExistsEntityEmptyException;
use Slicer\Exceptions\Query\QuerySavedInvalidQueryValueException;
use Slicer\Exceptions\Query\QuerySavedInvalidCachePeriodValueException;
use Slicer\Exceptions\Query\QuerySavedInvalidNameException;
use Slicer\Exceptions\Query\QueryCountInvalidParameterErrorException;
use Slicer\Exceptions\Query\QueryAggregationInvalidParameterException;
use Slicer\Exceptions\Query\QueryAggregationInvalidFilterQueryException;
use Slicer\Exceptions\Query\QueryExceededMaxNumberQuerysException;
use Slicer\Exceptions\Query\QueryInvalidOperatorUsageException;
use Slicer\Exceptions\Query\QueryInvalidParameterUsageException;
use Slicer\Exceptions\Query\QueryParameterInvalidColumnUsageException;
use Slicer\Exceptions\Query\QueryInvalidColumnUsageException;

use Slicer\Exceptions\InternalServerException;
use Slicer\Exceptions\ColumnCreateInternalException;

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