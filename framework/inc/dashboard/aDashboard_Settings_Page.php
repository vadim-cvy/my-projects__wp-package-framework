<?php

namespace YOUR_NAMESPACE\framework\inc\dashboard;

use \YOUR_NAMESPACE\framework\inc\site_pages\aSite_Page;

use \YOUR_NAMESPACE\Main;

use \YOUR_NAMESPACE\framework\inc\user_input\User_Input_Validation_Error;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aDashboard_Settings_Page extends aSite_Page
{
	protected $registered_settings;

	protected $save_process_validation_errors = [];

	protected function __construct()
	{
		parent::__construct( 'dashboard' );

		$this->register_settings();
	}

	abstract protected function register_settings() : void;

	protected function register_setting(
		string $setting_name,
		string $type,
		callable $sanitize_cb,
		$default = null,
		string $description = '',
		$show_in_rest = false
	) : void
	{
		$page_slug = $this->get_slug();

		$setting_name = Main::prefix( $setting_name );

		$sanitize_cb = function( $val ) use ( $setting_name, $type, $sanitize_cb, $default )
		{
			try
			{
				if ( $type === 'boolean' )
				{
					return !! $val;
				}

				if ( isset( $val ) )
				{
					return call_user_func( $sanitize_cb, $val );
				}
			}
			catch ( User_Input_Validation_Error $error )
			{
				$this->save_process_validation_errors[ $setting_name ] = $error->getMessage();
			}

			$current_value = Main::settings()->get( $setting_name );

			return isset( $current_value ) ? $current_value : $default;
		};

		register_setting( $page_slug, $setting_name, [
			'type' => $type,
			'sanitize_callback' => $sanitize_cb,
			'default' => $default,
			'description' => $description,
			'show_in_rest' => $show_in_rest,
		]);

		$this->registered_settings[] = $setting_name;
	}

	public function get_registered_settings()
	{
		return $this->registered_settings;
	}

	public function register_section( string $name, string $label, callable $header_render_cb ) : void
	{
		$page_slug = $this->get_slug();

		$header_render_cb = function() use ( $header_render_cb, $name, $label ) : void
		{
			call_user_func( $header_render_cb, $name, $label );
		};

		add_settings_section( $name, $label, $header_render_cb, $page_slug );
	}

	public function register_field(
		string $setting_name,
		string $label,
		string $section_name,
		callable $render_cb,
		array $args = []
	)
	{
		$setting_name = Main::prefix( $setting_name );

		$page_slug = $this->get_slug();

		$render_cb = function() use ( $render_cb, $setting_name, $args ) : void
		{
			$value = get_option( $setting_name );

			call_user_func( $render_cb, $setting_name, $value, $args );
		};

		add_settings_field( $setting_name, $label, $render_cb, $page_slug, $section_name, $args );
	}

	static public function get_slug() : string
	{
		return Main::prefix( static::get_slug_base() );
	}

	abstract static protected function get_slug_base() : string;

	public function get_user_permission() : string
	{
		return 'manage_options';
	}

	static public function get_raw_url() : string
	{
		return menu_page_url( static::get_slug(), false );
	}

	static public function is_current() : bool
	{
		return is_admin() && ! empty( $_GET['page'] ) && $_GET['page'] === static::get_slug();
	}

	public function get_save_process_validation_errors() : array
	{
		return $this->save_process_validation_errors;
	}
}