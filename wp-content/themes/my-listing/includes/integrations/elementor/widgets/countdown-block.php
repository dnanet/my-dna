<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Countdown_Block extends Widget_Base {

	public function get_name() {
		return 'case27-countdown-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Countdown Block', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-countdown';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );

		$this->start_controls_section(
			'section_countdown_block',
			[
				'label' => esc_html__( 'Content', 'my-listing' ),
			]
		);

		$this->add_control(
			'the_icon',
			[
			'label' => __( 'Icon', 'my-listing' ),
			'type' => Controls_Manager::ICON,
			]
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
			'the_countdown_date',
			[
				'label' => __( 'Select Date', 'my-listing' ),
				'type' => Controls_Manager::DATE_TIME,
				'default' => date('Y-m-d H:i'),
				'placeholder' => date('Y-m-d H:i'),
			]
		);

		$this->add_control(
			'the_countdown_number_color',
			[
				'label' => __( 'Numbers Color', 'my-listing' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => ['{{WRAPPER}} .element .countdown-list li p' => 'color: {{VALUE}}',],
			]
		);

		$this->add_control(
			'the_countdown_number_background',
			[
				'label' => __( 'Numbers Background', 'my-listing' ),
				'type' => Controls_Manager::COLOR,
				'default' => c27()->get_setting('general_brand_color', '#f24286'),
				'selectors' => ['{{WRAPPER}} .element .countdown-list li p' => 'background: {{VALUE}}',],
			]
		);


		$this->add_control(
			'the_countdown_labels_color',
			[
				'label' => __( 'Labels Color', 'my-listing' ),
				'type' => Controls_Manager::COLOR,
				'default' => c27()->get_setting('general_brand_color', '#f24286'),
				'selectors' => ['{{WRAPPER}} .element .countdown-list li p span' => 'color: {{VALUE}}',],
			]
		);

		$this->end_controls_section();

		$traits->block_styles();
	}


	protected function render( $instance = [] ) {
		c27()->get_section( 'countdown-block', [
			'icon' => $this->get_settings('the_icon'),
			'icon_style' => $this->get_settings('the_icon_style'),
			'title' => $this->get_settings('the_title'),
			'countdown_date' => $this->get_settings('the_countdown_date'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
