<?php

namespace YOUR_NAMESPACE\framework\inc\http;

use \YOUR_NAMESPACE\framework\inc\data_types\Arrays;

use \YOUR_NAMESPACE\inc\helpers\Date;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aRequest
{
    abstract static protected function get_args_raw() : array;

    static public function get_args() : array
    {
        return Arrays::switch_keys_case( static::get_args_raw(), 'cammel', 'snake' );
    }

    static protected function get_arg( string $name, $default = null )
    {
        $args = static::get_args();

        if ( ! isset( $args[ $name ] ) )
        {
            if ( ! isset( $default ) )
            {
                throw new Param_Error( 'Param "' . $name . '" is missed.' );
            }
            else
            {
                return $default;
            }
        }

        return $args[ $name ];
    }

    static public function get_str_arg( string $name, string $default = null ) : string
    {
        $value = static::get_arg( $name, $default );

        if ( ! isset( $value ) && isset( $default ) )
        {
            return $default;
        }

        if ( ! is_string( $value ) )
        {
            static::throw_param_error__wrong_type( $name, 'string' );
        }

        return stripslashes( $value );
    }

    static public function get_int_arg( string $name, int $default = null ) : int
    {
        try
        {
            $value = static::get_str_arg( $name, $default );
        }
        catch ( Param_Type_Validation_Error $error )
        {
            static::throw_param_error__wrong_type( $name, 'int' );
        }

        if ( ! is_numeric( $value ) )
        {
            static::throw_param_error__wrong_type( $name, 'int' );
        }

        return (int) $value;
    }

    static public function get_bool_arg( string $name, bool $default = null ) : int
    {
        return (bool) static::get_int_arg( $name, (int) $default );
    }

    static public function get_arr_arg( string $name, bool $is_json = false, array $default = null ) : array
    {
        if ( $is_json )
        {
            $expected_type = 'JSON array';

            try
            {
                $str_default = isset( $default ) ? '' : null;

                $value = static::get_str_arg( $name, $str_default );
            }
            catch ( Param_Type_Validation_Error $error )
            {
                static::throw_param_error__wrong_type( $name, $expected_type );
            }

            $value =
                ! empty( $value ) ?
                json_decode( stripslashes( $value ), true ) :
                [];
        }
        else
        {
            $value = static::get_arg( $name, $default );
        }

        if ( empty( $value ) && isset( $default ) )
        {
            return $default;
        }

        if ( ! is_array( $value ) )
        {
            static::throw_param_error__wrong_type( $name, 'array' );
        }

        return $value;
    }

    static public function get_date_arg( string $name, Date $default = null ) : Date
    {
        $date_str = static::get_str_arg( $name, $default ? '' : null );

        if ( empty( $date_str ) )
        {
            return $default;
        }

        return Date::createFromFormat( 'Ymd', $date_str );
    }

    static protected function throw_param_error__wrong_type( string $name, string $expected_type )
    {
        throw new Param_Type_Validation_Error( '"' . $name . '" param must be type of ' . $expected_type . '.' );
    }
}