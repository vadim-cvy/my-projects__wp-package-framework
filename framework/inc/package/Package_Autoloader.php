<?php

namespace YOUR_NAMESPACE\framework\inc\package;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Wrapper for the spl_autoload_register().
 */
class Package_Autoloader
{
    /**
     * Called class name with its namespace.
     *
     * @var string
     */
    protected $class_full_name = '';

    /**
     * The root of the package namespace.
     *
     * @var string
     */
    protected $package_root_namespace = '';

    /**
     * Package root directory path.
     *
     * @var string
     */
    protected $package_root_dir = '';

    /**
     * Autoloader entry point.
     *
     * @param string $package_root_namespace    See $this->package_root_namespace.
     * @param string $package_root_dir          See $this->package_root_dir.
     */
    public function __construct( string $package_root_namespace, string $package_root_dir )
    {
        $this->package_root_namespace = $package_root_namespace;
        $this->package_root_dir       = trailingslashit( $package_root_dir );

        spl_autoload_register( [ $this, '_load_class' ] );
    }

    /**
     * A callback for spl_autoload_register.
     *
     * @param string $class_full_name   See $this->class_full_name.
     * @return void
     */
    public function _load_class( string $class_full_name ) : void
    {
        $this->class_full_name = $class_full_name;

        if (
            $this->is_class_belong_to_package() &&
            file_exists( $this->get_class_file_path() )
        )
        {
            require_once $this->get_class_file_path();
        }
    }

    /**
     * Checks if called class belongs to the package autoloader is registered for.
     *
     * @return boolean  True if class belongs to the package, false otherwise.
     */
    protected function is_class_belong_to_package() : bool
    {
        return strpos( $this->class_full_name, $this->package_root_namespace ) !== false;
    }

    /**
     * Getter for the called class file path.
     *
     * @return string   Path to the called class file.
     */
    protected function get_class_file_path() : string
    {
        return $this->package_root_dir .
            $this->get_class_dir_relative_path() .
            $this->get_class_file_name();
    }

    /**
     * Getter for the called class base directory path (relative).
     *
     * @return string   Relative path to the called class base directory.
     */
    protected function get_class_dir_relative_path() : string
    {
        $namespace_without_root =
            str_replace( $this->package_root_namespace . '\\', '', $this->get_class_namespace() );

        // Convert namespace separator to directory separator.
        $relative_path = str_replace( '\\', DIRECTORY_SEPARATOR, $namespace_without_root );

        // Directory names are cread using "-" instead of "_" which are used in
        // the namespaces.
        $relative_path = str_replace( '_', '-', $relative_path );

        return $relative_path;
    }

    /**
     * Getter for the called class namespace.
     *
     * @return string   Called class namespace.
     */
    protected function get_class_namespace() : string
    {
        $pattern = '~' . $this->get_class_name() . '$~';

        return preg_replace( $pattern, '', $this->class_full_name );
    }

    /**
     * Getter for the called class file name.
     *
     * @return string   File name of the called class.
     */
    protected function get_class_file_name() : string
    {
        return $this->get_class_name() . '.php';
    }

    /**
     * Getter for the called class name.
     *
     * @return string   Called class name.
     */
    protected function get_class_name() : string
    {
        $full_name_parts = explode( '\\', $this->class_full_name );

        return array_pop( $full_name_parts );
    }
}