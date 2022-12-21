<?php
namespace YOUR_NAMESPACE\framework\inc\view_controllers;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\Main;

use \YOUR_NAMESPACE\framework\inc\data_types\Arrays;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aMetabox_Controller extends aView_Controller
{
    final protected function __construct()
    {
        if ( ! is_admin() )
        {
            return;
        }

        parent::__construct();

        WP::hooks()->listen( 'current_screen', [ $this, '_on_init' ] );
    }

    public function _on_init() : void
    {
        if ( $this->is_screen_match() && $this->is_visible() )
        {
            $this->on_init();
        }
    }

    protected function on_init() : void
    {
        if ( ! session_id() )
        {
            session_start();
        }

        $this->enqueue_assets();
    }

    protected function is_visible() : bool
    {
        return true;
    }

    abstract protected function get_screens() : array;

    abstract protected function get_title() : string;

    abstract protected function is_screen_match() : bool;

    protected function maybe_handle_submission() : void
    {
        static $is_processing = false;

        if ( $is_processing )
        {
            return;
        }

        if ( $this->is_submitted() )
        {
            $is_processing = true;

            $this->handle_submission();

            $is_processing = false;
        }
    }

    abstract protected function handle_submission() : void;

    protected function is_submitted() : bool
    {
        return ! empty( $_POST ) &&
            ! empty( $_POST[ Main::prefix( $this->get_name() ) ] ) &&
            ! empty( $_POST[ Main::prefix( $this->get_name() ) ]['is_submitted'] );
    }

    protected function get_submitted_data() : array
    {
        $data_json = stripslashes( $_POST[ Main::prefix( $this->get_name() ) ]['data'] );

        $data = json_decode( $data_json, true );

        return Arrays::switch_keys_case( $data, 'cammel', 'snake' );
    }
}