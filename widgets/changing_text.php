<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}


use \Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;

/**
 * Elementor changingText Widget.
 *
 * Elementor widget that inserts an embbedable content into the page, from any given URL.
 *
 * @since 1.0.0
 */
class Elementor_changingText_Widget extends \Elementor\Widget_Base
{
	
	public $max_utms = 10;

  /**
   * Get widget name.
   *
   * Retrieve changingText widget name.
   *
   * @since 1.0.0
   * @access public
   * @return string Widget name.
   */
  public function get_name()
  {
    return 'changingText';
  }

  /**
   * Get widget title.
   *
   * Retrieve changingText widget title.
   *
   * @since 1.0.0
   * @access public
   * @return string Widget title.
   */
  public function get_title()
  {
    return esc_html__('changingText', 'elementor-changingText-widget');
  }

  /**
   * Get widget icon.
   *
   * Retrieve changingText widget icon.
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
   * Retrieve the list of categories the changingText widget belongs to.
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
   * Retrieve the list of keywords the changingText widget belongs to.
   *
   * @since 1.0.0
   * @access public
   * @return array Widget keywords.
   */
  public function get_keywords()
  {
    return ['changingText', 'customize', 'utm', 'text'];
  }

	
  /**
   * Get utm's id.
   *
   * Retrieve the id of current utm.
   *
   * @since 1.0.0
   * @access public
   * @return text UTM's id.
   */
  public function get_utm_id($settings)
  {
	for($i = 1; $i <= $this->max_utms; $i++) {
		$utm = $_GET["UTM"];
		if(!isset($utm)) {
			$utm = $_GET["utm"];
		}
		$utm_settings = $settings["UTM$i"];
		if($utm_settings && $utm_settings == $utm)
    		return $i;
  	}
	  
	return;
  }
	
	
  /**
   * Get utm's text.
   *
   * Retrieve the customized text for current utm.
   *
   * @since 1.0.0
   * @access public
   * @return text UTM's text.
   */
  public function get_text($settings)
  {
	$utm_id = $this->get_utm_id($settings);
	return ($utm_id) ? $settings["UTM$utm_id-T"] : $settings['default_text'];
  }
	
  /**
   * Register changingText widget controls.
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
        'label' => esc_html__('Content', 'elementor-changingText-widget'),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );


		$this->add_control(
      "default_text",
			[
				'label' => esc_html__( "default Text", 'elementor-changingText-widget' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( "Enter your default title", 'elementor-changingText-widget' ),
			]
		);
	  for($i = 1; $i <= $this->max_utms; $i++) {
	  
		  $this->add_control(
      "UTM$i",
      [
        'label' => esc_html__("UTM $i", 'elementor-changingText-widget'),
        'type' => \Elementor\Controls_Manager::TEXT,
        'input_type' => 'text',
        'placeholder' => esc_html__('UTM', 'elementor-changingText-widget'),
      ]
    );

		$this->add_control(
      "UTM$i-T",
			[
				'label' => esc_html__( "UTM $i Text", 'elementor-changingText-widget' ),
				'type' => \Elementor\Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => esc_html__( "Enter your title for UTM $i", 'elementor-changingText-widget' ),
			]
		);
    
	  }

    $this->end_controls_section();
	  
	  $this->start_controls_section(
			'section_title',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
			]
		);

		$this->add_control(
			'link',
			[
				'label' => esc_html__( 'Link', 'elementor' ),
				'type' => \Elementor\Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'size',
			[
				'label' => esc_html__( 'Size', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default' => esc_html__( 'Default', 'elementor' ),
					'small' => esc_html__( 'Small', 'elementor' ),
					'medium' => esc_html__( 'Medium', 'elementor' ),
					'large' => esc_html__( 'Large', 'elementor' ),
					'xl' => esc_html__( 'XL', 'elementor' ),
					'xxl' => esc_html__( 'XXL', 'elementor' ),
				],
			]
		);

		$this->add_control(
			'header_size',
			[
				'label' => esc_html__( 'HTML Tag', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => esc_html__( 'Alignment', 'elementor' ),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'elementor' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'elementor' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'elementor' ),
						'icon' => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__( 'Justified', 'elementor' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'view',
			[
				'label' => esc_html__( 'View', 'elementor' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => 'traditional',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			[
				'label' => esc_html__( 'Title', 'elementor' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Text Color', 'elementor' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Text_Stroke::get_type(),
			[
				'name' => 'text_stroke',
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'text_shadow',
				'selector' => '{{WRAPPER}} .elementor-heading-title',
			]
		);

		$this->add_control(
			'blend_mode',
			[
				'label' => esc_html__( 'Blend Mode', 'elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'Normal', 'elementor' ),
					'multiply' => 'Multiply',
					'screen' => 'Screen',
					'overlay' => 'Overlay',
					'darken' => 'Darken',
					'lighten' => 'Lighten',
					'color-dodge' => 'Color Dodge',
					'saturation' => 'Saturation',
					'color' => 'Color',
					'difference' => 'Difference',
					'exclusion' => 'Exclusion',
					'hue' => 'Hue',
					'luminosity' => 'Luminosity',
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-heading-title' => 'mix-blend-mode: {{VALUE}}',
				],
				'separator' => 'none',
			]
		);

		$this->end_controls_section();
  }


	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$utm_id = $this->get_utm_id($settings);
		$text_id = ($utm_id) ? "UTM$utm_id-T" : "default_text";
		
		if ( '' === $settings[$text_id] ) {
			return;
		}

		$this->add_render_attribute( $text_id, 'class', 'elementor-heading-title' );

		if ( ! empty( $settings['size'] ) ) {
			$this->add_render_attribute( $text_id, 'class', 'elementor-size-' . $settings['size'] );
		}

		$this->add_inline_editing_attributes( $text_id );

		$title = $settings[$text_id];

		if ( ! empty( $settings['link']['url'] ) ) {
			$this->add_link_attributes( 'url', $settings['link'] );

			$title = sprintf( '<a %1$s>%2$s</a>', $this->get_render_attribute_string( 'url' ), $title );
		}

		$title_html = sprintf( '<%1$s %2$s>%3$s</%1$s>', \Elementor\Utils::validate_html_tag( $settings['header_size'] ), $this->get_render_attribute_string( $text_id ), $title );

		// PHPCS - the variable $title_html holds safe data.
		echo $title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Render heading widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {		
		
		?>
		<#
		var title = settings.default_text;

		if ( '' !== settings.link.url ) {
			title = '<a href="' + settings.link.url + '">' + title + '</a>';
		}

		view.addRenderAttribute( 'title', 'class', [ 'elementor-heading-title', 'elementor-size-' + settings.size ] );

		view.addInlineEditingAttributes( 'title' );

		var headerSizeTag = elementor.helpers.validateHTMLTag( settings.header_size ),
			title_html = '<' + headerSizeTag  + ' ' + view.getRenderAttributeString( 'title' ) + '>' + title + '</' + headerSizeTag + '>';

		print( title_html );
		#>
		<?php
	}
}
