<?php

namespace YOUR_NAMESPACE\framework\inc\data_types;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Contains snippets which help to work with strings.
 */
class Strings
{
    /**
     * Generates random string.
     *
     * @param integer $length       String length.
     * @param string $characters    Allowed characters.
     * @return string               Random string.
     */
    static public function generate_random_string(
        int $length,
        string $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ) : string
    {
        $characters_length = strlen( $characters );

        $output = '';

        for ( $i = 0; $i < $length; $i++ )
        {
            $character_index = rand( 0, $characters_length - 1 );

            $output .= $characters[ $character_index ];
        }

        return $output;
    }

    /**
     * Prefixes string if it is not prefixed yet.
     *
     * @param string $string    String that should be prefixed.
     * @param string $prefix    Prefix which should be added to the string.
     * @return string           Prefixed string.
     */
    static public function maybe_prefix_string( string $string, string $prefix ) : string
    {
        if ( empty( $prefix ) )
        {
            return $string;
        }

        return strpos( $string, $prefix ) !== 0 ?
            $prefix . $string :
            $string;
    }

    static public function cast( string $value )
    {
        if ( is_numeric( $value ) )
        {
            if ( (int) $value == $value )
            {
                $value = (int) $value;
            }
            else
            {
                $value = (float) $value;
            }
        }
        else
        {
            $decoded_value = json_decode( $value, true );

            if ( json_last_error() === JSON_ERROR_NONE )
            {
                $value = $decoded_value;
            }
        }

        return $value;
    }

    static public function switch_case( string $string, string $current_case, string $output_case ) : string
    {
        if ( $current_case === 'snake' )
        {
            $replace_regex = '/_([^_])/';
        }
        else if ( $current_case === 'cammel' )
        {
            $replace_regex = '/[A-Z]/';
        }

        return preg_replace_callback( $replace_regex,
            function ( array $match ) use ( $output_case )
            {
                if ( $output_case === 'snake' )
                {
                    return '_' . strtolower( $match[0] );
                }
                else if ( $output_case === 'cammel' )
                {
                    return ucfirst( $match[1] );
                }
            },
            $string
        );
    }
}