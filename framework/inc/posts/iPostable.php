<?php
namespace YOUR_NAMESPACE\framework\inc\posts;

use \YOUR_NAMESPACE\framework\inc\meta\iHas_Meta;
use \YOUR_NAMESPACE\framework\inc\meta\iHas_ACF;

if ( ! defined( 'ABSPATH' ) ) exit;

interface iPostable extends iHas_Meta, iHas_ACF
{
    public function get_id() : int;

    public function get_slug() : string;

    public function exists() : bool;

    public function update_common_meta( string $key, $value ) : void;

    public function get_common_meta_raw( string $key );

    public function delete_common_meta( string $key ) : void;

    public function get_edit_url() : string;

    public function get_url() : string;

    public function update( array $data ) : bool;

    public function delete( bool $force = false ) : void;
}