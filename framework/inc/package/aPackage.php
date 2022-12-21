<?php

namespace YOUR_NAMESPACE\framework\inc\package;

use \Exception;

use \YOUR_NAMESPACE\framework\inc\design_patterns\tSingleton;

use \YOUR_NAMESPACE\framework\inc\pluggable\Pluggable;

use \YOUR_NAMESPACE\framework\inc\dashboard\Dashboard;

use \YOUR_NAMESPACE\framework\inc\wp\WP;
use \YOUR_NAMESPACE\framework\inc\wp\Hooks;
use \YOUR_NAMESPACE\framework\inc\wp\AJAX;

use \YOUR_NAMESPACE\framework\inc\cookies\Cookies;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

use \RecursiveIteratorIterator;

use \RecursiveDirectoryIterator;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aPackage
{
    use tSingleton;

    protected function __construct()
    {
        if ( ! defined( 'YOUR_NAMESPACE_ENV' ) )
        {
            define( 'YOUR_NAMESPACE_ENV', 'production' );
        }

        $this->run();

        $this->init_page_controllers();
    }

    abstract protected function run() : void;

    protected function init_page_controllers() : void
    {
        $view_dirs = [
            $this->fs()->get_framework_view_dir(),
            $this->fs()->get_view_dir(),
        ];

        foreach ( $view_dirs as $dir )
        {
            $files_itterator = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $dir->get_path() ) );

            foreach ( $files_itterator as $file )
            {
                $controller_class_regex = '~' . DIRECTORY_SEPARATOR . 'Controller.php$~';

                if ( $file->isDir() || ! preg_match( $controller_class_regex, $file->getPathname() ) )
                {
                    continue;
                }

                $controller_dir_path = dirname( $file->getPathname() );

                $controller_dir_relative_path =
                    str_replace( $this->fs()->get_root_dir()->get_path(), '', $controller_dir_path );

                $controller_namespace = '\\YOUR_NAMESPACE\\' . $controller_dir_relative_path . '\\';

                $controller_namespace = str_replace( '-', '_', $controller_namespace );
                $controller_namespace = str_replace( DIRECTORY_SEPARATOR, '\\', $controller_namespace );

                $controller_class = $controller_namespace . 'Controller';

                $controller_class::get_instance();
            }
        }
    }

    abstract static public function get_type();

    abstract static protected function get_wrapped_pluggable() : Pluggable;

    static public function get_slug() : string
    {
        $namespace_parts = explode( '\\', __NAMESPACE__ );

        $slug = strtolower( $namespace_parts[0] );

        return $slug;
    }

    public function add_dashboard_error( string $error_message ) : void
    {
        $error_prefix =
            '<strong>' .
                '"' . $this->get_wrapped_pluggable()->get_name() . '" ' .
                    ucfirst( $this->get_type() ) . ' Error:' .
            '</strong> ';

        Dashboard::add_error( $error_prefix . $error_message );
    }

    static public function hooks() : Hooks
    {
        return WP::hooks()->create_child( static::get_slug() );
    }

    static public function register_activation_hook( callable $callback ) : void
    {
        register_activation_hook( static::get_wrapped_pluggable()->get_main_file()->get_path(), $callback );
    }

    static public function register_deactivation_hook( callable $callback ) : void
    {
        register_deactivation_hook( static::get_wrapped_pluggable()->get_main_file()->get_path(), $callback );
    }

    static public function dashboard_pages() : Package_Dashboard_Pages
    {
        return Package_Dashboard_Pages::get_instance();
    }

    static public function ajax() : AJAX
    {
        return WP::ajax()->create_child( static::get_slug() );
    }

    static public function settings() : Package_Settings
    {
        return new Package_Settings();
    }

    static public function cookies() : Package_Cookies
    {
        return Package_Cookies::get_instance();
    }

    static public function fs() : Package_Filesystem
    {
        return Package_Filesystem::get_instance();
    }

    static public function assets() : Package_Assets_Controller
    {
        return new Package_Assets_Controller();
    }

    static public function prefix( string $input_string, string $separator = '_' )
    {
        $prefix = static::get_slug() . $separator;

        return Strings::maybe_prefix_string( $input_string, $prefix );
    }

    static public function unprefix( string $input_string, string $separator = '_' )
    {
        $prefix = static::get_slug() . $separator;

        return preg_replace( '/^' . $prefix . '/', '', $input_string );
    }

    static public function schedule_cron_job( string $name, int $interval, callable $callback ) : void
    {
        $name = static::prefix( $name );

        $interval_name = static::maybe_register_cron_interval( $interval );

        // todo: do this on activation hook
        // todo: remove this cron on deactivation hook
        if ( ! wp_next_scheduled( $name ) )
        {
            wp_schedule_event( time(), $interval_name, $name );
        }

        WP::hooks()->listen( $name, $callback );
    }

    static protected function maybe_register_cron_interval( int $interval ) : string
    {
        $interval_name = static::prefix( intval( $interval ) . '_sec' );

        if ( empty( wp_get_schedules()[ $interval_name ] ) )
        {
            WP::hooks()->listen( 'cron_schedules', function( array $schedules ) use ( $interval, $interval_name ) : array
            {
                $schedules[ $interval_name ] = [
                    'interval' => $interval,
                    'display'  => 'Every ' . $interval . ' Seconds',
                ];

                return $schedules;
            });
        }

        return $interval_name;
    }
}