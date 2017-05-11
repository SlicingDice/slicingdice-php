<?php

namespace Slicer\Exceptions;

class MappedExceptions {
    public static function all(){
        return array(
            2 => 'Slicer\Exceptions\DemoUnavailableException',
            1502 => 'Slicer\Exceptions\RequestRateLimitException',
            1507 => 'Slicer\Exceptions\RequestBodySizeExceededException',
            2012 => 'Slicer\Exceptions\IndexEntitiesLimitException',
            2013 => 'Slicer\Exceptions\IndexColumnsLimitException',
        );
    }
}
?>