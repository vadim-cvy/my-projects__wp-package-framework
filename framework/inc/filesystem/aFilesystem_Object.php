<?php

namespace YOUR_NAMESPACE\framework\inc\filesystem;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aFilesystem_Object
{
    /**
     * Object path.
     *
     * @var string
     */
    protected $path;

    /**
     * @param string $path Object path.
     */
    public function __construct( string $path )
    {
        $this->set_path( $path );
    }

    /**
     * Sets object path.
     *
     * @param string $path Object path.
     */
    protected function set_path( string $path ) : void
    {
        $this->path = str_replace( '\\', '/', $path );
    }

    /**
     * Returns object path.
     *
     * @return string Object path.
     */
    public function get_path() : string
    {
        return $this->path;
    }

    /**
     * Returns object name.
     *
     * @return string Object name.
     */
    public function get_name() : string
    {
        return basename( $this->get_path() );
    }

    /**
     * Checks if object exists.
     *
     * @return boolean True if object exists, false otherwise.
     */
    public function exists() : bool
    {
        return file_exists( $this->get_path() );
    }

    /**
     * Validates if object exists. Throws an error if not.
     *
     * @return void
     */
    public function validate_exists() : void
    {
        if ( ! $this->exists() )
        {
            throw new Exception(
                ucfirst( $this->get_human_readable_object_type() ) .
                '( ' . $this->get_path() . ') does not exist!'
            );
        }
    }

    /**
     * Returns human readable object type i.e "directory" or "file".
     *
     * @return string Human readable object type i.e "directory" or "file".
     */
    abstract protected function get_human_readable_object_type() : string;

    public function get_parent() : Directory
    {
        return new Directory( dirname( $this->get_path() ) );
    }
}