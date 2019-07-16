<h3 class="section-title">
	Similar listings
	<p>You can optionally display a list of similar listings in the single listing page. This section will appear at the end of the page, below the current listing information.</p>
	<div class="form-group">
		<label>
			<input type="checkbox" v-model="single.similar_listings.enabled">
			Enable similar listings
		</label>
	</div>
</h3>

<div class="similar-listings-settings" :class="!single.similar_listings.enabled?'ml-overlay-disabled':''">
	<div class="section-title">
		Matching similar listings
		<p>Determine what should classify as a similar listing to the currently active one, based on the following attributes.</p>
		<div class="form-group">
			<label>
				<input type="checkbox" v-model="single.similar_listings.match_by_type">
				Must belong to the same listing type.
			</label>
		</div>

		<div class="form-group">
			<label>
				<input type="checkbox" v-model="single.similar_listings.match_by_category">
				Must have at least one category in common.
			</label>
		</div>

		<div class="form-group">
			<label>
				<input type="checkbox" v-model="single.similar_listings.match_by_tags">
				Must have at least one tag in common.
			</label>
		</div>

		<div class="form-group">
			<label>
				<input type="checkbox" v-model="single.similar_listings.match_by_region">
				Must belong to the same region (Regions taxonomy).
			</label>
		</div>
	</div>

	<div class="section-title">
		Displaying similar listings
		<p></p>
		<div class="form-group">
			<label>Order listings by</label>
			<div class="select-wrapper" style="display: inline-block; width: 130px;">
				<select v-model="single.similar_listings.orderby">
					<option value="priority">Priority</option>
					<option value="rating">Rating</option>
					<option value="proximity">Proximity</option>
				</select>
			</div>
		</div>

		<div class="form-group" v-show="single.similar_listings.orderby === 'proximity'">
			<br>
			<label>Listing must be within radius (in kilometers)</label>
			<input type="number" v-model="single.similar_listings.max_proximity" style="display: inline-block; width: 70px;">
		</div>

		<div class="form-group">
			<br>
			<label>Number of listings to show</label>
			<input type="number" v-model="single.similar_listings.listing_count" style="display: inline-block; width: 70px;">
		</div>
	</div>
</div>