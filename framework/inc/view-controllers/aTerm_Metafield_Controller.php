<?php
namespace YOUR_NAMESPACE\framework\inc\view_controllers;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\Main;

use \YOUR_NAMESPACE\inc\helpers\Assets;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aTerm_Metafield_Controller extends aMetabox_Controller
{
    protected function on_init() : void
    {
        parent::on_init();

        foreach ( $this->get_screens() as $tax_slug )
        {
            WP::hooks()->listen( $tax_slug . '_edit_form_fields', [ $this, 'render' ] );

            WP::hooks()->listen( 'edit_term', [ $this, '_on_term_save' ] );
        }
    }

    protected function is_screen_match() : bool
    {
        $screen = get_current_screen();

        $is_base_match =
            $screen->base === 'term' ||
            (
                $screen->base === 'edit-tags' &&
                ! empty( $_POST )
            );

        return $is_base_match && in_array( $screen->taxonomy, $this->get_screens() );
    }

    public function _on_term_save( int $term_id ) : void
    {
        if (
            ! empty( $_POST ) &&
            ! empty( $_POST['tag_ID'] ) &&
            (int) $_POST['tag_ID'] === $term_id
        )
        {
            $this->maybe_handle_submission();
        }
    }

    public function get_content() : string
    {
        return Main::fs()
            ->get_framework_view_dir()
            ->get_file( 'dashboard/blocks/term-metafield/templates/main.php' )
            ->get_content([
                'title' => $this->get_title(),
                'inner_content' => parent::get_content(),
                'slug' => $this->get_name(),
            ]);
    }

    public function get_predefined_vars() : array
    {
        return [
            'slug' => $this->get_name(),
        ];
    }

    public function enqueue_assets() : void
    {
        Assets::enqueue_predefined_js( 'vue' );

        parent::enqueue_assets();

        $this->localize_js_data( 'main', [ $this, '_get_js_data' ] );
    }

    public function _get_js_data() : array
    {
        return [
            'value' => $this->get_value(),
        ];
    }

    protected function get_sub_block_names() : array
    {
        return [
            'field_wrapper',
        ];
    }

    abstract protected function get_value();

    protected function get_submitted_data() : array
    {
        return [
            // todo: refactor and make terms templates more standartized as well. This is a quick hack
            stripslashes( $_POST[ $this->get_name() ] ),
        ];
    }
}