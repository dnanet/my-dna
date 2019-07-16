<h3 class="section-title">
	Add quick actions
	<p>Help users quickly access important listing details through quick actions. If the list is left empty, a default list of actions will be used instead.</p>
</h3>

<div class="fields-wrapper">
	<div class="fields-column left">
		<h4>Used quick actions <span>Click on an action to edit. Drag &amp; Drop to reorder.</span></h4>
		<draggable v-model="single.quick_actions" :options="{group: 'quick-actions-list', animation: 100, handle: 'h5'}" @start="drag=true" @end="drag=false" class="fields-draggable" :class="drag ? 'active' : ''">
			<div v-for="action, key in single.quick_actions" class="field quick-action">
				<h5>
					<span class="prefix">+</span>
					{{ action.label }}
					<small>{{ action.action }}</small>
					<span class="actions">
						<span title="Remove" @click.prevent="quickActions().remove(action)"><i class="mi delete"></i></span>
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
		<div v-if="!single.quick_actions.length" class="ml-empty">
			<i class="mi playlist_add"></i>
			<p>No actions added yet. Choose a preset from the list of available actions on the right.</p>
		</div>
		<div v-else class="placeholder-fields">
			<div class="field-wrapper">
				<div class="field"><h5>&nbsp;</h5></div>
				<div class="field"><h5>&nbsp;</h5></div>
				<div class="field"><h5>&nbsp;</h5></div>
				<div class="field"><h5>&nbsp;</h5></div>
			</div>
		</div>
	</div>

	<div class="fields-column right">
		<h4>Add new quick action</h4>
		<div class="form-group field add-new-field text-center add-new-quick-action">

			<div v-for="action in blueprints.quick_actions">
				<button class="btn btn-secondary block" @click.prevent="quickActions().add( action.action )">{{ action.label }}</button>
				<br>
			</div>
		</div>
	</div>
</div>

<!-- <pre>{{ single.quick_actions }}</pre> -->