<?php

namespace YOUR_NAMESPACE\framework\inc\package;

use \Exception;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\framework\inc\filesystem\Directory;

use \YOUR_NAMESPACE\Main;

use \YOUR_NAMESPACE\framework\inc\data_types\Arrays;

if ( ! defined( 'ABSPATH' ) ) exit;

class Package_Assets_Controller
{
    public function enqueue_css( string $handle, string $asset_file_url, array $extra_args = [] ) : void
    {
        $handle = Main::prefix( $handle );

        $extra_args = $this->get_css_asset_extra_args( $extra_args );

        $this->add_enqueue_scripts_callback(function() use ( $handle, $asset_file_url, $extra_args ) : void
        {
            $deps    = $extra_args['deps'];
            $version = $extra_args['version'];
            $media   = $extra_args['media'];

            wp_enqueue_style( $handle, $asset_file_url, $deps, $version, $media );
        });
    }

    public function enqueue_js( string $handle, string $asset_file_url, array $extra_args = [] ) : void
    {
        $handle = Main::prefix( $handle );

        $extra_args = $this->get_js_asset_extra_args( $extra_args );

        $this->add_enqueue_scripts_callback(function() use ( $handle, $asset_file_url, $extra_args ) : void
        {
            $deps      = $extra_args['deps'];
            $version   = ! empty( $extra_args['version'] ) ? $extra_args['version'] : null;
            $in_footer = $extra_args['in_footer'];

            wp_enqueue_script( $handle, $asset_file_url, $deps, $version, $in_footer );
        });

        if ( $extra_args['is_module'] )
        {
            $this->mark_script_as_module( $handle );
        }
    }

    public function localize_js_data( string $handle, string $object_name, callable $get_data_callback ) : void
    {
        // todo: check if script is enqueued

        $handle = Main::prefix( $handle );

        $this->add_enqueue_scripts_callback(function() use ( $handle, $object_name, $get_data_callback ) : void
        {
            $data = call_user_func( $get_data_callback );

            $data = Arrays::switch_keys_case( $data, 'snake', 'cammel' );

            wp_localize_script( $handle, $object_name, [
                'json' => json_encode( $data ),
            ]);
        });
    }

    protected function mark_script_as_module( string $script_handle )
    {
        $required_handle = $script_handle;

        WP::hooks()->listen( 'script_loader_tag',
            function( string $tag, string $handle ) use ( $required_handle ) : string
            {
                if ( $handle === $required_handle )
                {
                    $tag = str_replace( '<script', '<script type="module"', $tag );
                }

                return $tag;
            }
        );
    }

    protected function add_enqueue_scripts_callback( callable $callback )
    {
        WP::hooks()->listen( 'wp_enqueue_scripts', $callback, PHP_INT_MAX );
        WP::hooks()->listen( 'admin_enqueue_scripts', $callback, PHP_INT_MAX );
    }

    protected function get_css_asset_extra_args( array $custom_extra_args = [] ) : array
    {
        $defaults = [
            'deps'    => [],
            'media'   => '',
            'version' => ''
        ];

        return $this->get_asset_extra_args( $defaults, $custom_extra_args );
    }

    protected function get_js_asset_extra_args( array $custom_extra_args = [] ) : array
    {
        $defaults = [
            'deps'      => [],
            'version'   => '',
            'in_footer' => true,
            'is_module' => false,
        ];

        return $this->get_asset_extra_args( $defaults, $custom_extra_args );
    }

    protected function get_asset_extra_args( array $defaults, array $custom_extra_args ) : array
    {
        $extra_args = array_merge( $defaults, $custom_extra_args );

        $invalid_keys = array_diff_key( $defaults, $extra_args );

        if ( ! empty( $invalid_keys ) )
        {
            throw new Exception(
                'The following extra args keys are not valid: "' .
                implode( '", "', $invalid_keys ) . '"!'
            );
        }

        return $extra_args;
    }
}