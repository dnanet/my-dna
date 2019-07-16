<h3 class="section-title">
	Customize the cover style
	<span class="subtitle">Not sure what's this? <a href="https://27collective.net/files/mylisting/docs/#listings-single" target="_blank">View the docs</a>.</span>
</h3>

<div class="form-group cover-type">
	<label><input type="radio" v-model="single.cover.type" value="image"> Cover image</label>
	<label><input type="radio" v-model="single.cover.type" value="gallery"> Gallery slider</label>
	<label><input type="radio" v-model="single.cover.type" value="none"> None</label>
	<div class="bg" :class="single.cover.type">
		<div class="item"></div>
		<div class="item"></div>
		<div class="item"></div>
		<div class="cover-footer">
			<div class="profile-picture"></div>
			<div class="menu-pages">
				<div class="page"></div>
				<div class="page"></div>
				<div class="page"></div>
			</div>
		</div>
	</div>
</div>