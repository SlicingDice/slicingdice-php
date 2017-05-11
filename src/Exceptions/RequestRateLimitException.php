<?php

namespace Slicer\Exceptions;

use Slicer\Exceptions\SlicingDiceException;

class RequestRateLimitException extends SlicingDiceException
{
    public function __construct($data) {
        parent::__construct($data);
    }
}
?>