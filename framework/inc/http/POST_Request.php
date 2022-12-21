<?php

namespace YOUR_NAMESPACE\framework\inc\http;

if ( ! defined( 'ABSPATH' ) ) exit;

class POST_Request extends aRequest
{
    static protected function get_args_raw() : array
    {
        return ! empty( $_POST ) ? $_POST : [];
    }
}