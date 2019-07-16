<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Info_Cards extends Widget_Base {

	public function get_name() {
		return 'case27-info-cards-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Info Cards', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-gallery-grid';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );

		$this->start_controls_section(
			'the_info_cards_controls',
			['label' => esc_html__( 'Info Cards', 'my-listing' ),]
		);

		$this->add_control(
			'27_items',
			[
				'label' => __( 'Items', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'icon',
						'label' => __( 'Icon', 'my-listing' ),
						'type' => Controls_Manager::ICON,
					],
					[
						'name' => 'title',
						'label' => __( 'Title', 'my-listing' ),
						'type' => Controls_Manager::TEXT,
						'default' => '',
					],
					[
						'name' => 'content',
						'label' => __( 'Content', 'my-listing' ),
						'type' => Controls_Manager::WYSIWYG,
						'default' => '',
					],
					[
						'name' => 'size',
						'label' => __( 'Size', 'my-listing' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'col-md-4 col-sm-6',
						'options' => [
							'col-md-4 col-sm-6'  => __( 'One Third', 'my-listing' ),
							'col-md-6 col-sm-6' => __( 'Half', 'my-listing' ),
							'col-md-8 col-sm-12' => __( 'Two Thirds', 'my-listing' ),
						],
	 				],
	 			],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {
		c27()->get_section( 'info-cards', [
			'items' => $this->get_settings('27_items'),
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
