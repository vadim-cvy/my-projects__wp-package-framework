<?php
namespace YOUR_NAMESPACE;

use \YOUR_NAMESPACE\framework\inc\package\aTheme_Package;
use \YOUR_NAMESPACE\framework\inc\wp\WP;

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/framework/autoload.php';

class Main extends aTheme_Package
{
    protected function run() : void
    {
        WP::hooks()->listen( 'wp', [ $this, '_enqueue_plain_css' ] );
    }

    public function _enqueue_plain_css() : void
    {
        $this->assets()->enqueue_css( Main::prefix( 'plain_global' ), get_stylesheet_uri() );
    }
}

Main::get_instance();