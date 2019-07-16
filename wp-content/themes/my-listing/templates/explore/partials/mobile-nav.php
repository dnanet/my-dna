<div class="explore-mobile-nav">
	<ul class="nav nav-tabs">
		<li class="show-filters" :class="state.mobileTab === 'filters' ? 'active' : ''">
			<a href="#" @click.prevent="state.mobileTab = 'filters'"><i class="mi search"></i></a>
		</li>
		<li class="show-results" :class="state.mobileTab === 'results' ? 'active' : ''">
			<a href="#" @click.prevent="state.mobileTab = 'results';"><i class="mi format_list_bulleted"></i></a>
		</li>

		<?php if ($data['template'] !== 'explore-no-map'): ?>
			<li class="show-map" :class="state.mobileTab === 'map' ? 'active' : ''" v-if="map">
				<a href="#" @click.prevent="state.mobileTab = 'map'"><i class="mi map"></i></a>
			</li>
		<?php endif ?>
	</ul>
</div>