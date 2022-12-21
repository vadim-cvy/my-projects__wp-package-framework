<?php

namespace YOUR_NAMESPACE\framework\inc\thirdparty\acf\group;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Incapsulates ACF groups common helpers.
 */
class Groups
{
    /**
     * Returns all registered groups.
     *
     * @return array<Group>
     */
    public static function get_all() : array
    {
        return static::get_by_filters([]);
    }

    /**
     * Returns all groups that belong to specified post type.
     *
     * @param   string $post_type   Post type name.
     * @return  array<Group>        Groups that belong to the post type.
     */
    public static function get_by_post_type( string $post_type ) : array
    {
        return static::get_by_filters([
            'post_type' => $post_type,
        ]);
    }

    /**
     * Returns all groups that belong to specified taxonomy.
     *
     * @param   string $post_type   Taxonomy name.
     * @return  array<Group>        Groups that belong to the taxonomy.
     */
    public static function get_by_taxonomy( string $taxonomy ) : array
    {
        return static::get_by_filters([
            'taxonomy' => $taxonomy,
        ]);
    }

    /**
     * RA wrapper for acf_get_field_groups().
     *
     * @param   array<string,mixed> $filters    Filters the groups should be filtered by.
     * @return  array<Group>                    Groups that belong to the post type.
     */
    public static function get_by_filters( array $filters ) : array
    {
        $groups = [];

        foreach ( acf_get_field_groups( $filters ) as $group )
        {
            $groups[] = new Group( $group['key'] );
        }

        return $groups;
    }
}