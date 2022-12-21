<?php

namespace YOUR_NAMESPACE\framework\inc\meta;

if ( ! defined( 'ABSPATH' ) ) exit;

interface iHas_Meta
{
    public function update_common_meta( string $key, $value ) : void;

    public function get_common_meta( string $key, $default_value = null );

    public function delete_common_meta( string $key ) : void;

    public function update_package_meta( string $key, $value ) : void;

    public function get_package_meta( string $key, $default_value = null );

    public function delete_package_meta( string $key ) : void;
}