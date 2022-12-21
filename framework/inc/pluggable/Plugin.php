<?php

namespace YOUR_NAMESPACE\framework\inc\pluggable;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helps to work with plugins.
 */
class Plugin extends Pluggable
{
    /**
     * Wrapper for get_plugin_data().
     *
     * @return array<string> Plugin data.
     */
    protected function get_raw_data() : array
    {
        if ( ! function_exists( 'get_plugin_data' ) )
        {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        $markup = false;

        return get_plugin_data( $this->get_main_file()->get_path(), $markup );
    }

    public function get_file_url( string $path ) : string
    {
        $file_name = basename( $path );

        return trailingslashit( plugin_dir_url( $path ) ) . $file_name;
    }
}