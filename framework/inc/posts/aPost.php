<?php

namespace YOUR_NAMESPACE\framework\inc\posts;

use \Throwable;
use \Exception;
use \WP_Post;
use \DateTimeImmutable;

use \YOUR_NAMESPACE\framework\inc\meta\tHas_Meta;
use \YOUR_NAMESPACE\framework\inc\meta\tHas_ACF;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aPost implements iPostable
{
    use tHas_Meta,
        tHas_ACF;

    protected $id;

    public function __construct( int $id )
    {
        $this->id = $id;
    }

    final public function get_id() : int
    {
        return $this->id;
    }

    public function get_title() : string
    {
        return $this->get_original()->post_title;
    }

    public function get_content() : string
    {
        return $this->get_original()->post_content;
    }

    public function get_excerpt( int $length = -1, string $more_text = null ) : string
    {
        $excerpt_more_callback = function( $original_more_text ) use ( $more_text )
        {
            return isset( $more_text ) ? $more_text : $original_more_text;
        };

        $excerpt_length_callback = function( $original_length ) use ( $length )
        {
            return $length !== -1 ? $length : $original_length;
        };

        add_filter( 'excerpt_more', $excerpt_more_callback );
        add_filter( 'excerpt_length', $excerpt_length_callback );

        $excerpt = get_the_excerpt( $this->get_id() );

        remove_filter( 'excerpt_more', $excerpt_more_callback );
        remove_filter( 'excerpt_length', $excerpt_length_callback );

        return $excerpt;
    }

    public function get_featured_image_id() : int
    {
        $id = get_post_thumbnail_id( $this->get_id() );

        return $id ? $id : 0;
    }

    public function get_featured_image_url( $size = null ) : string
    {
        $featured_image_id = $this->get_featured_image_id();

        return $featured_image_id ? wp_get_attachment_url( $featured_image_id, $size ) : '';
    }

    public function get_url() : string
    {
        return get_permalink( $this->get_id() );
    }

    public function get_slug() : string
    {
        // todo: implement
    }

    public function exists() : bool
    {
        try
        {
            $this->get_original();
        }
        catch ( Post_Not_Exist_Error $err )
        {
            return false;
        }

        return $this->get_post_type() === $this->get_expected_post_type();
    }

    public function get_original() : WP_Post
    {
        $post = get_post( $this->get_id() );

        if ( ! $post )
        {
            throw new Post_Not_Exist_Error( 'Post with ID "' . $this->get_id() . '" does not exist.' );
        }

        return $post;
    }

    public function get_post_type() : string
    {
        return get_post_type( $this->get_id() );
    }

    abstract protected function get_expected_post_type() : string;

    public function set_terms( array $term_ids, string $taxonomy_slug, bool $append = false ) : void
    {
        wp_set_post_terms( $this->get_id(), $term_ids, $taxonomy_slug, $append );
    }

    public function get_terms( string $taxonomy_slug ) : array
    {
        return wp_get_post_terms( $this->get_id(), $taxonomy_slug );
    }

    public function remove_terms( array $term_ids, string $taxonomy_slug ) : void
    {
        wp_remove_object_terms( $this->get_id(), $term_ids, $taxonomy_slug );
    }

    public function get_datetime( string $field = null ) : DateTimeImmutable
    {
        return get_post_datetime( $this->get_id(), $field );
    }

    public function get_timestamp( string $field = 'date' )
    {
        return get_post_timestamp( $this->get_id(), $field );
    }

    public function update_common_meta( string $key, $value ) : void
    {
        update_post_meta( $this->get_id(), $key, $value );
    }

    public function get_common_meta_raw( string $key )
    {
        return get_post_meta( $this->get_id(), $key, true );
    }

    public function delete_common_meta( string $key ) : void
    {
        delete_post_meta( $this->get_id(), $key );
    }

    public function get_edit_url() : string
    {
        return get_edit_post_link( $this->get_id(), '' );
    }

    public function update( array $data ) : bool
    {
        $data['ID'] = $this->get_id();

        $result = wp_update_post( $data );

        return ! is_wp_error( $result );
    }

    public function update_status( string $status ) : void
    {
        $this->update([
            'post_status' => $status,
        ]);
    }

    public function get_status() : string
    {
        return get_post_status( $this->get_id() );
    }

    public function get_status_label() : string
    {
        global $wp_post_statuses;

        return $wp_post_statuses[ $this->get_status() ]->label;
    }

    public function delete( bool $force = false ) : void
    {
        wp_delete_post( $this->get_id(), $force );
    }

    public function has_shortcode( string $shortcode_name ) : bool
    {
        return has_shortcode( $this->get_original()->post_content, $shortcode_name );
    }
}