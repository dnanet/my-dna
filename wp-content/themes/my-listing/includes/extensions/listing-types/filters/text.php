<?php

namespace MyListing\Ext\Listing_Types\Filters;

class Text extends Filter {

	public function filter_props() {
		$this->props['type'] = 'text';
		$this->props['label'] = 'Text Search';
		$this->props['placeholder'] = '';
		$this->props['show_field'] = '';
		$this->props['allowed_fields'] = ['text', 'texteditor', 'wp-editor', 'checkbox', 'radio', 'select', 'multiselect', 'textarea', 'date', 'email', 'url', 'number'];
	}

	public function render() {
		$this->getLabelField();
		$this->getPlaceholderField();
		$this->getSourceField();
	}
}