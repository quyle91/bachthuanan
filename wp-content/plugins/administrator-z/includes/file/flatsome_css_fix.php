<?php
header( "Content-Type: text/css" );
require_once ( dirname( __FILE__ ) . '/../../../../../wp-load.php' );
?>
:root{
	--secondary-color:  <?= get_theme_mod('color_secondary', Flatsome_Default::COLOR_SECONDARY ); ?>;
	--success-color:  <?= get_theme_mod('color_success', Flatsome_Default::COLOR_SUCCESS ); ?>;
	--alert-color:  <?= get_theme_mod('color_alert', Flatsome_Default::COLOR_ALERT ); ?>;
	--big-radius: <?php echo apply_filters( 'adminz_pack1_big-radius', '10px' ); ?>;
	--small-radius: <?php echo apply_filters( 'adminz_pack1_small-radius', '5px' ); ?>;
	--form-controls-rarius: <?php echo apply_filters( 'adminz_pack1_form-controls-radius', '5px' ); ?>;;
	--main-gray: <?php echo apply_filters( 'adminz_pack1_main-gray', '#0000000a' ); ?>;
	--border-color: <?php echo apply_filters( 'adminz_pack1_border-color', 'transparent' ); ?>;
}
<?php
	$array = [
		'primary',
		'secondary', 
		'success',
		'alert'
	];
	foreach ($array as $key => $color) {
		?>
		.<?= $color ?>-color, .<?= $color ?>-color *{
			color: var(--<?= $color ?>-color);
		}
		.<?= $color ?>{
			background-color: var(--<?= $color ?>-color);
		}
		.<?= $color ?>.is-link,
		.<?= $color ?>.is-outline,
		.<?= $color ?>.is-underline {
			color: var(--<?= $color ?>-color);
		}
		.<?= $color ?>.is-outline:hover {
			background-color: var(--<?= $color ?>-color);
			border-color: var(--<?= $color ?>-color);
			color: #fff;
		}
		.<?= $color ?>-border {
			border-color: var(--<?= $color ?>-color);
		}
		.<?= $color ?>:focus-visible{
			outline-color: var(--<?= $color ?>-color);
		}
		.<?= $color ?>.is-outline:hover {
			background-color: var(--<?= $color ?>-color);
			border-color: var(--<?= $color ?>-color);
		}
		<?php
	}
?>

blockquote, table, table td{
	color:  inherit;
}

@media (max-width: 549px) {
	.flex-row.form-flat.medium-flex-wrap {
		align-items: flex-start;
	}
	.flex-row.form-flat.medium-flex-wrap .ml-half {
		margin-left: 0px !important;
	}
}
.row-nopaddingbottom .flickity-slider>.col,
.row-nopaddingbottom>.col,
.nopadding,.nopaddingbottom{
	padding-bottom: 0 !important;
}
.no-marginbottom, .no-marginbottom h1, .no-marginbottom h2, .no-marginbottom h3, .no-marginbottom h4, .no-marginbottom h5, .no-marginbottom h6{
	margin-bottom: 0px;
}
.row .section{
	padding-left: 15px;
	padding-right: 15px;
}
.bgr-size-auto .section-bg.bg-loaded{	
	background-size: auto !important;
}
.button{
	white-space: nowrap;
}
h1 strong, h2 strong, h3 strong, h4 strong, h5 strong, h6 strong {
	font-weight: 900;
}
@media (min-width: 768px) {
	body.page_for_transparent #header {position: absolute; } 
	body.page_for_transparent #header .header-wrapper:not(.stuck) .header-bottom, 
	body.page_for_transparent #header .header-wrapper:not(.stuck) .header-bg-color {background: transparent !important; } 
	body.page_for_transparent.header-shadow .header-wrapper:not(.stuck) {box-shadow: none !important; }
}	
			
/*header*/
<?php if( $color_texts = get_theme_mod('color_texts')): ?>
	.nav>li>a { color:  <?= esc_attr($color_texts); ?>; }
<?php endif; ?>

<?php 
if($header_height_mobile = get_theme_mod( 'header_height_mobile', 70 )){ 
	?>
	@media (max-width: 549px) {
		body .stuck .header-main{height: <?= $header_height_mobile; ?>px !important;}
		body .stuck #logo img{max-height: <?= $header_height_mobile; ?>px !important;}
	}
	<?php  
}
?>
.header-block{ width: unset; display: inline-block; }

/*footer */
.footer-1, .footer-2{
	background-size: 100%;
	background-position: center;
}
@media (max-width: 549px){
	.section-title a{
		margin-left: unset !important;
		margin-top:  15px;
		margin-bottom: 15px;
		padding-left:  0px;
	}
}				
.absolute-footer:not(.text-center) .footer-primary{ padding:  7.5px 0; }
.absolute-footer.text-center .footer-primary{ margin-right: unset; }
@media (max-width:  549px){
	.absolute-footer .container{ display: flex; flex-direction: column;}
}
<?php if(!get_theme_mod('footer_left_text') and !get_theme_mod('footer_right_text')): ?>
	.absolute-footer{ display: none; }
<?php endif; ?>

/*page element*/
.col.post-item .col-inner{
	height: 100%;
}
.row.equalize-box .col-inner{
	height: 100%;
}
.page-col .box-text-inner p{
	font-weight: bold;
}
.page-col .page-box.box-vertical .box-image .box-image{
	display: block;
	width: 100% !important;
}
.mfp-close{
	mix-blend-mode: unset;
}
.sliderbot .img-inner{
	border-radius: 0;
}
.dark .nav-divided>li+li>a:after{
	border-left: 1px solid rgb(255 255 255 / 65%);
}	
.page-checkout li.wc_payment_method,
li.list-style-none{
	list-style: none;
	margin-left: 0px !important;
}
.mfp-content .nav.nav-sidebar>li{
	width: calc(100% - 20px );
}
.mfp-content .nav.nav-sidebar>li:not(.header-social-icons)>a{
	padding-left: 10px;
}
.mfp-content .nav.nav-sidebar>li.html{
	padding-left:  0px;
	padding-right:  0px;
}
.mfp-content .nav.nav-sidebar>li.header-contact-wrapper ul li ,
.mfp-content .nav.nav-sidebar>li.header-contact-wrapper ul li a,
.mfp-content .nav.nav-sidebar>li.header-newsletter-item a{
	padding-left:  0px;
}
.nav-tabs>li>a{background-color: rgb(241 241 241);}
.portfolio-page-wrapper{
	padding-top: 30px;
}
.portfolio-single-page ul li{
	margin-left: 1.3em;
}
.dark .icon-box:hover .has-icon-bg .icon .icon-inner{
	background-color: transparent !important;
}
<?php $mobile_overlay_bg = get_theme_mod('mobile_overlay_bg'); if($mobile_overlay_bg){ ?>
	.main-menu-overlay{ background: #0b0b0b; }
	.main-menu-overlay+ .off-canvas:not(.off-canvas-center) .mfp-content{ background: <?=esc_attr($mobile_overlay_bg); ?> }
<?php } ?> 
@media only screen and (min-width: 850px){
	body.adminz_hide_headermain_on_scroll .header-wrapper.stuck #masthead{
		display: none;
	}
}
.section-title-container .section-title {
	margin-bottom: 0px !important;
}
.section-title-container .section-title .section-title-main {
	padding-bottom: 0px !important;
}

/*woocommerce*/				
@media (max-width:  549px){
	body.adminz_enable_vertical_product_mobile .product-small{
		display: flex;
	}
	body.adminz_enable_vertical_product_mobile .product-small .box-image{
		width: 25% !important;
		max-width: 25% !important;						
		margin:  15px 0px 15px 0px;
	}
	body.adminz_enable_vertical_product_mobile .has-shadow .product-small .box-image{
		margin-left:  15px;
	}
	body.adminz_enable_vertical_product_mobile .product-small .box-text{
		text-align: left;
		padding:  15px;
	}
}
@media (max-width:  549px){
	body.adminz_enable_vertical_product_related_mobile .related .product-small{
		display: flex;
	}
	body.adminz_enable_vertical_product_related_mobile .related .product-small .box-image{
		width: 25% !important;
		max-width: 25% !important;						
		margin:  15px 0px 15px 0px;
	}
	body.adminz_enable_vertical_product_related_mobile .related .has-shadow .product-small .box-image{
		margin-left:  15px;
	}
	body.adminz_enable_vertical_product_related_mobile .related .product-small .box-text{
		text-align: left;
		padding:  15px;
	}
}
.woocommerce-bacs-bank-details ul{
	list-style: none;
}
.woocommerce-bacs-bank-details ul li{
	font-size: 0.9em;
}
.woocommerce-password-strength.bad,
.woocommerce-password-strength.short{
	color: var(--alert-color);
} 
.related-products-wrapper>h3{
	max-width: unset;
}
.box-text-products ul{
	list-style: none;
}

body.adminz_custom_add_to_cart_text .add-to-cart-button a::before,
body.adminz_custom_add_to_cart_text .single_add_to_cart_button::before {
	content: "\e908"; 
	margin-left: -.15em; 
	margin-right: .4em; 
	font-weight: normal; 
	font-family: "fl-icons" !important;
}

/*contact form 7*/
input[type=submit].is-xsmall{font-size: .7em; }
input[type=submit].is-smaller{font-size: .75em; }
input[type=submit].is-mall{font-size: .8em; }
input[type=submit]{font-size: .97em; }
input[type=submit].is-large{font-size: 1.15em; }
input[type=submit].is-larger{font-size: 1.3em; }
input[type=submit].is-xlarge{font-size: 1.5em; }
.wpcf7-form{ margin-bottom: 0px; }
.wpcf7-response-output{
	margin: 0 0 1em !important;
}
.wpcf7-spinner{
	display: none;
}
.wpcf7-form .col .wpcf7-form-control:not(.wpcf7-not-valid) {
	margin-bottom: 0px;
}

/** button */
.adminz_button>i, .adminz_button.reveal-icon>i { display: inline-flex; }

/*zalo icon*/
.button.zalo:not(.is-outline), .button.zalo:hover{ color: #006eab !important; }
.button.skype:not(.is-outline), .button.skype:hover{ color: #0078ca !important; }
.button.whatsapp:not(.is-outline), .button.whatsapp:hover{ color: #51cb5a !important; }

/*ux_video*/
.video.video-fit >div{ width: 100% !important; }

/*menu element*/
body .ux-menu-title{ font-size: 1em; }

/*Select 2*/
<?php 
	if(wp_script_is('select2')):
		?>
		.select2-container .selection .select2-selection--multiple{
			height: unset !important;
			line-height: unset !important;
			padding-top: 0px !important;
			padding-bottom: 0px !important;
			min-height: unset !important;
		}	
		.select2-container .selection .select2-selection--multiple .select2-selection__choice{
			padding-top: 0px !important;
			padding-bottom: 0px !important;
		}	
		/*Fix lỗi không hiển thị nếu hidden*/
		.adminz_woo_form .select2-selection__rendered>li:first-child .select2-search__field{
			width: 100% !important;
		}	
		body .select2-container--default .select2-selection--multiple .select2-selection__rendered{
			padding: 0px;
		}
		<?php 
	endif; 
?>

html:not([ng-app="uxBuilder"]) select[multiple="multiple"]{ display: none; }

html[ng-app="uxBuilder"] select[multiple="multiple"]{ overflow: hidden; }

/** slider */
@media screen and (max-width: 549px){
	body .row-slider .flickity-prev-next-button {
		width: 36px !important;
	}
	body .row-slider .flickity-prev-next-button svg{
		padding: 20% !important;
	}
	body .slider-wrapper .flickity-prev-next-button{
		display: inline-block !important;
		opacity: 1 !important;
	}
}

/*Blog*/
.archive-page-header { display: none; }
.article-inner:hover{ box-shadow: none !important; }
@media (min-width: 850px){
	body.archive .blog-wrapper>.row.align-center>.large-10{ max-width: 100%; flex-basis: 100%; }
}

