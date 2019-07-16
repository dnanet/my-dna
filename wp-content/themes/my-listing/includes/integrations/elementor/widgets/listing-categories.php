<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Listing_Categories extends Widget_Base {

	public function get_name() {
		return 'case27-listing-categories-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Listing Categories', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-carousel';
	}

	protected function _register_controls() {
		$traits = new \MyListing\Int\Elementor\Traits( $this );
		$custom_taxonomies = mylisting_custom_taxonomies();

		$this->start_controls_section(
			'the_listing_categories',
			['label' => esc_html__( 'Listing Categories', 'my-listing' ),]
		);

		$this->add_control(
			'taxonomy',
			[
				'label'   => __( 'Taxonomy', 'my-listing' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'job_listing_category',
				'options' => array_merge( [
					'job_listing_category' => __( 'Categories', 'my-listing' ),
					'region' => __( 'Regions', 'my-listing' ),
					'case27_job_listing_tags' => __( 'Tags', 'my-listing' ),
					'listing_types' => __( 'Listing Types', 'my-listing' ),
				], $custom_taxonomies ),
			]
		);

		$this->add_control(
			'select_categories',
			[
				'label' => __( 'Select Categories', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'category_id',
						'label' => __( 'Select Category', 'my-listing' ),
						'type' => Controls_Manager::SELECT2,
						'options' => c27()->get_terms_dropdown_array([
							'taxonomy' => 'job_listing_category',
							'hide_empty' => false,
						]),
						'default' => '',
					]
				],
				'title_field' => 'Item #{{{ category_id }}}',
				'condition' => [ 'taxonomy' => 'job_listing_category' ],
			]
		);

		$this->add_control(
			'select_regions',
			[
				'label' => __( 'Select Regions', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'category_id',
						'label' => __( 'Select Region', 'my-listing' ),
						'type' => Controls_Manager::SELECT2,
						'options' => c27()->get_terms_dropdown_array([
							'taxonomy' => 'region',
							'hide_empty' => false,
						]),
						'default' => '',
					]
				],
				'title_field' => 'Item #{{{ category_id }}}',
				'condition' => [ 'taxonomy' => 'region' ],
			]
		);

		$this->add_control(
			'select_tags',
			[
				'label' => __( 'Select Tags', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'category_id',
						'label' => __( 'Select Tag', 'my-listing' ),
						'type' => Controls_Manager::SELECT2,
						'options' => c27()->get_terms_dropdown_array([
							'taxonomy' => 'case27_job_listing_tags',
							'hide_empty' => false,
						]),
						'default' => '',
					]
				],
				'title_field' => 'Item #{{{ category_id }}}',
				'condition' => [ 'taxonomy' => 'case27_job_listing_tags' ],
			]
		);

		$this->add_control(
			'select_listing_types',
			[
				'label' => __( 'Select Listing Types', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'category_id',
						'label' => __( 'Select Listing Type', 'my-listing' ),
						'type' => Controls_Manager::SELECT2,
						'options' => c27()->get_posts_dropdown_array( [
							'post_type' => 'case27_listing_type',
							'posts_per_page' => -1,
						] ),
						'default' => '',
					]
				],
				'condition' => [ 'taxonomy' => 'listing_types' ],
			]
		);

		// Add controls for custom taxonomies
		if ( $custom_taxonomies ) {
			foreach ( $custom_taxonomies as $slug => $label ) {
				$this->add_control(
					'select_' . $slug,
					[
						'label' => sprintf( _x( 'Select %s', 'custom taxonomy', 'my-listing' ), $label ),
						'type' => Controls_Manager::REPEATER,
						'fields' => [
							[
								'name' => 'category_id',
								'label' => __( 'Select Item', 'my-listing' ),
								'type' => Controls_Manager::SELECT2,
								'options' => c27()->get_terms_dropdown_array( [
									'taxonomy' => $slug,
									'hide_empty' => false,
								] ),
								'default' => '',
							]
						],
						'title_field' => 'Item #{{{ category_id }}}',
						'condition' => [ 'taxonomy' => $slug ],
					]
				);
			}
		}

		$this->add_control(
			'display_template',
			[
				'label' => __( 'Template', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'template_1' => __( 'Default', 'my-listing' ),
					'template_4' => __( 'Alternate', 'my-listing' ),
					'template_2' => __( 'Cards', 'my-listing' ),
					'template_3' => __( 'Cards Alternate', 'my-listing' ),
				],
			]
		);

		$this->add_control(
			'category_background_size',
			[
				'label' => __( 'Background Size', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'cover',
				'options' => [
					'cover' => 'Cover',
					'contain' => 'Contain',
					'auto' => 'Auto',
					'30%' => '30%',
					'40%' => '40%',
					'50%' => '50%',
					'60%' => '60%',
					'70%' => '70%',
					'80%' => '80%',
					'90%' => '90%',
					'100%' => '100%',
					'110%' => '110%',
					'120%' => '120%',
				],
				'condition' => ['display_template' => 'template_3'],
				'selectors' => [ '{{WRAPPER}} .car-item-img' => 'background-size: {{VALUE}}' ],
			]
		);


		$traits->choose_columns('Column Count', 'column_count', [
			'general' => ['min' => 1, 'max' => 4],
			'lg' => ['default' => 3], 'md' => ['default' => 3],
			'sm' => ['default' => 2], 'xs' => ['default' => 1],
		]);

		$traits->choose_overlay(__( 'Set an overlay', 'my-listing' ), '27_overlay');

		$this->end_controls_section();
	}

	protected function render( $instance = [] ) {

		$taxonomy = $this->get_settings( 'taxonomy' );

		switch ( $taxonomy ) {
			case 'region' :
				$terms = $this->get_settings('select_regions');
			break;

			case 'case27_job_listing_tags' :
				$terms = $this->get_settings('select_tags');
			break;

			case 'job_listing_category' :
				$terms = $this->get_settings('select_categories');
			break;

			case 'listing_types' :
				$terms = $this->get_settings('select_listing_types');
			break;

			default :
				$custom_taxonomies = mylisting_custom_taxonomies();

				foreach ( $custom_taxonomies as $slug => $label ) {
					if ( $taxonomy != $slug ) {
						continue;
					}

					$terms = $this->get_settings( "select_{$slug}" );
				}

			break;
		}

		c27()->get_section( 'listing-categories', [
			'taxonomy' => $this->get_settings('taxonomy'),
			'terms' => (array) $terms,
			'template' => $this->get_settings('display_template'),
			'overlay_type' => $this->get_settings('27_overlay'),
			'overlay_gradient' => $this->get_settings('27_overlay__gradient'),
			'overlay_solid_color' => $this->get_settings('27_overlay__solid_color'),
			'columns' => [
				'lg' => $this->get_settings('column_count__lg'),
				'md' => $this->get_settings('column_count__md'),
				'sm' => $this->get_settings('column_count__sm'),
				'xs' => $this->get_settings('column_count__xs'),
			],
		] );
	}

	protected function content_template() {}
	public function render_plain_content() {}
}
