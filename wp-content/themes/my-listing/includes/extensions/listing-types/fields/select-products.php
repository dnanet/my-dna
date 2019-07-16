<?php

namespace MyListing\Ext\Listing_Types\Fields;

class SelectProductsField extends Field {

	public function field_props() {
		$this->props['type'] = 'select-products';
		$this->props['product-type'] = [];
	}

	public function render() {
		$this->getLabelField();
		$this->getKeyField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getAllowedProductTypesField();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
	}
}