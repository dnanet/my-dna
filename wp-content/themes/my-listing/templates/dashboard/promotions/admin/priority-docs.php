<?php defined( 'ABSPATH' ) OR exit ?>

<h1>Listing Priority</h1>
<h3>How can listings gain higher priority?</h3>
<p>Listings can be given higher priority in search results in the following ways:</p>
<ol>
	<li>When submitting a listing, the user buy a premium package which automatically marks the listing as <strong>Featured</strong>, indefinitely.</li>
	<li>When the user buys a promotion package, which marks the listing as <strong>Promoted</strong>, for a set amount of time.</li>
	<li>The site owner manually assigning the priority to a listing.</li>
</ol>
<p>By default, listing priority is set to <strong>Normal</strong>, which means no extra priority is given to them.</p>

<h3>What are the different priority levels?</h3>
<p>The difference between <strong>Normal</strong>, <strong>Featured</strong>, and <strong>Promoted</strong> listings is as follows.</p>
<ul>
	<li><strong>Promoted</strong> listings have the highest importance in search results. They will also appear ahead of featured listings.</li>
	<li><strong>Featured</strong> listings are a level below promoted listings in importance, but will be shown ahead of normal listings.</li>
	<li><strong>Normal</strong> priority listings will appear below promoted and featured listings. They aren't given any special importance.</li>
</ul>

<h3>How does custom priority work?</h3>
<p>
In addition to this, when the site owner edits a listing through the backend form, there's another setting named <strong>Custom priority</strong>.
This is a special setting which can be useful to manually prioritise a listing above all others, even ahead of Promoted ones.
</p>
<p>It accepts a numeric value. Higher value means higher priority in search results. A value of <code>0</code> would mean no priority, <code>1</code> would be equal to featured listings, <code>2</code> would be equal to promoted listings, and anything higher than that would give it priority over every other listing.</p>