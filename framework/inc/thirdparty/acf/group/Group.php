<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\acf\group;

use \Exception;

use \YOUR_NAMESPACE\framework\inc\thirdparty\acf\tACF_Object_Has_Context;
use \YOUR_NAMESPACE\framework\inc\thirdparty\acf\field\Field;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Representation of the ACF group.
 */
class Group
{
    use tACF_Object_Has_Context;

    /**
     * Group key.
     *
     * @var string
     */
    protected $key = '';

    /**
     * Returns group by specified group id.
     *
     * @param   integer $group_id   Group id.
     * @return  Group               Group.
     */
    public static function get_by_id( int $group_id, string $context = '' ) : Group
    {
        $group_post = get_post( $group_id );

        if ( empty( $group_post ) )
        {
            throw new Exception( 'Group does not exist (id: ' . $group_id . ')!' );
        }

        $group_key = $group_post->post_name;

        return new static( $group_key, $context );
    }

    /**
     * @param string $group_key Group key.
     */
    public function __construct( string $group_key, string $context = '' )
    {
        $this->key = $group_key;

        if ( $context )
        {
            $this->set_context( $context );
        }
    }

    /**
     * Group key getter.
     *
     * @return string Group key.
     */
    final public function get_key() : string
    {
        return $this->key;
    }

    /**
     * Wrapper for acf_get_field_group().
     *
     * @return array<string,mixed> Group object.
     */
    public function get_original() : array
    {
        return acf_get_field_group( $this->get_key() );
    }

    /**
     * Returns group label(title).
     *
     * @return string Group label(title).
     */
    public function get_label() : string
    {
        return $this->get_original()['title'];
    }

    protected function throw_error( $message )
    {
        $message = 'Group (key: ' . $this->get_key() . '): ' . $message;

        throw new Exception( $message );
    }

    public function get_direct_children() : array
    {
        $direct_children = [];

        $fields_data = acf_get_fields( $this->get_key() );

        foreach ( $fields_data as $field_data )
        {
            $direct_children[] = new Field( $field_data['key'], $this->get_context( false ) );
        }

        return $direct_children;
    }

    public function get_all_children() : array
    {
        $all_children = [];

        foreach ( $this->get_direct_children() as $field )
        {
            $all_children[] = $field;

            if ( $field->has_children() )
            {
                $all_children = array_merge(
                    $all_children,
                    $field->get_all_children()
                );
            }
        }

        return $all_children;
    }

    public function get_end_children() : array
    {
        $end_children = [];

        foreach ( $this->get_all_children() as $field )
        {
            if ( ! $field->has_children() )
            {
                $end_children[] = $field;
            }
        }

        return $end_children;
    }
}