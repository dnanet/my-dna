<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Add_Listing extends Widget_Base {

	public function get_name() {
		return 'case27-add-listing-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Add Listing Form', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );

		$this->start_controls_section(
			'add_listing_choose_type',
			['label' => esc_html__( 'Listing type selection step', 'my-listing' ),]
		);

		$this->add_control(
			'size',
			[
				'label' => _x( 'Card Size', 'Elementor > Add Listing widget', 'my-listing' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'medium',
				'options' => [
					'small' => _x( 'Small', 'Elementor > Add Listing widget', 'my-listing' ),
					'medium' => _x( 'Regular', 'Elementor > Add Listing widget', 'my-listing' ),
					'large' => _x( 'Large', 'Elementor > Add Listing widget', 'my-listing' ),
				],
			]
		);

		$this->add_control(
			'listing_types',
			[
				'label' => _x( 'Listing Type(s)', 'Elementor > Add Listing widget', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'listing_type',
						'label' => _x( 'Listing Type', 'Elementor > Add Listing widget', 'my-listing' ),
						'type' => Controls_Manager::SELECT2,
						'options' => c27()->get_posts_dropdown_array([
							'post_type' => 'case27_listing_type',
							'posts_per_page' => -1,
						], 'post_name'),
						'default' => '',
					],
					[
						'name' => 'color',
						'label' => _x( 'Color', 'Elementor > Add Listing widget', 'my-listing' ),
						'type' => Controls_Manager::COLOR,
					],
				],
				'title_field' => '{{{ listing_type.toUpperCase() }}}',
			]
		);

		$this->add_control(
			'form_section_animation',
			[
				'label' => __( 'Enable form section animations', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'your-plugin' ),
				'label_off' => __( 'no', 'your-plugin' ),
				'return_value' => 'yes',
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'add_listing_choose_package',
			['label' => esc_html__( 'Package selection step', 'my-listing' ),]
		);

		$this->add_control(
			'packages_layout',
			[
				'label' => _x( 'Package layout', 'Elementor > Add Listing widget', 'my-listing' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'regular',
				'options' => [
					'regular' => _x( 'Show 3 packages per row', 'Elementor > Add Listing widget', 'my-listing' ),
					'compact' => _x( 'Show 4 packages per row', 'Elementor > Add Listing widget', 'my-listing' ),
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		c27()->get_section( 'add-listing', [
			'listing_types' => $this->get_settings('listing_types'),
			'size' => $this->get_settings('size'),
			'packages_layout' => $this->get_settings('packages_layout'),
			'form_section_animation' => $this->get_settings('form_section_animation'),
			'is_edit_mode' => Plugin::$instance->editor->is_edit_mode(),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
