<h3 class="section-title">
	Create and organize listing content
	<span class="subtitle">Not sure what's this? <a href="https://27collective.net/files/mylisting/docs/#listings-single" target="_blank">View the docs</a>. To edit a page, click on it.</span>
</h3>

<draggable v-model="single.menu_items" :options="{group: 'single-menu', animation: 100, draggable: '.field'}" @start="drag=true" @end="drag=false" class="fields-draggable menu-list" :class="drag ? 'active' : ''">
	<div v-for="menu_item in single.menu_items" class="field" @click="state.single.active_menu_item = menu_item">
		{{ menu_item.label }}
	</div>

	<div class="add-new">
		<div class="form-group">
			<div class="select-wrapper">
				<select @change="addMenuItem" id="single-add-menu-item">
					<option value="none" selected="selected">Add new tab</option>
					<option value="main">Profile</option>
					<option value="comments">Comments/Reviews</option>
					<option value="related_listings">Related Listings</option>
					<option value="store">Store</option>
					<option value="bookings">Bookings</option>
					<option value="custom">Custom</option>
				</select>
			</div>
		</div>
	</div>
</draggable>

<div class="form-group edit-page" v-if="state.single.active_menu_item">
	<div class="cover-button-wrapper">
		<div class="cover-button-options">

			<div class="form-group">
				<label>Label</label>
				<input type="text" v-model="state.single.active_menu_item.label">

				<?php c27()->get_partial('admin/input-language', ['object' => 'state.single.active_menu_item.label_l10n']) ?>
			</div>

			<div class="form-group">
				<label>URL slug</label>
				<input type="text" :value="state.single.active_menu_item.slug" @input="state.single.active_menu_item.slug = slugify( $event.target.value )" :placeholder="slugify( state.single.active_menu_item.label )">
				<p class="form-description">This value can be appended to the listing url to link directly to this tab.</p>
			</div>

			<div class="form-group" v-if="state.single.active_menu_item.page === 'store'">
				<label>Hide tab if there are no products? <input type="checkbox" v-model="state.single.active_menu_item.hide_if_empty"></label>
			</div>

			<div class="form-group" v-if="['main', 'custom'].indexOf(state.single.active_menu_item.page) !== -1">
				<label>Layout</label>
				<div class="select-wrapper">
					<select v-model="state.single.active_menu_item.template">
						<option value="masonry">Masonry (Two columns)</option>
						<option value="two-columns">Two Columns</option>
						<option value="content-sidebar">Two thirds / One third</option>
						<option value="sidebar-content">One third / Two thirds</option>
						<option value="full-width">Single column</option>
					</select>
				</div>
			</div>

			<div class="form-group" v-if="state.single.active_menu_item.page == 'store'">
				<label>Display products from field:</label>
				<div class="select-wrapper">
					<select v-model="state.single.active_menu_item.field">
						<option v-for="field in fieldsByType(['select-products'])" :value="field.slug">{{ field.label }}</option>
					</select>
				</div>
			</div>

			<div class="form-group" v-if="state.single.active_menu_item.page == 'related_listings'">
				<label>Of Listing Type:</label>
				<div class="select-wrapper">
					<select v-model="state.single.active_menu_item.related_listing_type">
						<option value="">-- All --</option>
						<?php foreach ($designer::$store['listing-types'] as $listing_type): ?>
							<option value="<?php echo $listing_type->post_name ?>"><?php echo $listing_type->post_title ?></option>
						<?php endforeach ?>
					</select>
				</div>
			</div>

			<div class="form-group" v-if="state.single.active_menu_item.page == 'bookings'">
				<label>Booking Service Provider:</label>
				<div class="select-wrapper">
					<select v-model="state.single.active_menu_item.provider">
						<option value="basic-form">Basic Form</option>
						<option value="timekit">Timekit</option>
					</select>
				</div>
			</div>

			<div class="form-group" v-if="state.single.active_menu_item.page == 'bookings' && state.single.active_menu_item.provider == 'basic-form'">
				<label>Submission sends email to:</label>
				<div class="select-wrapper">
					<select v-model="state.single.active_menu_item.field">
						<option v-for="field in fieldsByType(['email'])" :value="field.slug">{{ field.label }}</option>
					</select>
				</div>
			</div>

			<div class="form-group" v-if="state.single.active_menu_item.page == 'bookings' && state.single.active_menu_item.provider == 'basic-form'">
				<label>Contact Form ID:</label>
				<input type="text" v-model="state.single.active_menu_item.contact_form_id">
			</div>

			<div class="form-group" v-if="state.single.active_menu_item.page == 'bookings' && state.single.active_menu_item.provider == 'timekit'">
				<label>TimeKit Widget ID:</label>
				<div class="select-wrapper">
					<select v-model="state.single.active_menu_item.field">
						<option v-for="field in fieldsByType(['text'])" :value="field.slug">{{ field.label }}</option>
					</select>
				</div>
			</div>

			<div class="footer form-group">
				<label>&nbsp;</label>
				<button @click.prevent="state.single.active_menu_item = null" class="btn btn-primary btn-xs">Save</button>
				<button @click.prevent="deleteMenuItem(state.single.active_menu_item)" class="btn btn-plain btx-xs"><i class="mi delete"></i>Delete menu item</button>
			</div>
		</div>
	</div>
</div>

<div v-if="state.single.active_menu_item && (state.single.active_menu_item.page == 'main' || state.single.active_menu_item.page == 'custom')" class="page-layout-wrapper">
	<div class="page-layout">
		<div class="fields-wrapper page-layout-columns" :class="[state.single.active_menu_item.template, state.single.active_menu_item.sidebar.length ? 'sidebar-filled' : 'sidebar-empty']">
			<h5>Edit content blocks</h5>

			<?php foreach ( ['layout', 'sidebar'] as $key => $column ):
				$two_column_condition = sprintf( 'v-if="%s.indexOf(state.single.active_menu_item.template) !== -1"', "['two-columns', 'content-sidebar', 'sidebar-content']" );
				$two_column_condition = '';
				?>
				<div class="blocks-column" <?php echo $column == 'sidebar' ? $two_column_condition : '' ?> :class="state.single.active_menu_item.<?php echo $column ?>.length ? 'filled' : 'empty'">
					<draggable v-model="state.single.active_menu_item.<?php echo $column ?>" :options="{group: 'layout-blocks', animation: 100, draggable: '.field', filter: '.no-drag', handle: 'h5'}" @start="drag=true" @end="drag=false" class="fields-draggable">
						<div v-for="block in state.single.active_menu_item.<?php echo $column ?>" class="field toggleable" :class="'block-type-' + block.type + ' ' + (block === state.single.active_block ? 'open' : '')">
							<h5 @click="state.single.active_block = ( block !== state.single.active_block ) ? block : null">
								<span class="prefix">{{ block === state.single.active_block ? '-' : '+' }}</span>
								{{block.title}}
								<small>({{block.type}})</small>
								<span class="actions">
									<span title="Move" class="drag-to-add" <?php echo $two_column_condition ?> @click.stop="moveBlock(block, '<?php echo $column ?>')"><i class="mi compare_arrows"></i></span>
									<span title="Delete this field" @click.prevent="deleteBlock(block, '<?php echo $column ?>')"><i class="mi delete"></i></span>
								</span>
							</h5>

							<div class="options edit">
								<div class="form-group">
									<label>Label</label>
									<input type="text" v-model="block.title">
								</div>
								<div class="form-group" v-if="typeof block.show_field !== 'undefined'">
									<label>Use Field:</label>
									<div class="select-wrapper">
										<select v-model="block.show_field">
											<option v-for="field in fieldsByType(block.allowed_fields)" :value="field.slug">{{ field.label }}</option>
										</select>
									</div>
								</div>

								<div class="form-group full-width" v-if="typeof block.content !== 'undefined'">
									<label>Content (Type @ or [[ to see list of all fields you can use.). <a href="#" class="cts-show-tip" data-tip="bracket-syntax">More info on this.</a></label>
									<atwho :data="fieldsByType(block.allowed_fields)" v-model="block.content" placeholder="Example use:
&lt;iframe src=&quot;https://facebook.com/[[facebook-id]]&quot; title=&quot;[[listing-name]]&quot;&gt;&lt;/iframe&gt;
or
[show_tweets username=&quot;[[twitter-username]]&quot;]"></atwho>

									<!-- <pre>{{ block }}</pre> -->
								</div>

								<div v-if="block.options" v-for="option in block.options" class="form-group" :class="option.type" :style="option.type == 'textarea' ? 'width: 100%; float: none;' : ''">
									<div v-if="option.type == 'select'" class="select-option">
										<label>{{ option.label }}</label>
										<div class="select-wrapper">
											<select v-model="option.value">
												<option v-for="(choice_label, choice) in fieldsByTypeFormatted(option.choices)" :value="choice">{{ choice_label }}</option>
											</select>
										</div>
									</div>

									<div v-if="option.type == 'multiselect'" class="select-option">
										<label>{{ option.label }}</label>
										<select v-model="option.value" multiple="multiple">
											<option v-for="(choice_label, choice) in fieldsByTypeFormatted(option.choices)" :value="choice">{{ choice_label }}</option>
										</select>
									</div>

									<div v-if="option.type == 'number'" class="select-option">
										<label>{{ option.label }}</label>
										<input type="number" v-model="option.value">
									</div>

									<div v-if="option.type == 'textarea'">
										<label>{{ option.label }}</label>
										<textarea rows="10" v-model="option.value"></textarea>
									</div>

									<div v-if="option.type == 'repeater'" class="repeater-option">
										<label>{{ option.label }}</label>
										<draggable v-model="option.value" :options="{group: 'repeater', animation: 100, handle: 'h5'}" @start="drag=true" @end="drag=false" class="fields-draggable buttons-list menu-list" :class="drag ? 'active' : ''">
											<div v-for="(row, row_id) in option.value" class="repeater-row field">
												<h5>
													<span class="prefix">+</span>
													{{ row.label }}
													<span class="actions">
													<span title="Delete this row" @click.prevent="option.value.splice(row_id, 1)"><i class="mi delete"></i></span>
													</span>
												</h5>
												<div class="edit">
													<div class="form-group" v-if="option.fields.indexOf('icon') > -1">
														<label>Icon</label>
														<iconpicker v-model="row.icon"></iconpicker>
													</div>

													<div class="form-group" v-if="option.fields.indexOf('label') > -1">
														<label>Label</label>
														<input type="text" v-model="row.label">
													</div>

													<div class="form-group" v-if="option.fields.indexOf('show_field') > -1">
														<label>Field to use</label>
														<div class="select-wrapper">
															<select v-model="row.show_field">
																<option value="" disabled="disabled">Use Field:</option>
																<option v-for="field in fieldsByType(['text', 'texteditor', 'wp-editor', 'checkbox', 'radio', 'select', 'multiselect', 'textarea', 'date', 'time', 'datetime', 'work-hours', 'email', 'url', 'number', 'location', 'file'])" :value="field.slug">{{ field.label }}</option>
																<option value="__listing_rating">Rating</option>
															</select>
														</div>
													</div>

													<div class="form-group" v-if="option.fields.indexOf('content') > -1">
														<label>Content</label>
														<input type="text" v-model="row.content">
													</div>
												</div>
											</div>

											<br>

											<a class="btn btn-primary" @click.prevent="option.value.push({label: '', show_field: '', content: '[[field]]', icon: ''})">Add row</a>
										</draggable>
									</div>
								</div>

								<hr>

								<div class="advanced-settings">
									<div class="form-group">
										<label>Icon</label>
										<iconpicker v-model="block.icon"></iconpicker>
									</div>

									<div class="form-group">
										<label>Custom Block ID</label>
										<input type="text" v-model="block.id">
									</div>

									<div class="form-group">
										<label>Custom Block Classes</label>
										<input type="text" v-model="block.class">
									</div>

									<?php // @todo: Refactor field and block visibility to use the same source code. ?>
									<div class="field-visibility" v-show="block.type === 'raw'">
										<div class="form-group full-width">
											<label><input type="checkbox" v-model="block.conditional_logic"> Enable package visibility</label>
										</div>

										<div class="visibility-rules" v-show="block.conditional_logic">
											<label>Show this block if</label>
											<p></p>
											<div class="conditions">
												<div class="condition-group" v-for="conditionGroup, groupKey in block.conditions" v-if="conditionGroup.length">
													<div class="or-divider">
														or
													</div>
													<div class="condition" v-for="condition in conditionGroup">
														<div class="form-group">
															<select v-model="condition.key">
																<option value="__listing_package">Listing Package</option>
															</select>
														</div>

														<div class="form-group">
															<select v-model="condition.compare">
																<option value="==">is equal to</option>
															</select>
														</div>

														<div class="form-group">
															<div class="select-wrapper">
																<select v-model="condition.value">
																	<option value="--none--">No Package</option>
																	<option v-for="package in settings.packages.used" :value="package.package">{{ packages().getPackageTitle(package) }}</option>
																</select>
															</div>
														</div>

														<span class="actions" @click="conditions().deleteConditionGroup(conditionGroup, block)">
															<span><i class="mi close"></i></span>
														</span>
													</div>
												</div>

												<button class="btn btn-primary" @click.prevent="conditions().addOrCondition(block)">Add Rule</button>
												<!-- <pre>{{ block }}</pre> -->
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</draggable>
				</div>
			<?php endforeach ?>

			<div style="clear: both;"></div>

			<div class="form-group add-new add-new-block">
				<label>Add a new block</label>
				<div class="select-wrapper">
					<select id="single-add-block">
						<option value="none" selected="selected">Select block type</option>
						<option v-for="block in blueprints.layout_blocks" :value="block.type">{{ block.title }}</option>
					</select>
				</div>
				<button class="btn btn-primary pull-right" @click.prevent="addBlock">Add block</button>
			</div>
		</div>
	</div>
</div>