<?php

namespace MyListing\Ext\Listing_Types\Fields;

class TextField extends Field {

	public function field_props() {
		$this->props['type'] = 'text';
	}

	public function render() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
	}
}