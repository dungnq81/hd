<?php

namespace Addons\Editor;

\defined( 'ABSPATH' ) || die;

/**
 * TinyMCE Plugin
 *
 * @author   WEBHD
 */
final class TinyMCE {

	// --------------------------------------------------

	public function __construct() {

		/** Custom styles. */
		add_editor_style( ADDONS_URL . "assets/css/editor-style.css" );

		add_filter( 'mce_buttons', [ &$this, 'tinymce_add_table_button' ] );
		add_filter( 'mce_external_plugins', [ &$this, 'tinymce_add_table_plugin' ] );
	}

	// --------------------------------------------------

	/**
	 * @param $buttons
	 *
	 * @return mixed
	 */
	public function tinymce_add_table_button( $buttons ): mixed {

		array_push( $buttons, 'separator', 'unlink' );
		array_push( $buttons, 'separator', 'alignjustify' );
		array_push( $buttons, 'separator', 'table' );
		array_push( $buttons, 'separator', 'charmap' );
		array_push( $buttons, 'separator', 'backcolor' );
		array_push( $buttons, 'separator', 'superscript' );
		array_push( $buttons, 'separator', 'subscript' );
		array_push( $buttons, 'separator', 'codesample' );
		array_push( $buttons, 'separator', 'toc' );

		//array_push( $buttons, 'separator', 'fullscreen' );

		return $buttons;
	}

	// --------------------------------------------------

	/**
	 * @param $plugins
	 *
	 * @return mixed
	 */
	public function tinymce_add_table_plugin( $plugins ): mixed {

		$plugins['table']      = ADDONS_URL . 'src/Editor/tinymce/table/plugin.min.js';
		$plugins['codesample'] = ADDONS_URL . 'src/Editor/tinymce/codesample/plugin.min.js';
		$plugins['toc']        = ADDONS_URL . 'src/Editor/tinymce/toc/plugin.min.js';

		//$plugins['fullscreen'] = ADDONS_URL . 'src/Editor/tinymce/fullscreen/plugin.min.js';

		return $plugins;
	}
}