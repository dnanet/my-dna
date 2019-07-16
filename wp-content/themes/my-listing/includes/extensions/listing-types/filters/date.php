<?php

namespace MyListing\Ext\Listing_Types\Filters;

class Date extends Filter {

	public function filter_props() {
		$this->props['type'] = 'date';
		$this->props['label'] = 'Date';
		$this->props['show_field'] = '';
		$this->props['allowed_fields'] = ['date'];

		$this->props['options'][] = [
			'label' => 'Type',
			'name' => 'type',
			'type' => 'select',
			'value' => 'exact',
			'choices' => [
				'exact' => 'Exact Date',
				'range' => 'Date Range',
			],
		];

		$this->props['options'][] = [
			'label' => 'Format',
			'name' => 'format',
			'type' => 'select',
			'value' => 'ymd',
			'choices' => [
				'ymd' => 'Year + Month + Day',
				'year' => 'Years Only',
			],
		];
	}

	public function render() {
		$this->getLabelField();
		$this->getSourceField();
	}
}