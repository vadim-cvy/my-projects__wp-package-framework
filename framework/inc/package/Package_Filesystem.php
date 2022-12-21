<?php

namespace YOUR_NAMESPACE\framework\inc\package;

use \YOUR_NAMESPACE\framework\inc\design_patterns\tSingleton;

use \YOUR_NAMESPACE\framework\inc\filesystem\Directory;
use \YOUR_NAMESPACE\framework\inc\filesystem\File;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Simplifies working with package dirs and files.
 */
class Package_Filesystem
{
    use tSingleton;

    /**
     * Returns package root dir.
     *
     * @return Directory Package root dir.
     */
    public function get_root_dir() : Directory
    {
        $namespace_parts = explode( '\\', __NAMESPACE__ );

        // Steps back number required to get back to the root dir.
        $steps_back_number = count( $namespace_parts ) - 1;

        $dir_path = __DIR__ . '/' . str_repeat( '../', $steps_back_number );

        return new Directory( $dir_path );
    }

    /**
     * Returns package main file.
     *
     * @return File Package main file.
     */
    public function get_main_file() : File
    {
        return static::get_root_dir()->get_file( 'Main.php' );
    }

    public function get_view_dir() : Directory
    {
        return static::get_root_dir()->get_sub_dir( 'view' );
    }

    public function get_framework_view_dir() : Directory
    {
        return static::get_root_dir()->get_sub_dir( 'framework/view' );
    }

    public function get_build_dir() : Directory
    {
        return static::get_root_dir()->get_sub_dir( 'build' );
    }
}