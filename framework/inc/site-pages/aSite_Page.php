<?php

namespace YOUR_NAMESPACE\framework\inc\site_pages;

use \YOUR_NAMESPACE\framework\inc\design_patterns\tSingleton;

use \YOUR_NAMESPACE\framework\inc\wp\WP;
use \YOUR_NAMESPACE\framework\inc\wp\Hooks;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aSite_Page
{
    use tSingleton;

    protected function __construct()
    {
        WP::hooks()->listen( 'wp', [ $this, '_on_maybe_is_current' ], 1 );
        WP::hooks()->listen( 'current_screen', [ $this, '_on_maybe_is_current' ], 1 );
    }

    static public function get_url( array $query_args = [] ) : string
    {
        $url = static::get_raw_url();

        foreach ( $query_args as $key => $value )
        {
            $url = add_query_arg( $key, $value, $url );
        }

        return $url;
    }

    abstract static protected function get_raw_url() : string;

    static public function redirect( array $query_args = [] ) : void
    {
        $url = static::get_url( $query_args );

        wp_redirect( $url );

        exit();
    }

    abstract static public function is_current() : bool;

    public function _on_maybe_is_current() : void
    {
        if ( $this->is_current() )
        {
            $this->on_is_current();
        }
    }

    protected function on_is_current() : void
    {
        $this->hooks()->do_action( 'is_current' );
    }

    static protected function hooks_base() : Hooks
    {
        return Main::hooks()->create_child( 'site_page' );
    }

    abstract static public function hooks() : Hooks;
}