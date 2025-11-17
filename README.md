# WordPress Breadcrumbs

A lightweight, professional, and SEO-friendly breadcrumbs navigation module designed for easy integration into any WordPress theme.

This module provides a complete, hierarchical breadcrumb trail without relying on any external plugins. It is built using an object-oriented approach, ensuring clean code, maintainability, and extensibility.

## ✨ Features

* **SEO Friendly:** Includes structured data markup (Schema.org) to enhance search engine visibility.
* **Accessible:** Implements `aria-label` and `aria-current` attributes for better screen reader support.
* **Fully Hierarchical:** Correctly handles posts, pages, custom post types, taxonomies, archives, and 404 pages.
* **Lightweight & Performant:** Optimized code with no unnecessary bloat.
* **Easy to Integrate:** Simple two-step integration process.
* **Configurable:** Easily customize settings like the home title and separator via arguments.

---

## 🚀 Installation

Follow these two steps to integrate the module into your theme:

1.  **Copy the Files:**
    Download the `wordpress-breadcrumbs-module` directory and place it inside your theme's root folder. Your theme structure should look something like this:
    ```
    /wp-content/themes/your-theme/
    |-- /wp-breadcrumbs/
    |-- functions.php
    |-- index.php
    |-- style.css
    ```

2.  **Include the Module:**
    Open your theme's `functions.php` file and add the following line to include the module's loader:
    ```php
    require_once get_template_directory() . '/wp-breadcrumbs/load.php';
    ```

---

## 🛠️ How to Use

To display the breadcrumbs, add the following function call within your theme's template files (e.g., `header.php`, `page.php`, or `single.php`) wherever you want the breadcrumbs to appear.

### Basic Usage

It's recommended to wrap the call in a `function_exists` check.

```php
<div class="container">
    <?php
    if ( function_exists( 'hussainas_display_breadcrumbs' ) ) {
        hussainas_display_breadcrumbs();
    }
    ?>
</div>
```

### Advanced Usage (Customization)

You can pass an array of arguments to `hussainas_display_breadcrumbs()` to override the default settings.

```php
<?php
$breadcrumb_args = [
    'separator'       => '›', // Changes the separator character
    'home_title'      => 'Portal', // Changes the "Home" link text
    'container_class' => 'custom-breadcrumbs-class', // Adds a custom class to the <nav>
];

if ( function_exists( 'hussainas_display_breadcrumbs' ) ) {
    hussainas_display_breadcrumbs( $breadcrumb_args );
}
?>
```

**Available Arguments:**

* `'container_tag'`: (string) The HTML tag for the container. Default: `nav`.
* `'container_class'`: (string) The CSS class for the container. Default: `hussainas-breadcrumbs`.
* `'list_tag'`: (string) The HTML tag for the list. Default: `ol`.
* `'list_class'`: (string) The CSS class for the list. Default: `hussainas-breadcrumbs-list`.
* `'item_tag'`: (string) The HTML tag for each list item. Default: `li`.
* `'separator'`: (string) The separator character (hidden, used for styling hook if needed). Default: `/`.
* `'home_title'`: (string) The text for the "Home" link. Default: `Home`.
* `'404_title'`: (string) The text for 404 pages. Default: `404 Not Found`.
* `'search_title_prefix'`: (string) The prefix for search results. Default: `Search results for:`.

---

## 🎨 Basic Styling (CSS)

This module generates semantic HTML. You can use the following CSS as a starting point. Add it to your theme's `style.css` file.

```css
/*
 * Basic Breadcrumbs Styling
 */
.hussainas-breadcrumbs {
    font-size: 0.9em;
    color: #777;
    margin-bottom: 1.5em;
}

.hussainas-breadcrumbs-list {
    list-style: none;
    margin: 0;
    padding: 0;
}

/* Display items inline */
.hussainas-breadcrumbs-list li {
    display: inline-block;
    margin: 0;
    padding: 0;
}

/* Add the separator */
.hussainas-breadcrumbs-list li:not(:last-child)::after {
    content: var(--breadcrumb-separator, '/'); /* Uses custom property or fallback */
    margin: 0 0.5em;
    opacity: 0.7;
}

.hussainas-breadcrumbs-list li a {
    text-decoration: none;
    color: #555;
}

.hussainas-breadcrumbs-list li a:hover {
    text-decoration: underline;
}

/* Style for the current page item */
.hussainas-breadcrumbs-list li[aria-current="page"] {
    font-weight: 600;
    color: #333;
}
```
