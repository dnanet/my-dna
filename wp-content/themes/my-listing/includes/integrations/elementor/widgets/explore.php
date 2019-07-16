<?php

namespace MyListing\Int\Elementor\Widgets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Plugin;
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class Explore extends Widget_Base {

	public function get_name() {
		return 'case27-explore-widget';
	}

	public function get_title() {
		return __( '<strong>27</strong> > Explore Listings', 'my-listing' );
	}

	public function get_icon() {
		return 'eicon-post';
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
			'27_title',
			[
				'label' => __( 'Title', 'my-listing' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'What are you looking for?', 'my-listing' ),
			]
		);

		$this->add_control(
			'27_active_tab',
			[
				'label' => __( 'Active Tab', 'my-listing' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'search-form',
				'options' => [
					'search-form' => __( 'Filters', 'my-listing' ),
					'categories' => __( 'Categories', 'my-listing' ),
				],
			]
		);

		$this->add_control(
			'27_template',
			[
				'label' => __( 'Template', 'my-listing' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'explore-1',
				'options' => [
					'explore-1' => __( 'Template 1', 'my-listing' ),
					'explore-2' => __( 'Template 2', 'my-listing' ),
					'explore-no-map' => __( 'Template 3', 'my-listing' ),
				],
			]
		);

		$this->add_control(
			'27_finder_columns',
			[
				'label' => __( 'Columns by default', 'my-listing' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'finder-one-columns',
				'options' => [
					'finder-one-columns' => __( 'One', 'my-listing' ),
					'finder-two-columns' => __( 'Two', 'my-listing' ),
					'finder-three-columns' => __( 'Three', 'my-listing' ),
				],
				'condition' => ['27_template' => ['explore-1', 'explore-2']],
			]
		);

		$this->add_control(
			'27_map_skin',
			[
				'label' => __( 'Map Skin', 'my-listing' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'skin1',
				'options' => c27()->get_map_skins(),
			]
		);

		$this->add_control(
			'27_scroll_to_results',
			[
				'label' => __( 'Automatically scroll to results?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'my-listing' ),
				'label_off' => __( 'No', 'my-listing' ),
				'return_value' => 'yes',
				'condition' => ['27_template' => ['explore-2']],
			]
		);

		$this->add_control(
			'27_scroll_wheel',
			[
				'label' => __( 'Zoom map using mouse scroll?', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'my-listing' ),
				'label_off' => __( 'No', 'my-listing' ),
				'return_value' => 'yes',
				'condition' => ['27_template' => ['explore-1', 'explore-2']],
			]
		);

		$this->add_control(
			'27_disable_live_url_update',
			[
				'label' => __( 'Disable live url update?', 'my-listing' ),
				'description' => __( 'When listing filters are used in Explore page, the url in the browser\'s address-bar is updated to reflect their new values. You can use this option to disable that behavior.', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => __( 'Yes', 'my-listing' ),
				'label_off' => __( 'No', 'my-listing' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'27_listing_types',
			[
				'label' => __( 'Listing Types', 'my-listing' ),
				'type' => Controls_Manager::REPEATER,
				'fields' => [
					[
						'name' => 'type',
						'label' => __( 'Select Listing Type', 'my-listing' ),
						'type' => Controls_Manager::SELECT2,
						'options' => c27()->get_posts_dropdown_array([
							'post_type' => 'case27_listing_type',
							'posts_per_page' => -1,
						], 'post_name'),
						'default' => '',
					],
				],
				'title_field' => '{{{ type.toUpperCase() }}}',
			]
		);

		$this->add_control(
			'types_template',
			[
				'label' => _x( 'Display listing types as', 'Elementor > Explore widget settings', 'my-listing' ),
				'type' => Controls_Manager::SELECT2,
				'default' => 'topbar',
				'options' => [
					'topbar' => _x( 'Navbar', 'Elementor > Explore widget settings', 'my-listing' ),
					'dropdown' => _x( 'Dropdown', 'Elementor > Explore widget settings', 'my-listing' ),
				],
			]
		);

		$this->add_control(
		    'categories_tab_heading',
		    [
		        'label' => __( 'Categories Tab', 'my-listing' ),
		        'type' => Controls_Manager::HEADING,
		        'separator' => 'before',
		    ]
		);

		$this->add_control(
			'categories_count',
			[
				'label'   => __( 'Item Count', 'my-listing' ),
				'description'   => __( 'Set the amount of items to show in the "Categories" tab, per listing type. Leave blank to show all.', 'my-listing' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 0,
			]
		);

		$this->add_control(
			'categories_order_by',
			[
				'label'   => __( 'Order By', 'my-listing' ),
				'description'   => __( 'Set which categories should appear first, based on one of these attributes. Default: "Count".', 'my-listing' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'count',
				'options' => [
					'count' => __( 'Item Count', 'my-listing' ),
					'name' => __( 'Item Name', 'my-listing' ),
				],
			]
		);

		$this->add_control(
			'categories_order',
			[
				'label'   => __( 'Order', 'my-listing' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'DESC',
				'options' => [
					'ASC' => __( 'Ascending', 'my-listing' ),
					'DESC' => __( 'Descending', 'my-listing' ),
				],
			]
		);

		$this->add_control(
			'categories_hide_empty',
			[
				'label' => __( 'Hide empty items?', 'my-listing' ),
				'description'   => __( 'Set whether to show or hide items that will yield no results.', 'my-listing' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => __( 'Yes', 'your-plugin' ),
				'label_off' => __( 'No', 'your-plugin' ),
				'return_value' => 'yes',
			]
		);

		$traits->choose_overlay(__( 'Set an overlay for categories.', 'my-listing' ), '27_categories_overlay');

		$this->end_controls_section();
	}


	protected function render( $instance = [] ) {
		c27()->get_section( 'explore', [
			'title' => $this->get_settings('27_title'),
			'active_tab' => $this->get_settings('27_active_tab'),
			'listing_types' => $this->get_settings('27_listing_types'),
			'types_template' => $this->get_settings('types_template'),
			'categories' => [
				'count'      => $this->get_settings( 'categories_count' ),
				'order'      => $this->get_settings( 'categories_order' ),
				'order_by'   => $this->get_settings( 'categories_order_by' ),
				'hide_empty' => $this->get_settings( 'categories_hide_empty' ),
			],
			'scroll_to_results' => 'yes' == $this->get_settings( '27_scroll_to_results' ),
			'scroll_wheel' => 'yes' == $this->get_settings( '27_scroll_wheel' ),
			'disable_live_url_update' => 'yes' === $this->get_settings( '27_disable_live_url_update' ),
			'map_skin' => $this->get_settings('27_map_skin'),
			'template' => $this->get_settings('27_template'),
			'finder_columns' => $this->get_settings('27_finder_columns'),
			'is_edit_mode' => Plugin::$instance->editor->is_edit_mode(),
			'categories_overlay' => [
				'type' => $this->get_settings('27_categories_overlay'),
				'gradient' => $this->get_settings('27_categories_overlay__gradient'),
				'solid_color' => $this->get_settings('27_categories_overlay__solid_color'),
			],
		] );
	}

	protected function content_template() {}
	public function render_plain_content( $instance = [] ) {}
}
