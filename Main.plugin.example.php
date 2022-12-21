<?php
namespace YOUR_NAMESPACE;

// Todo (new project): uncomment this if you're building a plugin
// use \YOUR_NAMESPACE\framework\inc\package\aPlugin_Package;

// Todo (new project): uncomment this if you're building a theme
// use \YOUR_NAMESPACE\framework\inc\package\aTheme_Package;
// use \YOUR_NAMESPACE\framework\inc\wp\WP;

if ( ! defined( 'ABSPATH' ) ) exit;

require_once __DIR__ . '/framework/autoload.php';

// Todo (new project): uncomment this if you're building a plugin
// class Main extends aTheme_Plugin
// Todo (new project): uncomment this if you're building a theme
// class Main extends aTheme_Package
{
    protected function run() : void
    {
        // Todo (new project): uncomment this if you're building a theme
        // WP::hooks()->listen( 'wp', [ $this, '_enqueue_plain_css' ] );
    }

    // Todo (new project): uncomment this if you're building a theme
    // public function _enqueue_plain_css() : void
    // {
    //     $this->assets()->enqueue_css( Main::prefix( 'plain_global' ), get_stylesheet_uri() );
    // }
}

Main::get_instance();