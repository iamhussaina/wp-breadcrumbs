<?php
/**
 * Public template functions for the Hussainas Breadcrumbs Module.
 *
 * @package WordPress
 * @subpackage HussainasBreadcrumbsModule
 * @since 1.0.0
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Displays the breadcrumbs navigation.
 *
 * This is the primary function for theme integration.
 * It instantiates the Hussainas_Breadcrumbs class, generates the HTML,
 * and echoes it to the page.
 *
 * @param array $args Optional. Arguments to override default settings.
 * @see Hussainas_Breadcrumbs::__construct() for details on $args.
 */
function hussainas_display_breadcrumbs( $args = [] ) {

	// Instantiate the breadcrumbs generator
	$breadcrumbs_generator = new Hussainas_Breadcrumbs( $args );

	// Generate and echo the breadcrumbs HTML
	// We are echoing HTML, so we trust the output from our class.
	// The class internals (Hussainas_Breadcrumbs) are responsible for
	// correctly escaping all data.
	echo $breadcrumbs_generator->generate_breadcrumbs(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
