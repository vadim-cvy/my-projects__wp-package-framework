<?php

namespace YOUR_NAMESPACE\framework\inc\http;

if ( ! defined( 'ABSPATH' ) ) exit;

class GET_Request extends aRequest
{
    static protected function get_args_raw() : array
    {
        return ! empty( $_GET ) ? $_GET : [];
    }
}