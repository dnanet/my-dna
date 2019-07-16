<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Map extends Widget_Base {

	public function get_name() {
		return 'case27-map-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Map', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-google-maps';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );

		$this->start_controls_section(
			'section_map_block',
			[
				'label' => esc_html__( 'Map', 'my-listing' ),
			]
		);

		$this->add_control(
			'the_template',
			[
			'label' => __( 'Template', 'my-listing' ),
			'type' => Controls_Manager::SELECT2,
			'default' => 'default',
			'options' => [
				'default' => __( 'Default', 'my-listing' ),
				'block' => __( 'Block', 'my-listing' ),
				'full_width_content' => __( 'Full width + Content Overlay', 'my-listing' ),
			],
			]
		);

		$this->add_control(
			'the_content',
			[
				'label' => __( 'Content', 'my-listing' ),
				'type' => Controls_Manager::WYSIWYG,
				'condition' => ['the_template' => 'full_width_content'],
			]
		);

		$this->add_control(
			'the_icon',
			[
			'label' => __( 'Icon', 'my-listing' ),
			'type' => Controls_Manager::ICON,
			'condition' => ['the_template' => 'block'],
			]
		);

		$this->add_control(
			'the_title',
			[
				'label' => __( 'Title', 'my-listing' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition' => ['the_template' => 'block'],
			]
		);

		$this->add_control(
			'show_get_directions',
			[
				'label' => __( 'Show "Get Directions" Link?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'my-listing' ),
				'label_off' => __( 'Hide', 'my-listing' ),
				'return_value' => 'yes',
				'condition' => ['the_template' => 'block'],
			]
		);

		$this->end_controls_section();

		$traits->map_controls();
	}

	protected function render( $instance = [] ) {
		c27()->get_section( 'map', [
			'options' => [
				'items_type' => $this->get_settings('the_map_items'),
				'skin' => $this->get_settings('the_skin'),
				'zoom' => $this->get_settings('the_zoom')['size'],
				'locations' => $this->get_settings('the_locations'),
				'cluster_markers' => $this->get_settings('the_cluster_markers'),
				'listings_query' => [
					'lat' => $this->get_settings('27_listings_lat'),
					'lng' => $this->get_settings('27_listings_lng'),
					'radius' => $this->get_settings('27_listings_radius'),
					'listing_type' => $this->get_settings('27_listings_type'),
					'count' => $this->get_settings('27_listings_count'),
				],
				],
			'template' => $this->get_settings('the_template'),
			'title' => $this->get_settings('the_title'),
			'icon' => $this->get_settings('the_icon'),
			'icon_style' => $this->get_settings('the_icon_style'),
			'show_get_directions' => $this->get_settings('show_get_directions'),
			'content' => $this->get_settings('the_content'),
			'is_edit_mode' => Plugin::$instance->editor->is_edit_mode(),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
