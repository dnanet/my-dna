<?php defined( 'ABSPATH' ) or exit; ?>

<h1>Permalink Settings</h1>
<h3>How can I modify listing permalinks?</h3>
<p>The listing permalink structure can be modified in <code>WP Admin > Settings > Permalinks</code>, under the <code>Listing Base</code> setting.</p>
<p>The default structure is <code>https://sitename/listings/listing-name</code> &mdash; however this can be modified to display other data, such as the listing type, region, and category.</p>
<p>The <code>/listings/</code> base in the url is not mandatory. A common example is to replace it with the listing type. To achieve that, simply set <code>Listing Base</code> to <code>%listing_type%</code>.</p>

<h3>What tags can I use?</h3>
<p>The currently supported tags are:</p>
<ul>
	<li><code>%listing_type%</code> &mdash; Displays the listing type</li>
	<li><code>%listing_category%</code> &mdash; Displays the listing category, or the first selected category in case multiple categories are enabled.</li>
	<li><code>%listing_region%</code> &mdash; Displays the listing region, or the first selected region in case multiple regions are enabled.</li>
</ul>
<p>Multiple tags can be used in the listing base. The below examples are all valid:</p>
<ul>
	<li><code>listings/%listing_type%/</code></li>
	<li><code>listings/%listing_region%/%listing_category%</code></li>
	<li><code>%listing_type%/%listing_category%</code></li>
	<li><code>%listing_region%</code></li>
	<li><code>explore/%listing_category%</code></li>
</ul>

<h3>How can I modify the value returned by <code>%listing_type%</code> tag?</h3>
<p>This value can be modified in the listing type editor, which you can access in <code>WP Admin > Listing Types</code>. Go to the <code>General</code> tab in the editor, and look for the <code>Permalink</code> setting.</p>
