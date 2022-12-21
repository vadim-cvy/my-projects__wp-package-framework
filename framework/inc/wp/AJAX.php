<?php

namespace YOUR_NAMESPACE\framework\inc\wp;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

use \YOUR_NAMESPACE\framework\inc\data_types\Arrays;

use \YOUR_NAMESPACE\framework\inc\http\Param_Error;

use \YOUR_NAMESPACE\framework\inc\user_input\User_Input_Validation_Error;

if ( ! defined( 'ABSPATH' ) ) exit;

class AJAX
{
    protected $action_name_prefix = '';

    public function __construct( $action_name_prefix = '' )
    {
        $this->action_name_prefix = $action_name_prefix;
    }

    public function create_child( string $child_prefix_part ) : AJAX
    {
        return new static( $this->action_name_prefix . $child_prefix_part . '/' );
    }

    public function add( string $action_name, callable $callback ) : void
    {
        $this->add_priv( $action_name, $callback );

        $this->add_nopriv( $action_name, $callback );
    }

    public function add_priv( string $action_name, callable $callback ) : void
    {
        $action_name = $this->prefix_action_name( $action_name );

        $hook_name = 'wp_ajax_' . $action_name;

        WP::hooks()->listen( $hook_name, $this->wrap_callback( $callback ) );
    }

    public function add_nopriv( string $action_name, callable $callback ) : void
    {
        $action_name = $this->prefix_action_name( $action_name );

        $hook_name = 'wp_ajax_nopriv_' . $action_name;

        WP::hooks()->listen( $hook_name, $this->wrap_callback( $callback ) );
    }

    protected function wrap_callback( callable $callback ) : callable
    {
        return function() use( $callback ) : void
        {
            try
            {
                call_user_func( $callback );
            }
            catch ( Param_Error | User_Input_Validation_Error $error )
            {
                AJAX::send_error( $error->getMessage() );
            }
        };
    }

    static public function send_error( string $message, array $data = [] ) : void
    {
        $data['message'] = $message;

        wp_send_json_error(
            Arrays::switch_keys_case( $data, 'snake', 'cammel' )
        );
    }

    static public function send_success( array $data = [] ) : void
    {
        wp_send_json_success(
            Arrays::switch_keys_case( $data, 'snake', 'cammel' )
        );
    }

    protected function prefix_action_name( string $name ) : string
    {
        return Strings::maybe_prefix_string( $name, $this->action_name_prefix );
    }
}