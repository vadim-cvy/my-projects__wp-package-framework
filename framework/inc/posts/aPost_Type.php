<?php

namespace YOUR_NAMESPACE\framework\inc\posts;

use \YOUR_NAMESPACE\framework\inc\design_patterns\tSingleton;

use \YOUR_NAMESPACE\framework\inc\view_controllers\aView_Controller;

use \YOUR_NAMESPACE\framework\inc\view_controllers\Dynamic_Template;

use \YOUR_NAMESPACE\framework\inc\dashboard\Dashboard_Table_Column;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\Main;

use \WP_Query;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aPost_Type
{
    use tSingleton;

    static public function add_posts_list_column(
        string $slug,
        string $label,
        callable $render_cell_cb,
        string $insert_after = ''
    ) : void
    {
        new Dashboard_Table_Column( $slug, $label, static::get_slug(), $render_cell_cb, $insert_after );
    }

    static public function remove_posts_list_column( string $slug ) : void
    {
        $hook_name = 'manage_' . static::get_slug() . '_posts_columns';

        WP::hooks()->listen( $hook_name, function( array $columns ) use ( $slug ) : array
        {
            unset( $columns[ $slug ] );

            return $columns;
        } );
    }

    static public function register_post_status( string $slug, string $label, array $args = [] )
    {
        $args['label'] = $label;
        $args['post_type'] = static::get_slug();

        register_post_status( Main::prefix( $slug ), $args );
    }

    static public function wrap_post( int $post_id ) : aPost
    {
        return new Post( $post_id );
    }

    static public function get_posts( array $args = [] ) : array
    {
        $posts = static::query( $args )->posts;

        if ( empty( $args['fields'] ) )
        {
            foreach ( $posts as $i => $post )
            {
                $posts[ $i ] = static::wrap_post( $post->ID );
            }
        }

        return $posts;
    }

    static public function query( array $args = [] ) : WP_Query
    {
        $args['post_type'] = static::get_slug();

        return new WP_Query( $args );
    }

    abstract static public function get_slug() : string;

    static public function create_post( array $args = [] ) : aPost
    {
        $args['post_type'] = static::get_slug();

        $post_id = wp_insert_post( $args );

        return static::wrap_post( $post_id );
    }

    static public function get_archive_url() : string
    {
        return get_post_type_archive_link( static::get_slug() );
    }

    static public function is_archive_page() : bool
    {
        return is_post_type_archive( static::get_slug() );
    }

    static public function is_singular() : bool
    {
        return is_singular( static::get_slug() );
    }

    static public function remove_metabox( string $name, string $context ) : void
    {
        $screens = [ static::get_slug() ];

        WP::hooks()->listen( 'admin_menu', function() use ( $name, $screens, $context ) : void
        {
            remove_meta_box( $name, $screens, $context );
        });
    }

    static public function is_screen_match( string $base ) : bool
    {
        $screen = get_current_screen();

        return $screen->base === $base && $screen->post_type === static::get_slug();
    }
}