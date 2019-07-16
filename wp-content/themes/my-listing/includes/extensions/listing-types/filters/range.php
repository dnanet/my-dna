<?php

namespace MyListing\Ext\Listing_Types\Filters;

class Range extends Filter {

	public function filter_props() {
		$this->props['type'] = 'range';
		$this->props['label'] = 'Range';
		$this->props['show_field'] = '';
		$this->props['allowed_fields'] = ['text', 'number'];

		$this->props['options'][] = [
			'label' => 'Type',
			'name' => 'type',
			'type' => 'select',
			'value' => 'range',
			'choices' => [
				'range' => 'Range Slider',
				'simple' => 'Single Slider',
			],
		];

		$this->props['options'][] = [
			'label' => 'Step size',
			'name' => 'step',
			'type' => 'number',
			'value' => 1,
		];

		$this->props['options'][] = [
			'label' => 'Prefix',
			'name' => 'prefix',
			'type' => 'text',
			'value' => '',
		];

		$this->props['options'][] = [
			'label' => 'Suffix',
			'name' => 'suffix',
			'type' => 'text',
			'value' => '',
		];

		$this->props['options'][] = [
			'label' => 'Format Value',
			'name' => 'format_value',
			'type' => 'checkbox',
			'value' => 0,
		];
	}

	public function render() {
		$this->getLabelField();
		$this->getSourceField();
	}
}