<?php

namespace YOUR_NAMESPACE\framework\inc\pluggable;

use \YOUR_NAMESPACE\framework\inc\filesystem\Directory;
use \YOUR_NAMESPACE\framework\inc\filesystem\File;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class Pluggable
{
    protected $main_file;

    static public function get_by_main_file_relative_path( string $main_file_relative_path ) : Pluggable
    {
        $plugins_dir = new Directory( WP_PLUGIN_DIR );

        $main_file = $plugins_dir->get_file( $main_file_relative_path );

        return new static( $main_file );
    }

    public function __construct( File $main_file )
    {
        $this->main_file = $main_file;
    }

    public function get_main_file_relative_path() : string
    {
        $path_parts = explode( '/', $this->get_main_file()->get_path() );

        $dir_name  = $path_parts[ count( $path_parts ) - 2 ];
        $file_name = $path_parts[ count( $path_parts ) - 1 ];

        return $dir_name . '/' . $file_name;
    }

    public function get_main_file() : File
    {
        return $this->main_file;
    }

    public function get_dir() : Directory
    {
        return $this->get_main_file()->get_parent();
    }

    public function get_data() : array
    {
        $data = $this->get_raw_data();

        return Main::hooks()->apply_filters( 'pluggable/data', $data, $this->get_main_file_relative_path() );
    }

    abstract protected function get_raw_data() : array;

    /**
     * Plugin/theme version.
     *
     * @return string
     */
    public function get_version() : string
    {
        return $this->get_data()['Version'];
    }

    /**
     * Plugin/theme name.
     *
     * @return string
     */
    public function get_name() : string
    {
        return $this->get_data()['Name'];
    }

    /**
     * Plugin/theme text domain.
     *
     * @return string
     */
    public function get_text_domain() : string
    {
        return $this->get_data()['TextDomain'];
    }

    abstract public function get_file_url( string $path ) : string;
}