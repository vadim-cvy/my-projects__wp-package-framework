<?php

namespace YOUR_NAMESPACE\framework\inc\view_controllers;

use \YOUR_NAMESPACE\framework\inc\design_patterns\tSingleton;

use \YOUR_NAMESPACE\framework\inc\filesystem\File;
use \YOUR_NAMESPACE\framework\inc\filesystem\Directory;

use \YOUR_NAMESPACE\framework\inc\wp\Hooks;

use \YOUR_NAMESPACE\Main;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\framework\inc\wp\AJAX;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aView_Controller
{
    use tSingleton;

    protected $custom_vars = [];

    public function hooks() : Hooks
    {
        return Main::hooks()
            ->create_child( 'view' )
            ->create_child( $this->get_type() )
            ->create_child( $this->get_name() );
    }

    public function get_content() : string
    {
        $this->validate_required_custom_vars();

        return $this->get_template_file()->get_content( $this->get_vars() );
    }

    public function get_template_file( bool $validate_exists = true ) : File
    {
        return $this->get_templates_dir( $validate_exists )->get_file( 'main.php', $validate_exists );
    }

    public function render() : void
    {
        echo $this->get_content();
    }

    protected function set_var( string $name, $value ) : void
    {
        $this->custom_vars[ $name ] = $value;
    }

    protected function get_required_custom_vars() : array
    {
        return [];
    }

    protected function validate_required_custom_vars() : void
    {
        if ( empty( $this->get_required_custom_vars() ) )
        {
            return;
        }

        $missed_vars = array_diff( $this->get_required_custom_vars(), array_keys( $this->custom_vars ) );

        if ( ! empty( $missed_vars ) )
        {
            throw new Exception( 'Missing required context vars: "' . implode( '", "', $missed_vars ) . '".' );
        }
    }

    protected function get_vars() : array
    {
        $vars = array_merge(
            $this->get_predefined_vars(),
            $this->custom_vars
        );

        $vars['sub_blocks'] = $this->get_sub_blocks();

        return $this->hooks()->apply_filters( 'vars', $vars );
    }

    protected function get_var( string $name )
    {
        return $this->get_vars()[ $name ];
    }

    protected function get_predefined_vars() : array
    {
        return [];
    }

    protected function get_sub_block( string $block_name ) : aView_Controller
    {
        return $this->get_sub_blocks()[ $block_name ];
    }

    final protected function get_sub_blocks() : array
    {
        $sub_blocks = [];

        $namespace_parts = explode( '\\', get_called_class() );

        foreach ( $this->get_sub_block_names() as $block_name )
        {
            foreach ( [ 'common', $namespace_parts[2] ] as $end )
            {
                $block_class_name =
                    '\\' . $namespace_parts[0] .
                    '\\' . $namespace_parts[1] .
                    '\\' . $end .
                    ( $this->is_framework() ? '\\' . $namespace_parts[2] : '' ) .
                    '\\blocks' .
                    '\\' . $block_name .
                    '\\controller\\Controller';

                if ( $end === 'common' && ! class_exists( $block_class_name ) )
                {
                    continue;
                }

                $sub_blocks[ $block_name ] = $block_class_name::get_instance();

                if ( $end === 'common' && class_exists( $block_class_name ) )
                {
                    break;
                }
            }
        }

        return $sub_blocks;
    }

    protected function get_sub_block_names() : array
    {
        return [];
    }

    public function enqueue_assets() : void
    {
        foreach ( $this->get_sub_blocks() as $sub_template )
        {
            $sub_template->enqueue_assets();
        }

        if ( $this->get_js_build_dir( false )->exists() )
        {
            $this->enqueue_js( 'main' );
        }

        if ( $this->get_css_build_dir( false )->exists() )
        {
            $this->enqueue_css( 'main' );
        }
    }

    public function localize_js_data( string $handle, callable $get_data_callback, string $object_name = '' )
    {
        if ( empty( $object_name ) )
        {
            $object_name = Strings::switch_case( Main::prefix( $this->get_name() ), 'snake', 'cammel' );
        }

        Main::assets()->localize_js_data( $this->prefix_asset_handle( $handle ), $object_name, $get_data_callback );
    }

    public function enqueue_js( string $handle, string $src = '', array $extra_args = [] ) : void
    {
        if ( $handle === 'main' )
        {
            $asset_file = $this->get_js_build_dir()->get_file( 'main.js' );

            $src = $this->get_internal_asset_src( $asset_file );

            $extra_args = array_merge(
                $this->get_internal_asset_extra_args( $asset_file ),
                $extra_args
            );
        }

        Main::assets()->enqueue_js( $this->prefix_asset_handle( $handle ), $src, $extra_args );
    }

    public function enqueue_css( string $handle, string $src = '', array $extra_args = [] ) : void
    {
        if ( $handle === 'main' )
        {
            $asset_file = $this->get_css_build_dir()->get_file( 'main.css' );

            $src = $this->get_internal_asset_src( $asset_file );

            $extra_args = array_merge(
                $this->get_internal_asset_extra_args( $asset_file ),
                $extra_args
            );
        }

        Main::assets()->enqueue_css( $this->prefix_asset_handle( $handle ), $src, $extra_args );
    }

    protected function get_internal_asset_src( File $asset_file ) : string
    {
        return Main::get_wrapped_pluggable()->get_file_url( $asset_file->get_path() );
    }

    protected function get_internal_asset_extra_args( File $asset_file ) : array
    {
        return [
            'version' => (string) $asset_file->get_modified_time(),
        ];
    }

    protected function prefix_asset_handle( string $handle ) : string
    {
        return ( $this->is_framework() ? 'framework__' : '' ) .
            $this->get_type() . '__' .
            $this->get_name() . '__' .
            $handle;
    }

    protected function get_css_build_dir( bool $validate_exists = true ) : Directory
    {
        return $this->get_assets_build_dir( 'css', $validate_exists );
    }

    protected function get_js_build_dir( bool $validate_exists = true ) : Directory
    {
        return $this->get_assets_build_dir( 'js', $validate_exists );
    }

    protected function get_assets_build_dir( string $type, bool $validate_exists = true ) : Directory
    {
        $assets_dir_relative_path =
            str_replace( Main::fs()->get_root_dir()->get_path() . 'view/', '', $this->get_root_dir()->get_path() ) .
            $type;

        return Main::fs()->get_build_dir()->get_sub_dir( $assets_dir_relative_path, $validate_exists );
    }

    protected function get_templates_dir( bool $validate_exists = true ) : Directory
    {
        return static::get_root_dir()->get_sub_dir( 'templates', $validate_exists );
    }

    protected function get_root_dir() : Directory
    {
        $namespace_parts = explode( '\\', get_called_class() );

        $relative_path_parts = array_slice( $namespace_parts, 1, -2 );

        $relative_path = str_replace( '_', '-', implode( '/', $relative_path_parts ) );

        return Main::fs()->get_root_dir()->get_sub_dir( $relative_path );
    }

    public function get_name() : string
    {
        $namespace_part_index = 4;

        if ( $this->is_framework() )
        {
            $namespace_part_index += 1;
        }

        return explode( '\\', get_called_class() )[ $namespace_part_index ];
    }

    protected function get_end() : string
    {
        $namespace_part_index = 2;

        if ( $this->is_framework() )
        {
            $namespace_part_index += 1;
        }

        return explode( '\\', get_called_class() )[ $namespace_part_index ]; // front_end, dashboard
    }

    protected function get_type() : string
    {
        $namespace_part_index = 3;

        if ( $this->is_framework() )
        {
            $namespace_part_index += 1;
        }

        $type = explode( '\\', get_called_class() )[ $namespace_part_index ]; // pages, blocks

        return preg_replace( '/s$/', '', $type );
    }

    protected function is_framework() : bool
    {
        return explode( '\\', get_called_class() )[1] === 'framework';
    }

    public function ajax() : AJAX
    {
        return Main::ajax()
            ->create_child( 'view' )
            ->create_child( $this->get_type() )
            ->create_child( $this->get_name() );
    }
}