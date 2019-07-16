<?php $detail_limit = absint( apply_filters( 'mylisting/type-editor/cover-details/limit', 3 ) ); ?>

<h3 class="section-title">
	Cover details and call-to-actions
	<p>Display important listing information and actions on the cover section of the listing. Up to <?php echo $detail_limit ?> items can be added.</p>
	<p v-if="single.buttons && single.buttons.length">Looking for <a href="#" @click.prevent="setTab('single-page', 'buttons')">cover buttons</a>?</p>
</h3>

<h3 class="section-title" style="clear: both; margin-top: 50px; margin-bottom: 15px;">
	Add cover details
</h3>

<div class="fields-wrapper">
	<div class="fields-column left">
		<h4>Used details <span>Click to edit. Drag &amp; Drop to reorder.</span></h4>
		<draggable v-model="single.cover_details" :options="{group: 'cover-details-list', animation: 100, handle: 'h5'}" @start="drag=true" @end="drag=false" class="fields-draggable" :class="drag ? 'active' : ''">
			<div v-for="detail, key in single.cover_details" class="field cover-detail">
				<h5>
					<span class="prefix">+</span>
					{{ detail.label }}
					<span class="actions">
						<span title="Remove" @click.prevent="coverDetails().remove(detail)"><i class="mi delete"></i></span>
					</span>
				</h5>
				<div class="edit">
					<div class="form-group full-width">
						<label>Label</label>
						<input type="text" v-model="detail.label">
					</div>

					<div class="form-group full-width">
						<label>Field</label>
						<div class="select-wrapper">
							<select v-model="detail.field">
								<option v-for="field in textFields()" :value="field.slug">{{ field.label }}</option>
							</select>
						</div>
					</div>

					<div class="form-group full-width">
						<label>Format</label>
						<div class="select-wrapper">
							<select v-model="detail.format">
								<option value="plain">None</option>
								<option value="number">Number</option>
								<option value="date">Date</option>
								<option value="datetime">Date & Time</option>
								<option value="time">Time</option>
							</select>
						</div>
					</div>

					<div class="form-group full-width">
						<label>Prefix</label>
						<input type="text" v-model="detail.prefix">
					</div>

					<div class="form-group full-width">
						<label>Suffix</label>
						<input type="text" v-model="detail.suffix">
					</div>
				</div>
			</div>
		</draggable>
		<div v-if="single.cover_details.length + single.cover_actions.length >= <?php echo $detail_limit ?>" class="ml-empty">
			<i class="mi error_outline"></i>
			<p>You've reached the maximum number of details allowed (<?php echo $detail_limit ?>).</p>
		</div>
		<div v-else-if="!single.cover_details.length" class="ml-empty">
			<i class="mi playlist_add"></i>
			<p>No details added yet.</p>
		</div>
		<div v-else class="placeholder-fields">
			<div class="field-wrapper">
				<div class="field"><h5>&nbsp;</h5></div>
				<div class="field"><h5>&nbsp;</h5></div>
			</div>
		</div>
	</div>

	<div class="fields-column right">
		<h4>Add new detail</h4>
		<div class="form-group field add-new-field text-center add-new-order" :class="single.cover_details.length + single.cover_actions.length >= <?php echo $detail_limit ?> ? 'ml-overlay-disabled' : ''">
			<button class="btn btn-primary block" @click.prevent="coverDetails().add( 'New detail...' )">Add detail</button>
			<small>or choose a preset</small>
			<button class="btn btn-secondary block" @click.prevent="coverDetails().add( 'Event date', 'job_date', 'date' )">Event date</button>
			<button class="btn btn-secondary block" @click.prevent="coverDetails().add( 'Price', 'price_range', 'plain' )">Price</button>
			<button class="btn btn-secondary block" @click.prevent="coverDetails().add( 'Contact email', 'job_email', 'plain' )">Contact email</button>
		</div>
	</div>
</div>

<!-- <pre>{{ single.cover_details }}</pre> -->

<h3 class="section-title" style="clear: both; margin-top: 50px; margin-bottom: 15px;">
	Add Call-to-Action
</h3>

<div class="fields-wrapper">
	<div class="fields-column left">
		<h4>Used actions <span>Click to edit. Drag &amp; Drop to reorder.</span></h4>
		<draggable v-model="single.cover_actions" :options="{group: 'cover-actions-list', animation: 100, handle: 'h5'}" @start="drag=true" @end="drag=false" class="fields-draggable" :class="drag ? 'active' : ''">
			<div v-for="action, key in single.cover_actions" class="field cover-action">
				<h5>
					<span class="prefix">+</span>
					{{ action.label }}
					<small>{{ action.action }}</small>
					<span class="actions">
						<span title="Remove" @click.prevent="coverActions().remove(action)"><i class="mi delete"></i></span>
					</span>
				</h5>
				<div class="edit">

					<div class="form-group">
						<label>Icon</label>
						<iconpicker v-model="action.icon"></iconpicker>
					</div>

					<div class="form-group full-width">
						<label>Label</label>
						<atwho :data="allFields()" v-model="action.label" template="input"></atwho>
						<p class="form-description">This form item supports the <a href="#" class="cts-show-tip" data-tip="bracket-syntax">field bracket syntax.</a></p>
					</div>

					<div class="form-group full-width" v-if="typeof action.link !== 'undefined'">
						<label>Link to</label>
						<atwho :data="allFields()" v-model="action.link" template="input" placeholder="e.g. `tel:[[work_phone]]`"></atwho>
						<p class="form-description">Create a custom link action. <a href="#" class="cts-show-tip" data-tip="bracket-syntax">Bracket syntax</a> is supported.</p>
					</div>

				</div>
			</div>
		</draggable>
		<div v-if="single.cover_details.length + single.cover_actions.length >= <?php echo $detail_limit ?>" class="ml-empty">
			<i class="mi error_outline"></i>
			<p>You've reached the maximum number of details allowed (<?php echo $detail_limit ?>).</p>
		</div>
		<div v-else-if="!single.cover_actions.length" class="ml-empty">
			<i class="mi playlist_add"></i>
			<p>No details added yet.</p>
		</div>
		<div v-else class="placeholder-fields">
			<div class="field-wrapper">
				<div class="field"><h5>&nbsp;</h5></div>
				<div class="field"><h5>&nbsp;</h5></div>
			</div>
		</div>
	</div>

	<div class="fields-column right">
		<h4>Add new action</h4>
		<div class="form-group field add-new-field text-center add-new-cover-action" :class="single.cover_details.length + single.cover_actions.length >= <?php echo $detail_limit ?> ? 'ml-overlay-disabled' : ''">

			<div v-for="action in blueprints.quick_actions">
				<button class="btn btn-secondary block" @click.prevent="coverActions().add( action.action )">{{ action.label }}</button>
				<br>
			</div>
		</div>
	</div>
</div>

<!-- <pre>{{ single.cover_actions }}</pre> -->