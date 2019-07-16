<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Title_Bar extends Widget_Base {

	public function get_name() {
		return 'case27-title-bar-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Title Bar', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-divider';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );

		$this->start_controls_section(
			'the_title_bar_controls',
			['label' => esc_html__( 'Page Heading', 'my-listing' ),]
		);

		$this->add_control(
			'the_title',
			[
				'label' => __( 'Title', 'my-listing' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'my-listing' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .page-head .ph-details h1, {{WRAPPER}} .page-head .page-directory li a, {{WRAPPER}} .page-head .page-directory li a span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'show_breadcrumbs',
			[
				'label' => __( 'Show Breadcrumbs?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Show', 'my-listing' ),
				'label_off' => __( 'Hide', 'my-listing' ),
				'return_value' => 'yes',
			]
		);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		c27()->get_section( 'title-bar', [
			'title' => $this->get_settings('the_title'),
			'show_breadcrumbs' => $this->get_settings('show_breadcrumbs'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
