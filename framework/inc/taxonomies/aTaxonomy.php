<?php

namespace YOUR_NAMESPACE\framework\inc\taxonomies;

use \Exception;

use \YOUR_NAMESPACE\framework\inc\design_patterns\tSingleton;

use \YOUR_NAMESPACE\framework\inc\wp\Hooks;

use \YOUR_NAMESPACE\framework\inc\package\tHas_Package_Based_Slug;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aTaxonomy
{
    use tSingleton;

    public static function get_term_by( string $field_name, string $field_value ) : Term
    {
        $raw_term = get_term_by( $field_name, $field_value, static::get_slug() );

        if ( empty( $raw_term ) )
        {
            throw new Term_Not_Exist_Error(
                'Can\'t find term with "' . $field_name . '" field equal to "' . $field_value . '"!'
            );
        }

        return static::wrap_term( $raw_term->term_id );
    }

    public static function get_terms( array $args = [] ) : array
    {
        $args['taxonomy'] = static::get_slug();

        $terms = get_terms( $args );

        if ( is_wp_error( $terms ) )
        {
            throw new Exception( $terms->get_error_message() );
        }

        if ( empty( $args['fields'] ) || $args['fields'] !== 'ids' )
        {
            foreach ( $terms as $key => $term )
            {
                $terms[ $key ] = static::wrap_term( $term->term_id );
            }
        }

        return $terms;
    }

    public static function create_term( string $label ) : Term
    {
        $result = wp_create_term( $label, static::get_slug() );

        if ( is_wp_error( $result ) )
        {
            throw new Exception( $result->get_error_message() );
        }

        $term_id = is_array( $result ) ? $result['term_id'] : $result;

        return static::wrap_term( $term_id );
    }

    public static function wrap_term( int $term_id ) : Term
    {
        return new Term( $term_id );
    }

    static public function term_exists( int $term_id ) : bool
    {
        return ! empty( term_exists( $term_id, static::get_slug() ) );
    }

    static public function hooks() : Hooks
    {
        return Main::hooks()->create_child( 'taxonomy/' . static::get_slug() );
    }

    abstract static public function get_slug() : string;
}