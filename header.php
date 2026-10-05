<?php
/**
 * Header: doctype + <head> (wp_head) + preloader/header/popup clonados.
 * @package logisko
 */
?>
<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="shortcut icon" type="image/x-icon" href="<?php echo esc_url( grenvios_img_url( 'favicon.png' ) ); ?>">
	<?php grenvios_perf_mark( 'head: antes de wp_head' ); wp_head(); grenvios_perf_mark( 'wp_head' ); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php logisko_part( 'header' ); grenvios_perf_mark( 'markup cabecera' ); /* preloader + header.main-header + popup search (markup real) */ ?>
