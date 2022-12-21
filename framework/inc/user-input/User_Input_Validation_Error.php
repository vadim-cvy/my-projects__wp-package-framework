<?php

namespace YOUR_NAMESPACE\framework\inc\user_input;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class User_Input_Validation_Error extends Exception
{
    public function extend_context( $context ) : void
    {
        $this->message = $context . ' >> ' . $this->getMessage();
    }
}