<?php

namespace Slicer\Core;

use Slicer\Exceptions\MappedExceptions;

use Slicer\Exceptions\Auth\AuthMissingHeaderException;
use Slicer\Exceptions\Auth\AuthAPIKeyException;
use Slicer\Exceptions\Auth\AuthInvalidAPIKeyException;
use Slicer\Exceptions\Auth\AuthInvalidRemoteException;
use Slicer\Exceptions\Auth\CustomKeyInvalidFieldCreationException;
use Slicer\Exceptions\Auth\CustomKeyInvalidPermissionForFieldException;
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
use Slicer\Exceptions\Request\RequestExceedLimitException;

use Slicer\Exceptions\Account\AccountMissingPaymentMethodException;
use Slicer\Exceptions\Account\AccountPaymentRequiredException;
use Slicer\Exceptions\Account\AccountBannedException;
use Slicer\Exceptions\Account\AccountDisabledException;

use Slicer\Exceptions\Field\FieldMissingParamException;
use Slicer\Exceptions\Field\FieldTypeException;
use Slicer\Exceptions\Field\FieldIntegerValuesException;
use Slicer\Exceptions\Field\FieldAlreadyExistsException;
use Slicer\Exceptions\Field\FieldLimitException;
use Slicer\Exceptions\Field\FieldTimeSeriesLimitException;
use Slicer\Exceptions\Field\FieldTimeSeriesSystemLimitException;
use Slicer\Exceptions\Field\FieldDecimalTypeException;
use Slicer\Exceptions\Field\FieldStorageValueException;
use Slicer\Exceptions\Field\FieldInvalidApiName;
use Slicer\Exceptions\Field\FieldInvalidNameException;
use Slicer\Exceptions\Field\FieldInvalidDescriptionException;
use Slicer\Exceptions\Field\FieldExceedDescriptionlengthException;
use Slicer\Exceptions\Field\FieldInvalidCardinalityException;
use Slicer\Exceptions\Field\FieldDecimalLimitException;
use Slicer\Exceptions\Field\FieldRangeLimitException;
use Slicer\Exceptions\Field\FieldExceededMaxNameLenghtException;
use Slicer\Exceptions\Field\FieldExceededMaxApiNameLenghtException;
use Slicer\Exceptions\Field\FieldEmptyEntityIdException;
use Slicer\Exceptions\Field\FieldExceededPermitedValueException;

use Slicer\Exceptions\Index\IndexInvalidDecimalPlacesException;
use Slicer\Exceptions\Index\IndexEntityValueTypeException;
use Slicer\Exceptions\Index\IndexFieldNameTypeException;
use Slicer\Exceptions\Index\IndexFieldTypeException;
use Slicer\Exceptions\Index\IndexEntityNameTooBigException;
use Slicer\Exceptions\Index\IndexFieldValueTooBigException;
use Slicer\Exceptions\Index\IndexFieldNotActiveException;
use Slicer\Exceptions\Index\IndexIdLimitException;
use Slicer\Exceptions\Index\IndexFieldLimitException;
use Slicer\Exceptions\Index\IndexDateFormatException;
use Slicer\Exceptions\Index\IndexFieldStringEmptyValueException;
use Slicer\Exceptions\Index\IndexFieldTimeseriesInvalidParameterException;
use Slicer\Exceptions\Index\IndexFieldNumericInvalidValueException;
use Slicer\Exceptions\Index\IndexFieldTimeseriesMissingValueException;
use Slicer\Exceptions\Index\QueryTimeSeriesInvalidPrecisionSecondsException;
use Slicer\Exceptions\Index\QueryTimeSeriesInvalidPrecisionMinutesException;
use Slicer\Exceptions\Index\QueryTimeSeriesInvalidPrecisionHoursException;
use Slicer\Exceptions\Index\QueryDateFormatException;
use Slicer\Exceptions\Index\QueryRelativeIntervalException;

use Slicer\Exceptions\Query\QueryMissingQueryException;
use Slicer\Exceptions\Query\QueryInvalidTypeException;
use Slicer\Exceptions\Query\QueryMissingTypeParamException;
use Slicer\Exceptions\Query\QueryInvalidOperatorException;
use Slicer\Exceptions\Query\QueryIncorrectOperatorUsageException;
use Slicer\Exceptions\Query\QueryFieldNotActiveException;
use Slicer\Exceptions\Query\QueryMissingOperatorException;
use Slicer\Exceptions\Query\QueryIncompleteException;
use Slicer\Exceptions\Query\QueryEventCountQueryException;
use Slicer\Exceptions\Query\QueryInvalidMetricException;
use Slicer\Exceptions\Query\QueryFieldLimitException;
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
use Slicer\Exceptions\Query\QueryDataExtractionFieldLimitException;
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
use Slicer\Exceptions\Query\QueryParameterInvalidFieldUsageException;
use Slicer\Exceptions\Query\QueryInvalidFieldUsageException;

use Slicer\Exceptions\InternalServerException;
use Slicer\Exceptions\FieldCreateInternalException;

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