<?php
// Format message
add_filter( 'woothemes_syl_message', 'wp_kses_post' );
add_filter( 'woothemes_syl_message', 'wptexturize' );
add_filter( 'woothemes_syl_message', 'convert_smilies' );
add_filter( 'woothemes_syl_message', 'convert_chars' );
add_filter( 'woothemes_syl_message', 'wpautop' );
add_filter( 'woothemes_syl_message', 'shortcode_unautop' );
add_filter( 'woothemes_syl_message', 'prepend_attachment' );

// Add Custom CSS
add_action( 'after_syl_header', 'syl_custom_css' );

// Add Custom Header Code
add_action( 'after_syl_header', 'syl_custom_code_header' );

// Add Custom Footer Code
add_action( 'syl_footer', 'syl_custom_code_footer' );

// Add Progress Bar
add_action( 'syl_launch_pad_elements', 'syl_progressbar' );
add_action( 'before_syl_header', 'syl_progressbar_enqueue' );

// Add Countdown
add_action( 'syl_launch_pad_elements', 'syl_countdown' );
add_action( 'before_syl_header', 'syl_countdown_enqueue' );
add_action( 'syl_footer_scripts', 'syl_countdown_js' );

// Add Supersized
add_action( 'before_syl_header', 'syl_supersized_enqueue' );
add_action( 'syl_footer_scripts', 'syl_supersized_js' );

// Add Newsletter
add_action( 'syl_launch_pad_elements', 'syl_newsletter' );

// Add Social Links
add_action( 'syl_social_links', 'syl_social_links' );

// Add Google analytics code
add_action( 'syl_footer_scripts', 'syl_google_analytics_code' );

// Add WooThemes link to footer
add_action( 'syl_footer', 'syl_footer_woothemes_link' );

// Process wysija subscription
add_action( 'woothemes_syl_wysija', 'syl_process_wysija_subscription' );
?>