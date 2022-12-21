<?php

namespace YOUR_NAMESPACE\framework\inc\dashboard;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\framework\inc\filesystem\Directory;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

class Dashboard
{
    /**
     * Adds error dashboard notice.
     *
     * @param string $message Error message.
     * @return void
     */
    static public function add_error( string $message ) : void
    {
        static::add_notice( 'error', $message );
    }

    /**
     * Adds dashboard notice.
     *
     * @param string $type      Notice type (error, info, etc).
     * @param string $message   Notice message.
     * @return void
     */
    static protected function add_notice( string $type, string $message ) : void
    {
        $template_file = static::get_templates_dir()->get_file( 'blocks/notice/templates/main.php' );

        $template_args = compact( 'type', 'message' );

        WP::hooks()->listen( 'admin_notices', function() use ( $type, $message, $template_file, $template_args ) : void
        {
            $template_file->require( $template_args );
        });
    }

    static protected function get_templates_dir() : Directory
    {
        return Main::fs()->get_framework_view_dir()->get_sub_dir( 'dashboard/templates' );
    }
}