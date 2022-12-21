<?php

namespace YOUR_NAMESPACE\framework\inc\view_controllers;

use \YOUR_NAMESPACE\framework\inc\posts\aPost_Type;

use \YOUR_NAMESPACE\framework\inc\site_pages\aSite_Page;
use \YOUR_NAMESPACE\framework\inc\site_pages\aPost_Type_Page;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aDashboard_Post_Type_Page_View_Controller extends aPage_View_Controller
{
  protected function get_post_type() : aPost_Type
	{
		return $this->get_page_instance()->get_post_type_object();
	}

	protected function add_column(
		string $slug,
		string $label,
		callable $render_cell_cb,
		string $insert_after = 'title'
	) : void
	{
		$this->get_post_type()->add_posts_list_column( $slug, $label, $render_cell_cb, $insert_after );
	}

	protected function remove_column( string $slug ) : void
	{
		$this->get_post_type()->remove_posts_list_column( $slug );
	}

	final static public function get_page_instance() : aSite_Page
	{
		return static::get_post_type_page_instance();
	}

	abstract static protected function get_post_type_page_instance() : aPost_Type_Page;
}