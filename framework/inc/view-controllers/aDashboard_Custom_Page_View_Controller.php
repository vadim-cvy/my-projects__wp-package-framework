<?php

namespace YOUR_NAMESPACE\framework\inc\view_controllers;

use \YOUR_NAMESPACE\framework\inc\wp\WP;

use \YOUR_NAMESPACE\Main;

use \YOUR_NAMESPACE\framework\inc\filesystem\File;

use \YOUR_NAMESPACE\framework\inc\data_types\Strings;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aDashboard_Custom_Page_View_Controller extends aPage_View_Controller
{
	protected $is_first_section_rendered = false;

    final protected function __construct()
	{
		parent::__construct();

		WP::hooks()->listen( 'admin_menu', [ $this, '_register' ] );

		WP::hooks()->listen( 'admin_menu', [ $this, '_remember_save_process_errors' ] );
	}

	protected function on_is_current() : void
	{
		parent::on_is_current();

		$this->maybe_display_notifications();
	}

	abstract protected function get_menu_title() : string;

	abstract protected function get_page_title() : string;

	protected function get_vars() : array
	{
		global $wp_settings_sections;

		$slug = $this->get_page_instance()->get_slug();

		return array_merge( parent::get_vars(), [
			'slug' => $slug,
			'section_names' => array_column( $wp_settings_sections[ $slug ], 'id' ),
		]);
	}

	public function _register() : void
	{
		add_menu_page(
			$this->get_page_title(),
			$this->get_menu_title(),
			$this->get_page_instance()->get_user_permission(),
			$this->get_page_instance()->get_slug(),
			[ $this, 'render' ],
		);
	}

	protected function register_section( string $name, string $label, array $fields ) : void
	{
		$this->get_page_instance()->register_section( $name, $label, [ $this, '_render_section_header' ] );

		foreach ( $fields as $setting_name => $field )
		{
			$setting_name = Main::prefix( $setting_name );

			$this->get_page_instance()->register_field(
				$setting_name,
				$field['label'],
				$name,
				[ $this, '_render_field' ]
			);
		}
	}

	public function _render_section_header( string $name, string $label ) : void
	{
		echo $this->get_template_part( 'section_header.php' )->get_content(array_merge(
			$this->get_vars(),
			[
				'name' => $name,
				'label' => $label,
				'is_first_section' => ! $this->is_first_section_rendered,
			]
		));

		$this->is_first_section_rendered = true;
	}

	public function _render_field( string $setting_name, $value, array $args = [] ) : void
	{
		$value_normalized = esc_attr(
			is_array( $value ) ?
			json_encode( $value ) :
			$value
		);

		$vue_v_model =
			'settings.' .
			Strings::switch_case( $setting_name, 'snake', 'cammel' );

		echo $this->get_template_part( 'fields/field.php' )->get_content(array_merge(
			$this->get_vars(),
			[
				'setting_name' => esc_attr( $setting_name ),
				'vue_v_model' => $vue_v_model,
				'value_raw' => $value,
				'value' => $value_normalized,
				'args' => array_map( 'esc_attr', $args ),
				'field_input_template_path' =>
					$this->get_templates_dir()
					->get_sub_dir( 'fields' )
					->get_file( Main::unprefix( $setting_name ) . '.php' )
					->get_path(),
			]
		));
	}

	public function get_template_file() : File
    {
        return $this->get_template_part( 'main.php' );
    }

	protected function get_template_part( string $relative_path ) : File
	{
		$templates_dir = $this->get_templates_dir( false );

		if ( $templates_dir->exists() )
		{
			$file = $templates_dir->get_file( $relative_path, false );

			if ( $file->exists() )
			{
				return $file;
			}
		}

		return Main::fs()
			->get_framework_view_dir()
			->get_sub_dir( 'dashboard/pages/settings/templates' )
			->get_file( $relative_path );
	}

	public function enqueue_assets() : void
	{
		wp_enqueue_editor();

		parent::enqueue_assets();
	}

	public function _remember_save_process_errors() : void
	{
		foreach ( $this->get_page_instance()->get_save_process_validation_errors() as $setting_name => $message )
		{
			$this->remember_error( $message, $setting_name );
		}
	}

	protected function maybe_display_notifications() : void
	{
		$errors = $this->get_remembered_errors();

		if ( ! empty( $errors ) )
		{
			foreach ( $errors as $error )
			{
				add_settings_error( $this->get_page_instance()->get_slug(), '', $error );
			}

			$this->forget_remembered_errors();
		}
		else if ( isset( $_GET['settings-updated'] ) )
		{
			add_settings_error( $this->get_page_instance()->get_slug(), 'success', 'Settings Saved', 'updated' );
		}
	}

	protected function get_remembered_errors() : array
	{
		return ! empty( $_SESSION[ $this->get_errors_session_key() ] ) ?
			$_SESSION[ $this->get_errors_session_key() ] :
			[];
	}

	protected function remember_error( string $message, string $setting_name = '' ) : void
	{
		$key = $this->get_errors_session_key();

		if ( ! isset( $_SESSION[ $key ] ) )
		{
			$_SESSION[ $key ] = [];
		}

		$message_prefix = '';

		if ( ! empty( $setting_name ) )
		{
			$setting_name . ' field ';
		}

		$message_prefix .= 'error';

		$message_prefix = '<b>' . ucfirst( $message_prefix ) . ': </b>';

		$_SESSION[ $key ][ $setting_name ] = $message_prefix . $message;
	}

	protected function forget_remembered_errors() : void
	{
		$_SESSION[ $this->get_errors_session_key() ] = [];
	}

	protected function get_errors_session_key() : string
	{
		if ( ! session_id() )
		{
			session_start();
		}

		return $this->get_page_instance()->get_slug() . '_errors';
	}
}