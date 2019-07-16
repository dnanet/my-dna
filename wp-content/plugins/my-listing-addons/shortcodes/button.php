<?php

namespace CASE27\Shortcodes;

/**
 * Button shortcode.
 */
class Button {

	public $name = '27-button',
		$title = '',
		$description = '',
	    $attributes = [
			'href' => '#',
			'style' => 1,
			'width' => '',
			'size' => '',
			'animated' => 'no',
	    ];

	public function __construct()
	{
		$this->title = __( 'Button', 'my-listing' );
		$this->description = __( 'Generate a button.', 'my-listing' );

		add_shortcode($this->name, [$this, 'add_shortcode']);
	}

	public function add_shortcode($atts, $content = null)
	{
		$atts = shortcode_atts( $this->attributes, $atts );

		return do_shortcode( sprintf(
				'<a href="%2$s" class="buttons button-%3$s %4$s %5$s %6$s">%1$s %7$s</a>',
				$content, esc_url( $atts['href'] ), esc_attr( $atts['style'] ), $atts['width'], $atts['size'],
				$atts['animated'] == 'yes' ? 'button-animated' : '', $atts['animated'] == 'yes' ? c27()->get_icon_markup('material-icons://keyboard_arrow_right') : ''
			));
	}

	public function output_options() { ?>
		<div class="form-group">
			<label>Content</label>
			<textarea data-content></textarea>
		</div>

		<div class="form-group">
			<label>Link to (href)</label>
			<input type="text" data-attr="href">
		</div>

		<div class="form-group">
			<label>Style</label>
			<select data-attr="style">
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
			</select>
		</div>

		<div class="form-group">
			<label>Width</label>
			<select data-attr="width">
				<option value="">Auto</option>
				<option value="full-width">Full</option>
			</select>
		</div>

		<div class="form-group">
			<label>Size</label>
			<select data-attr="size">
				<option value="">Normal</option>
				<option value="medium">Medium</option>
				<option value="small">Small</option>
			</select>
		</div>

		<div class="form-group">
			<label>Animated</label>
			<select data-attr="animated">
				<option value="yes">Yes</option>
				<option value="no">No</option>
			</select>
		</div>
	<?php }
}

return new Button;
