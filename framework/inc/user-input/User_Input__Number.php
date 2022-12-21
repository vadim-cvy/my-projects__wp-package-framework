<?php

namespace YOUR_NAMESPACE\framework\inc\user_input;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class User_Input__Number extends aUser_Input
{
    use tUser_Input_Textual;

    public function is_empty() : bool
    {
        $value = $this->get_normalized_float();

        return empty( $value ) && ! is_numeric( $value );
    }

    public function get_normalized() : float
    {
        return $this->get_normalized_float();
    }

    public function get_normalized_float( int $decimals = -1 ) : float
    {
        if ( ! is_numeric( $this->value ) )
        {
            $this->throw_value_error( 'Value must be a number.' );
        }

        $value = $this->value;

        if ( $decimals !== -1 )
        {
            $value = number_format( $value, $decimals );
        }

        return (float) $value;
    }

    public function get_normalized_int() : int
    {
        return (int) $this->get_normalized_float();
    }

    public function validate_is_between( $min, $max, bool $allow_equal_to_min_max = true  ) : aUser_Input
    {
        $value = $this->get_normalized_float();

        $min_error_text =
            'Value must be greater than ' .
            ( $allow_equal_to_min_max ? 'or equal to ' : '' ) .
            '"' . $min . '"';

        $max_error_text =
            'Value must be less than ' .
            ( $allow_equal_to_min_max ? 'or equal to ' : '' ) .
            '"' . $max . '"';

        if ( $value < $min )
        {
            $this->throw_value_error( $min_error_text, $value );
        }

        if ( $value > $max )
        {
            $this->throw_value_error( $max_error_text, $value );
        }

        if ( ! $allow_equal_to_min_max )
        {
            if ( $value == $min )
            {
                $this->throw_value_error( $min_error_text, $value );
            }

            if ( $value == $max )
            {
                $this->throw_value_error( $max_error_text, $value );
            }
        }

        return $this;
    }

    public function validate_int() : User_Input__Number
    {
        $float_value = $this->get_normalized_float();

        $int_value = (int) $float_value;

        if ( $float_value != $int_value )
        {
            $this->value_error( 'Number must be an integer, float passed.', $float_value );
        }

        return $this;
    }

    public function validate_id() : User_Input__Number
    {
        if ( $this->get_normalized_int() < 1 )
        {
            $this->value_error( 'ID must be positive integer.', $id );
        }

        return $this;
    }
}