<?php

//namespace Slicer\Exceptions;

class SlicingDiceException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}
?>