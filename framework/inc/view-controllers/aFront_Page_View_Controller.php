<?php

namespace YOUR_NAMESPACE\framework\inc\view_controllers;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\framework\inc\site_pages\aSite_Page;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aFront_Page_View_Controller extends aPage_View_Controller
{
    protected function __construct()
    {
        parent::__construct();
    }

    protected function on_is_current() : void
    {
        global $your_namespace_page_template;

        parent::on_is_current();

        $your_namespace_page_template = $this;

        /**
         * Implicit theme template override works for plugin packages only.
         *
         * Follow WP templates standard files architecture if you're worknig on the
         * theme package.
         * Use $your_namespace_page_template->render() in standard template files to render
         * `view/front-end/pages/{YOUR-PAGE-NAME}/templates/main.php`.
         */
        if ( $this->get_template_file( false )->exists() && ! Main::get_type() === 'theme' )
        {
            WP::hooks()->listen( 'template_include', [ $this, '_override_theme_template_path' ] );
        }
    }

    public function _override_theme_template_path() : string
    {
        return Main::fs()
            ->get_framework_view_dir()
            ->get_file( 'front-end/pages/page/templates/main.php' )
            ->get_path();
    }
}