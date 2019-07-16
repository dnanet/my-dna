<?php

namespace MyListing\Ext\Listing_Types\Filters;

class Proximity extends Filter {

	public function filter_props() {
		$this->props['type'] = 'proximity';
		$this->props['label'] = 'Proximity';

		$this->props['options'][] = [
			'label' => 'Units',
			'name' => 'units',
			'type' => 'select',
			'value' => 'metric',
			'choices' => [
				'metric' => 'Kilometres',
				'imperial' => 'Miles',
			],
		];

		$this->props['options'][] = [
			'label' => 'Step size',
			'name' => 'step',
			'type' => 'number',
			'value' => 1,
		];

		$this->props['options'][] = [
			'label' => 'Max value',
			'name' => 'max',
			'type' => 'number',
			'value' => 500,
		];

		$this->props['options'][] = [
			'label' => 'Default value',
			'name' => 'default',
			'type' => 'number',
			'value' => 10,
		];
	}

	public function render() {
		$this->getLabelField();
	}
}