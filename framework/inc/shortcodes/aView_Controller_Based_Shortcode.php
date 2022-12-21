<?php

namespace YOUR_NAMESPACE\framework\inc\shortcodes;

use \Exception;
use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\framework\inc\view_controllers\aView_Controller;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aView_Controller_Based_Shortcode extends aShortcode
{
    protected $are_assets_enqueued = false;

    protected function __construct()
    {
        WP::hooks()->listen( 'wp', [ $this, '_maybe_enqueue_assets' ] );

        parent::__construct();
    }

    static protected function get_slug_base() : string
    {
        return static::get_view_controller()->get_name();
    }

    public function _maybe_enqueue_assets() : void
    {
        if ( $this->appears_in_current_post() )
        {
            $this->enqueue_assets();
        }
    }

    public function enqueue_assets() : void
    {
        $this->get_view_controller()->enqueue_assets();

        $this->are_assets_enqueued = true;
    }

    protected function render( array $attributes, string $content ) : void
    {
        if ( ! $this->are_assets_enqueued )
        {
            throw new Exception(
                'Assets are not enqueued! ' .
                'Seems like you\'re using shortcode outside of the main query post content. ' .
                'If so than you must call ' . static::class . '::enqueue_assets() manually.'
            );
        }

        if ( ! empty( $content ) )
        {
            $attributes['content'] = $content;
        }

        $this->get_view_controller()->render( $attributes );
    }

    abstract static protected function get_view_controller() : aView_Controller;
}