<?php

namespace Slicer\Exceptions;

use Slicer\Exceptions\SlicingDiceException;

class IndexColumnsLimitException extends SlicingDiceException
{
    public function __construct($data) {
        parent::__construct($data);
    }
}
?>