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
function register_personalization_widgets($widgets_manager)
{

  require_once(__DIR__ . '/widgets/changing_text.php');
  require_once(__DIR__ . '/widgets/recognition_stage.php');

  $widgets_manager->register(new \Elementor_changingText_Widget());
  $widgets_manager->register(new \Elementor_RecognitionStage_Widget());
}

add_action('elementor/widgets/register', 'register_personalization_widgets');

// function set_avatar_cookie($avatar)
// {
//   if (!isset($_COOKIE['wp_personalize_avatars'])) {
//     // set a cookie for 1 year
//     setcookie('wp_personalize_avatars', json_encode([$avatar]), time() + 31556926, '/');
//   } else if (isset($_COOKIE['wp_personalize_avatars'])) {
//     $avatars = get_recognized_avatars();
//     array_push($avatars, $avatar);
//     setcookie('wp_personalize_avatars', json_encode($avatars), time() + 31556926, '/');
//   }
// }

function set_recognition_stages($stage, $avatar)
{
  $new_stage = ["stage" => $stage, "avatar" => $avatar];

  if (!isset($_COOKIE['wp_personalize_recognition_stages'])) {
    // set a cookie for 1 year
    setcookie('wp_personalize_recognition_stages', json_encode($new_stage), time() + 31556926, '/');
  }
  if (isset($_COOKIE['wp_personalize_recognition_stages'])) {
    $stages = get_recognized_stages();
    array_push($stages, $new_stage);
    setcookie('wp_personalize_recognition_stages', json_encode($stages), time() + 31556926, '/');
  }

  $avatar = get_recognized_avatar();
  setcookie('wp_personalize_avatar', $avatar, time() + 31556926, '/');
}

function get_recognized_stages()
{
  return json_decode(urldecode($_COOKIE['wp_personalize_recognition_stages']));
}

function get_recognized_avatar()
{
  return end(get_recognized_stages())['avatar'];
}

function get_recognized_stage()
{
  return end(get_recognized_stages())['stage'];
}

add_filter("wp_personalization_get_recognized_avatar", "get_recognized_avatar", 10, 0);

function send_custom_webhook($record, $handler)
{
  $form_name = $record->get_form_settings('form_name');

  // Replace MY_FORM_NAME with the name you gave your form
  if ('recognize_user' !== $form_name && 'Recognize User' !== $form_name) {
    return;
  }

  $raw_fields = $record->get('fields');
  $fields = [];
  foreach ($raw_fields as $id => $field) {
    $fields[$id] = $field['value'];
  }
  $avatar = apply_filters("wp_personalize_set_avatar_id", $fields['intent'], $fields['problems']);
  // set_avatar_cookie($avatar);
  set_recognition_stages("questionnair", $avatar);
}
add_action('elementor_pro/forms/new_record', 'send_custom_webhook', 10, 2);

function set_avatar_id($intent, $problems)
{
  $avatar = 0;

  if ($intent == "startup product" && str_contains("no time", $problems)) {
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

  return $avatar;
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

  return $avatar;
}

add_filter('wp_personalize_recognize_action', 'recognize_action', 10, 1);

function recognize_action_callback()
{
  if ($_POST['action']) {
    $avatar = apply_filters('wp_personalize_recognize_action', $_POST['action']);

    set_recognition_stages("action", $avatar);
  }
}

add_action('rest_api_init', function () {
  register_rest_route('wp-personalize/v1', '/recognize_action', array(
    'methods' => 'POST',
    'callback' => 'recognize_action_callback',
  ));
});
