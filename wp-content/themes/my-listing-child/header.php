<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php esc_attr( bloginfo( 'charset' ) ) ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="pingback" href="<?php esc_attr( bloginfo( 'pingback_url' ) ) ?>">
	<?php /*
    <!-- Global site tag (gtag.js) - Google Analytics -->
	<!--
	    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-124615778-1"></script>
	    <script>
	      window.dataLayer = window.dataLayer || [];
	      function gtag(){dataLayer.push(arguments);}
	      gtag('js', new Date());

	      gtag('config', 'UA-124615778-1');
	    </script>
	-->
    <!-- Google Tag Manager -->
		<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
		new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
		j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
		'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
		})(window,document,'script','dataLayer','GTM-PPP7M7V');</script>
	<!-- End Google Tag Manager -->
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
	<script>
	  (adsbygoogle = window.adsbygoogle || []).push({
	    google_ad_client: "ca-pub-3363409313433659",
	    enable_page_level_ads: true
	  });
	</script>
	*/ ?>

	<!-- Facebook Pixel Code -->
		<script>
		  !function(f,b,e,v,n,t,s)
		  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
		  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
		  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
		  n.queue=[];t=b.createElement(e);t.async=!0;
		  t.src=v;s=b.getElementsByTagName(e)[0];
		  s.parentNode.insertBefore(t,s)}(window, document,'script',
		  'https://connect.facebook.net/en_US/fbevents.js');
		  fbq('init', '470934719773259');
		  fbq('track', 'PageView');
		</script>
		<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=470934719773259&ev=PageView&noscript=1"/></noscript>
	<!-- End Facebook Pixel Code -->

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php /*
    <!-- Google Tag Manager (noscript) -->
	<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PPP7M7V"
	height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
	*/ ?>
<?php
/**
 * Action hook immediately after the opening <body> tag.
 *
 * @since 1.6.6
 */
do_action( 'mylisting/body/start' ) ?>

<?php
// Initialize custom styles global.
$GLOBALS['case27_custom_styles'] = '';

// Wrap site in #c27-site-wrapper div.
printf( '<div id="c27-site-wrapper">' );

// Include loading screen animation.
c27()->get_partial( 'loading-screens/' . c27()->get_setting( 'general_loading_overlay', 'none' ) ); ?>

<?php
/**
 * Only include the default header if an Elementor custom one doesn't exist.
 *
 * @link  https://developers.elementor.com/theme-locations-api/migrating-themes/
 * @since 2.0
 */
if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
	$pageTop = apply_filters('case27_pagetop_args', [
		'header' => [
			'show' => true,
			'args' => [],
		],

		'title-bar' => [
			'show' => c27()->get_setting('header_show_title_bar', false),
			'args' => [
				'title' => get_the_archive_title(),
				'ref' => 'default-title-bar',
			],
		]
	]);

	if ($pageTop['header']['show']) {
		c27()->get_section('header', $pageTop['header']['args']);

		if ($pageTop['title-bar']['show']) {
			c27()->get_section('title-bar', $pageTop['title-bar']['args']);
		}
	}
} ?>

<?php
/*
if ( function_exists('yoast_breadcrumb') && !is_front_page() ) {
  yoast_breadcrumb( '<div class="yoast_breadcrumb"><p id="breadcrumbs">','</p></div>' );
}
*/
?>
