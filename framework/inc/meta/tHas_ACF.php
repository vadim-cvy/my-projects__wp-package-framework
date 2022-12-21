<?php

namespace YOUR_NAMESPACE\framework\inc\meta;

use \YOUR_NAMESPACE\framework\inc\thirdparty\acf\field\Field;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tHas_ACF
{
    public function get_acf_field_instance( string $field_selector ) : Field
    {
        return new Field( $field_selector, $this->get_id() );
    }

    public function update_common_acf( string $field_selector, $value ) : void
    {
        $this->get_acf_field_instance( $field_selector )->update( $value );
    }

    public function get_common_acf( string $field_selector, $default_value = null )
    {
        return $this->get_acf_field_instance( $field_selector, $default_value )->get_value( $default_value );
    }

    public function update_package_acf( string $field_name, $value ) : void
    {
        $this->update_common_acf( Main::prefix( $field_name ), $value );
    }

    public function get_package_acf( string $field_name, $default_value = null )
    {
        return $this->get_common_acf( Main::prefix( $field_name ), $default_value );
    }

    public function delete_package_acf( string $field_name ) : void
    {
        $this->delete_common_acf( Main::prefix( $field_name ) );
    }
}