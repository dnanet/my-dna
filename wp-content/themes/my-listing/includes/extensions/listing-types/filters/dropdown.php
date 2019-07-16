<?php

namespace MyListing\Ext\Listing_Types\Filters;

class Dropdown extends Filter {

	public function filter_props() {
		$this->props['type'] = 'dropdown';
		$this->props['label'] = 'Dropdown';
		$this->props['show_field'] = '';
		$this->props['allowed_fields'] = ['text', 'checkbox', 'radio', 'select', 'multiselect', 'date', 'term-multiselect', 'term-select', 'related-listing', 'number', 'location'];

		$this->props['options'][] = [
			'label' => 'Placeholder',
			'name' => 'placeholder',
			'type' => 'text',
			'value' => '',
		];

		$this->props['options'][] = [
			'label' => 'Count',
			'name' => 'count',
			'type' => 'number',
			'value' => 8,
		];

		$this->props['options'][] = [
			'label' => 'Order by',
			'name' => 'order_by',
			'type' => 'select',
			'value' => 'count',
			'choices' => [
				'name' => 'Name',
				'count' => 'Count',
				'meta_value' => 'Value',
				'meta_value_num' => 'Numerical value',
				'include' => 'Include Order',
			],
		];

		$this->props['options'][] = [
			'label' => 'Order',
			'name' => 'order',
			'type' => 'select',
			'value' => 'DESC',
			'choices' => [
				'ASC' => 'Ascending',
				'DESC' => 'Descending',
			],
		];

		$this->props['options'][] = [
			'label' => 'Hide empty terms?',
			'description' => 'Terms that don\'t yield any results will be hidden. Currently only works for taxonomy terms (categories, regions, tags, etc.)',
			'name' => 'hide_empty',
			'type' => 'checkbox',
			'value' => 1,
		];

		$this->props['options'][] = [
			'label' => 'Multiselect?',
			'name' => 'multiselect',
			'type' => 'checkbox',
			'value' => 1,
			'form' => 'advanced',
		];

		$this->props['options'][] = [
			'label' => 'Multiselect behavior',
			'name' => 'behavior',
			'type' => 'select',
			'value' => 'any',
			'description' => 'Determine the search logic to be used when selecting multiple terms.',
			'choices' => [
				'any' => 'Show listings matching ANY of the selected terms',
				'all' => 'Show listings matching ALL of the selected terms',
			],
		];
	}

	public function render() {
		$this->getLabelField();
		$this->getSourceField();
	}
}