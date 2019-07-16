<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Scheme_Color;
use \Elementor\Controls_Manager;

class List_Block extends Widget_Base {

	public function get_name() {
		return 'case27-list-block-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > List Block', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-bullet-list';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );

		$this->start_controls_section(
			'section_content_block',
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
			'the_items',
			[
				'label' => __( 'Content', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'type',
						'label' => __( 'Type', 'my-listing' ),
						'type' => Controls_Manager::SELECT2,
						'default' => 'plain_text',
						'options' => [
							'plain_text' => __( 'Plain Text', 'my-listing' ),
							'link' => __( 'Link', 'my-listing' ),
						],
					],
					[
						'name' => 'title',
						'label' => __( 'Title', 'my-listing' ),
						'type' => Controls_Manager::TEXT,
						'default' => '',
					],
					[
						'name' => 'icon',
						'label' => __( 'Icon', 'my-listing' ),
						'type' => Controls_Manager::ICON,
					],
					[
						'name' => 'link',
						'label' => __( 'Link', 'my-listing' ),
						'type' => Controls_Manager::URL,
						'default' => [
							'url' => 'http://',
							'is_external' => false,
						],
						'show_external' => true,
						'condition' => [
							'type' => 'link',
						]
					],
					[
						'name' => 'link_hover_color',
						'label' => __( 'Icon Hover Color', 'my-listing' ),
						'type' => Controls_Manager::COLOR,
						'scheme' => [
							'type' => Scheme_Color::get_type(),
							'value' => Scheme_Color::COLOR_1,
						],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$traits->block_styles();
	}


	protected function render( $instance = [] ) {
		c27()->get_section( 'list-block', [
			'icon' => $this->get_settings('the_icon'),
			'icon_style' => $this->get_settings('the_icon_style'),
			'title' => $this->get_settings('the_title'),
			'items' => $this->get_settings('the_items'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
