<?php

namespace YOUR_NAMESPACE\framework\inc\taxonomies;

use \Throwable;

use \WP_Term;

use \YOUR_NAMESPACE\framework\inc\meta\tHas_Meta_Wrapper;
use \YOUR_NAMESPACE\framework\inc\meta\tHas_ACF_Wrapper;

if ( ! defined( 'ABSPATH' ) ) exit;

trait tTerm_Wrapper
{
    use tHas_Meta_Wrapper,
        tHas_ACF_Wrapper;

    abstract protected function get_wrapped_term() : Term;

    protected function get_wrapped_has_acf()
    {
        return $this->get_wrapped_term();
    }

    protected function get_wrapped_has_meta()
    {
        return $this->get_wrapped_term();
    }

    public function get_id() : int
    {
        return $this->get_wrapped_term()->get_id();
    }

    public function get_label() : string
    {
        return $this->get_wrapped_term()->get_label();
    }

    public function get_description() : string
    {
        return $this->get_wrapped_term()->get_description();
    }

    public function get_slug() : string
    {
        return $this->get_wrapped_term()->get_slug();
    }

    public function get_original() : WP_Term
    {
        return $this->get_wrapped_term()->get_original();
    }

    public function exists() : bool
    {
        return $this->get_wrapped_term()->exists();
    }

    public function update_common_meta( string $key, $value ) : void
    {
        $this->get_wrapped_term()->update_common_meta( $key, $value );
    }

    public function get_common_meta_raw( string $key )
    {
        return $this->get_wrapped_term()->get_common_meta_raw( $key );
    }

    public function delete_common_meta( string $key ) : void
    {
        $this->get_wrapped_term()->delete_common_meta( $key );
    }

    public function get_edit_url() : string
    {
        return $this->get_wrapped_term()->get_edit_url();
    }
}