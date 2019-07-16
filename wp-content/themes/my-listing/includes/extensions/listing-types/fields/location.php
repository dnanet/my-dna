<?php

namespace MyListing\Ext\Listing_Types\Fields;

class LocationField extends Field {

	public function field_props() {
		$this->props['type'] = 'location';
		$this->props['map-skin'] = false;
		$this->props['map-default-location'] = [
			'lat' => 0,
			'lng' => 0,
		];
	}

	public function render() {
		$this->getLabelField();
		$this->getPlaceholderField();
		$this->getDescriptionField();
		$this->getMapSkinField();
		$this->getMapDefaultLocationField();
		$this->getRequiredField();
		$this->getShowInSubmitFormField();
		$this->getShowInAdminField();
	}

	public function getMapSkinField() { ?>
		<div class="form-group">
			<label>Map Skin</label>
			<div class="select-wrapper">
				<select v-model="field['map-skin']">
					<?php foreach ( c27()->get_map_skins() as $skin => $label ): ?>
						<option value="<?php echo esc_attr( $skin ) ?>"><?php echo esc_html( $label ) ?></option>
					<?php endforeach ?>
				</select>
			</div>
		</div>
	<?php }

	public function getMapDefaultLocationField() { ?>
		<div class="form-group">
			<label>Default map location</label>
			<input type="number" min="-90" max="90" v-model="field['map-default-location']['lat']" step="any" style="width: 49%;" placeholder="Latitude">
			<input type="number" min="-180" max="180" v-model="field['map-default-location']['lng']" step="any" style="width: 49%; float: right;" placeholder="Longitude">
			<p class="form-description">When the field is empty, this will be used as the map center.</p>
		</div>
	<?php }
}