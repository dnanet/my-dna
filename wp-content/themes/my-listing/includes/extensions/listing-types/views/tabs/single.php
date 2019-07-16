<div class="sub-tabs">
	<ul>
		<li :class="currentTab == 'single-page' && currentSubTab == 'style' ? 'active' : ''" class="single-page-tab-style">
			<a @click.prevent="setTab('single-page', 'style')">Cover style</a>
		</li>
		<li :class="currentTab == 'single-page' && currentSubTab == 'cover-details' ? 'active' : ''" class="single-page-tab-cover-details">
			<a @click.prevent="setTab('single-page', 'cover-details')">Cover details</a>
		</li>
		<li :class="currentTab == 'single-page' && currentSubTab == 'quick-actions' ? 'active' : ''" class="single-page-tab-quick-actions">
			<a @click.prevent="setTab('single-page', 'quick-actions')">Quick Actions</a>
		</li>
		<li :class="currentTab == 'single-page' && currentSubTab == 'pages' ? 'active' : ''" class="single-page-tab-pages">
			<a @click.prevent="setTab('single-page', 'pages')">Content &amp; Tabs</a>
		</li>
		<li :class="currentTab == 'single-page' && currentSubTab == 'similar-listings' ? 'active' : ''" class="single-page-tab-similar-listings">
			<a @click.prevent="setTab('single-page', 'similar-listings')">Similar Listings</a>
		</li>
	</ul>
</div>

<div class="tab-content">
	<input type="hidden" v-model="single_page_options_json_string" name="case27_listing_type_single_page_options">

	<div class="single-page-tab-style-content" v-show="currentSubTab == 'style'">
		<?php require_once locate_template( 'includes/extensions/listing-types/views/tabs/single-style.php' ) ?>
	</div>

	<div class="single-page-tab-buttons-content" v-show="currentSubTab == 'buttons'">
		<?php require_once locate_template( 'includes/extensions/listing-types/views/tabs/single-buttons.php' ) ?>
	</div>

	<div class="single-page-tab-quick-actions-content" v-show="currentSubTab == 'quick-actions'">
		<?php require_once locate_template( 'includes/extensions/listing-types/views/tabs/single-quick-actions.php' ) ?>
	</div>

	<div class="single-page-tab-cover-details-content" v-show="currentSubTab == 'cover-details'">
		<?php require_once locate_template( 'includes/extensions/listing-types/views/tabs/single-cover-details.php' ) ?>
	</div>

	<div class="single-page-tab-pages-content" v-show="currentSubTab == 'pages'">
		<?php require_once locate_template( 'includes/extensions/listing-types/views/tabs/single-pages.php' ) ?>
	</div>

	<div class="single-page-tab-similar-listings-content" v-show="currentSubTab == 'similar-listings'">
		<?php require_once locate_template( 'includes/extensions/listing-types/views/tabs/single-similar-listings.php' ) ?>
	</div>
</div>

<!-- <pre>{{ single.menu_items[0] }}</pre> -->
