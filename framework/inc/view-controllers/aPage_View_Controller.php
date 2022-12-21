<?php

namespace YOUR_NAMESPACE\framework\inc\view_controllers;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\framework\inc\site_pages\aSite_Page;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aPage_View_Controller extends aView_Controller
{
    protected function __construct()
    {
		$this->get_page_instance()->hooks()->listen( 'is_current', [ $this, '_on_is_current' ] );
    }

    public function _on_is_current( string $end ) : void
    {
        if (
            ( $this->get_end() === 'front_end' && ! is_admin() ) ||
            ( $this->get_end() === 'dashboard' && is_admin() )
        )
        {
            $this->on_is_current();
        }
    }

    protected function on_is_current() : void
    {
        $this->enqueue_assets();
    }

    abstract static public function get_page_instance() : aSite_Page;
}