<?php
/**
 * Hussainas Breadcrumbs Module Loader
 *
 * This file loads the necessary components for the breadcrumbs module.
 * It should be included in your theme's functions.php file.
 *
 * @package WordPress
 * @subpackage HussainasBreadcrumbsModule
 * @version     1.0.0
 * @author      Hussain Ahmed Shrabon
 * @license     MIT
 * @link        https://github.com/iamhussaina
 * @textdomain  hussainas
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define a constant for the module path for easier inclusion.
if ( ! defined( 'HUSSAINAS_BREADCRUMBS_DIR' ) ) {
	define( 'HUSSAINAS_BREADCRUMBS_DIR', trailingslashit( dirname( __FILE__ ) ) );
}

// Load the main breadcrumbs class.
if ( ! class_exists( 'Hussainas_Breadcrumbs' ) ) {
	require_once HUSSAINAS_BREADCRUMBS_DIR . 'includes/class-hussainas-breadcrumbs.php';
}

// Load the public template functions.
require_once HUSSAINAS_BREADCRUMBS_DIR . 'includes/hussainas-template-functions.php';
