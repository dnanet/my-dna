<?php

namespace MyListing\Ext\Listing_Types\Filters;

class WP_Search extends Filter {

	public function filter_props() {
		$this->props['type'] = 'wp-search';
		$this->props['label'] = 'General Search Box';
		$this->props['placeholder'] = 'Enter keywords...';
	}

	public function render() {
		$this->getLabelField();
		$this->getPlaceholderField();
	}
}