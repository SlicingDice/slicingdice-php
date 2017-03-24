<?php

namespace Slicer\Exceptions;

class MappedExceptions {
    public static function all(){
        return array(
            10 => 'Slicer\Exceptions\Auth\AuthMissingHeaderException',
            11 => 'Slicer\Exceptions\Auth\AuthAPIKeyException',
            12 => 'Slicer\Exceptions\Auth\AuthInvalidAPIKeyException',
            13 => 'Slicer\Exceptions\Auth\AuthIncorrectPermissionException',
            14 => 'Slicer\Exceptions\Auth\AuthInvalidRemoteException',
            15 => 'Slicer\Exceptions\Auth\CustomKeyInvalidFieldCreationException',
            16 => 'Slicer\Exceptions\Auth\CustomKeyInvalidPermissionForFieldException',
            17 => 'Slicer\Exceptions\Auth\CustomKeyInvalidOperationException',
            18 => 'Slicer\Exceptions\Auth\CustomKeyNotPermittedException',
            19 => 'Slicer\Exceptions\Auth\CustomKeyRouteNotPermittedException',
            20 => 'Slicer\Exceptions\Auth\DemoApiInvalidEndpointException',
            # Request validations (21 - 29)
            21 => 'Slicer\Exceptions\Request\RequestMissingContentTypeException',
            22 => 'Slicer\Exceptions\Request\RequestIncorrectContentTypeValueException',
            23 => 'Slicer\Exceptions\Request\RequestRateLimitException',
            24 => 'Slicer\Exceptions\Request\RequestInvalidJsonException',
            25 => 'Slicer\Exceptions\Request\RequestInvalidHttpMethodException',
            26 => 'Slicer\Exceptions\Request\RequestInvalidEndpointException',
            27 => 'Slicer\Exceptions\Request\RequestIncorrectHttpException',
            28 => 'Slicer\Exceptions\Request\RequestExceedLimitException',
            # Account Errors (30 - 39)
            30 => 'Slicer\Exceptions\Account\AccountMissingPaymentMethodException',
            31 => 'Slicer\Exceptions\Account\AccountPaymentRequiredException',
            32 => 'Slicer\Exceptions\Account\AccountBannedException',
            33 => 'Slicer\Exceptions\Account\AccountDisabledException',
            # Field errors (40 - 59)
            40 => 'Slicer\Exceptions\Field\FieldMissingParamException',
            41 => 'Slicer\Exceptions\Field\FieldTypeException',
            42 => 'Slicer\Exceptions\Field\FieldIntegerValuesException',
            43 => 'Slicer\Exceptions\Field\FieldAlreadyExistsException',
            44 => 'Slicer\Exceptions\Field\FieldLimitException',
            45 => 'Slicer\Exceptions\Field\FieldTimeSeriesLimitException',
            46 => 'Slicer\Exceptions\Field\FieldTimeSeriesSystemLimitException',
            47 => 'Slicer\Exceptions\Field\FieldDecimalTypeException',
            48 => 'Slicer\Exceptions\Field\FieldStorageValueException',
            49 => 'Slicer\Exceptions\Field\FieldInvalidApiName',
            50 => 'Slicer\Exceptions\Field\FieldInvalidNameException',
            51 => 'Slicer\Exceptions\Field\FieldInvalidDescriptionException',
            52 => 'Slicer\Exceptions\Field\FieldExceedDescriptionlengthException',
            53 => 'Slicer\Exceptions\Field\FieldInvalidCardinalityException',
            54 => 'Slicer\Exceptions\Field\FieldDecimalLimitException',
            55 => 'Slicer\Exceptions\Field\FieldRangeLimitException',
            56 => 'Slicer\Exceptions\Field\FieldExceededMaxNameLenghtException',
            57 => 'Slicer\Exceptions\Field\FieldExceededMaxApiNameLenghtException',
            58 => 'Slicer\Exceptions\Field\FieldEmptyEntityIdException',
            59 => 'Slicer\Exceptions\Field\FieldExceededPermitedValueException',
            # Index errors (60 - 79)
            60 => 'Slicer\Exceptions\Index\IndexInvalidDecimalPlacesException',
            61 => 'Slicer\Exceptions\Index\IndexEntityValueTypeException',
            62 => 'Slicer\Exceptions\Index\IndexFieldNameTypeException',
            63 => 'Slicer\Exceptions\Index\IndexFieldTypeException',
            64 => 'Slicer\Exceptions\Index\IndexEntityNameTooBigException',
            65 => 'Slicer\Exceptions\Index\IndexFieldValueTooBigException',
            66 => 'Slicer\Exceptions\Index\IndexInvalidDecimalPlacesException',
            67 => 'Slicer\Exceptions\Index\IndexFieldNotActiveException',
            68 => 'Slicer\Exceptions\Index\IndexIdLimitException',
            69 => 'Slicer\Exceptions\Index\IndexFieldLimitException',
            70 => 'Slicer\Exceptions\Index\IndexDateFormatException',
            71 => 'Slicer\Exceptions\Index\IndexFieldStringEmptyValueException',
            72 => 'Slicer\Exceptions\Index\IndexFieldTimeseriesInvalidParameterException',
            73 => 'Slicer\Exceptions\Index\IndexFieldNumericInvalidValueException',
            74 => 'Slicer\Exceptions\Index\IndexFieldTimeseriesMissingValueException',
            75 => 'Slicer\Exceptions\Index\QueryTimeSeriesInvalidPrecisionSecondsException',
            76 => 'Slicer\Exceptions\Index\QueryTimeSeriesInvalidPrecisionMinutesException',
            77 => 'Slicer\Exceptions\Index\QueryTimeSeriesInvalidPrecisionHoursException',
            78 => 'Slicer\Exceptions\Index\QueryDateFormatException',
            79 => 'Slicer\Exceptions\Index\QueryRelativeIntervalException',
            # Query errors (80 - 109)
            80 => 'Slicer\Exceptions\Query\QueryMissingQueryException',
            81 => 'Slicer\Exceptions\Query\QueryInvalidTypeException',
            82 => 'Slicer\Exceptions\Query\QueryMissingTypeParamException',
            83 => 'Slicer\Exceptions\Query\QueryInvalidOperatorException',
            84 => 'Slicer\Exceptions\Query\QueryIncorrectOperatorUsageException',
            85 => 'Slicer\Exceptions\Query\QueryFieldNotActiveException',
            86 => 'Slicer\Exceptions\Query\QueryMissingOperatorException',
            87 => 'Slicer\Exceptions\Query\QueryIncompleteException',
            88 => 'Slicer\Exceptions\Query\QueryEventCountQueryException',
            89 => 'Slicer\Exceptions\Query\QueryInvalidMetricException',
            90 => 'Slicer\Exceptions\Query\QueryIntegerException',
            91 => 'Slicer\Exceptions\Query\QueryFieldLimitException',
            92 => 'Slicer\Exceptions\Query\QueryLevelLimitException',
            93 => 'Slicer\Exceptions\Query\QueryBadAggsFormationException',
            94 => 'Slicer\Exceptions\Query\QueryInvalidAggFilterException',
            95 => 'Slicer\Exceptions\Query\QueryMetricsLevelException',
            96 => 'Slicer\Exceptions\Query\QueryTimeSeriesException',
            97 => 'Slicer\Exceptions\Query\QueryMetricsTypeException',
            98 => 'Slicer\Exceptions\Query\QueryContainsNumericException',
            99 => 'Slicer\Exceptions\Query\QueryExistsEntityLimitException',
            100 => 'Slicer\Exceptions\Query\QueryMultipleFiltersException',
            102 => 'Slicer\Exceptions\Query\QueryMissingNameParamException',
            103 => 'Slicer\Exceptions\Query\QuerySavedAlreadyExistsException',
            104 => 'Slicer\Exceptions\Query\QuerySavedNotExistsException',
            105 => 'Slicer\Exceptions\Query\QuerySavedInvalidTypeException',
            106 => 'Slicer\Exceptions\Query\MethodNotAllowedException',
            107 => 'Slicer\Exceptions\Query\QueryExistsMissingIdsException',
            108 => 'Slicer\Exceptions\Query\QueryInvalidFormatException',
            109 => 'Slicer\Exceptions\Query\QueryTopValuesParameterEmptyException',
            110 => 'Slicer\Exceptions\Query\QueryDataExtractionLimitValueException',
            111 => 'Slicer\Exceptions\Query\QueryDataExtractionLimitValueTooBigException',
            112 => 'Slicer\Exceptions\Query\QueryDataExtractionLimitAndPageTokenValueException',
            113 => 'Slicer\Exceptions\Query\QueryDataExtractionPageTokenValueException',
            114 => 'Slicer\Exceptions\Query\QueryDataExtractionFieldLimitException',
            115 => 'Slicer\Exceptions\Query\QueryExistsEntityEmptyException',
            116 => 'Slicer\Exceptions\Query\QuerySavedInvalidQueryValueException',
            117 => 'Slicer\Exceptions\Query\QuerySavedInvalidCachePeriodValueException',
            118 => 'Slicer\Exceptions\Query\QuerySavedInvalidNameException',
            119 => 'Slicer\Exceptions\Query\QueryCountInvalidParameterErrorException',
            120 => 'Slicer\Exceptions\Query\QueryAggregationInvalidParameterException',
            121 => 'Slicer\Exceptions\Query\QueryAggregationInvalidFilterQueryException',
            122 => 'Slicer\Exceptions\Query\QueryExceededMaxNumberQuerysException',
            123 => 'Slicer\Exceptions\Query\QueryExceededMaxNumberQuerysException',
            124 => 'Slicer\Exceptions\Query\QueryInvalidOperatorUsageException',
            125 => 'Slicer\Exceptions\Query\QueryInvalidParameterUsageException',
            126 => 'Slicer\Exceptions\Query\QueryParameterInvalidFieldUsageException',
            127 => 'Slicer\Exceptions\Query\QueryInvalidFieldUsageException',
            # Internal errors (110 - 120)
            130 => '\Slicer\Exceptions\InternalServerException',
            131 => '\Slicer\Exceptions\FieldCreateInternalException'
        );
    }
}
?>