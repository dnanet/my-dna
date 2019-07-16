<?php

namespace MyListing\Ext\Listing_Types\Filters;

class Location extends Filter {

	public function filter_props() {
		$this->props['type'] = 'location';
		$this->props['label'] = 'Listing Location';
		$this->props['placeholder'] = 'Enter location...';
	}

	public function render() {
		$this->getLabelField();
		$this->getPlaceholderField();
	}
}