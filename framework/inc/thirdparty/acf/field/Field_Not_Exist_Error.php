<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\acf\field;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Error that should triggered when some operation requires ACF filed but one
 * does not exist.
 */
class Field_Not_Exist_Error extends \Exception
{
    /**
     * @param Field $field Field instance.
     */
    public function __construct( Field $field )
    {
        $message  = 'Field "' . $field->get_key() . '" does not exist!';

        parent::__construct( $message );
    }
}