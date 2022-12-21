<?php

namespace YOUR_NAMESPACE\framework\inc\meta;

use \YOUR_NAMESPACE\framework\inc\thirdparty\acf\field\Field;

if ( ! defined( 'ABSPATH' ) ) exit;

interface iHas_ACF
{
    public function get_acf_field_instance( string $field_selector ) : Field;

    public function update_common_acf( string $field_selector, $value ) : void;

    public function get_common_acf( string $field_selector, $default_value = null );

    // todo: implement
    // public function delete_common_acf( string $field_selector ) : void;

    public function update_package_acf( string $field_name, $value ) : void;

    public function get_package_acf( string $field_name, $default_value = null );

    public function delete_package_acf( string $field_name ) : void;
}