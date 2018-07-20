<?php
/**
 * Plugin Name: DTL Detector
 * Plugin URI: https://github.com/DustOfWinter/dtl_detector
 * Description: Light module that uses Pantheon environment variable to remind you whether you're on Dev, Test, or Live.
 * Version: 0.1
 * Author: Dustin Himes
 * Author URI: http://dustinhimes.com
**/

define('DTL_PLUGIN_URL', plugins_url('', __FILE__));
define('DTL_PLUGIN_VERSION', '0.1');

function dtl_detector_add(){
	//get the admin menu
    global $wp_admin_bar;

    //build the menu args
    $args = dtl_detector_build_args();

    if(isset($args)){
	    //add the node
	    $wp_admin_bar->add_node($args);
	}
}

add_action( 'admin_bar_menu', 'dtl_detector_add', 999 );

function dtl_detector_build_args(){

	//get Pantheon Environment Variable
	$environment = $_ENV['PANTHEON_ENVIRONMENT'];

	if(isset($environment)){
		//set the title
		switch ($environment) {
			case 'dev':
				$title = "Development";
				break;
			case 'test':
				$title = "Test";
				break;
			case 'live':
				$title = "Live";
				break;
		}

		//set the CSS class
		$class = "dtl-detector ".$environment;

		//create args array
		$args = array(
			'title' => $title,
			'meta' => array(
				'class' => $class,
			),
		);

		return $args;
	}
}

function dtl_loader_activation_check(){
	if(isset($_ENV['PANTHEON_ENVIRONMENT'])){
		return TRUE;
	}
	else{
		return FALSE;
	}
}

// only allow activation if needed variables exist
function dtl_loader_activate(){
	if(!dtl_loader_activation_check()){
		deactivate_plugins( plugin_basename(__FILE__) );
		wp_die( __('DTL Detector only supports the Pantheon environment!', 'dtl_loader') );
	}
}
register_activation_hook( __FILE__, 'dtl_loader_activate' );

// add style
add_action( 'wp_enqueue_scripts', 'dtl_detector_style_enqueue');
add_action( 'admin_enqueue_scripts', 'dtl_detector_style_enqueue');
function dtl_detector_style_enqueue() {
	wp_register_style('dtl-detector', DTL_PLUGIN_URL.'/css/dtl_detector.css', false, DTL_PLUGIN_VERSION);
    wp_enqueue_style('dtl-detector');
}