<?php

namespace YOUR_NAMESPACE\framework\inc\meta;

use \YOUR_NAMESPACE\framework\inc\thirdparty\acf\field\Field;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tHas_ACF_Wrapper
{
    public function get_acf_field_instance( string $field_selector ) : Field
    {
        return $this->get_wrapped_has_acf()->get_acf_field_instance( $field_selector );
    }

    public function update_common_acf( string $field_selector, $value ) : void
    {
        $this->get_wrapped_has_acf()->update_common_acf( $field_selector, $value );
    }

    public function get_common_acf( string $field_selector, $default_value = null )
    {
        return $this->get_wrapped_has_acf()->get_common_acf( $field_selector, $default_value );
    }

    public function update_package_acf( string $field_name, $value ) : void
    {
        $this->get_wrapped_has_acf()->update_package_acf( $field_name, $value );
    }

    public function get_package_acf( string $field_name, $default_value = null )
    {
        return $this->get_wrapped_has_acf()->get_package_acf( $field_name, $default_value );
    }

    public function delete_package_acf( string $field_name ) : void
    {
        $this->get_wrapped_has_acf()->delete_package_acf( $field_name );
    }
}