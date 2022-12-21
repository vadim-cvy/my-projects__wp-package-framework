<?php

namespace YOUR_NAMESPACE\framework\inc\user_input;

if ( ! defined( 'ABSPATH' ) ) exit;

class User_Input__Array extends aUser_Input
{
    public function get_normalized( callable $normalize_element_callback = null )
    {
        $array = $this->value;

        if ( empty( $normalize_element_callback ) )
        {
            return $array;
        }

        try
        {
            foreach ( $array as $key => $value )
            {
                try
                {
                    $array[ $key ] = $normalize_element_callback( $value, $key );
                }
                catch ( User_Input_Validation_Error $err )
                {
                    $err->extend_context( $key, $err );

                    throw $err;
                }
            }
        }
        catch ( User_Input_Validation_Error $err )
        {
            $err->extend_context( $this->context, $err );

            throw $err;
        }

        return $array;
    }

    public function is_empty() : bool
    {
        return empty( $this->get_normalized() );
    }

    public function validate_has_keys_only( array $allowed_keys ) : User_Input__Array
    {
        $unexpected_keys = array_diff( array_keys( $this->value ), $allowed_keys );

        if ( ! empty( $unexpected_keys ) )
        {
            $this->throw_error(
                'Unexpected keys passed: "' . implode( '", "', $unexpected_keys ) . '". ' .
                'Allowed keys are: "' . implode( '", "', $allowed_keys ) . '". '
            );
        }

        return $this;
    }

    public function validate_has_keys( array $required_keys ) : User_Input__Array
    {
        $missed_keys = array_diff( $required_keys, array_keys( $this->value ) );

        if ( ! empty( $missed_keys ) )
        {
            $this->throw_error( 'The following keys are missed: "' . implode( '", "', $missed_keys ) . '".' );
        }

        return $this;
    }

    public function validate_min_length( int $min_length ) : User_Input__Array
    {
        if ( count( $this->value ) < $min_length )
        {
            $error_message = 'Array must contain at least ' . $min_length . ' %s.';

            $this->throw_error( sprintf( $error_message,
                $min_length > 1 ? 'elements' : 'element'
            ));
        }

        return $this;
    }

    public function validate_max_length( int $max_length ) : User_Input__Array
    {
        if ( count( $this->value ) > $max_length )
        {
            $error_message = 'Array must not contain more than ' . $max_length . ' %s.';

            $this->throw_error( sprintf( $error_message,
                $max_length > 1 ? 'elements' : 'element'
            ));
        }

        return $this;
    }

    public function validate_unique() : User_Input__Array
    {
        if ( count( array_unique( $this->value ) ) !== count( $this->value ) )
        {
            $this->throw_error( 'Array must contain unique values only.' );
        }

        return $this;
    }
}