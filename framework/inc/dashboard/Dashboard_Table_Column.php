<?php

namespace YOUR_NAMESPACE\framework\inc\dashboard;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\Main;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Representation of the WP dashboard table column.
 *
 * Works for all types of tables: post types, taxonomies, users.
 */
class Dashboard_Table_Column
{
    /**
     * Name of the screen the column belongs to.
     *
     * @var string  Screen name. Ex:
     *              "my_custom_post_type" - My Custom Post Type posts table;
     *              "my_custom_taxonomy" - My Custom Taxonomy terms table;
     *              "users" - Users table.
     */
    protected $screen_name;

    protected $slug;

    protected $label;

    protected $render_cell_cb;

    public function __construct(
        string $slug,
        string $label,
        string $screen_name,
        callable $render_cell_cb,
        string $insert_after = ''
    )
    {
        $this->slug           = Main::prefix( $slug );
        $this->label          = $label;
        $this->screen_name    = $screen_name;
        $this->render_cell_cb = $render_cell_cb;
        $this->insert_after   = $insert_after;

        $this->register();
    }

    /**
     * Registers the column in the dashboard table.
     *
     * @return void
     */
    protected function register() : void
    {
        $is_users_screen = $this->screen_name === 'users';

        if ( $is_users_screen )
        {
            $add_column_hook_name = 'manage_' . $this->screen_name . '_columns';
        }
        else
        {
            $add_column_hook_name = 'manage_edit-' . $this->screen_name . '_columns';
        }

        $is_post_type_screen = ! empty( get_post_type_object( $this->screen_name ) );

        if ( $is_post_type_screen )
        {
            $print_cell_hook_name = 'manage_' . $this->screen_name . '_posts_custom_column';
        }
        else
        {
            $print_cell_hook_name = 'manage_' . $this->screen_name . '_custom_column';
        }

        WP::hooks()->listen( $add_column_hook_name, [ $this, '_add_column' ] );

        WP::hooks()->listen( $print_cell_hook_name, [ $this, '_print_column_cell' ] );
    }

    /**
     * A callback for 'manage_{screen_name}_columns'.
     *
     * @param   array<string> $table_columns    Inital table columns.
     * @return  array<string>                   $table_columns merged with current column.
     */
    public function _add_column( array $table_columns ) : array
    {
        if ( $this->insert_after && ! empty( $table_columns[ $this->insert_after ] ) )
        {
            $table_columns_updated = [];

            foreach ( $table_columns as $key => $label )
            {
                $table_columns_updated[ $key ] = $label;

                if ( $key === $this->insert_after )
                {
                    $table_columns_updated[ $this->slug ] = $this->label;
                }
            }

            $table_columns = $table_columns_updated;
        }
        else
        {
            $table_columns[ $this->slug ] = $this->label;
        }

        return $table_columns;
    }

    /**
     * Prints column cell.
     *
     * @param string        $arg_1  Column name in case current table is post type table,
     *                              empty string otherwise.
     * @param string|int    $arg_2  Empty string in case current table is post type table,
     *                              column name otherwise;
     * @param integer       $arg_3  Empty string in case current table is post type table,
     *                              Term id in case current table is taxonomy table,
     *                              User id in case current table is users table.
     * @return string               Cell content in case current table is users table,
     *                              empty string otherwise.
     */
    public function _print_column_cell( string $arg_1, $arg_2, int $arg_3 = 0 ) : string
    {
        // WP has a strange sense of humor and it passes a blank string as a first argument
        // while second argument is column name (for taxonomies and users). But post types
        // recieve column name as a first argument...
        $column_name =
            $this->get_current_table_type() === 'posts' ?
            $arg_1 :
            $arg_2;

        if ( $column_name !== $this->slug )
        {
            return $this->get_current_table_type() === 'users' ? $arg_1 : '';
        }

        $object_id =
            ! empty( $arg_1 ) ?
            $arg_2 :
            $arg_3;

        ob_start();

        call_user_func( $this->render_cell_cb, $column_name, $object_id );

        $content = ob_get_contents();

        ob_end_clean();

        // Users table don't want we to print the cell as we do for post and tax tables.
        // Users table wants we to return a content as a string.
        // Funny.
        if ( $this->get_current_table_type() !== 'users' )
        {
            echo $content;
        }

        return $content;
    }

    protected function get_current_table_type() : string
    {
        $screen = get_current_screen();

        if ( $screen->base === 'edit' )
        {
            return 'posts';
        }
        else if ( $screen->base === 'edit-tags' )
        {
            return 'tax';
        }
        else if ( $screen->base === 'users' )
        {
            return 'users';
        }
    }
}
