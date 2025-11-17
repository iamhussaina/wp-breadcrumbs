<?php
/**
 * The core breadcrumbs generation class.
 *
 * This class handles the logic, construction, and rendering
 * of the breadcrumb trail.
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
 * Class Hussainas_Breadcrumbs
 *
 * Generates and displays a hierarchical, SEO-friendly breadcrumb trail.
 */
class Hussainas_Breadcrumbs {

	/**
	 * Array of breadcrumb items.
	 *
	 * @var array
	 */
	private $crumbs = [];

	/**
	 * Module settings.
	 *
	 * @var array
	 */
	private $settings = [];

	/**
	 * Constructor.
	 *
	 * @param array $args Custom arguments to override default settings.
	 */
	public function __construct( $args = [] ) {
		$this->settings = $this->get_default_settings( $args );
	}

	/**
	 * Get default settings merged with user arguments.
	 *
	 * @param array $args User-defined arguments.
	 * @return array Merged settings.
	 */
	private function get_default_settings( $args ) {
		$defaults = [
			'container_tag'       => 'nav',
			'container_class'     => 'hussainas-breadcrumbs',
			'list_tag'            => 'ol',
			'list_class'          => 'hussainas-breadcrumbs-list',
			'item_tag'            => 'li',
			'separator'           => '/', // Primarily for CSS hook.
			'home_title'          => esc_html__( 'Home', 'hussainas' ),
			'404_title'           => esc_html__( '404 Not Found', 'hussainas' ),
			'search_title_prefix' => esc_html__( 'Search results for:', 'hussainas' ),
		];

		return wp_parse_args( $args, $defaults );
	}

	/**
	 * Generate the complete breadcrumbs HTML.
	 *
	 * @return string The breadcrumbs HTML.
	 */
	public function generate_breadcrumbs() {
		// Build the trail of crumbs
		$this->build_trail();

		// Render the HTML
		return $this->render();
	}

	/**
	 * Build the array of breadcrumb items based on the current page.
	 */
	private function build_trail() {
		// 1. Add the Home/Front Page crumb.
		$this->add_home_crumb();

		// 2. Add crumbs based on context.
		if ( is_front_page() ) {
			// We are on the front page, do nothing more.
			return;

		} elseif ( is_home() ) {
			// Blog posts page.
			$this->add_crumb( get_the_title( get_option( 'page_for_posts' ) ) );

		} elseif ( is_singular() ) {
			// Singular post, page, or CPT.
			$this->add_singular_crumbs();

		} elseif ( is_archive() ) {
			// Archive pages (category, tag, CPT archive, etc.)
			$this->add_archive_crumbs();

		} elseif ( is_search() ) {
			// Search results page.
			$this->add_search_crumb();

		} elseif ( is_404() ) {
			// 404 Not Found page.
			$this->add_404_crumb();
		}
	}

	/**
	 * Render the final HTML for the breadcrumbs.
	 *
	 * @return string The complete HTML markup.
	 */
	private function render() {
		if ( empty( $this->crumbs ) ) {
			return '';
		}

		$html      = '';
		$settings  = $this->settings;
		$item_count = count( $this->crumbs );

		// Open container.
		$html .= sprintf(
			'<%s class="%s" aria-label="breadcrumb" style="--breadcrumb-separator: \'%s\';">',
			esc_attr( $settings['container_tag'] ),
			esc_attr( $settings['container_class'] ),
			esc_attr( $settings['separator'] )
		);

		// Open list with Schema.org markup.
		$html .= sprintf(
			'<%s class="%s" itemscope itemtype="https://schema.org/BreadcrumbList">',
			esc_attr( $settings['list_tag'] ),
			esc_attr( $settings['list_class'] )
		);

		// Build each list item.
		foreach ( $this->crumbs as $i => $crumb ) {
			$position = $i + 1;
			$is_last  = ( $position === $item_count );

			// Open item.
			$html .= sprintf(
				'<%s itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"',
				esc_attr( $settings['item_tag'] )
			);

			// Add 'aria-current' for the last item.
			if ( $is_last ) {
				$html .= ' aria-current="page"';
			}

			$html .= '>';

			// Add the link or text.
			if ( ! $is_last && ! empty( $crumb['link'] ) ) {
				$html .= sprintf(
					'<a itemprop="item" href="%s"><span itemprop="name">%s</span></a>',
					esc_url( $crumb['link'] ),
					esc_html( $crumb['title'] )
				);
			} else {
				$html .= sprintf(
					'<span itemprop="name">%s</span>',
					esc_html( $crumb['title'] )
				);
			}

			// Add schema.org position.
			$html .= sprintf( '<meta itemprop="position" content="%d" />', $position );

			// Close item.
			$html .= sprintf( '</%s>', esc_attr( $settings['item_tag'] ) );
		}

		// Close list.
		$html .= sprintf( '</%s>', esc_attr( $settings['list_tag'] ) );

		// Close container.
		$html .= sprintf( '</%s>', esc_attr( $settings['container_tag'] ) );

		return $html;
	}

	/**
	 * Helper function to add a crumb to the trail.
	 *
	 * @param string $title The text for the crumb.
	 * @param string $link  Optional. The URL for the crumb.
	 */
	private function add_crumb( $title, $link = '' ) {
		$this->crumbs[] = [
			'title' => $title,
			'link'  => $link,
		];
	}

	/**
	 * Adds the "Home" crumb.
	 */
	private function add_home_crumb() {
		$this->add_crumb( $this->settings['home_title'], esc_url( home_url( '/' ) ) );
	}

	/**
	 * Adds crumbs for singular items (post, page, CPT).
	 */
	private function add_singular_crumbs() {
		global $post;
		$post_type = get_post_type( $post );

		// 1. Add post type archive link (if it's a CPT and has an archive).
		if ( 'post' !== $post_type && 'page' !== $post_type ) {
			$post_type_object = get_post_type_object( $post_type );
			if ( $post_type_object && $post_type_object->has_archive ) {
				$this->add_crumb( $post_type_object->labels->name, get_post_type_archive_link( $post_type ) );
			}
		}

		// 2. Add hierarchical taxonomy terms (e.g., categories).
		if ( 'post' === $post_type ) {
			$terms = get_the_category( $post->ID );
			if ( $terms ) {
				// We'll just use the first category for simplicity.
				// A more complex solution might find a "primary" category.
				$term = $terms[0];
				$this->add_taxonomy_ancestors( $term, 'category' );
			}
		} elseif ( 'page' !== $post_type ) {
			// Handle custom post type taxonomies.
			// This logic can be expanded to find the "primary" taxonomy.
			$taxonomies = get_object_taxonomies( $post_type, 'objects' );
			$taxonomies = wp_filter_object_list( $taxonomies, [ 'hierarchical' => true ], 'and', 'name' );
			
			if ( ! empty( $taxonomies ) ) {
				$taxonomy_name = reset( $taxonomies ); // Get the first hierarchical taxonomy.
				$terms = get_the_terms( $post->ID, $taxonomy_name );
				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					$this->add_taxonomy_ancestors( $terms[0], $taxonomy_name );
				}
			}
		}

		// 3. Add page ancestors (if it's a hierarchical page).
		if ( 'page' === $post_type && $post->post_parent ) {
			$ancestors = get_post_ancestors( $post->ID );
			$ancestors = array_reverse( $ancestors );
			foreach ( $ancestors as $ancestor_id ) {
				$this->add_crumb( get_the_title( $ancestor_id ), get_permalink( $ancestor_id ) );
			}
		}

		// 4. Add the current item.
		$this->add_crumb( get_the_title( $post->ID ) );
	}

	/**
	 * Adds crumbs for archive pages.
	 */
	private function add_archive_crumbs() {
		if ( is_category() || is_tag() || is_tax() ) {
			// Taxonomy archive.
			$term = get_queried_object();
			if ( $term ) {
				$this->add_taxonomy_ancestors( $term, $term->taxonomy );
				$this->add_crumb( $term->name );
			}
		} elseif ( is_post_type_archive() ) {
			// CPT Archive.
			$this->add_crumb( post_type_archive_title( '', false ) );

		} elseif ( is_author() ) {
			// Author Archive.
			$this->add_crumb( sprintf(
				esc_html__( 'Author: %s', 'hussainas' ),
				get_the_author_meta( 'display_name', get_query_var( 'author' ) )
			) );

		} elseif ( is_date() ) {
			// Date Archive.
			if ( is_year() ) {
				$this->add_crumb( get_the_date( 'Y' ) );
			} elseif ( is_month() ) {
				$this->add_crumb( get_the_date( 'F Y' ) );
			} elseif ( is_day() ) {
				$this->add_crumb( get_the_date( 'F j, Y' ) );
			}
		}
	}

	/**
	 * Adds crumbs for a search results page.
	 */
	private function add_search_crumb() {
		$this->add_crumb( $this->settings['search_title_prefix'] . ' "' . get_search_query() . '"' );
	}

	/**
	 * Adds the crumb for a 404 page.
	 */
	private function add_404_crumb() {
		$this->add_crumb( $this->settings['404_title'] );
	}

	/**
	 * Recursively adds ancestor terms for a taxonomy.
	 *
	 * @param WP_Term $term The term object.
	 * @param string  $taxonomy The taxonomy name.
	 */
	private function add_taxonomy_ancestors( $term, $taxonomy ) {
		if ( ! $term || is_wp_error( $term ) || $term->parent === 0 ) {
			return;
		}

		$ancestors = get_ancestors( $term->term_id, $taxonomy, 'taxonomy' );
		$ancestors = array_reverse( $ancestors );

		foreach ( $ancestors as $ancestor_id ) {
			$ancestor_term = get_term( $ancestor_id, $taxonomy );
			if ( $ancestor_term && ! is_wp_error( $ancestor_term ) ) {
				$this->add_crumb( $ancestor_term->name, get_term_link( $ancestor_term ) );
			}
		}
	}
}
