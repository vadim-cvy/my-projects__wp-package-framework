<?php

namespace YOUR_NAMESPACE\framework\inc\pluggable;

use \YOUR_NAMESPACE\Main;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class Theme extends Pluggable
{
    protected function get_raw_data() : array
    {
        return get_theme_data( $this->get_dir()->get_path() );
    }

    public function get_file_url( string $path ) : string
    {
        $relative_path = str_replace( $this->get_dir()->get_path(), '', $path );

        return get_stylesheet_directory_uri() . '/' . $relative_path;
    }
}