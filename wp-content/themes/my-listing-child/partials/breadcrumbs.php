<?php
if ( function_exists('yoast_breadcrumb') && !is_front_page() ) {
  yoast_breadcrumb( '<div class="yoast_breadcrumb"><p id="breadcrumbs">','</p></div>' );
}
?>
