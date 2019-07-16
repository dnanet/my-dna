<?php
/**
 * Code Field module for Advanced Custom Fields.
 *
 * @version   1.0
 * @author    27collective
 *
 * @copyright 2018 27collective (https://27collective.net)
 * @license   GNU General Public License v2.0
 * @link      http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @copyright 2017 Peter Tasker (https://petetasker.com)
 * @license   GNU General Public License v2.0
 * @link      http://www.gnu.org/licenses/gpl-2.0.html
 *
 */

namespace MyListing\Ext\Advanced_Custom_Fields\Code_Field;

if ( ! defined('ABSPATH') ) {
	exit;
}

class Code_Field extends \acf_field {

	public $config = [
		'codemirror_version' => '',
		'dir' => '',
		'uri' => '',
	];

	/**
	 * This function will setup the field type data.
	 */
	function __construct() {
		$this->config['codemirror_version'] = 'codemirror-5.23.0';
		$this->config['dir'] = trailingslashit( get_template_directory() ) . 'includes/extensions/advanced-custom-fields/code-field/';
		$this->config['uri'] = trailingslashit( get_template_directory_uri() ) . 'includes/extensions/advanced-custom-fields/code-field/';

		// Name (string) Single word, no spaces. Underscores allowed
		$this->name = 'cts_code_field';

		// Label (string) Multiple words, can include spaces, visible when selecting a field type
		$this->label = 'Code Area';

		// Category (string) basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME
		$this->category = 'basic';

		// Defaults (array) Array of default settings which are merged into the field object. These are used later in settings
		$this->defaults = array(
			'mode'  => 'htmlmixed',
			'theme' => 'material',
		);

		// do not delete!
		parent::__construct();
	}


	/**
	 * Create extra settings for your field.
	 * These are visible when editing a field.
	 */
	function render_field_settings( $field ) {
		// default_value
		acf_render_field_setting( $field, array(
			'label'        => 'Default Value',
			'instructions' => 'Appears when creating a new post',
			'type'         => 'textarea',
			'name'         => 'default_value',
		) );

		// placeholder
		acf_render_field_setting( $field, array(
			'label'        => 'Placeholder Text',
			'instructions' => 'Appears within the input',
			'type'         => 'text',
			'name'         => 'placeholder',
		) );

		// Editor mode.
		acf_render_field_setting( $field, array(
			'label'        => 'Editor mode',
			'instructions' => '',
			'type'         => 'select',
			'name'         => 'mode',
			'choices'      => array(
				'htmlmixed'               => "HTML Mixed",
				'javascript'              => "JavaScript",
				'text/html'               => "HTML",
				'css'                     => "CSS",
				'application/x-httpd-php' => "PHP",
			),
		) );

		acf_render_field_setting( $field, array(
			'label'        => 'Editor theme',
			'instructions' => 'Themes can be previewed on the <a href="https://codemirror.net/demo/theme.html#default" target="_blank">codemirror website</a>',
			'type'         => 'select',
			'name'         => 'theme',
			'choices'      => $this->get_codemirror_themes(),
		) );
	}

	/**
	 * Create the HTML interface for your field
	 */
	function render_field( $field ) {
        $dir = trailingslashit( get_template_directory_uri() ) . 'includes/extensions/advanced-custom-fields/code-field/';
		$safe_slug = str_replace( "-", "_", $field['id'] );
		// vars
		$o = array( 'id', 'class', 'name', 'placeholder', 'mode', 'theme' );
		$e = '';

		// populate atts
		$atts = array();
		foreach ( $o as $k ) {
			$atts[ $k ] = $field[ $k ];
		}

		$atts['class'] = 'cts-code-field-box';

		$e .= '<textarea ' . acf_esc_attr( $atts ) . ' >';
		$e .= esc_textarea( $field['value'] );
		$e .= '</textarea>';


		echo $e;
		wp_enqueue_style( "codemirror-curr-style-{$field['theme']}", "{$dir}js/" . $this->config['codemirror_version'] . "/theme/{$field['theme']}.css", [], CASE27_THEME_VERSION );
	}

	/**
	 * This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	 * Use this action to add CSS + JavaScript to assist your render_field() action.
	 */
	function input_admin_enqueue_scripts() {

        $uri = trailingslashit( $this->config['uri'] );
        $version = $this->config['codemirror_version'];

		if ( version_compare( $GLOBALS['wp_version'], '4.9', '>=' ) ) {
			wp_enqueue_script( 'wp-codemirror' );
			wp_enqueue_style( 'wp-codemirror' );
			wp_enqueue_script( 'csslint' );
			wp_enqueue_script( 'jshint' );
			wp_enqueue_script( 'jsonlint' );
			wp_enqueue_script( 'htmlhint' );
			wp_enqueue_script( 'htmlhint-kses' );

			// Alias wp.CodeMirror to CodeMirror
			wp_add_inline_script( 'wp-codemirror', 'window.CodeMirror = wp.CodeMirror;' );
		} else {
			// CodeMirror isn't in WP core until WP 4.9
			wp_enqueue_script( 'acf-input-code-field-codemirror', "{$uri}js/{$version}/lib/codemirror.js" );

			wp_enqueue_script( 'acf-input-code-field-codemirror-showhint', "{$uri}js/{$version}/addon/hint/show-hint.js" );
			wp_enqueue_script( 'acf-input-code-field-codemirror-xmlhint', "{$uri}js/{$version}/addon/hint/xml-hint.js" );
			wp_enqueue_script( 'acf-input-code-field-codemirror-htmlhint', "{$uri}js/{$version}/addon/hint/html-hint.js" );

			wp_enqueue_style( 'acf-input-code-field',"{$uri}js/{$version}/lib/codemirror.css" );
		}

		// CodeMirror modes
		wp_enqueue_script( 'acf-input-code-field-codemirror-css', "{$uri}js/{$version}/mode/css/css.js" );
		wp_enqueue_script( 'acf-input-code-field-codemirror-js', "{$uri}js/{$version}/mode/javascript/javascript.js" );
		wp_enqueue_script( 'acf-input-code-field-codemirror-xml', "{$uri}js/{$version}/mode/xml/xml.js" );
		wp_enqueue_script( 'acf-input-code-field-codemirror-clike', "{$uri}js/{$version}/mode/clike/clike.js" );
		wp_enqueue_script( 'acf-input-code-field-codemirror-php', "{$uri}js/{$version}/mode/php/php.js" );
		wp_enqueue_script( 'acf-input-code-field-codemirror-htmlmixed', "{$uri}js/{$version}/mode/htmlmixed/htmlmixed.js" );

		wp_enqueue_script( 'acf-input-code-field-codemirror-selection', "{$uri}js/{$version}/addon/selection/mark-selection.js", array( 'wp-codemirror' ) );
		wp_enqueue_script( 'acf-input-code-field-codemirror-matchbrackets', "{$uri}js/{$version}/addon/edit/matchbrackets.js", array( 'wp-codemirror' ) );
		wp_enqueue_script( 'acf-input-code-field-codemirror-autorefresh', "{$uri}js/{$version}/addon/display/autorefresh.js", array( 'wp-codemirror' ) );

		// register & include CSS
		wp_enqueue_style( 'cts-acf-input-code-field-css', "{$uri}css/input.css", [], CASE27_THEME_VERSION );

		// Register the script
		wp_enqueue_script( 'cts-acf-input-code-field-input', "{$uri}js/input.js", [], CASE27_THEME_VERSION );
	}

    public function get_codemirror_themes() {
    	$list = [
    		'3024-day', '3024-night', 'abcdef', 'ambiance-mobile', 'ambiance',
			'base16-dark', 'base16-light', 'bespin', 'blackboard', 'cobalt',
			'colorforth', 'dracula', 'duotone-dark', 'duotone-light', 'eclipse',
			'elegant', 'erlang-dark', 'hopscotch', 'icecoder', 'isotope', 'lesser-dark',
			'liquibyte', 'material', 'mbo', 'mdn-like', 'midnight', 'monokai', 'neat',
			'neo', 'night', 'panda-syntax', 'paraiso-dark', 'paraiso-light',
			'pastel-on-dark', 'railscasts', 'rubyblue', 'seti', 'solarized',
			'the-matrix', 'tomorrow-night-bright', 'tomorrow-night-eighties',
			'ttcn', 'twilight', 'vibrant-ink', 'xq-dark', 'xq-light', 'yeti', 'zenburn',
    	];

    	$themes = [];
        foreach ( $list as $theme ) {
        	$themes[ $theme ] = ucwords( str_replace( '-', ' ', $theme ) );
        }

        return $themes;
    }
}
