<?php

namespace YOUR_NAMESPACE\framework\inc\taxonomies;

use \Throwable;

use \WP_Term;

use \YOUR_NAMESPACE\framework\inc\meta\tHas_Meta;
use \YOUR_NAMESPACE\framework\inc\meta\tHas_ACF;

if ( ! defined( 'ABSPATH' ) ) exit;

class Term implements iTerm
{
    use tHas_Meta,
        tHas_ACF;

    protected $id = 0;

    public function __construct( int $term_id )
    {
        $this->id = $term_id;
    }

    final public function get_id() : int
    {
        return $this->id;
    }

    public function get_label() : string
    {
        return $this->get_original()->name;
    }

    public function get_description() : string
    {
        return $this->get_original()->description;
    }

    public function get_slug() : string
    {
        return $this->get_original()->slug;
    }

    public function get_original() : WP_Term
    {
        return get_term( $this->get_id() );
    }

    public function exists() : bool
    {
        try
        {
            $this->get_original();

            return true;
        }
        catch ( Throwable $error )
        {
            return false;
        }
    }

    public function update_common_meta( string $key, $value ) : void
    {
        update_term_meta( $this->get_id(), $key, $value );
    }

    public function get_common_meta_raw( string $key )
    {
        return get_term_meta( $this->get_id(), $key, true );
    }

    public function delete_common_meta( string $key ) : void
    {
        delete_term_meta( $this->get_id(), $key );
    }

    public function get_edit_url() : string
    {
        return get_edit_term_link( $this->get_id() );
    }

    public function get_url() : string
    {
        // todo: implement
    }

    public function update( array $data ) : bool
    {
        // todo: implement
    }

    public function delete( bool $force = false ) : void
    {
        // todo: implement
    }
}