<?php
class QueryFieldLimitException extends SlicingDiceException
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    public function __toString() {
        return __CLASS__ . ": {$this->message}\n";
    }
}
?>