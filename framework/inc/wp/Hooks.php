<?php

namespace YOUR_NAMESPACE\framework\inc\wp;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class Hooks
{
    protected $hooks_prefix;

    public function __construct( $hooks_prefix = '' )
    {
        $this->hooks_prefix = $hooks_prefix;
    }

    public function create_child( string $child_prefix_part ) : Hooks
    {
        return new static( $this->hooks_prefix . $child_prefix_part . '/' );
    }

    public function do_action( string $name, ...$args ) : void
    {
        $args = array_merge(
            [
                $this->prefix_hook_name( $name ),
            ],
            $args
        );

        call_user_func_array( 'do_action', $args );
    }

    public function apply_filters( string $name, $value, ...$args )
    {
        $args = array_merge(
            [
                $this->prefix_hook_name( $name ),
                $value,
            ],
            $args
        );

        return call_user_func_array( 'apply_filters', $args );
    }

    public function listen( string $name, callable $callback, int $order = 10 ) : void
    {
        $this->validate_did( $name, $order );

        add_filter( $this->prefix_hook_name( $name ), $callback, $order, 99 );
    }

    protected function validate_did( string $name, int $order = null ) : void
    {
        if ( $this->did( $name, $order ) )
        {
            throw new Exception( '"' . $this->prefix_hook_name( $name ) . '" hook has already fired!' );
        }
    }

    public function did( string $name, int $order = null ) : bool
    {
        global $wp_actions;

        $name = $this->prefix_hook_name( $name );

        if ( ! did_action( $name ) )
        {
            return false;
        }

        if ( isset( $order ) )
        {
            return $wp_actions[ $name ] > $order;
        }

        return false;
    }

    protected function prefix_hook_name( string $name ) : string
    {
        return Strings::maybe_prefix_string( $name, $this->hooks_prefix );
    }
}