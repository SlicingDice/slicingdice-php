<?php

namespace Slicer\Exceptions;

class SlicingDiceException extends \Exception
{
    public function __construct($data) {
        if(!is_array($data) ) {
            parent::__construct( $data );
        } else {
            if (array_key_exists('code', $data)) {
                $this->code = $data['code'];
            }
            if (array_key_exists('more-info', $data)) {
                $this->more_info = $data['more-info'];
            }
            if (array_key_exists('message', $data)) {
                parent::__construct($data['message']);
            }
        }
    }

    public function __toString() {
        return __CLASS__ . ": code={$this->code} message={$this->message} more_info={$this->more_info}\n";
    }
}
?>
