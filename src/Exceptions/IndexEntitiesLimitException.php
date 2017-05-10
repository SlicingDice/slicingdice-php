<?php

namespace Slicer\Exceptions;

use Slicer\Exceptions\SlicingDiceException;

class IndexEntitiesLimitException extends SlicingDiceException
{
    public function __construct($data) {
        parent::__construct($data);
    }
}
?>