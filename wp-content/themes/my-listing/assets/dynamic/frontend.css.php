<?php
    header("Content-type: text/css; charset: UTF-8");

    // Validate accent color.
    $color_regex = join( '', [
        '/^(',
            '\#[\da-f]{3}|\#[\da-f]{6}',
            '|rgba\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)(,\s*(0\.\d+|1))\)',
            '|hsla\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)(,\s*(0\.\d+|1))\)',
            '|rgb\(((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*,\s*){2}((\d{1,2}|1\d\d|2([0-4]\d|5[0-5]))\s*)',
            '|hsl\(\s*((\d{1,2}|[1-2]\d{2}|3([0-5]\d|60)))\s*,\s*((\d{1,2}|100)\s*%)\s*,\s*((\d{1,2}|100)\s*%)\)',
        ')$/',
    ] );

	  $brand = ! empty( $_GET['brand'] ) ? $_GET['brand'] : '#f24286';
    if ( ! preg_match( $color_regex, $brand ) ) {
        $brand = '#f24286';
    }
?>

.add-listing-nav li.active, .add-listing-nav li:hover {
    border-right-color:  <?php echo $brand; ?>;
}

#submit-job-form .field > input:focus, #submit-job-form .field > textarea:focus {
  border-bottom-color: <?php echo $brand; ?>;
}



.number-stats p:hover  .animated-bars::before, .number-stats p:hover  .animated-bars::after,
.number-stats p:hover  .animated-bars,   .add-listing-nav li.active i > span {
  background: <?php echo $brand; ?>; }

  .add-listing-nav li.active i, .add-listing-loader .spinner-layer.layer-1  {
  border-color:  <?php echo $brand; ?> !important;
  }

.quick-listing-actions li a.bookmarked i:before,
.job_listing_packages .form-section-wrapper .form-section .fieldset-login_required p a.buttons i,
.c_widget .menu > li > a:hover, .c27-submit-listing-form .fieldset-login_required > p > a.buttons i, .social-networks-repeater div div button[type="button"]:hover i, .finder-tabs .nav>li.active>a i, .finder-tabs .nav>li.active>a p  {
  color: <?php echo $brand; ?>;
}

/* style.css */
 .explore-head .explore-types > div.active i, .explore-head .explore-types > div:hover i,
  .explore-head .explore-types > div.active h4, .explore-head .explore-types > div:hover h4, a.listing-action-promoted, .number-stats p:hover span, .cts-autocomplete-dropdown .suggestion i  {
  color: <?php echo $brand; ?> !important;
 }
.woocommerce-MyAccount-navigation ul li.current-menu-item a > i { color: <?php echo $brand; ?>; }
.woocommerce-MyAccount-navigation ul li.current-menu-item a:hover { border-color: <?php echo $brand; ?>; }

.explore-mobile-nav ul li.active::before, .profile-header .profile-menu ul li a:after,.profile-header .profile-menu ul li a:after, .explore-head .explore-types > div::after, .finder-tabs .nav-tabs li::after, .main-nav .i-dropdown::before, ul.dropdown-menu::before, .instant-results::before  {
  background-color: <?php echo $brand; ?>;
}

.explore-mobile-nav ul li.active i {
  color:  <?php echo $brand; ?>;
}
.lmb-calltoaction > a,
.header-bottom-wrapper .header-button a {
  background:  <?php echo $brand; ?>;
}

.quick-listing-actions > ul >li >a:hover, .quick-listing-actions > ul >li >a:hover i {
  color: <?php echo $brand; ?>;
}

a:focus, a:hover { color: <?php echo $brand; ?>; }
.button-1 { color: <?php echo $brand; ?>; }
.button-1:hover { background: <?php echo $brand; ?>; }
.button-1:visited { color: <?php echo $brand; ?>; }
.button-1:visited:hover { background: <?php echo $brand; ?>; }
.button-2 { background: <?php echo $brand; ?>; }
.button-3 { color: <?php echo $brand; ?>; border-color: <?php echo $brand; ?>; }
.button-3:hover { background: <?php echo $brand; ?>; }
.button-3:visited { color: <?php echo $brand; ?>; }
.button-5 { color: <?php echo $brand; ?>; }
.button-5:hover { background: <?php echo $brand; ?>; }
.button-5:visited { color: <?php echo $brand; ?>; }
.button-5:visited:hover { background: <?php echo $brand; ?>; }
.button-6:hover { background: <?php echo $brand; ?>; }
.button-6:visited:hover { background: <?php echo $brand; ?>; }
.pagination .buttons:hover { border-color: <?php echo $brand; ?>; background: <?php echo $brand; ?>; }
.i-dropdown li a:hover { color: <?php echo $brand; ?>; }
.user-profile-dropdown .i-dropdown li:last-child a { color: <?php echo $brand; ?>; }
.bookmark.bookmarked { color: <?php echo $brand; ?>; }
.bookmark.bookmarked:visited, a.bookmark.bookmarked:focus { color: <?php echo $brand; ?>; }
.featured-plan-badge { border-left-color: <?php echo $brand; ?>; }
.rtl .featured-plan-badge { border-right-color: <?php echo $brand; ?>; }
.header-cart-counter { background: <?php echo $brand; ?>; }
.ld-bookmark.bookmarked { background: <?php echo $brand; ?>; }
.ld-info ul li > a:hover { color: <?php echo $brand; ?>; }
input:focus, textarea:focus  { border-color: <?php echo $brand; ?>; box-shadow: 0px 1px 0px 0px <?php echo $brand; ?>; }
.gradient1 { color: <?php echo $brand; ?>; }
.profile-header .profile-menu ul li.active a { border-color: <?php echo $brand; ?>; color: <?php echo $brand; ?>;}
.cover-buttons .button-primary { color: <?php echo $brand; ?>; }
.cover-buttons .button-secondary { background: <?php echo $brand; ?>; }
.cover-buttons > span:hover { background: <?php echo $brand; ?>; }
.inside-date { color: <?php echo $brand; ?>; }
.profile-cover-no-bg .inside-date { background: <?php echo $brand; ?>; }
.lf-nav li a:hover { background:  <?php echo $brand; ?>; }
.clusterDiv { background: <?php echo $brand; ?>; }
.service-item:hover { background: <?php echo $brand; ?>; border-color:  <?php echo $brand; ?>; }
.service-item-info a { color: <?php echo $brand; ?>; }
.service-item-icon span { color: <?php echo $brand; ?>; }
.pricing-item .plan-price { color: <?php echo $brand; ?>; }
.pricing-item .plan-price span.woocommerce-Price-amount { color: <?php echo $brand; ?>; }
.pricing-item .plan-price > span { color: <?php echo $brand; ?>; }

.pricing-item.active .plan-name,
.pricing-item.featured .plan-name { color: <?php echo $brand; ?>; }
.testimonial-image.active { border-color: <?php echo $brand; ?>; }
.testimonial-content p { color: <?php echo $brand; ?>; }
.footer .blog-feed .blogTitle a:hover h5 { color: <?php echo $brand; ?>; }
.footer-bottom .social-links ul li a:hover { border-color: <?php echo $brand; ?>; }
.featured-categories.default .slc-icon i { color: <?php echo $brand; ?>; }
.featured-categories.default ul li a:hover { background: <?php echo $brand; ?>; border-color:  <?php echo $brand; ?>; }
.bl-tabs .nav-tabs>li.active>a { border-color: <?php echo $brand; ?>; color: <?php echo $brand; ?>; }
.bl-tabs .nav-tabs>li>a:hover { border-bottom-color: <?php echo $brand; ?>; }

.mobile-explore-head a { background: <?php echo $brand; ?>; }
.fl-head a:hover  { color: <?php echo $brand; ?>; }
.column-switch a:hover { color: <?php echo $brand; ?>; }
.ui-slider-horizontal .ui-slider-range,
.radius .ui-slider-horizontal .ui-slider-range { background: <?php echo $brand; ?>; }
.single-team .social-nav li a:hover i { color: <?php echo $brand; ?>; }
a.back-to-top:hover { background: <?php echo $brand; ?>; }
.car-tabs .nav-tabs>li.active>a,
.car-tabs .nav-tabs>li.active>a:focus,
.car-tabs .nav-tabs>li.active>a:hover { color: <?php echo $brand; ?>; }
.tagcloud > a:hover,
ul.tags li a:hover { background: <?php echo $brand; ?>; border-color: <?php echo $brand; ?>; }
.faq .title-style-1 i { background: <?php echo $brand; ?>; }
.gallery-nav li  a:hover i { background: <?php echo $brand; ?>; border-color:  <?php echo $brand; ?>; }
.lf-item-container .gallery-nav ul li i:hover { background: <?php echo $brand; ?>; border-color:  <?php echo $brand; ?>; }

.single-comment .listing-rating i {
    color:  <?php echo $brand; ?>;
}

.comment-info a:hover { color: <?php echo $brand; ?>; }
.comment-info a:hover i { color: <?php echo $brand; ?>; }
.rating-number label:hover,
.rating-number .rating-star-active i,
.rating-number label:hover ~ label { color: <?php echo $brand; ?>; }
.rating-number input[type="radio"]:checked ~ label { color:<?php echo $brand; ?>; }
.review-gallery-add:hover { color:<?php echo $brand; ?>; }
.review-gallery-image-remove { background:<?php echo $brand; ?>; }
.countdown-box { background: <?php echo $brand; ?> !important; }
.countdown-box .countdown-list li p { color: <?php echo $brand; ?>; }
.lf-nav.lf-nav-light li a { color: <?php echo $brand; ?>; }
.lf-nav.lf-nav-light li a:hover { border-color: <?php echo $brand; ?>; background: <?php echo $brand; ?>; }
.md-checkbox input[type="checkbox"]:checked + label:before,
.md-checkbox input[type="radio"]:checked + label:before { background: <?php echo $brand; ?>; }
.fs-tabs .nav-tabs>li.active>a,
.fs-tabs .nav-tabs>li.active>a:focus,
.fs-tabs .nav-tabs>li.active>a:hover { color: <?php echo $brand; ?>; }
.featured-light .fs-tabs .nav-tabs>li.active > a,
.featured-light .fs-tabs .nav-tabs>li.active > a i { color: <?php echo $brand; ?>; }
.profile-menu #border-bottom { border-color: <?php echo $brand; ?>; }
.listing-form-heading { border-color: <?php echo $brand; ?>; }
.showing-filter a:hover p { color: <?php echo $brand; ?>; }
.showing-filter a:hover { color: <?php echo $brand; ?>; }
.filter-label { border-bottom-color: <?php echo $brand; ?>; }
.finder-search .searching-for i { color: <?php echo $brand; ?>; }
.cover-buttons .buttons.button-primary a { color: <?php echo $brand; ?>; }
.list-block .details-list a:hover .term-icon.image-icon { background: <?php echo $brand; ?>; border-color: <?php echo $brand; ?>; }
.file-list a:hover .file-icon i {
  background: <?php echo $brand; ?>;
  border-color: <?php echo $brand; ?>;
}

.user-promotion-packages a.promo-item-action.process-promotion:hover, .friendship-button.is_friend.remove:hover {
  border-color:  <?php echo $brand; ?>;
  color:  <?php echo $brand; ?>;
}



.promo-product-item:hover {
  border-color: <?php echo $brand; ?>;
}

/* responsive.css */
@media only screen and (max-width : 1200px) {
.i-nav > ul > li.current-menu-item > a {color: <?php echo $brand; ?>; }
  .i-nav ul li a:hover { color: <?php echo $brand; ?>; }
  .i-nav ul li a:hover i { color: <?php echo $brand; ?>; }
  .mobile-menu-open  ul li a:hover { color: <?php echo $brand; ?>; }
  .mobile-menu-open .i-dropdown  .i-dropdown .i-dropdown li a:hover { color: <?php echo $brand; ?>; }
  .mobile-menu-open .i-dropdown li a:hover { color: <?php echo $brand; ?>; }
}

/* custom.css */
.single-product.woocommerce .star-rating span { color: <?php echo $brand; ?>; }
.select2-container--default .select2-selection--single .select2-selection__clear:before { color: <?php echo $brand; ?>; }
.select2-container--open .select2-selection--single .select2-selection__rendered { border-color: <?php echo $brand; ?>; box-shadow: 0px 1px 0px 0px <?php echo $brand; ?>; }
.select2-container--default .select2-selection--single .select2-selection__arrow:before { color: <?php echo $brand; ?>; }
.select2-results__option.select2-results__option--highlighted,
.select2-container--default .select2-results__option[aria-selected=true] { background: <?php echo $brand; ?>; }
.select2-container--default .select2-results__option--highlighted[aria-selected=false] { color: <?php echo $brand; ?>; }
.featured-search .datepicker-wrapper .reset-value,
.daterangepicker td.active,
.daterangepicker td.active:hover { background: <?php echo $brand; ?>; }
.daterangepicker .input-mini.active { border-color: <?php echo $brand; ?>; }
.select2-container--default .select2-search--dropdown .select2-search__field { border-color: <?php echo $brand; ?>; }
.pac-container .pac-item:hover > span { color: <?php echo $brand; ?>; }
.wcv-pro-dashboard .wcv-grid h2, .wcv-pro-dashboard .wcv-grid h3, .wcv-pro-dashboard .wcv-grid h6 { border-bottom-color: <?php echo $brand; ?>; }
.wcv-pro-dashboard .wcv-tabs.top>.tabs-nav li.active a,
.wcv-pro-dashboard .wcv-tabs.top>.tabs-nav li a:hover { border-color: <?php echo $brand; ?>; color: <?php echo $brand; ?>; }
.wcv-pro-dashboard .wcv-navigation ul.menu.black li a:hover { color: <?php echo $brand; ?>; }
.wcv-pro-dashboard .wcv-navigation ul.menu.black li.active a { background: <?php echo $brand; ?>; }
.wcv-pro-dashboard .wcv-form textarea:focus,
.wcv-pro-dashboard .wcv-form input[type=url]:focus,
.wcv-pro-dashboard .wcv-form input[type=email]:focus,
.wcv-pro-dashboard .wcv-form input[type=text]:focus { border-color: <?php echo $brand; ?>; box-shadow: 0px 1px 0px 0px <?php echo $brand; ?>; }
body.wcv-pro-dashboard .select2-results li.select2-highlighted { color: <?php echo $brand; ?>; }
.wcvendors-pro-dashboard-wrapper .control-group .select2-container.tag-select2.select2-container-active .select2-choices,
.wcvendors-pro-dashboard-wrapper .control-group .select2-container.category-select2.select2-container-active .select2-choices {
    border-color: <?php echo $brand; ?>;
    box-shadow: 0px 1px 0px 0px <?php echo $brand; ?>;
}
.wcv-pro-dashboard .wcvendors-pro-dashboard-wrapper .select2-container .select2-choice .select2-arrow:before { color: <?php echo $brand; ?>; }

/* tmp.css */
.job_listing_preview .wpcf7 input[type="submit"].wpcf7-form-control,
.wpcf7-form input[type="submit"] { background: <?php echo $brand; ?>; }
.c27-pick-package.c27-picked { border-color: <?php echo $brand; ?>; }
.round-icon .title-style-1 i { background: <?php echo $brand; ?>; }
.listing-preview .listing-details .c27-bookmark-button.bookmarked,
.listing-preview .event-host .c27-bookmark-button.bookmarked { background: <?php echo $brand; ?>; }
.form-group .job-manager-uploaded-files progress[value]::-webkit-progress-value,
.form-group .job-manager-uploaded-files progress[value]::-webkit-progress-bar {
  background: <?php echo $brand; ?>;
}
.form-group .job-manager-uploaded-files progress[value]::-moz-progress-bar {
  background: <?php echo $brand; ?>;
}
.form-group .job-manager-uploaded-files progress:before {
  border-bottom-color: <?php echo $brand; ?>;
  border-left-color: <?php echo $brand; ?>;
}
.rtl .form-group .job-manager-uploaded-files progress:before {
  border-right-color: <?php echo $brand; ?>;
}
@supports (-ms-ime-align: auto) {
  .form-group .job-manager-uploaded-files progress {
    background-color: <?php echo $brand; ?>;
  }
}
@media all and (-ms-high-contrast: none), (-ms-high-contrast: active) {
  .form-group .job-manager-uploaded-files progress[value],
  .form-group .job-manager-uploaded-files progress {
    color: <?php echo $brand; ?>;
    border-color: <?php echo $brand; ?>;
  }
}

.promo-item-icon i  {
  background:<?php echo $brand; ?> !important;
}
/* tmp-2.css */

.header-scroll.header-scroll-dark-skin .header-button a.buttons.button-2 { color: <?php echo $brand; ?> }
.header-scroll.header-scroll-dark-skin .header-button a.buttons.button-2:hover { background: <?php echo $brand; ?> }
.header-scroll.header-scroll-dark-skin .header-button a.buttons.button-2:visited { color: <?php echo $brand; ?> }
.header-scroll.header-scroll-dark-skin .header-button a.buttons.button-2:visited:hover { background: <?php echo $brand; ?> }
nav.woocommerce-pagination ul.page-numbers li a.page-numbers:hover,
a.page-numbers:hover,
nav.job-manager-pagination ul li a:hover { border-color: <?php echo $brand; ?>; background: <?php echo $brand; ?>; }
.header-scroll.header-scroll-light-skin .header-button a.buttons.button-1,
.job-manager-form fieldset .account-sign-in .button,
.woocommerce-order-details p.order-again a,
.job-manager-form >p input[type='submit'],
.c27-wc-vendors.wc-vendors-store-settings input[type="submit"].btn,
p.return-to-shop a.button.wc-backward,
.c27-wc-vendors .form-row input[type="submit"].button,
.c27-content-wrapper .post-password-form p > input,
.woocommerce #review_form #respond .form-submit input[type='submit'],
.c_widget .woocommerce-product-search input[type='submit'],
.c_widget .search-form input[type='submit'],
.woocommerce div.product form.cart .button,
#robokassa_payment_form input[type=submit],
#TB_ajaxContent div button[type=button]#save,
.woocommerce-ResetPassword .woocommerce-form-row button[type="submit"].button,
.woocommerce-ResetPassword .woocommerce-form-row input[type="submit"].button,
.woocommerce-checkout-review-order .woocommerce-checkout-payment .place-order button.button.alt,
.woocommerce-checkout-review-order .woocommerce-checkout-payment .place-order input.button.alt,
.cart-collaterals .wc-proceed-to-checkout a.button,
.woocommerce .woocommerce-EditAccountForm.edit-account > p:last-of-type input,
.woocommerce .woocommerce-EditAccountForm.edit-account > p:last-of-type button,
.wcv-form .control-group .control.append-button .wcv-button,
.wcvendors-pro-dashboard-wrapper form input[type=submit],
form input[type=submit],
form .listing-form-submit-btn button[type=submit],
.woocommerce-MyAccount-content .woocommerce-address-fields .woocommerce-address-fields__field-wrapper ~ p button[type=submit],
.woocommerce-checkout .woocommerce-form-login-toggle ~ .woocommerce-form.woocommerce-form-login .form-row button[type=submit],
.friends #buddypress #item-body > #friend-list li .action a.button,
.woocommerce .woocommerce-info ~ form.woocommerce-form-login p.form-row input[type=submit],
.woocommerce .woocommerce-address-fields .woocommerce-address-fields__field-wrapper ~ p input, .checkout_coupon .form-row-last button.button, .woocommerce-product-search button,
#wc-cart-modal .woocommerce-mini-cart__buttons a:nth-child(2)  {
  background: <?php echo $brand; ?>;
}



  #robokassa_payment_form a.button:hover,
.woocommerce table.my_account_orders .order-actions.subscription-actions a.button:hover,
#TB_ajaxContent div button[type=button]#cancel:hover,
.wcv-pro-dashboard .wcv_product_attributes button.button:hover,
.wcvendors-pro-dashboard-wrapper form input[type=submit]#product_save_button:hover,
.wcv-media-uploader-gallery a:hover,
a[class*="wcv-media-uploader-featured"]:hover,
.wcv_shipping_rates a.button.insert:hover,
a.wcv-file-uploader-add_wcv_store_icon_id:hover,
a.wcv-file-uploader-add_wcv_store_banner_id:hover,
a.wcv-button.button:hover,
.wcv-form .control-group .control>input[type=submit]:hover,
.wcv-grid a.button.shop_coupon:hover,
.wcv-grid a.button.product:hover,
.c27-wc-vendors-export form input:hover,
.c27-wc-vendors.wc-vendors-dashboard form input[type="submit"].btn:hover,
.c27-wc-vendors .wc-vendors-menu a.button:hover,
.c_widget .widget_shopping_cart_content .buttons a:hover,
.woocommerce-info ~ .checkout_coupon .form-row-last button.button:hover,
.woocommerce-info ~ .checkout_coupon .form-row-last input.button:hover,
.woocommerce table.shop_table td.actions .coupon ~ input[type="submit"]:hover {
  background: <?php echo $brand; ?>;
}

.job-manager-form fieldset div.field.account-sign-in {
  border-top-color: <?php echo $brand; ?>;
}

.job-dashboard-actions li a:hover {
  border-color: <?php echo $brand; ?>;
  color: <?php echo $brand; ?>;
}

.woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover {
  color:  <?php echo $brand; ?>;
  border-color:  <?php echo $brand; ?>;
}

.star-rating {
    color: <?php echo $brand; ?>;
}

  #robokassa_payment_form a.button:visited:hover,
.woocommerce table.my_account_orders .order-actions.subscription-actions a.button:visited:hover,
#TB_ajaxContent div button[type=button]#cancel:visited:hover,
.wcv-pro-dashboard .wcv_product_attributes button.button:visited:hover,
.wcvendors-pro-dashboard-wrapper form input[type=submit]#product_save_button:visited:hover,
.wcv-media-uploader-gallery a:visited:hover,
a[class*="wcv-media-uploader-featured"]:visited:hover,
.wcv_shipping_rates a.button.insert:visited:hover,
a.wcv-file-uploader-add_wcv_store_icon_id:visited:hover,
a.wcv-file-uploader-add_wcv_store_banner_id:visited:hover,
a.wcv-button.button:visited:hover,
.wcv-form .control-group .control>input[type=submit]:visited:hover,
.wcv-grid a.button.shop_coupon:visited:hover,
.wcv-grid a.button.product:visited:hover,
.c27-wc-vendors-export form input:visited:hover,
.c27-wc-vendors.wc-vendors-dashboard form input[type="submit"].btn:visited:hover,
.c27-wc-vendors .wc-vendors-menu a.button:visited:hover,
.ranges .range_inputs button:visited:hover,
.social-networks-repeater > input[type="button"]:visited:hover,
.c_widget .price_slider_wrapper .price_slider_amount .button:visited:hover,
.c_widget .widget_shopping_cart_content .buttons a:visited:hover,
.woocommerce-info ~ .checkout_coupon .form-row-last button.button:visited:hover,
.woocommerce-info ~ .checkout_coupon .form-row-last input.button:visited:hover,
.woocommerce table.shop_table td.actions .coupon ~ button[type="submit"]:visited:hover,
.woocommerce table.shop_table td.actions .coupon ~ input[type="submit"]:visited:hover,
.woocommerce table.shop_table td.actions .coupon input:last-of-type:visited:hover,
.woocommerce table.my_account_orders td.woocommerce-orders-table__cell-order-actions a.button:visited:hover,
.woocommerce-account .addresses .title a.edit:visited:hover,
#wc-cart-modal .woocommerce-mini-cart__buttons a:first-child:hover,
.c_widget .price_slider_wrapper .price_slider_amount .button:hover,
.ranges .range_inputs button:hover,
.woocommerce table.shop_table td.actions .coupon button:last-of-type:hover,
.woocommerce table.shop_table td.actions .coupon ~ button[type="submit"]:hover,
.woocommerce table.my_account_orders td.woocommerce-orders-table__cell-order-actions a.button:hover, table.shop_table.c27-bookmarks-table .bookmark-actions a:hover,
.job-dashboard-actions li a:hover, .woocommerce-account .addresses .title a.edit:hover {
  border-color: <?php echo $brand; ?>;
  color: <?php echo $brand; ?> !important;
}

.woocommerce-EditAccountForm legend, .cts-connected-accounts > h5, .cts-user-picture-settings > h5, .woocommerce-Address-title > h3, .subscription_details ~ h2, .order_details ~ header h2, .woocommerce-column__title,.woocommerce .woocommerce-MyAccount-content form > h3 {
 border-color: <?php echo $brand; ?>;
}

ul.products li.product a.button.add_to_cart_button.added:before {
   color: <?php echo $brand; ?>;
}

.woocommerce ul.products li.product span.onsale:after {
  border-right-color: <?php echo $brand; ?>;
}
.rtl.woocommerce ul.products li.product span.onsale:after {
  border-left-color: <?php echo $brand; ?>;
}

.woocommerce form.checkout #order_review table.woocommerce-checkout-review-order-table { background: <?php echo $brand; ?>; }
.woocommerce-customer-details .woocommerce-column h2.woocommerce-column__title,
.woocommerce-order-details h2.woocommerce-order-details__title,
.c27-wc-vendors.wc-vendors-store-settings form div p > b,
.c27-wc-vendors.wc-vendors-store-settings > h2,
.c27-wc-vendors.wc-vendors-dashboard .c27-wc-vendors-orders > h2,
.c27-wc-vendors.wc-vendors-dashboard > h2,
.woocommerce .woocommerce-info ~ form.woocommerce-form-login .apsl-login-networks .apsl-login-new-text,
.woocommerce form.checkout #order_review_heading,
.woocommerce form.checkout.woocommerce-checkout h3#ship-to-different-address label span,
.woocommerce form.checkout.woocommerce-checkout .woocommerce-additional-fields > h3,
.woocommerce form.checkout.woocommerce-checkout .woocommerce-billing-fields > h3 { border-bottom-color: <?php echo $brand; ?>; }

.woocommerce-account .woocommerce-MyAccount-navigation > ul > li:after,
.explore-head .explore-types > div.active::after,
.explore-head .explore-types > div:hover::after, .finder-tabs .nav-tabs li.active:after, .finder-tabs .nav-tabs li:hover:after, .main-nav .i-dropdown::before, .main-nav .i-dropdown::before, ul.dropdown-menu::before , .instant-results::before {
  background-color: <?php echo $brand; ?>;
}
.woocommerce div.product p.price,
.woocommerce div.product span.price { color: <?php echo $brand; ?>; }
.woocommerce div.product .woocommerce-tabs ul.tabs li a:hover,
.woocommerce div.product .woocommerce-tabs ul.tabs li.active a { border-color: <?php echo $brand; ?>; color: <?php echo $brand; ?>; }
.form-group .job-manager-uploaded-file-preview a { background: <?php echo $brand; ?>; }

.woocommerce-MyAccount-navigation ul li.current-menu-item a {
color: <?php echo $brand; ?>;}
.woocommerce table.shop_table td.actions .coupon input.input-text:focus { border-color: <?php echo $brand; ?>; box-shadow: 0px 1px 0px 0px <?php echo $brand; ?>; }
.woocommerce form .form-row.woocommerce-validated .select2-container:focus,
.woocommerce form .form-row.woocommerce-validated input.input-text:focus,
.woocommerce form .form-row.woocommerce-validated select:focus { border-color: <?php echo $brand; ?>; }
.woocommerce form .form-row textarea:focus { box-shadow: 0px 1px 0px 0px <?php echo $brand; ?>; }

.rp4wp-related-job_listing>ul li.job_listing a:focus .position h3,
.rp4wp-related-job_listing>ul li.job_listing a:hover .position h3,
.rp4wp-related-job_listing>ul li.no_job_listings_found a:focus .position h3,
.rp4wp-related-job_listing>ul li.no_job_listings_found a:hover .position h3,
ul.job_listings li.job_listing a:focus .position h3,
ul.job_listings li.job_listing a:hover .position h3,
ul.job_listings li.no_job_listings_found a:focus .position h3,
ul.job_listings li.no_job_listings_found a:hover .position h3 {
  color: <?php echo $brand; ?>;
}

.work-hours-repeater li.day button[type="button"], .social-networks-repeater div div button[type="button"], .main-nav li.current-menu-item a > i {
    color: <?php echo $brand; ?>;
}

.work-hours-repeater li.day button[type="button"]:hover, .social-networks-repeater div div button[type="button"]:hover, .i-nav > ul > li.current-menu-item > a {
    border-color: <?php echo $brand; ?>;
}




.product_meta span.posted_in > a:hover, .tagged_as > a:hover {
   color: <?php echo $brand; ?>;
   border-color: <?php echo $brand; ?>;
}
.job-manager-uploaded-file .job-manager-uploaded-file-name a.job-manager-remove-uploaded-file { background: <?php echo $brand; ?>; }

.woocommerce-store-notice, p.demo_store { background: <?php echo $brand; ?>; }

/* buddypress */
#buddypress #item-header-cover-image #item-buttons div.generic-button a.friendship-button.is_friend { color: <?php echo $brand; ?>; }
#buddypress div#item-nav div.item-list-tabs ul li.current a,
#buddypress div#item-nav div.item-list-tabs ul li.selected a {
  border-color: <?php echo $brand; ?>;
}

.woocommerce div.product span.onsale:after {
  border-left-color: <?php echo $brand; ?>;
}
.rtl.woocommerce div.product span.onsale:after {
  border-right-color: <?php echo $brand; ?>;
}
#buddypress div#whats-new-options #whats-new-submit input[type="submit"] {
  background: <?php echo $brand; ?>;
}
#buddypress div.activity-meta a.button:focus,
#buddypress div.activity-meta a.button:hover {
  color: <?php echo $brand; ?>;
}
#buddypress form.standard-form#group-settings-form textarea#group-desc:focus,
#buddypress .groups-members-search form input[type="text"]:focus,
#buddypress div.dir-search form input[type=text]:focus,
#buddypress #register-page > .standard-form .register-section input[type=password]:focus,
#buddypress #register-page > .standard-form .register-section input[type=email]:focus,
#buddypress #item-body .item-list-tabs .message-search input[type=text]:focus,
#buddypress form.standard-form textarea#message_content:focus,
#buddypress #item-body form.standard-form input[type="email"]:focus,
#buddypress #item-body form.standard-form input[type="password"]:focus,
#buddypress .profile .single-fields .editfield input[type="number"]:focus,
.form-field input:focus, .form-field textarea:focus,
#buddypress form.standard-form input[type=text]:focus {
  border-color: <?php echo $brand; ?> !important;
  box-shadow: 0px 1px 0px 0px <?php echo $brand; ?> !important;
}

.form-field .md-checkbox input[type="checkbox"]:checked + label:before,
.form-field .md-checkbox input[type="radio"]:checked + label:before,
.form-field .uploaded-file a.remove-uploaded-file,
.form-field .review-gallery-image-remove,
#buddypress #create-group-form .item-list-tabs ul li.current a:hover,
#buddypress #create-group-form .item-list-tabs ul li.selected a:hover, #buddypress div.activity-comments form input, #members_search_submit, #buddypress .standard-form div.submit input#submit, #send_reply_button,
#buddypress .standard-form div.submit input[type="submit"],
.buddypress .submit #send {
  background: <?php echo $brand; ?> !important;
}
.form-field .review-gallery-add:hover,
#buddypress .standard-form#create-group-form .item-list-tabs ul li a:hover {
  color: <?php echo $brand; ?>;
}

#buddypress #groups-directory-form div.item-list-tabs ul li a:hover,
#buddypress #members-directory-form div.item-list-tabs ul li a:hover,
.directory.activity #buddypress div.item-list-tabs ul li a:hover,
#buddypress #item-body div.item-list-tabs#subnav ul li a:hover {
  color: <?php echo $brand; ?>;
}
.groups #buddypress #item-meta span.highlight { background: <?php echo $brand; ?>; }
.group-cover-image #buddypress form.standard-form#group-settings-form > h2,
.groups #buddypress .standard-form#group-settings-form > fieldset > legend,
form.standard-form h3.section-header,
.bp-user #buddypress div.profile h2, #buddypress #item-body div.item-list-tabs ul li.selected a  {
  border-color: <?php echo $brand; ?>;
}




#buddypress .standard-form#create-group-form div.submit input[type="submit"] {
  background: <?php echo $brand; ?>;
}

#buddypress #create-group-form .item-body #previous-next input[type=button],
#buddypress #create-group-form .item-body #previous-next input[type=button]:visited {
    color: <?php echo $brand; ?>;
}

.buddypress.c_widget .item-options a.selected {
    color: <?php echo $brand; ?>;
}
.woocommerce-cart .element .pf-body .cart-collaterals {
  background: <?php echo $brand; ?>;
}
.woocommerce-cart .element .pf-body .cart-collaterals form.woocommerce-shipping-calculator .shipping-calculator-form p button.button,
.woocommerce-cart .element .pf-body .cart-collaterals .cart_totals .wc-proceed-to-checkout a.checkout-button {
    color: <?php echo $brand; ?>;
}
.woocommerce-cart .element .pf-body .cart-collaterals form.woocommerce-shipping-calculator .shipping-calculator-form p button.button:hover,
.woocommerce-cart .element .pf-body .cart-collaterals .cart_totals .wc-proceed-to-checkout a.checkout-button:hover {
    background: <?php echo $brand; ?>;
}
.buddypress.c_widget #bp-login-widget-form .bp-login-widget-register-link a {
    background: <?php echo $brand; ?>;
}
.buddypress.c_widget .bp-login-widget-user-links .bp-login-widget-user-logout a,
.buddypress.c_widget .bp-login-widget-user-links .bp-login-widget-user-logout a:visited {
    color: <?php echo $brand; ?>;
}
.buddypress.c_widget .bp-login-widget-user-links .bp-login-widget-user-logout a:hover,
.buddypress.c_widget .bp-login-widget-user-links .bp-login-widget-user-logout a:visited:hover {
    background: <?php echo $brand; ?>;
}

.bp-avatar-nav ul.avatar-nav-items li.avatar-nav-item.current a {
  border-color: <?php echo $brand; ?>;
  color: <?php echo $brand; ?>;
}
.bp-avatar-nav ul.avatar-nav-items li.avatar-nav-item a:hover {
  border-color: <?php echo $brand; ?>;
  color: <?php echo $brand; ?>;
}

#buddypress div.pagination .pagination-links a:hover {
  border-color: <?php echo $brand; ?>;
  background: <?php echo $brand; ?>;
}

/* Material desgin */
.md-group input:focus ~ label {
  color: <?php echo $brand; ?>;
}
.md-border-line {
  background: <?php echo $brand; ?>;
}
.md-active label {
  color: <?php echo $brand; ?>;
}
.finder-tabs .nav-tabs>li>a:after {
  background: <?php echo $brand; ?>;
}
.select2-container--below .select2-selection__rendered:after {
  background: <?php echo $brand; ?>;
}

/* mapbox.css */
.cts-marker-cluster { background: <?php echo $brand; ?> !important; }

.promotion-keys-wrapper .round-icon .title-style-1 i { color: <?php echo $brand; ?>; }
.woocommerce-pagination.woocommerce-pagination--without-numbers.woocommerce-Pagination a:hover { background-color: <?php echo $brand; ?>; border-color: <?php echo $brand; ?>; }


  .job_listing_preview_title { background: <?php echo $brand; ?>; }
  .job_listing_preview_title input[type="submit"].job-manager-button-submit-listing.button.button-2 { color: <?php echo $brand; ?>; }
  .job_listing_preview_title input[type="submit"].job-manager-button-submit-listing.button.button-2:hover { background: <?php echo $brand; ?>; }
  .job_listing_preview_title input[type="submit"].job-manager-button-submit-listing.button.button-2:visited { color: <?php echo $brand; ?>; }
  .job_listing_preview_title input[type="submit"].job-manager-button-submit-listing.button.button-2:visited:hover { background: <?php echo $brand; ?>; }


@media (max-width:768px) {
.woocommerce-cart .element .pf-body .woocommerce-cart-form table.shop_table td.product-price,
.woocommerce-cart .element .pf-body .woocommerce-cart-form table.shop_table td.product-subtotal,
.woocommerce-cart .element .pf-body .woocommerce-cart-form table.shop_table td.product-quantity {
    background: <?php echo $brand; ?>;
}


.form-field .select2-container--default .select2-selection--multiple[aria-expaned=true],
.woocommerce-cart .element .pf-body .woocommerce-cart-form table.shop_table td.product-quantity:after {
    border-bottom-color: <?php echo $brand; ?>;
}
}

@media only screen and (min-width : 1201px) {
  .c27-packages .user-packages { background: <?php echo $brand; ?>; }
  .c27-packages .job_listing_packages  .user-packages  ul.job_packages li.user-job-package .md-checkbox input[type="radio"]:checked + label:after { border-color: <?php echo $brand; ?>; }
  .c27-packages .user-packages .round-icon .title-style-1 .toggle-my-packages i { color: <?php echo $brand; ?>; }
}

/* Four Dots spinner. */
<?php
$defaultColor = ['r' => '0', 'g' => '0', 'b' => '0', 'a' => '1'];
$loaderColor = ! empty( $_GET['loader'] ) ? $_GET['loader'] : $defaultColor;

// Validate spinner color.
if ( array_keys( $loaderColor ) !== ['r', 'g', 'b', 'a'] ) {
    $loaderColor = $defaultColor;
}

$loaderColor['r'] = intval( $loaderColor['r'] );
$loaderColor['g'] = intval( $loaderColor['g'] );
$loaderColor['b'] = intval( $loaderColor['b'] );
$loaderColor['a'] = floatval( $loaderColor['a'] );

if ( min( $loaderColor ) < 0 || max( [ $loaderColor['r'], $loaderColor['g'], $loaderColor['b'] ] ) > 255 || $loaderColor['a'] > 1 ) {
  $loaderColor = $defaultColor;
}

$secondaryLoaderColor = $loaderColor;
$secondaryLoaderColor['a'] = '0.2';
?>
@keyframes spin3 {
  0%, 100% {
    box-shadow: 10px 10px rgba(<?php echo join(', ', $loaderColor) ?>), -10px 10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>),
                -10px -10px rgba(<?php echo join(', ', $loaderColor) ?>), 10px -10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>); }
    25% {
        box-shadow: -10px 10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>), -10px -10px rgba(<?php echo join(', ', $loaderColor) ?>),
                    10px -10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>), 10px 10px rgba(<?php echo join(', ', $loaderColor) ?>); }
    50% {
        box-shadow: -10px -10px rgba(<?php echo join(', ', $loaderColor) ?>), 10px -10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>),
                    10px 10px rgba(<?php echo join(', ', $loaderColor) ?>), -10px 10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>); }
    75% {
        box-shadow: 10px -10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>), 10px 10px rgba(<?php echo join(', ', $loaderColor) ?>),
                    -10px 10px rgba(<?php echo join(', ', $secondaryLoaderColor) ?>), -10px -10px rgba(<?php echo join(', ', $loaderColor) ?>); }
}

#wp<?php echo 'adminbar' ?> { top: 0 !important; }