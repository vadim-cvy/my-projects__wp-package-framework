<?php

namespace YOUR_NAMESPACE\framework\inc\filesystem;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Helps to work with files.
 */
class File extends aFilesystem_Object
{
    /**
     * Returns file last modified time.
     *
     * @return integer File last modified time.
     */
    public function get_modified_time() : int
    {
        return filemtime( $this->get_path() );
    }

    /**
     * Returns file extension.
     *
     * @return string File extension.
     */
    public function get_extension() : string
    {
        return pathinfo( $this->get_path(), PATHINFO_EXTENSION );
    }

    /**
     * Wrapper for require() function.
     *
     * @param array $variables Variables that will be passed to the file.
     * @return void
     */
    public function require( array $variables = [] ) : void
    {
        $this->validate_can_require();

        extract( $variables );

        require $this->get_path();
    }

    public function get_content( array $template_args = [] ) : string
    {
        ob_start();

        $this->require( $template_args );

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    /**
     * Validates if file can be passed to require() function. Throws error if not.
     *
     * @return void
     */
    protected function validate_can_require() : void
    {
        if ( $this->get_extension() !== 'php' )
        {
            throw new Exception( 'This method can be called for PHP files only!' );
        }
    }

    public function get_human_readable_object_type() : string
    {
        return 'file';
    }
}