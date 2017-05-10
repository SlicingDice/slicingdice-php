<?php

namespace Slicer\Exceptions;

use Slicer\Exceptions\SlicingDiceException;

class RequestBodySizeExceededException extends SlicingDiceException
{
    public function __construct($data) {
        parent::__construct($data);
    }
}
?>