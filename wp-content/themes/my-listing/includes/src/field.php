<?php

namespace MyListing\Src;

class Field {
	public
		$value,
		$options;

	public function __construct() {
		$this->value = false;
		$this->options = [];
	}

	public function valid() {
		return c27()->is_valid_field_value( $this->value );
	}
}