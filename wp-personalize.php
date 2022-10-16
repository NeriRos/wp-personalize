<?php

/**
 * Plugin Name: Personalize WP
 * Description: Change your website to fit different visitors.
 * Plugin URI:  https://digitalize.co.il
 * Version:     1.0.0
 * Author:      Neriya Rosner
 * Author URI:  https://digitalize.co.il
 * Text Domain: wp-personalize
 *
 * Elementor tested up to: 3.7.0
 * Elementor Pro tested up to: 3.7.0
 */

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_changing_text_widget($widgets_manager)
{

  require_once(__DIR__ . '/widgets/changing_text.php');

  $widgets_manager->register(new \Elementor_changingText_Widget());
}

add_action('elementor/widgets/register', 'register_changing_text_widget');

function get_utm()

add_filter('wp-personalize-get-utm', )