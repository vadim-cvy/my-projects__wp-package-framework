<?php

namespace YOUR_NAMESPACE\framework\inc\shortcodes;

use \Exception;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\framework\inc\design_patterns\tSingleton;

use \YOUR_NAMESPACE\framework\inc\package\tHas_Package_Based_Slug;

use \YOUR_NAMESPACE\Main;

use \YOUR_NAMESPACE\framework\inc\thirdparty\bb\Beaver_Builder;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aShortcode
{
    use tSingleton,
        tHas_Package_Based_Slug;

    protected function __construct()
    {
        if (
            Beaver_Builder::is_editor_screen() ||
            ( is_admin() && ! wp_doing_ajax() )
        )
        {
            return;
        }

        WP::hooks()->listen( 'init', [ $this, '_register' ] );
    }

    public function _register() : void
    {
        add_shortcode( $this->get_slug(), [ $this, 'get_content' ] );
    }

    public function get_content( $attributes, string $content = '' ) : string
    {
        if ( empty( $attributes ) )
        {
            $attributes = [];
        }

        ob_start();

        $errors = $this->get_attributes_validation_errors( $attributes );

        if ( ! empty( $errors ) )
        {
            $this->render_errors( $errors );
        }
        else
        {
            if ( ! empty( $attributes ) )
            {
                $attributes = $this->prepare_attributes( $attributes );
            }

            $this->render( $attributes, $content );
        }

        $content = ob_get_contents();

        ob_end_clean();

        return $content;
    }

    protected function render_errors( array $error_messages ) : void
    {
        foreach ( $error_messages as $error_message )
        {
            trigger_error( $error_message, E_USER_NOTICE );
        }

        $template =
            Main::fs()->get_framework_view_dir()->get_file( 'front-end/blocks/shortcode_errors.php' );

        $template->require([
            'shortcode_slug' => $this->get_slug(),
            'error_messages' => $error_messages,
        ]);
    }

    protected function get_attributes_validation_errors( array $attributes ) : array
    {
        $errors = [];

        $attr_names = array_keys( $attributes );

        $unexpected_attrs = array_diff( $attr_names, $this->get_allowed_attributes() );

        if ( ! empty( $unexpected_attrs ) )
        {
            $errors[] =
                'Unexpected attributes: "' . implode( '", "', $unexpected_attrs ) . '"! ' .
                'Expected attributes are: "' . implode( '", "', $this->get_allowed_attributes() ) . '".';
        }

        $missed_attrs = array_diff( $this->get_required_attributes(), $attr_names );

        if ( ! empty( $missed_attrs ) )
        {
            $errors[] = 'Required attributes are missed: "' . implode( '", "', $missed_attrs ) . '"!';
        }

        return $errors;
    }

    abstract static protected function get_allowed_attributes() : array;

    abstract static protected function get_required_attributes() : array;

    protected function prepare_attributes( array $attributes ) : array
    {
        throw new Exception(
            'Class ' . get_class( $this ) . ' must override parent method ' . __METHOD__ . '!'
        );
    }

    abstract protected function render( array $attributes, string $content ) : void;

    /**
     * Checks if current post contains this shortcode.
     *
     * @return boolean True if curent post contains this shortcode, false otherwise.
     */
    static public function appears_in_current_post() : bool
    {
        if ( ! is_singular() )
        {
            return false;
        }

        return static::appears_in_post( get_the_ID() );
    }

    /**
     * Checks if post contains this shortcode.
     *
     * @return boolean True if post contains this shortcode, false otherwise.
     */
    static public function appears_in_post( int $post_id ) : bool
    {
        $post_content = get_post_field( 'post_content', $post_id );

        return has_shortcode( $post_content, static::get_slug() );
    }
}