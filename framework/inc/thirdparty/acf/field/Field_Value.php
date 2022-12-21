<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\acf\field;

use \Exception;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

if ( ! defined( 'ABSPATH' ) ) exit;

class Field_Value
{
    protected $field;

    public function __construct( Field $field )
    {
        $this->field = $field;
    }

    // Todo: refactor this giant method
    // todo: implement default value
    public function get_safe( bool $format_value = true, $default_value = null )
    {
        $decode_post_id_hook_name     = 'acf/decode_post_id';
        $decode_post_id_hook_callback = [ $this, '_validate_decoded_post_id' ];

        $unexisting_hook_name     = 'acf/get_invalid_field_value';
        $unexisting_hook_callback = [ $this, '_throw_unexisting_field_error' ];

        $hooks_order = 9999;

        /**
         * Add validation hooks: start
         */
        add_filter( $decode_post_id_hook_name, $decode_post_id_hook_callback, $hooks_order );

        add_action( $unexisting_hook_name, $unexisting_hook_callback, $hooks_order );
        /**
         * Add validation hooks: end
         */

        /**
         * Get value: start
         */
        if ( $this->field->is_subfield() )
        {
            $parent_value = $this->field->get_parent()->get_value( $format_value );

            /**
             * Sometimes the parent output depends on the $format_value.
             * If $format_value = false than parent value will probably contain
             * field keys instead of field names.
             */
            return isset( $parent_value[ $this->field->get_name() ] ) ?
                $parent_value[ $this->field->get_name() ] :
                $parent_value[ $this->field->get_key() ];
        }

        $value = get_field( $this->field->get_key(), $this->field->get_context(), $format_value );

        if ( is_string( $value ) )
        {
            $value = Strings::cast( $value );
        }

        /**
         * Get value: end
         */

        /**
         * Remove validation hooks: start
         */
        remove_filter( $decode_post_id_hook_name, $decode_post_id_hook_callback, $hooks_order );

        remove_action( $unexisting_hook_name, $unexisting_hook_callback, $hooks_order );
        /**
         * Remove validation hooks: end
         */

        return $value;
    }

    public function _validate_decoded_post_id( $decoded_post_id ) : array
    {
        if ( $decoded_post_id['type'] === 'option' )
        {
            if ( $decoded_post_id['id'] !== 'option' && $decoded_post_id['id'] !== 'options' )
            {
                throw new Exception(
                    'Specified ACF field context "' . $decoded_post_id['id'] . '" does not exist!'
                );
            }
        }

        return $decoded_post_id;
    }

    public function _throw_unexisting_field_error( array $field ) : void
    {
        throw new \Exception(
            'Can\'t find the ACF field (selector: "' . $field['name'] . '")! ' .
            'Possible reasons are: ' .
            '1) There is a misspell in the field name/key. ' .
            '2) The wrong context is specified or it is not specified at all. ' .
            '3) The field is a sub-field and can not be accessed dirrectly.'
        );
    }
}