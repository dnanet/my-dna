<?php

namespace MyListing\Includes;

class Strings {
	use \MyListing\Src\Traits\Instantiatable;

	public $strings, $text_domains, $theme;

	public $countries;

	public function __construct() {
		add_action( 'after_setup_theme', function() {
			$this->theme = get_translations_for_domain( 'my-listing' );
			$this->strings = $this->get_strings();
			$this->text_domains = array_keys( $this->strings );

			add_filter( 'gettext', function( $translated, $text, $domain ) {
				if ( in_array( $domain, $this->text_domains ) && isset( $this->strings[$domain][$text] ) ) {
					return $this->translate( $this->strings[$domain][$text] );
				}

				return $translated;
			}, 0, 15 );

			add_filter( 'gettext_with_context', function( $translated, $text, $context, $domain ) {
				if ( in_array( $domain, $this->text_domains ) && isset( $this->strings[$domain][$text] ) ) {
					return $this->translate( $this->strings[$domain][$text] );
				}

				return $translated;
			}, 0, 20 );
		});
	}

	private function translate( $text ) {
		if ( is_array( $text ) ) {
			return vsprintf( $this->theme->translate[ $text[0] ], $text[1] );
		}

		return $this->theme->translate( $text ) ? : $text;
	}

	private function get_strings()
	{
		return [
			'wp-job-manager' => [
				'Job' => __('Listing', 'my-listing'),
				'Jobs' => __('Listings', 'my-listing'),
				'job' => __( 'listing', 'my-listing' ),
				'jobs' => __( 'listings', 'my-listing' ),
				'Job Listings' => __('Listings', 'my-listing'),
				'Job category' => __( 'Category', 'my-listing' ),
				'Job categories' => __( 'Categories', 'my-listing' ),
				'Job Categories' => __( 'Categories', 'my-listing' ),
				'job-category' => __( 'category', 'my-listing' ),
				'Job type' => __( 'Type', 'my-listing' ),
				'Job types' => __( 'Types', 'my-listing' ),
				'Job Types' => __( 'Types', 'my-listing' ),
				'Job base' => __( 'Listing base', 'my-listing' ),
				'Job category base' => __( 'Listing category base', 'my-listing' ),
				'Job type base' => __( 'Listing type base', 'my-listing' ),
				'job-type' => __( 'listing-type', 'my-listing' ),
				'Jobs will be shown if within ANY selected category' => __( 'Listings will be shown if within ANY selected category', 'my-listing' ),
				'Jobs will be shown if within ALL selected categories' => __( 'Listings will be shown if within ALL selected categories', 'my-listing' ),
				'Position filled?' => __( 'Listing filled?', 'my-listing' ),
				'A video about your company' => __( 'A video about your listing', 'my-listing' ),
				'Job Submission' => __( 'Listing Submission', 'my-listing' ),
				'Submit Job Form Page' => __( 'Submit Listing Form Page', 'my-listing' ),
				'Job Dashboard Page' => __( 'Listing Dashboard Page', 'my-listing' ),
				'Job Listings Page' => __( 'Listings Page', 'my-listing' ),
				'Add a job via the back-end' => __( 'Add a listing via the back-end', 'my-listing' ),
				'Add a job via the front-end' => __( 'Add a listing via the front-end', 'my-listing' ),
				'Find out more about the front-end job submission form' => __( 'Find out more about the front-end listing submission form', 'my-listing' ),
				'View submitted job listings' => __( 'View submitted listings', 'my-listing' ),
				'Add the [jobs] shortcode to a page to list jobs' => __( 'Add the [jobs] shortcode to a page to list listings', 'my-listing' ),
				'View the job dashboard' => __( 'View the listing dashboard', 'my-listing' ),
				'Find out more about the front-end job dashboard' => __( 'Find out more about the front-end listing dashboard', 'my-listing' ),
				'Job Title' => __( 'Listing Name', 'my-listing' ),
				'Apply for job' => __( 'Apply', 'my-listing' ),
				'To apply for this job please visit the following URL: <a href="%1$s" target="_blank">%1$s &rarr;</a>' => __( 'To apply please visit the following URL: <a href="%1$s" target="_blank">%1$s &rarr;</a>', 'my-listing' ),
				'To apply for this job <strong>email your details to</strong> <a class="job_application_email" href="mailto:%1$s%2$s">%1$s</a>' => __( 'To apply <strong>email your details to</strong> <a class="job_application_email" href="mailto:%1$s%2$s">%1$s</a>', 'my-listing' ),
				'Anywhere' => __( '&mdash;', 'my-listing' ),
				'Job Submission Form' => __( 'Listing Submission Form', 'my-listing' ),
			],

			'wp-job-manager-wc-paid-listings' => [
				'Choose a package before entering job details' => __( 'Choose a package before entering listing details', 'my-listing' ),
				'Choose a package after entering job details' => __( 'Choose a package after entering listing details', 'my-listing' ),
				'Job Package' => __( 'Listing Package', 'my-listing' ),
				'Job Package Subscription' => __( 'Listing Package Subscription', 'my-listing' ),
				'Job Listing' => __( 'Listing', 'my-listing' ),
				'Job listing limit' => __( 'Listing limit', 'my-listing' ),
				'Job listing duration' => __( 'Listing duration', 'my-listing' ),
				'The number of days that the job listing will be active.' => __( 'The number of days that the listing will be active', 'my-listing' ),
				'Feature job listings?' => __( 'Feature listings?', 'my-listing' ),
				'Feature this job listing - it will be styled differently and sticky.' => __( 'Feature this listing - it will be styled differently and sticky.', 'my-listing' ),
				'My Job Packages' => __( 'My Listing Packages', 'my-listing' ),
				'Jobs Remaining' => __( 'Listings Remaining', 'my-listing' ),
			],

			'jwapl' => [
				'Job Package' => __( 'Listing Package', 'my-listing' ),
				'Job Package Subscription' => __( 'Listing Package Subscription', 'my-listing' ),
			],
		];
	}

	public function get_datepicker_locale() {
		return [
			'format'           => apply_filters( 'mylisting/datepicker/date_format', 'DD MMMM, YY' ),
			'timeFormat'       => apply_filters( 'mylisting/datepicker/time_format', 'h:mm A' ),
			'dateTimeFormat'   => apply_filters( 'mylisting/datepicker/datetime_format', 'DD MMMM, YY, h:mm A' ),
			'timePicker24Hour' => apply_filters( 'mylisting/datepicker/enable_24h_format', false ),
	        'firstDay'         => apply_filters( 'mylisting/datepicker/first_day', 1 ),
	        'applyLabel'       => _x( 'Apply',        'Datepicker apply date', 'my-listing' ),
	        'cancelLabel'      => _x( 'Cancel',       'Datepicker cancel date', 'my-listing' ),
	        'customRangeLabel' => _x( 'Custom Range', 'Datepicker custom range', 'my-listing' ),
	        'daysOfWeek' => [
	        	_x( 'Su', 'Datepicker weekday names', 'my-listing' ),
	        	_x( 'Mo', 'Datepicker weekday names', 'my-listing' ),
	        	_x( 'Tu', 'Datepicker weekday names', 'my-listing' ),
	        	_x( 'We', 'Datepicker weekday names', 'my-listing' ),
	        	_x( 'Th', 'Datepicker weekday names', 'my-listing' ),
	        	_x( 'Fr', 'Datepicker weekday names', 'my-listing' ),
	        	_x( 'Sa', 'Datepicker weekday names', 'my-listing' ),
	        ],
	        'monthNames' => [
	        	_x( 'January',   'Datepicker month names', 'my-listing' ),
	        	_x( 'February',  'Datepicker month names', 'my-listing' ),
	        	_x( 'March',     'Datepicker month names', 'my-listing' ),
	        	_x( 'April',     'Datepicker month names', 'my-listing' ),
	        	_x( 'May',       'Datepicker month names', 'my-listing' ),
	        	_x( 'June',      'Datepicker month names', 'my-listing' ),
	        	_x( 'July',      'Datepicker month names', 'my-listing' ),
	        	_x( 'August',    'Datepicker month names', 'my-listing' ),
	        	_x( 'September', 'Datepicker month names', 'my-listing' ),
	        	_x( 'October',   'Datepicker month names', 'my-listing' ),
	        	_x( 'November',  'Datepicker month names', 'my-listing' ),
	        	_x( 'December',  'Datepicker month names', 'my-listing' ),
	        ],
		];
	}

	public function get_countries() {
		if ( ! empty( $this->countries ) ) {
			return $this->countries;
		}

		$this->countries = [
			'AF' => _x( 'Afghanistan', 'Country Names', 'my-listing' ),
			'AX' => _x( 'Aland Islands', 'Country Names', 'my-listing' ),
			'AL' => _x( 'Albania', 'Country Names', 'my-listing' ),
			'DZ' => _x( 'Algeria', 'Country Names', 'my-listing' ),
			'AS' => _x( 'American Samoa', 'Country Names', 'my-listing' ),
			'AD' => _x( 'Andorra', 'Country Names', 'my-listing' ),
			'AO' => _x( 'Angola', 'Country Names', 'my-listing' ),
			'AI' => _x( 'Anguilla', 'Country Names', 'my-listing' ),
			'AQ' => _x( 'Antarctica', 'Country Names', 'my-listing' ),
			'AG' => _x( 'Antigua And Barbuda', 'Country Names', 'my-listing' ),
			'AR' => _x( 'Argentina', 'Country Names', 'my-listing' ),
			'AM' => _x( 'Armenia', 'Country Names', 'my-listing' ),
			'AW' => _x( 'Aruba', 'Country Names', 'my-listing' ),
			'AU' => _x( 'Australia', 'Country Names', 'my-listing' ),
			'AT' => _x( 'Austria', 'Country Names', 'my-listing' ),
			'AZ' => _x( 'Azerbaijan', 'Country Names', 'my-listing' ),
			'BS' => _x( 'Bahamas', 'Country Names', 'my-listing' ),
			'BH' => _x( 'Bahrain', 'Country Names', 'my-listing' ),
			'BD' => _x( 'Bangladesh', 'Country Names', 'my-listing' ),
			'BB' => _x( 'Barbados', 'Country Names', 'my-listing' ),
			'BY' => _x( 'Belarus', 'Country Names', 'my-listing' ),
			'BE' => _x( 'Belgium', 'Country Names', 'my-listing' ),
			'BZ' => _x( 'Belize', 'Country Names', 'my-listing' ),
			'BJ' => _x( 'Benin', 'Country Names', 'my-listing' ),
			'BM' => _x( 'Bermuda', 'Country Names', 'my-listing' ),
			'BT' => _x( 'Bhutan', 'Country Names', 'my-listing' ),
			'BO' => _x( 'Bolivia', 'Country Names', 'my-listing' ),
			'BA' => _x( 'Bosnia And Herzegovina', 'Country Names', 'my-listing' ),
			'BW' => _x( 'Botswana', 'Country Names', 'my-listing' ),
			'BV' => _x( 'Bouvet Island', 'Country Names', 'my-listing' ),
			'BR' => _x( 'Brazil', 'Country Names', 'my-listing' ),
			'IO' => _x( 'British Indian Ocean Territory', 'Country Names', 'my-listing' ),
			'BN' => _x( 'Brunei Darussalam', 'Country Names', 'my-listing' ),
			'BG' => _x( 'Bulgaria', 'Country Names', 'my-listing' ),
			'BF' => _x( 'Burkina Faso', 'Country Names', 'my-listing' ),
			'BI' => _x( 'Burundi', 'Country Names', 'my-listing' ),
			'KH' => _x( 'Cambodia', 'Country Names', 'my-listing' ),
			'CM' => _x( 'Cameroon', 'Country Names', 'my-listing' ),
			'CA' => _x( 'Canada', 'Country Names', 'my-listing' ),
			'CV' => _x( 'Cape Verde', 'Country Names', 'my-listing' ),
			'KY' => _x( 'Cayman Islands', 'Country Names', 'my-listing' ),
			'CF' => _x( 'Central African Republic', 'Country Names', 'my-listing' ),
			'TD' => _x( 'Chad', 'Country Names', 'my-listing' ),
			'CL' => _x( 'Chile', 'Country Names', 'my-listing' ),
			'CN' => _x( 'China', 'Country Names', 'my-listing' ),
			'CX' => _x( 'Christmas Island', 'Country Names', 'my-listing' ),
			'CC' => _x( 'Cocos (Keeling) Islands', 'Country Names', 'my-listing' ),
			'CO' => _x( 'Colombia', 'Country Names', 'my-listing' ),
			'KM' => _x( 'Comoros', 'Country Names', 'my-listing' ),
			'CG' => _x( 'Congo', 'Country Names', 'my-listing' ),
			'CD' => _x( 'Congo, Democratic Republic', 'Country Names', 'my-listing' ),
			'CK' => _x( 'Cook Islands', 'Country Names', 'my-listing' ),
			'CR' => _x( 'Costa Rica', 'Country Names', 'my-listing' ),
			'CI' => _x( 'Cote D\'Ivoire', 'Country Names', 'my-listing' ),
			'HR' => _x( 'Croatia', 'Country Names', 'my-listing' ),
			'CU' => _x( 'Cuba', 'Country Names', 'my-listing' ),
			'CY' => _x( 'Cyprus', 'Country Names', 'my-listing' ),
			'CZ' => _x( 'Czech Republic', 'Country Names', 'my-listing' ),
			'DK' => _x( 'Denmark', 'Country Names', 'my-listing' ),
			'DJ' => _x( 'Djibouti', 'Country Names', 'my-listing' ),
			'DM' => _x( 'Dominica', 'Country Names', 'my-listing' ),
			'DO' => _x( 'Dominican Republic', 'Country Names', 'my-listing' ),
			'EC' => _x( 'Ecuador', 'Country Names', 'my-listing' ),
			'EG' => _x( 'Egypt', 'Country Names', 'my-listing' ),
			'SV' => _x( 'El Salvador', 'Country Names', 'my-listing' ),
			'GQ' => _x( 'Equatorial Guinea', 'Country Names', 'my-listing' ),
			'ER' => _x( 'Eritrea', 'Country Names', 'my-listing' ),
			'EE' => _x( 'Estonia', 'Country Names', 'my-listing' ),
			'ET' => _x( 'Ethiopia', 'Country Names', 'my-listing' ),
			'FK' => _x( 'Falkland Islands (Malvinas)', 'Country Names', 'my-listing' ),
			'FO' => _x( 'Faroe Islands', 'Country Names', 'my-listing' ),
			'FJ' => _x( 'Fiji', 'Country Names', 'my-listing' ),
			'FI' => _x( 'Finland', 'Country Names', 'my-listing' ),
			'FR' => _x( 'France', 'Country Names', 'my-listing' ),
			'GF' => _x( 'French Guiana', 'Country Names', 'my-listing' ),
			'PF' => _x( 'French Polynesia', 'Country Names', 'my-listing' ),
			'TF' => _x( 'French Southern Territories', 'Country Names', 'my-listing' ),
			'GA' => _x( 'Gabon', 'Country Names', 'my-listing' ),
			'GM' => _x( 'Gambia', 'Country Names', 'my-listing' ),
			'GE' => _x( 'Georgia', 'Country Names', 'my-listing' ),
			'DE' => _x( 'Germany', 'Country Names', 'my-listing' ),
			'GH' => _x( 'Ghana', 'Country Names', 'my-listing' ),
			'GI' => _x( 'Gibraltar', 'Country Names', 'my-listing' ),
			'GR' => _x( 'Greece', 'Country Names', 'my-listing' ),
			'GL' => _x( 'Greenland', 'Country Names', 'my-listing' ),
			'GD' => _x( 'Grenada', 'Country Names', 'my-listing' ),
			'GP' => _x( 'Guadeloupe', 'Country Names', 'my-listing' ),
			'GU' => _x( 'Guam', 'Country Names', 'my-listing' ),
			'GT' => _x( 'Guatemala', 'Country Names', 'my-listing' ),
			'GG' => _x( 'Guernsey', 'Country Names', 'my-listing' ),
			'GN' => _x( 'Guinea', 'Country Names', 'my-listing' ),
			'GW' => _x( 'Guinea-Bissau', 'Country Names', 'my-listing' ),
			'GY' => _x( 'Guyana', 'Country Names', 'my-listing' ),
			'HT' => _x( 'Haiti', 'Country Names', 'my-listing' ),
			'HM' => _x( 'Heard Island & Mcdonald Islands', 'Country Names', 'my-listing' ),
			'VA' => _x( 'Holy See (Vatican City State)', 'Country Names', 'my-listing' ),
			'HN' => _x( 'Honduras', 'Country Names', 'my-listing' ),
			'HK' => _x( 'Hong Kong', 'Country Names', 'my-listing' ),
			'HU' => _x( 'Hungary', 'Country Names', 'my-listing' ),
			'IS' => _x( 'Iceland', 'Country Names', 'my-listing' ),
			'IN' => _x( 'India', 'Country Names', 'my-listing' ),
			'ID' => _x( 'Indonesia', 'Country Names', 'my-listing' ),
			'IR' => _x( 'Iran, Islamic Republic Of', 'Country Names', 'my-listing' ),
			'IQ' => _x( 'Iraq', 'Country Names', 'my-listing' ),
			'IE' => _x( 'Ireland', 'Country Names', 'my-listing' ),
			'IM' => _x( 'Isle Of Man', 'Country Names', 'my-listing' ),
			'IL' => _x( 'Israel', 'Country Names', 'my-listing' ),
			'IT' => _x( 'Italy', 'Country Names', 'my-listing' ),
			'JM' => _x( 'Jamaica', 'Country Names', 'my-listing' ),
			'JP' => _x( 'Japan', 'Country Names', 'my-listing' ),
			'JE' => _x( 'Jersey', 'Country Names', 'my-listing' ),
			'JO' => _x( 'Jordan', 'Country Names', 'my-listing' ),
			'KZ' => _x( 'Kazakhstan', 'Country Names', 'my-listing' ),
			'KE' => _x( 'Kenya', 'Country Names', 'my-listing' ),
			'KI' => _x( 'Kiribati', 'Country Names', 'my-listing' ),
			'KR' => _x( 'Korea', 'Country Names', 'my-listing' ),
			'KW' => _x( 'Kuwait', 'Country Names', 'my-listing' ),
			'KG' => _x( 'Kyrgyzstan', 'Country Names', 'my-listing' ),
			'LA' => _x( 'Lao People\'s Democratic Republic', 'Country Names', 'my-listing' ),
			'LV' => _x( 'Latvia', 'Country Names', 'my-listing' ),
			'LB' => _x( 'Lebanon', 'Country Names', 'my-listing' ),
			'LS' => _x( 'Lesotho', 'Country Names', 'my-listing' ),
			'LR' => _x( 'Liberia', 'Country Names', 'my-listing' ),
			'LY' => _x( 'Libyan Arab Jamahiriya', 'Country Names', 'my-listing' ),
			'LI' => _x( 'Liechtenstein', 'Country Names', 'my-listing' ),
			'LT' => _x( 'Lithuania', 'Country Names', 'my-listing' ),
			'LU' => _x( 'Luxembourg', 'Country Names', 'my-listing' ),
			'MO' => _x( 'Macao', 'Country Names', 'my-listing' ),
			'MK' => _x( 'Macedonia', 'Country Names', 'my-listing' ),
			'MG' => _x( 'Madagascar', 'Country Names', 'my-listing' ),
			'MW' => _x( 'Malawi', 'Country Names', 'my-listing' ),
			'MY' => _x( 'Malaysia', 'Country Names', 'my-listing' ),
			'MV' => _x( 'Maldives', 'Country Names', 'my-listing' ),
			'ML' => _x( 'Mali', 'Country Names', 'my-listing' ),
			'MT' => _x( 'Malta', 'Country Names', 'my-listing' ),
			'MH' => _x( 'Marshall Islands', 'Country Names', 'my-listing' ),
			'MQ' => _x( 'Martinique', 'Country Names', 'my-listing' ),
			'MR' => _x( 'Mauritania', 'Country Names', 'my-listing' ),
			'MU' => _x( 'Mauritius', 'Country Names', 'my-listing' ),
			'YT' => _x( 'Mayotte', 'Country Names', 'my-listing' ),
			'MX' => _x( 'Mexico', 'Country Names', 'my-listing' ),
			'FM' => _x( 'Micronesia, Federated States Of', 'Country Names', 'my-listing' ),
			'MD' => _x( 'Moldova', 'Country Names', 'my-listing' ),
			'MC' => _x( 'Monaco', 'Country Names', 'my-listing' ),
			'MN' => _x( 'Mongolia', 'Country Names', 'my-listing' ),
			'ME' => _x( 'Montenegro', 'Country Names', 'my-listing' ),
			'MS' => _x( 'Montserrat', 'Country Names', 'my-listing' ),
			'MA' => _x( 'Morocco', 'Country Names', 'my-listing' ),
			'MZ' => _x( 'Mozambique', 'Country Names', 'my-listing' ),
			'MM' => _x( 'Myanmar', 'Country Names', 'my-listing' ),
			'NA' => _x( 'Namibia', 'Country Names', 'my-listing' ),
			'NR' => _x( 'Nauru', 'Country Names', 'my-listing' ),
			'NP' => _x( 'Nepal', 'Country Names', 'my-listing' ),
			'NL' => _x( 'Netherlands', 'Country Names', 'my-listing' ),
			'AN' => _x( 'Netherlands Antilles', 'Country Names', 'my-listing' ),
			'NC' => _x( 'New Caledonia', 'Country Names', 'my-listing' ),
			'NZ' => _x( 'New Zealand', 'Country Names', 'my-listing' ),
			'NI' => _x( 'Nicaragua', 'Country Names', 'my-listing' ),
			'NE' => _x( 'Niger', 'Country Names', 'my-listing' ),
			'NG' => _x( 'Nigeria', 'Country Names', 'my-listing' ),
			'NU' => _x( 'Niue', 'Country Names', 'my-listing' ),
			'NF' => _x( 'Norfolk Island', 'Country Names', 'my-listing' ),
			'MP' => _x( 'Northern Mariana Islands', 'Country Names', 'my-listing' ),
			'NO' => _x( 'Norway', 'Country Names', 'my-listing' ),
			'OM' => _x( 'Oman', 'Country Names', 'my-listing' ),
			'PK' => _x( 'Pakistan', 'Country Names', 'my-listing' ),
			'PW' => _x( 'Palau', 'Country Names', 'my-listing' ),
			'PS' => _x( 'Palestinian Territory, Occupied', 'Country Names', 'my-listing' ),
			'PA' => _x( 'Panama', 'Country Names', 'my-listing' ),
			'PG' => _x( 'Papua New Guinea', 'Country Names', 'my-listing' ),
			'PY' => _x( 'Paraguay', 'Country Names', 'my-listing' ),
			'PE' => _x( 'Peru', 'Country Names', 'my-listing' ),
			'PH' => _x( 'Philippines', 'Country Names', 'my-listing' ),
			'PN' => _x( 'Pitcairn', 'Country Names', 'my-listing' ),
			'PL' => _x( 'Poland', 'Country Names', 'my-listing' ),
			'PT' => _x( 'Portugal', 'Country Names', 'my-listing' ),
			'PR' => _x( 'Puerto Rico', 'Country Names', 'my-listing' ),
			'QA' => _x( 'Qatar', 'Country Names', 'my-listing' ),
			'RE' => _x( 'Reunion', 'Country Names', 'my-listing' ),
			'RO' => _x( 'Romania', 'Country Names', 'my-listing' ),
			'RU' => _x( 'Russian Federation', 'Country Names', 'my-listing' ),
			'RW' => _x( 'Rwanda', 'Country Names', 'my-listing' ),
			'BL' => _x( 'Saint Barthelemy', 'Country Names', 'my-listing' ),
			'SH' => _x( 'Saint Helena', 'Country Names', 'my-listing' ),
			'KN' => _x( 'Saint Kitts And Nevis', 'Country Names', 'my-listing' ),
			'LC' => _x( 'Saint Lucia', 'Country Names', 'my-listing' ),
			'MF' => _x( 'Saint Martin', 'Country Names', 'my-listing' ),
			'PM' => _x( 'Saint Pierre And Miquelon', 'Country Names', 'my-listing' ),
			'VC' => _x( 'Saint Vincent And Grenadines', 'Country Names', 'my-listing' ),
			'WS' => _x( 'Samoa', 'Country Names', 'my-listing' ),
			'SM' => _x( 'San Marino', 'Country Names', 'my-listing' ),
			'ST' => _x( 'Sao Tome And Principe', 'Country Names', 'my-listing' ),
			'SA' => _x( 'Saudi Arabia', 'Country Names', 'my-listing' ),
			'SN' => _x( 'Senegal', 'Country Names', 'my-listing' ),
			'RS' => _x( 'Serbia', 'Country Names', 'my-listing' ),
			'SC' => _x( 'Seychelles', 'Country Names', 'my-listing' ),
			'SL' => _x( 'Sierra Leone', 'Country Names', 'my-listing' ),
			'SG' => _x( 'Singapore', 'Country Names', 'my-listing' ),
			'SK' => _x( 'Slovakia', 'Country Names', 'my-listing' ),
			'SI' => _x( 'Slovenia', 'Country Names', 'my-listing' ),
			'SB' => _x( 'Solomon Islands', 'Country Names', 'my-listing' ),
			'SO' => _x( 'Somalia', 'Country Names', 'my-listing' ),
			'ZA' => _x( 'South Africa', 'Country Names', 'my-listing' ),
			'GS' => _x( 'South Georgia And Sandwich Isl.', 'Country Names', 'my-listing' ),
			'ES' => _x( 'Spain', 'Country Names', 'my-listing' ),
			'LK' => _x( 'Sri Lanka', 'Country Names', 'my-listing' ),
			'SD' => _x( 'Sudan', 'Country Names', 'my-listing' ),
			'SR' => _x( 'Suriname', 'Country Names', 'my-listing' ),
			'SJ' => _x( 'Svalbard And Jan Mayen', 'Country Names', 'my-listing' ),
			'SZ' => _x( 'Swaziland', 'Country Names', 'my-listing' ),
			'SE' => _x( 'Sweden', 'Country Names', 'my-listing' ),
			'CH' => _x( 'Switzerland', 'Country Names', 'my-listing' ),
			'SY' => _x( 'Syrian Arab Republic', 'Country Names', 'my-listing' ),
			'TW' => _x( 'Taiwan', 'Country Names', 'my-listing' ),
			'TJ' => _x( 'Tajikistan', 'Country Names', 'my-listing' ),
			'TZ' => _x( 'Tanzania', 'Country Names', 'my-listing' ),
			'TH' => _x( 'Thailand', 'Country Names', 'my-listing' ),
			'TL' => _x( 'Timor-Leste', 'Country Names', 'my-listing' ),
			'TG' => _x( 'Togo', 'Country Names', 'my-listing' ),
			'TK' => _x( 'Tokelau', 'Country Names', 'my-listing' ),
			'TO' => _x( 'Tonga', 'Country Names', 'my-listing' ),
			'TT' => _x( 'Trinidad And Tobago', 'Country Names', 'my-listing' ),
			'TN' => _x( 'Tunisia', 'Country Names', 'my-listing' ),
			'TR' => _x( 'Turkey', 'Country Names', 'my-listing' ),
			'TM' => _x( 'Turkmenistan', 'Country Names', 'my-listing' ),
			'TC' => _x( 'Turks And Caicos Islands', 'Country Names', 'my-listing' ),
			'TV' => _x( 'Tuvalu', 'Country Names', 'my-listing' ),
			'UG' => _x( 'Uganda', 'Country Names', 'my-listing' ),
			'UA' => _x( 'Ukraine', 'Country Names', 'my-listing' ),
			'AE' => _x( 'United Arab Emirates', 'Country Names', 'my-listing' ),
			'GB' => _x( 'United Kingdom', 'Country Names', 'my-listing' ),
			'US' => _x( 'United States', 'Country Names', 'my-listing' ),
			'UM' => _x( 'United States Outlying Islands', 'Country Names', 'my-listing' ),
			'UY' => _x( 'Uruguay', 'Country Names', 'my-listing' ),
			'UZ' => _x( 'Uzbekistan', 'Country Names', 'my-listing' ),
			'VU' => _x( 'Vanuatu', 'Country Names', 'my-listing' ),
			'VE' => _x( 'Venezuela', 'Country Names', 'my-listing' ),
			'VN' => _x( 'Viet Nam', 'Country Names', 'my-listing' ),
			'VG' => _x( 'Virgin Islands, British', 'Country Names', 'my-listing' ),
			'VI' => _x( 'Virgin Islands, U.S.', 'Country Names', 'my-listing' ),
			'WF' => _x( 'Wallis And Futuna', 'Country Names', 'my-listing' ),
			'XK' => _x( 'Kosovo', 'Country Names', 'my-listing' ),
			'EH' => _x( 'Western Sahara', 'Country Names', 'my-listing' ),
			'YE' => _x( 'Yemen', 'Country Names', 'my-listing' ),
			'ZM' => _x( 'Zambia', 'Country Names', 'my-listing' ),
			'ZW' => _x( 'Zimbabwe', 'Country Names', 'my-listing' ),
		];

		return $this->countries;
	}

	public function get_country( $code ) {
		$countries = $this->get_countries();
		if ( empty( $countries[ $code ] ) ) {
			return false;
		}

		return $countries[ $code ];
	}
}

mylisting()->register( 'strings', Strings::instance() );
