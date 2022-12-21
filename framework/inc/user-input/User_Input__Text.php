<?php

namespace YOUR_NAMESPACE\framework\inc\user_input;

if ( ! defined( 'ABSPATH' ) ) exit;

class User_Input__Text extends aUser_Input
{
    use tUser_Input_Textual;

    public function is_empty() : bool
    {
        $value = $this->get_normalized();

        return ! is_numeric( $value ) && empty( $value );
    }

    public function get_normalized( bool $is_html_allowed = false )
    {
        return $is_html_allowed ? wp_kses_post( $this->value ) : sanitize_text_field( $this->value );
    }

    public function validate_email() : User_Input__Text
    {
        if ( ! filter_var( $this->get_normalized(), FILTER_VALIDATE_EMAIL ) )
        {
            $this->throw_value_error( 'Email is invalid.' );
        }

        return $this;
    }
}