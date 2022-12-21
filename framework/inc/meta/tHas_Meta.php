<?php

namespace YOUR_NAMESPACE\framework\inc\meta;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tHas_Meta
{
    abstract public function update_common_meta( string $key, $value ) : void;

    abstract protected function get_common_meta_raw( string $key );

    public function get_common_meta( string $key, $default_value = null )
    {
        $value = $this->get_common_meta_raw( $key );

        if ( ( $value === '' || $value === false ) )
        {
            $value = isset( $default_value ) ? $default_value : null;
        }

        return $this->normalize_meta_value( $key, $value );
    }

    protected function normalize_meta_value( string $key, $value )
    {
        if ( is_string( $value ) )
        {
            $value = Strings::cast( $value );
        }

        return $value;
    }

    abstract public function delete_common_meta( string $key ) : void;

    public function update_package_meta( string $key, $value ) : void
    {
        $this->update_common_meta( Main::prefix( $key ), $value );
    }

    public function get_package_meta( string $key, $default_value = null )
    {
        return $this->get_common_meta( Main::prefix( $key ), $default_value );
    }

    public function delete_package_meta( string $key ) : void
    {
        $this->delete_common_meta( Main::prefix( $key ) );
    }
}