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

function set_avatar_cookie($avatar)
{
  if (!isset($_COOKIE['wp_personalize_avatars'])) {
    // set a cookie for 1 year
    setcookie('wp_personalize_avatars', [$avatar], time() + 31556926);
  } else if (isset($_COOKIE['wp_personalize_avatars'])) {
    $avatars = $_COOKIE['wp_personalize_avatars'];
    array_push($avatars, $avatar);
    setcookie('wp_personalize_avatars', $avatars, time() + 31556926);
  }
}

function get_avatar()
{
  return end($_COOKIE['wp_personalize_avatars']);
}

function set_recognition_stages($stage)
{
  if (!isset($_COOKIE['wp_personalize_recognition_stages'])) {

    // set a cookie for 1 year
    setcookie('wp_personalize_recognition_stages', [$stage], time() + 31556926);
  }
  if (isset($_COOKIE['wp_personalize_recognition_stages'])) {
    $stages = $_COOKIE['wp_personalize_recognition_stages'];
    $avatar = get_avatar();
    $last_stage = array_pop($stages);
    array_push($stages, $last_stage . "|" . $avatar, $stage);
    setcookie('wp_personalize_recognition_stages', $stages, time() + 31556926);
  }
}

function send_custom_webhook($record, $handler)
{
  $form_name = $record->get_form_settings('form_name');

  // Replace MY_FORM_NAME with the name you gave your form
  if ('recognize_user' !== $form_name || 'Recognize User' !== $form_name) {
    return;
  }

  $raw_fields = $record->get('fields');
  $fields = [];
  foreach ($raw_fields as $id => $field) {
    $fields[$id] = $field['value'];
  }

  do_action("wp_personalize_set_avatar_id_by_intent", $fields['intent'], $fields['problems']);
}
add_action('elementor_pro/forms/new_record', 'send_custom_webhook', 10, 2);

function set_avatar_id($intent, $problems)
{
  $avatar = 0;

  if ($intent == "startup product" && in_array("no time", $problems)) {
    $avatar = 4.2;
  } else if ($intent == "startup product") {
    $avatar = 4;
  } else if ($intent == "services scale") {
    $avatar = 6;
  } else if ($intent == "efficient developer") {
    $avatar = 2;
  } else if ($intent == "learn spec") {
    $avatar = 0;
  } else {
    $avatar = -1;
  }

  set_avatar_cookie($avatar);
  set_recognition_stages("questionnair");
}

add_filter('wp_personalize_set_avatar_id', 'set_avatar_id', 10, 2);


function recognize_action($action)
{
  $avatar = -2;

  switch ($action) {
    case 'customer_learn_more-entrepreneur':
      $avatar = 4;
      break;

    case 'customer_learn_more-software-developer':
      $avatar = 4;
      break;

    case 'customer_learn_more-services':
      $avatar = 4;
      break;

    default:
      break;
  }

  set_recognition_stages("action");
  if ($avatar != -2)
    set_avatar_cookie($avatar);
}

add_filter('wp_personalize_recognize_action', 'recognize_action', 10, 1);

function recognize_action_callback()
{
  if ($_POST['action'])
    do_action('wp_personalize_recognize_action', $_POST['action']);
}

add_action('rest_api_init', function () {
  register_rest_route('wp-personalize/v1', '/recognize_action', array(
    'methods' => 'POST',
    'callback' => 'recognize_action_callback',
  ));
});
