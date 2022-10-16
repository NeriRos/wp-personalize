<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor RecognitionStage Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Elementor_RecognitionStage_Widget extends \Elementor\Widget_Base
{

	public $timeout = 5;

	/**
	 * Get widget name.
	 *
	 * Retrieve RecognitionStage widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'RecognitionStage';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve RecognitionStage widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return esc_html__('RecognitionStage', 'elementor-RecognitionStage-widget');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve RecognitionStage widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eicon-code';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the RecognitionStage widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return ['general'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the RecognitionStage widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords()
	{
		return ['RecognitionStage', 'personalize', 'recognition', 'stage'];
	}

	/**
	 * Register RecognitionStage widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls()
	{

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__('Content', 'elementor-RecognitionStage-widget'),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->end_controls_section();
	}

	function recognize_user()
	{
		global $wpdb;
		$timeout_new = $this->timeout * 1000;

		print("test " . $_COOKIE['wp_personalize_avatar']);
		// Check if cookie is already set
		if (isset($_COOKIE['wp_personalize_avatar'])) {
			// Use information stored in the cookie 
			$avatar = $_COOKIE['wp_personalize_avatar'];

			$args = array(
				'post_title'      => "conversion_stage_avatar_$avatar",
				'post_type'       => 'elementor_library',
				'post_status'     => 'publish',
				'posts_per_page'  => -1,
				'order'           => 'ASC',
				'meta_query'      => array(
					array(
						'key'         => '_elementor_template_type',
						'value'       => 'popup',
						'compare'     => 'LIKE',
					),
				)
			);

			$query = new WP_Query($args);
			$posts = $query->get_posts();

			$recognition_stage_script = "<script>window.onload = function() {setTimeout(function() {";

			foreach ($posts as $popup) {
				if ($popup->title == "conversion_stage_avatar_$avatar")
					$recognition_stage_script .= "elementorProFrontend.modules.popup.showPopup( { id: $popup->ID } );";
			}

			$recognition_stage_script .= "}, $timeout_new);};</script>";
		} else {
			$posttitle = 'conversion_stage_unrecognized';
			$postid = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_title = '" . $posttitle . "'");
			$recognition_stage_script = "<script>
			window.onload = function() {
				setTimeout(function() {
					elementorProFrontend.modules.popup.showPopup( { id: $postid } );
				}, $timeout_new);
			};</script>";
		}

		// Add a shortcode 
		return $recognition_stage_script;
	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
	{
		print("
			<script>
				function recognize_action(action) {
					jQuery.ajax({url: '/wp-json/wp-personalize/v1/recognize_action', method: 'POST', data: {action}});
				}
			</script>
		");

		print($this->recognize_user());
	}
}
