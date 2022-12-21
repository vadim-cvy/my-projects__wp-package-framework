<?php

namespace YOUR_NAMESPACE\framework\inc\filesystem;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helps to work with directories.
 */
class Directory extends aFilesystem_Object
{
    /**
     * Sets directory path.
     *
     * @param string $path Directory path.
     */
    protected function set_path( string $path ) : void
    {
        $path =
            realpath( $path ) ?
            realpath( $path ) :
            $path;

        $path = trailingslashit( $path );

        parent::set_path( $path );
    }

    /**
     * Returns specified child file.
     *
     * @param string $relative_path     Child file relative path.
     * @param boolean $validate_exists  Set true if error should be thrown if file does not exist.
     * @return File             Child file.
     */
    public function get_file( string $relative_path, bool $validate_exists = true ) : File
    {
        $file = new File( $this->get_child_object_absolute_path( $relative_path ) );

        if ( $validate_exists )
        {
            $file->validate_exists();
        }

        return $file;
    }

    /**
     * Returns specified child directory.
     *
     * @param string $relative_path     Child directory relative path.
     * @param boolean $validate_exists  Set true if error should be thrown if child directory does not exist.
     * @return Directory                Child directory.
     */
    public function get_sub_dir( string $relative_path, bool $validate_exists = true ) : Directory
    {
        $dir = new Directory( $this->get_child_object_absolute_path( $relative_path ) );

        if ( $validate_exists )
        {
            $dir->validate_exists();
        }

        return $dir;
    }

    /**
     * Returns absolute path to child object.
     *
     * @param string $object_relative_path  Child object relative path.
     * @return string                       Child object absolute path.
     */
    protected function get_child_object_absolute_path( string $object_relative_path ) : string
    {
        return $this->get_path() . $object_relative_path;
    }

    public function get_human_readable_object_type() : string
    {
        return 'directory';
    }
}