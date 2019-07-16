<?php

namespace MyListing\Ext\Listing_Types\Filters;

class Checkboxes extends Filter {

	public function filter_props() {
		$this->props['type'] = 'checkboxes';
		$this->props['label'] = 'Checkboxes';
		$this->props['show_field'] = '';
		$this->props['form'] = 'advanced';
		$this->props['allowed_fields'] = ['term-multiselect', 'term-select', 'text', 'select', 'multiselect', 'checkbox', 'radio', 'number', 'location'];

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
			'label' => 'Hide empty?',
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