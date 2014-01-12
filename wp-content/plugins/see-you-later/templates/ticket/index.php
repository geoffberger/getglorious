<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php syl_title(); ?></title>
	<link rel="stylesheet" type="text/css" href="<?php syl_get_template_url( 'style.css' ); ?>" media="screen" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<link href='http://fonts.googleapis.com/css?family=Patua+One|Ovo' rel='stylesheet' type='text/css'>
	<?php syl_header(); ?>
</head>
<body <?php body_class(); ?>>
<div id="wrapper">
	<div id="header" class="col-full">
		<div id="logo">
			<?php syl_logo(); ?>
		</div><!-- /#logo -->
	</div><!-- /#header -->

	<div id="content" class="col-full">
    	<div id="main">
	       	<div id="intro" class="block">
	    		<h1><span><?php syl_text_heading(); ?></span></h1>
	    		<p><?php syl_message(); ?></p>
	    		<?php do_action( 'syl_social_links' ); ?>
	    	</div><!-- #intro -->
	    	<?php do_action( 'syl_launch_pad_elements' ); ?>
   		</div><!--/#main-->
    </div><!-- /#content -->
	<div id="footer" class="col-full">
		<div id="copyright">
			<?php syl_custom_footer(); ?>
		</div>
	</div><!-- /#footer  -->
</div><!-- /#wrapper -->
<?php syl_footer(); ?>
</body>
</html>