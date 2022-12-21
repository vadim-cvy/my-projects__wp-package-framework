<?php
namespace YOUR_NAMESPACE\framework\inc\view_controllers;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aPost_Metabox_Controller extends aMetabox_Controller
{
    protected function on_init() : void
    {
        parent::on_init();

        if ( ! is_admin() )
        {
            return;
        }

        WP::hooks()->listen( 'add_meta_boxes', [ $this, '_register' ] );

        WP::hooks()->listen( 'save_post', [ $this, '_on_post_save' ] );
    }

    protected function get_priority() : string
    {
        return 'default';
    }

    protected function get_context() : string
    {
        return 'advanced';
    }

    protected function is_screen_match() : bool
    {
        $screen = get_current_screen();

        return $screen->base === 'post' && in_array( $screen->post_type, $this->get_screens() );
    }

    public function _register() : void
    {
        add_meta_box(
            $this->get_name(),
            $this->get_title(),
            [ $this, 'render' ],
            $this->get_screens(),
            $this->get_context(),
            $this->get_priority()
        );
    }

    public function _on_post_save( int $post_id ) : void
    {
        if (
            ! empty( $_POST ) &&
            ! empty( $_POST['post_ID'] ) &&
            (int) $_POST['post_ID'] === $post_id
        )
        {
            $this->maybe_handle_submission();
        }
    }

    public function get_content() : string
    {
        return Main::fs()
            ->get_framework_view_dir()
            ->get_file( 'dashboard/blocks/post-metabox/templates/main.php' )
            ->get_content([
                'inner_content' => parent::get_content(),
                'slug' => $this->get_name(),
            ]);
    }
}