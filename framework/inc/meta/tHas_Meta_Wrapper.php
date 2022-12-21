<?php

namespace YOUR_NAMESPACE\framework\inc\meta;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tHas_Meta_Wrapper
{
    public function update_common_meta( string $key, $value ) : void
    {
        $this->get_wrapped_has_meta()->update_common_meta( $key, $value );
    }

    public function get_common_meta( string $key, $default_value = null )
    {
        return $this->get_wrapped_has_meta()->get_common_meta( $key, $default_value );
    }

    public function delete_common_meta( string $key ) : void
    {
        $this->get_wrapped_has_meta()->delete_common_meta( $key );
    }

    public function update_package_meta( string $key, $value ) : void
    {
        $this->get_wrapped_has_meta()->update_package_meta( $key, $value );
    }

    public function get_package_meta( string $key, $default_value = null )
    {
        return $this->get_wrapped_has_meta()->get_package_meta( $key, $default_value );
    }

    public function delete_package_meta( string $key ) : void
    {
        $this->get_wrapped_has_meta()->delete_package_meta( $key );
    }
}