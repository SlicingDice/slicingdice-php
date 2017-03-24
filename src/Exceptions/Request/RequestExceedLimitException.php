<?php 

namespace Slicer\Exceptions\Request;

use Slicer\Exceptions\SlicingDiceException;

class RequestExceedLimitException extends SlicingDiceException
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    public function __toString() {
        return __CLASS__ . ": {$this->message}\n";
    }
}
?>