<h1>Bracket Syntax</h1>
<p>You can output a listing field's value by wrapping the field name in double brackets.</p>
<p>For example, to get the listing description, use this: <code>[[description]]</code></p>
<p>This will also work for custom fields. If you have a field named "Price per day", then you can get it's value using <code>[[price-per-day]]</code></p>
<p>Type <code>@</code> or <code>[[</code> in the form input to see the list of all available fields.</p>
<hr>
<p>In addition to custom fields, you can also output other listing data, through the following keys:</p>
<table>
	<thead>
		<tr>
			<td>Key</td>
			<td>Outputs</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><code>[[:id]]</code></td>
			<td>Listing ID</td>
		</tr>
		<tr>
			<td><code>[[:url]]</code></td>
			<td>Listing URL</td>
		</tr>
		<tr>
			<td><code>[[:authid]]</code></td>
			<td>Author user ID</td>
		</tr>
		<tr>
			<td><code>[[:authname]]</code></td>
			<td>Author display name</td>
		</tr>
		<tr>
			<td><code>[[:authlogin]]</code></td>
			<td>Author username</td>
		</tr>
		<tr>
			<td><code>[[:lat]]</code></td>
			<td>Latitude</td>
		</tr>
		<tr>
			<td><code>[[:lng]]</code></td>
			<td>Longitude</td>
		</tr>
		<tr>
			<td><code>[[:reviews-average]]</code></td>
			<td>Average review score</td>
		</tr>
		<tr>
			<td><code>[[:reviews-count]]</code></td>
			<td>Number of reviews</td>
		</tr>
		<tr>
			<td><code>[[:reviews-mode]]</code></td>
			<td>Review mode: 5 or 10 stars</td>
		</tr>
		<tr>
			<td><code>[[:currentuserid]]</code></td>
			<td>Logged in user ID</td>
		</tr>
		<tr>
			<td><code>[[:currentusername]]</code></td>
			<td>Logged in user display name</td>
		</tr>
		<tr>
			<td><code>[[:currentuserlogin]]</code></td>
			<td>Logged in user username</td>
		</tr>
		<tr>
			<td><code>[[:date]]</code></td>
			<td>Date listing was created at, formatted to site's date settings</td>
		</tr>
		<tr>
			<td><code>[[:rawdate]]</code></td>
			<td>Date listing was created at, unformatted</td>
		</tr>
	</tbody>
</table>