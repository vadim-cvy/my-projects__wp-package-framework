<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\acf\field;

use \Exception;

use \YOUR_NAMESPACE\framework\inc\thirdparty\acf\tACF_Object_Has_Context;

use \YOUR_NAMESPACE\framework\inc\thirdparty\acf\group\Group;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Representation of ACF Field.
 */
class Field
{
    use tACF_Object_Has_Context;

    /**
     * Field key
     *
     * @var string
     */
    protected $key = '';

    static public function get_by_id( int $field_id, string $context = '' ) : Field
    {
        $field_post = get_post( $field_id );

        if ( empty( $field_post ) )
        {
            throw new Exception( 'Field does not exist (id:' . $field_id . ')!' );
        }

        $field_key = $field_post->post_name;

        $field = new static( $field_key, $context );

        return $field;
    }

    public function __construct( string $field_key, string $context = '' )
    {
        $this->key = $field_key;

        if ( $context )
        {
            $this->set_context( $context );
        }
    }

    /**
     * Field key getter.
     *
     * @return string Field key.
     */
    final public function get_key() : string
    {
        return $this->key;
    }

    public function get_label( bool $include_parent_fields = false, bool $include_parent_group = false ) : string
    {
        $label = '';

        foreach ( array_reverse( $this->get_parents() ) as $i => $parent )
        {

            if ( $i === 0 )
            {
                if ( ! $include_parent_group )
                {
                    continue;
                }
            }
            else
            {
                if ( ! $include_parent_fields )
                {
                    continue;
                }
            }

            $label .= $parent->get_label() . ' > ';
        }

        $label .= $this->get_original()['label'];

        return $label;
    }

    /**
     * Checks if field exists.
     *
     * @return boolean True if field exists, false otherwise.
     */
    public function exists() : bool
    {
        try
        {
            $this->validate_exists();
        }
        catch ( Field_Not_Exist_Error $error )
        {
            return false;
        }

        return true;
    }

    /**
     * Checks and throws error if field does not exist.
     *
     * @return void
     */
    public function validate_exists()
    {
        $this->get_original();
    }

    /**
     * Wrapper for get_field_object().
     *
     * @return array<string,mixed> Field object.
     */
    public function get_original() : array
    {
        $context = $this->get_context( false );

        $field_object = get_field_object( $this->get_key(), $context );

        if ( empty( $field_object ) )
        {
            throw new Field_Not_Exist_Error( $this );
        }

        return $field_object;
    }

    /**
     * Returns field parent.
     *
     * @return Group|Field Field parent (group or field instance).
     */
    public function get_parent()
    {
        $parent_id = $this->get_original()['parent'];

        if ( is_string( $parent_id ) && strpos( $parent_id, 'group_' ) === 0 )
        {
            $parent = new Group( $parent_id, $this->get_context( false ) );
        }
        else
        {
            try
            {
                $parent = static::get_by_id( $parent_id );

                $parent->validate_exists();
            }
            catch ( Field_Not_Exist_Error $error )
            {
                $parent = Group::get_by_id( $parent_id );
            }
        }

        return $parent;
    }

    /**
     * Returns all field parents.
     *
     * @return array<Group|Field> Field parents.
     */
    public function get_parents() : array
    {
        $parents = [];

        $parent = $this->get_parent();

        while ( true )
        {
            $parents[] = $parent;

            if ( ! is_a( $parent, '\\' . self::class ) )
            {

                break;
            }

            $parent = $parent->get_parent();
        }

        return $parents;
    }

    /**
     * Returns field parent group.
     *
     * @return Group Field parent group.
     */
    public function get_group() : Group
    {
        $parents = $this->get_parents();

        return array_pop( $parents );
    }

    /**
     * Returns field type.
     *
     * @return string Field type.
     */
    public function get_type() : string
    {
        return $this->get_original()['type'];
    }

    /**
     * Returns field name.
     *
     * @return string Field name.
     */
    public function get_name() : string
    {
        return $this->get_original()['name'];
    }

    public function get_value( $default_value = null, bool $format_value = true )
    {
        $value_object = new Field_Value( $this );

        return $value_object->get_safe( $format_value, $default_value );
    }

    // todo: implement the same way as get_value() - error must be triggered if context is invalid or field is not registered
    public function update( $value )
    {
        update_field( $this->get_key(), $value, $this->get_context() );
    }

    /**
     * Checks if field has a parent filed.
     *
     * @return boolean True if field has a parent field, false otherwise.
     */
    public function is_subfield()
    {
        return count( $this->get_parents() ) > 1;
    }

    public function add_validation_error( string $error_message ) : void
    {
        acf_add_validation_error( $this->get_input_name(), $error_message );
    }

    protected function get_input_name() : string
    {
        $input_name_tree = $this->get_parents();

        // Remove group. Group key does not appear in field <input> names.
        unset( $input_name_tree[0] );

        $input_name_tree[] = $this;

        $input_name_tree = array_reverse( $input_name_tree );

        $input_name = 'acf';

        foreach ( $input_name_tree as $field )
        {
            $input_name .= '[' . $field->get_key() . ']';
        }

        return $input_name;
    }

    public function has_rows() : bool
    {
        return have_rows( $this->get_key(), $this->get_context() );
    }

    public function has_children() : bool
    {
        if ( ! $this->has_rows() )
        {
            return false;
        }

        // Todo: complete. List all types that return true for $this->has_rows() but do not have child fields in fact
        $ignore_types = [
            'checkbox',
            'radio',
            'text',
        ];

        if ( in_array( $this->get_type(), $ignore_types ) )
        {
            return false;
        }

        return true;
    }

    public function get_direct_children() : array
    {
        $direct_children = [];

        if ( $this->has_children() )
        {
            while ( $this->has_rows() )
            {
                the_row();

                $sub_fields = get_row();

                foreach ( array_keys( $sub_fields ) as $field_key )
                {
                    $direct_children[] = new static( $field_key, $this->get_context( false ) );
                }
            }
        }

        return $direct_children;
    }

    public function get_all_children() : array
    {
        $all_children = [];

        foreach ( $this->get_direct_children() as $child_field )
        {
            $all_children[] = $child_field;

            $all_children = array_merge(
                $all_children,
                $child_field->get_all_children()
            );
        }

        return $all_children;
    }

    public function get_end_children() : array
    {
        return array_filter( $this->get_all_children(), function( Field $child_field )
        {
            return ! $child_field->has_children();
        });
    }

    public function has_validation_errors() : bool
    {
        return acf_validate_value( $this->get_value(), $this->get_original(), '' );
    }

    public function get_db_name() : string
    {
        global $wpdb;

        $sql = $wpdb->prepare(
            "SELECT
                `meta_key`
            FROM
                `$wpdb->postmeta`
            WHERE
                `meta_value` = %s",
            [
                $this->get_key(),
            ]
        );

        $prefixed_name = $wpdb->get_var( $sql );

        $unprefixed_name = preg_replace( '~^_~', '', $prefixed_name );

        return $unprefixed_name;
    }
}