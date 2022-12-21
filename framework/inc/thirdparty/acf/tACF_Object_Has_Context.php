<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\acf;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tACF_Object_Has_Context
{
    /**
     * ACF context which should be used by get_field(), update_field(), etc.
     *
     * @var string  Context. Ex:
     *              "1" - Post with id 1;
     *              "tax_name_3" - Term with id 3 (belongs to taxonomy "tax_name");
     *              "user_5" - User with id 5.
     */
    protected $context = '';

    /**
     * Setter for $this->context.
     *
     * @param string $context See documentation of $this->context.
     * @return void
     */
    public function set_context( string $context ) : void
    {
        $this->context = $context;
    }

    /**
     * Getter for $this->context.
     *
     * @return string See documentation of $this->context.
     */
    public function get_context( bool $validate = true ) : string
    {
        if ( $validate && empty( $this->context ) )
        {
            throw new Exception( 'Context is not set!' );
        }

        return $this->context;
    }
}