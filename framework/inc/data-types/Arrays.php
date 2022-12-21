<?php

namespace YOUR_NAMESPACE\framework\inc\data_types;

use \YOUR_NAMESPACE\framework\inc\user_input\User_Input;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Contains snippets which help to work with arrays.
 */
class Arrays
{
    /**
     * Applies callback to each value of the array recursively.
     *
     * @param array $array          Input array
     * @param callable $callback    Callback which will be applied to input array values.
     * @return array                Array created based on the mapped input array values.
     */
    public static function map_recursive( array $array, callable $callback ) : array
    {
        foreach ( $array as $key => $value )
        {
            $array[ $key ] =
                is_array( $value ) ?
                static::map_recursive( $value, $callback ) :
                call_user_func( $callback, $value, $key );
        }

        return $array;
    }

    /**
     * Filters array recursively. Wrapus array_filter().
     *
     * @param array $array          Input array.
     * @param callable $callback    Callback which should be used to filter array.
     * @return array
     */
    public static function filter_recursive( array $array, callable $callback = null ) : array
    {
        foreach ( $array as $key => $value )
        {
            if ( is_array( $value ) )
            {
                $array[ $key ] = static::filter_recursive( $value, $callback );
            }
        }

        return array_filter( $array, $callback, ARRAY_FILTER_USE_BOTH );
    }

    public static function merge_recursive( array $array_1, array $array_2, bool $merge_numeric = false ) : array
    {
        foreach ( $array_2 as $key => $value )
        {
            if ( is_numeric( $key ) && ! $merge_numeric )
            {
                $array_1[] = $value;
            }
            else
            {
                $array_1[ $key ] =
                    isset( $array_1[ $key ] ) && is_array( $value ) ?
                    static::merge_recursive( $array_1[ $key ], $array_2[ $key ] ) :
                    $value;
            }
        }

        return $array_1;
    }

    /**
     * Checks if arary is associative.
     *
     * @param array $array  Input array.
     * @return boolean      True if array is associative, false otherwise.
     */
    public static function is_assoc( array $array ) : bool
    {
        $numeric_keys = array_filter( array_keys( $array ), 'is_numeric' );

        return count( $numeric_keys ) === count( $array );
    }

    public static function switch_keys_case( array $input_array, string $current_case, string $output_case ) : array
    {
        $output_array = [];

        foreach ( $input_array as $key => $value )
        {
            $key = Strings::switch_case( $key, $current_case, $output_case );

            if ( is_array( $value ) )
            {
                $value = static::switch_keys_case( $value, $current_case, $output_case );
            }

            $output_array[ $key ] = $value;
        }

        return $output_array;
    }

    public static function get_prev_key( array $array, string $current_key )
    {
        $keys = array_keys( $array );

        $current_key_index = array_search( $current_key, $keys );

        if ( $current_key_index === false )
        {
            throw new Error( 'Can\'t find passed key "' . $current_key . '"!' );
        }

        return $current_key_index !== 0 ? $keys[ $current_key_index - 1 ] : false;
    }

    public static function array_column_preserve_keys( array $array, string $key ) : array
    {
        return array_filter( array_combine( array_keys( $array ), array_column( $array, $key ) ) );
    }

    static public function flat( array $multidimensional_array ) : array
    {
        $flat_array = [];

        array_walk_recursive( $multidimensional_array, function( $value, $key ) use ( &$flat_array )
        {
            $flat_array[ $key ] = $value;
        });

        return $flat_array;
    }

    static public function validate_contains_type_only( array $array, string $allowed_type ) : void
    {
        static::validate_contains_types_only( $array, [
            $allowed_type
        ]);
    }

    static public function validate_contains_types_only( array $array, array $allowed_types ) : void
    {
        foreach ( $array as $key => $value )
        {
            $is_type_match = false;

            $value_type = gettype( $value );

            foreach ( $allowed_types as $allowed_type )
            {
                $is_class_name = strpos( $allowed_type, '\\' ) !== false;

                if (
                    ( $is_class_name && is_a( $value, $allowed_type ) ) ||
                    $value_type === $allowed_type
                )
                {
                    $is_type_match = true;

                    break;
                }
            }

            if ( ! $is_type_match )
            {
                throw new \Error(
                    'Array element (key: "' . $key . '") contains a value of unexpected type! ' .
                    'Value type is "' . $value_type . '" while expected value ' .
                        _n( 'type is', 'types are', count( $allowed_types ) ) . ': ' .
                        '"' .implode( '", "', $allowed_types ) . '".'
                );
            }
        }
    }

    public static function slice_by_keys( array $input_array, array $keys )
    {
        return array_intersect_key( $input_array, array_flip( $keys ) );
    }
}