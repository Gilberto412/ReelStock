<?php
/**
 * Class: Jet_Reviews
 * Name: Jet Reviews
 * Slug: jet-reviews
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Widget_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Jet_Reviews extends Jet_Reviews_Base {

	private $__review_data = [];

	private $__primary_review_data = [];

	public function get_name() {
		return 'jet-reviews';
	}

	public function get_title() {
		return esc_html__( 'Static Review', 'jet-reviews' );
	}

	public function get_icon() {
		return 'jet-reviews-icon-static-widget';
	}

	public function get_help_url() {
		return 'https://crocoblock.com/knowledge-base/article-category/jetreviews/?utm_source=jetreviews&utm_medium=jet-reviews&utm_campaign=need-help';
	}

	public function get_categories() {
		return array( 'jet-reviews' );
	}

	protected function register_controls() {

		$css_scheme = apply_filters(
			'jet-reviews/widget/css-scheme',
			array(
				'header'          => '.jet-review__header',
				'total_average'   => '.jet-review__total-average',
				'form'            => '.jet-review__form',
				'form_input'      => '.jet-review__form input[type="text"], .jet-review__form textarea',
				'review_item'     => '.jet-review__item',
				'author'          => '.jet-review__user',
				'title'           => '.jet-review__title',
				'fields_box'      => '.jet-review__fields',
				'field'           => '.jet-review__field',
				'field_heading'   => '.jet-review__field-heading',
				'stars'           => '.jet-review__stars',
				'field_value'     => '.jet-review__field-val',
				'progress'        => '.jet-review__progress',
				'progress_bar'    => '.jet-review__progress-bar',
				'progress_value'  => '.jet-review__progress-val',
				'summary'         => '.jet-review__summary-content',
				'summary_data'    => '.jet-review__summary-data',
				'summary_value'   => '.jet-review__summary-val',
				'summary_title'   => '.jet-review__summary-title',
				'summary_content' => '.jet-review__summary-text',
				'summary_legend'  => '.jet-review__summary-legend',
				'summary'         => '.jet-review__summary-content',
			)
		);

		$this->start_controls_section(
			'section_content',
			array(
				'label' => esc_html__( 'Content', 'jet-reviews' ),
			)
		);

		$this->add_control(
			'content_source',
			array(
				'type'       => 'select',
				'label'      => esc_html__( 'Content Source', 'jet-reviews' ),
				'default'    => 'manually',
				'options'    => $this->__review_sources(),
			)
		);

		$this->add_control(
			'review_post_id',
			array(
				'label'       => esc_html__( 'Get Data From Post', 'jet-reviews' ),
				'description' => esc_html__( 'Enter post ID to get data from or leave empy to get data from current post', 'jet-reviews' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'condition'   => array(
					'content_source' => 'post-meta',
				),
			)
		);

		$this->add_control(
			'show_review_author',
			array(
				'label'        => esc_html__( 'Show Review Author', 'jet-reviews' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-reviews' ),
				'label_off'    => esc_html__( 'No', 'jet-reviews' ),
				'return_value' => 'yes',
				'default'      => 'no',
			)
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'field_label',
			array(
				'label' => esc_html__( 'Label', 'jet-reviews' ),
				'type' => Controls_Manager::TEXT,
			)
		);

		$repeater->add_control(
			'field_value',
			array(
				'label'   => esc_html__( 'Field Value', 'jet-reviews' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 9,
				'min'     => 1,
				'max'     => 100,
				'step'    => 0.1,
			)
		);

		$repeater->add_control(
			'field_max',
			array(
				'label'   => esc_html__( 'Field Max', 'jet-reviews' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 10,
				'min'     => 1,
				'max'     => 100,
				'step'    => 0.1,
			)
		);

		$this->add_control(
			'review_fields',
			array(
				'type'    => Controls_Manager::REPEATER,
				'fields'  => $repeater->get_controls(),
				'default' => array(
					array(
						'field_label' => esc_html__( 'Design', 'jet-reviews' ),
						'field_value' => 9,
						'field_max'   => 10,
					),
				),
				'title_field' => '{{{ field_label }}}',
				'condition'   => array(
					'content_source'      => 'manually',
				),
			)
		);

		$this->add_control(
			'summary_title',
			array(
				'label'     => esc_html__( 'Summary Title', 'jet-reviews' ),
				'default'   => esc_html__( 'Review Summary Title', 'jet-reviews' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'content_source' => 'manually',
				),
			)
		);

		$this->add_control(
			'summary_text',
			array(
				'label'     => esc_html__( 'Summary Text', 'jet-reviews' ),
				'type'      => Controls_Manager::WYSIWYG,
				'default'   => esc_html__( 'Review Summary Description', 'jet-reviews' ),
				'condition' => array(
					'content_source' => 'manually',
				),
			)
		);

		$this->add_control(
			'summary_legend',
			array(
				'label'     => esc_html__( 'Summary Legend', 'jet-reviews' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => esc_html__( 'Nice!', 'jet-reviews' ),
				'condition' => array(
					'content_source' => 'manually',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_sdata',
			array(
				'label' => esc_html__( 'Structured Data', 'jet-reviews' ),
			)
		);

		$this->add_control(
			'add_sdata',
			array(
				'label'        => esc_html__( 'Add Structured Data to review box', 'jet-reviews' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-reviews' ),
				'label_off'    => esc_html__( 'No', 'jet-reviews' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'sdata_item_name',
			array(
				'label'     => esc_html__( 'Review Item Name', 'jet-reviews' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'add_sdata'      => 'yes',
					'content_source' => 'manually',
				),
			)
		);

		$this->add_control(
			'sdata_item_image',
			array(
				'label'   => esc_html__( 'Review Item Image', 'jet-reviews' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition' => array(
					'add_sdata'      => 'yes',
					'content_source' => 'manually',
				),
			)
		);

		$this->add_control(
			'sdata_item_description',
			array(
				'label'     => esc_html__( 'Review Item Description', 'jet-reviews' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => '',
				'condition' => array(
					'add_sdata'      => 'yes',
					'content_source' => 'manually',
				),
			)
		);

		$default_date = date( 'Y-m-d H:i' );

		$this->add_control(
			'sdata_review_date',
			array(
				'label'     => esc_html__( 'Review Date', 'jet-reviews' ),
				'type'      => Controls_Manager::DATE_TIME,
				'default'   => $default_date,
				'separator' => 'before',
				'condition' => array(
					'add_sdata'      => 'yes',
					'content_source' => 'manually',
				),
			)
		);

		$this->add_control(
			'sdata_review_author',
			array(
				'label'     => esc_html__( 'Review Author Name', 'jet-reviews' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => '',
				'condition' => array(
					'add_sdata'      => 'yes',
					'content_source' => 'manually',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_header_settings',
			array(
				'label' => esc_html__( 'Header Settings', 'jet-reviews' ),
			)
		);

		$this->add_control(
			'review_title',
			array(
				'label'   => esc_html__( 'Title', 'jet-reviews' ),
				'type'    => Controls_Manager::TEXT,
				'condition'   => array(
					'content_source'      => 'manually',
				),
			)
		);

		$this->add_control(
			'total_average_layout',
			array(
				'label'   => esc_html__( 'Average Layout', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'points',
				'options' => array(
					'stars'      => esc_html__( 'Stars', 'jet-reviews' ),
					'percentage' => esc_html__( 'Percentage', 'jet-reviews' ),
					'points'     => esc_html__( 'Points', 'jet-reviews' ),
				),
			)
		);

		$this->add_control(
			'total_average_progressbar',
			array(
				'label'        => esc_html__( 'Progressbar', 'jet-reviews' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-reviews' ),
				'label_off'    => esc_html__( 'Hide', 'jet-reviews' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => array(
					'total_average_layout' => array( 'percentage', 'points' ),
				),
			)
		);

		$this->add_control(
			'total_average_value_position',
			array(
				'label'   => esc_html__( 'Values Position', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'above',
				'options' => array(
					'above'  => esc_html__( 'Above Progressbar', 'jet-reviews' ),
					'inside' => esc_html__( 'Inside Progressbar', 'jet-reviews' ),
				),
				'condition' => array(
					'total_average_layout'      => array( 'percentage', 'points' ),
					'total_average_progressbar' => 'yes'
				),
			)
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_fields_settings',
			array(
				'label' => esc_html__( 'Fields Settings', 'jet-reviews' ),
			)
		);

		$this->add_control(
			'fields_layout',
			array(
				'label'   => esc_html__( 'Layout', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'points',
				'options' => array(
					'stars'      => esc_html__( 'Stars', 'jet-reviews' ),
					'percentage' => esc_html__( 'Percentage', 'jet-reviews' ),
					'points'     => esc_html__( 'Points', 'jet-reviews' ),
				),
			)
		);

		$this->add_control(
			'fields_stars_position',
			array(
				'label'   => esc_html__( 'Stars Position', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'right' => esc_html__( 'Right', 'jet-reviews' ),
					'left'  => esc_html__( 'Left', 'jet-reviews' ),
				),
				'condition' => array(
					'fields_layout' => array( 'stars' ),
				),
			)
		);

		$this->add_control(
			'fields_progressbar',
			array(
				'label'        => esc_html__( 'Progressbar', 'jet-reviews' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-reviews' ),
				'label_off'    => esc_html__( 'Hide', 'jet-reviews' ),
				'return_value' => 'yes',
				'default'      => 'yes',
				'condition' => array(
					'fields_layout' => array( 'percentage', 'points' ),
				),
			)
		);

		$this->add_control(
			'fields_value_position',
			array(
				'label'   => esc_html__( 'Values Position', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'above',
				'options' => array(
					'above'  => esc_html__( 'Above Progressbar', 'jet-reviews' ),
					'inside' => esc_html__( 'Inside Progressbar', 'jet-reviews' ),
				),
				'condition' => array(
					'fields_layout'      => array( 'percentage', 'points' ),
					'fields_progressbar' => 'yes'
				),
			)
		);

		$this->add_control(
			'fields_value_alignment',
			array(
				'label'   => esc_html__( 'Values Alignment', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'space-between',
				'options' => array(
					'space-between' => ! is_rtl() ? esc_html__( 'Right', 'jet-reviews' ) : esc_html__( 'Left', 'jet-reviews' ),
					'flex-start'    => ! is_rtl() ? esc_html__( 'Left', 'jet-reviews' ) : esc_html__( 'Right', 'jet-reviews' ),
				),
				'condition' => array(
					'fields_layout'         => array( 'percentage', 'points' ),
					'fields_value_position' => 'above',
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field_heading'] => 'justify-content: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'fields_label_suffix',
			array(
				'label'     => esc_html__( 'Label Suffix', 'jet-reviews' ),
				'type'      => Controls_Manager::TEXT,
				'condition' => array(
					'content_source'      => 'manually',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_summary_settings',
			array(
				'label' => esc_html__( 'Summary Settings', 'jet-reviews' ),
			)
		);

		$this->add_control(
			'summary_result_position',
			array(
				'label'   => esc_html__( 'Summary Results Block Position', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'label_block' => true,
				'options' => array(
					'right'  => ! is_rtl() ? esc_html__( 'At right side of summary box', 'jet-reviews' ) : esc_html__( 'At left side of summary box', 'jet-reviews' ),
					'bottom' => esc_html__( 'At bottom of summary box', 'jet-reviews' ),
					'left'   => ! is_rtl() ? esc_html__( 'At left side of summary box', 'jet-reviews' ) : esc_html__( 'At right side of summary box', 'jet-reviews' ),
					'top'    => esc_html__( 'At top of summary box', 'jet-reviews' ),
				),
			)
		);

		$this->add_control(
			'summary_result_width',
			array(
				'label'   => esc_html__( 'Results Block Width', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 30,
					'unit' => '%',
				),
				'range' => array(
					'%' => array(
						'min' => 1,
						'max' => 100,
					),
				),
				'condition' => array(
					'summary_result_position' => array( 'left', 'right' ),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] => 'width: {{SIZE}}%;',
					'{{WRAPPER}} ' . $css_scheme['summary'] => 'width: calc( 100% - {{SIZE}}% );',
				),
			)
		);

		$this->add_control(
			'summary_layout',
			array(
				'label'   => esc_html__( 'Summary Average Layout', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'points',
				'options' => array(
					'stars'      => esc_html__( 'Stars', 'jet-reviews' ),
					'percentage' => esc_html__( 'Percentage', 'jet-reviews' ),
					'points'     => esc_html__( 'Points', 'jet-reviews' ),
				),
			)
		);

		$this->add_control(
			'summary_progressbar',
			array(
				'label'        => esc_html__( 'Progressbar', 'jet-reviews' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'jet-reviews' ),
				'label_off'    => esc_html__( 'Hide', 'jet-reviews' ),
				'return_value' => 'yes',
				'default'      => '',
				'condition' => array(
					'summary_layout' => array( 'percentage', 'points' ),
				),
			)
		);

		$this->add_control(
			'summary_value_position',
			array(
				'label'   => esc_html__( 'Values Position', 'jet-reviews' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'above',
				'options' => array(
					'above'  => esc_html__( 'Above Progressbar', 'jet-reviews' ),
					'inside' => esc_html__( 'Inside Progressbar', 'jet-reviews' ),
				),
				'condition' => array(
					'summary_layout'      => array( 'percentage', 'points' ),
					'summary_progressbar' => 'yes'
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_item_style',
			array(
				'label'      => esc_html__( 'Review', 'jet-reviews' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_responsive_control(
			'review_item_gap',
			array(
				'label'   => esc_html__( 'Review Gap', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 40,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['review_item'] => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'review_item_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['review_item'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'review_item_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['review_item'],
			)
		);

		$this->add_responsive_control(
			'review_item_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['review_item'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'review_item_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['review_item'],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_title_style',
			array(
				'label'      => esc_html__( 'Header', 'jet-reviews' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'title_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-reviews' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' .jet-review__header-top' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['header'] . ' .jet-review__header-top' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['header'] . ' .jet-review__header-top' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'title_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['header'] . ' .jet-review__header-top',
			)
		);

		$this->add_responsive_control(
			'title_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' .jet-review__header-top' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'title_style',
			array(
				'label'     => esc_html__( 'Title', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			)
		);

		$this->add_responsive_control(
			'title_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-reviews' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-reviews' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-reviews' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-reviews' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-reviews-text-align-control',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_total_average_style',
			array(
				'label'      => esc_html__( 'Total Average', 'jet-reviews' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'total_average_value_style',
			array(
				'label'     => esc_html__( 'Value', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'total_average_value_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['total_average'] . ' .jet-review__total-average-val' => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['total_average'] . ' ' . $css_scheme['progress_value'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'total_average_value_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['total_average'] . ', {{WRAPPER}} ' . $css_scheme['total_average'] . ' ' . $css_scheme['progress_value'],
			)
		);

		$this->add_control(
			'total_average_stars_style',
			array(
				'label'     => esc_html__( 'Stars Rating', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'total_average_stars_size',
			array(
				'label'   => esc_html__( 'Stars Size', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 16,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['stars'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'total_average_stars_color_empty',
			array(
				'label'  => esc_html__( 'Empty Stars Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['stars'] . ' .jet-review__stars-empty'=> 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'total_average_stars_color_filled',
			array(
				'label'  => esc_html__( 'Filled Stars Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['stars'] . ' .jet-review__stars-filled'=> 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'total_average_stars_gap',
			array(
				'label'   => esc_html__( 'Stars Gap', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['stars'] . ' i' => 'margin: 0 calc( {{SIZE}}px/2 );',
				),
			)
		);

		$this->add_control(
			'total_average_progress_style',
			array(
				'label'     => esc_html__( 'Progress Styles', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'total_average_progress_height',
			array(
				'label'   => esc_html__( 'Progress Height', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 30,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['progress_bar'] => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'total_average_progress_canvas_color',
			array(
				'label'  => esc_html__( 'Progress Canvas Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['progress'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'total_average_progress_canvas_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['progress'],
			)
		);

		$this->add_control(
			'total_average_progress_bar_color',
			array(
				'label'  => esc_html__( 'Progress Bar Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['progress_bar'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'total_average_progress_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['header'] . ' ' . $css_scheme['progress'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'total_average_progress_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['header'] . ' ' . $css_scheme['progress'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'total_average_progress_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['progress'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['header'] . ' ' . $css_scheme['progress'] . ' .jet-review__progress-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_form_style',
			array(
				'label'      => esc_html__( 'Review Form', 'jet-reviews' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'form_container_style',
			array(
				'label'     => esc_html__( 'Container', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_responsive_control(
			'form_fields_gap',
			array(
				'label'   => esc_html__( 'Fields Gap', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 10,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_container_border',
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form'],
			]
		);

		$this->add_control(
			'form_container_border_radius',
			[
				'label' => __( 'Border Radius', 'jet-reviews' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'form_container_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form'],
			]
		);

		$this->add_responsive_control(
			'form_container_padding',
			[
				'label' => __( 'Padding', 'jet-reviews' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_container_margin',
			[
				'label' => __( 'Margin', 'jet-reviews' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'form_title_style',
			array(
				'label'     => esc_html__( 'Title', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'form_title_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'form_title_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-title',
			)
		);

		$this->add_control(
			'form_label_style',
			array(
				'label'     => esc_html__( 'Label', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'form_label_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form'] . ' label' => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .type-range span' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'form_label_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' label',
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .type-range span',
				]
			)
		);

		$this->add_control(
			'form_slider_style',
			array(
				'label'     => esc_html__( 'Slider', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'field_slider_color',
			array(
				'label'  => esc_html__( 'Slider Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form'] . ' input[type=range]::-webkit-slider-runnable-track'=> 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['form'] . ' input[type=range]::-moz-range-track'=> 'background-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['form'] . ' input[type=range]::-webkit-slider-thumb'=> 'border-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['form'] . ' input[type=range]::-moz-range-thumb'=> 'border-color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-field .current-value'=> 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'form_input_style',
			array(
				'label'     => esc_html__( 'Input', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'form_input_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['form_input'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'form_input_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form_input'],
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'form_input_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['form_input'],
			)
		);

		$this->add_responsive_control(
			'form_input_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['form_input'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'form_input_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['form_input'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'form_submit_style',
			array(
				'label'     => esc_html__( 'Submit', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'submit_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit',
			]
		);

		$this->start_controls_tabs( 'tabs_submit_style' );

		$this->start_controls_tab(
			'tab_submit_normal',
			[
				'label' => __( 'Normal', 'jet-reviews' ),
			]
		);

		$this->add_control(
			'submit_text_color',
			[
				'label' => __( 'Text Color', 'jet-reviews' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submit_background_color',
			[
				'label' => __( 'Background Color', 'jet-reviews' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_submit_hover',
			[
				'label' => __( 'Hover', 'jet-reviews' ),
			]
		);

		$this->add_control(
			'submit_hover_color',
			[
				'label' => __( 'Text Color', 'jet-reviews' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submit_background_hover_color',
			[
				'label' => __( 'Background Color', 'jet-reviews' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'submit_hover_border_color',
			[
				'label' => __( 'Border Color', 'jet-reviews' ),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'submit_border',
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'submit_border_radius',
			[
				'label' => __( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submit_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit',
			]
		);

		$this->add_responsive_control(
			'submit_padding',
			[
				'label' => __( 'Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} ' . $css_scheme['form'] . ' .jet-review__form-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_author_style',
			array(
				'label'      => esc_html__( 'Author', 'jet-reviews' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'author_container_style',
			array(
				'label'     => esc_html__( 'Container', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'author_container_background',
				'selector' => '{{WRAPPER}} ' . $css_scheme['author'],
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'author_container_border',
				'label'       => esc_html__( 'Border', 'jet-elements' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'  => '{{WRAPPER}} ' . $css_scheme['author'],
			)
		);

		$this->add_responsive_control(
			'author_container_border_radius',
			array(
				'label'      => __( 'Border Radius', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['author'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'author_container_padding',
			array(
				'label'      => __( 'Padding', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['author'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'author_container_margin',
			array(
				'label'      => __( 'Margin', 'jet-elements' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['author'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name' => 'author_container_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['author'],
			)
		);

		$this->add_control(
			'author_avatar_style',
			array(
				'label'     => esc_html__( 'Avatar', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'author_avatar_size',
			array(
				'label'   => esc_html__( 'Avatar Size', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 60,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 30,
						'max' => 128,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-avatar img' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				),
			)
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'author_avatar_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-avatar img',
			)
		);

		$this->add_responsive_control(
			'author_avatar_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'author_avatar_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-avatar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'author_name_style',
			array(
				'label'     => esc_html__( 'Name', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'author_name_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-name' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_name_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-name',
			)
		);

		$this->add_control(
			'author_mail_style',
			array(
				'label'     => esc_html__( 'Mail', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'author_mail_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-mail' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_mail_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-mail',
			)
		);

		$this->add_control(
			'author_date_style',
			array(
				'label'     => esc_html__( 'Date', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'author_date_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-date' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'author_date_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['author'] . ' .jet-review__user-date',
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_fields_style',
			array(
				'label'      => esc_html__( 'Fields', 'jet-reviews' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'fields_box_style',
			array(
				'label'     => esc_html__( 'Fields Box', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'fields_box_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['fields_box'],
			)
		);

		$this->add_responsive_control(
			'fields_box_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['fields_box'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'fields_box_padding',
			array(
				'label'      => esc_html__( 'Fields Box Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['fields_box'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'fields_label_style',
			array(
				'label'     => esc_html__( 'Label', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'field_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['field'],
			)
		);

		$this->add_control(
			'fields_value_style',
			array(
				'label'     => esc_html__( 'Value', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'field_value_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field_value'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['progress_value'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_value_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['field_value'] . ', {{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['progress_value'],
			)
		);

		$this->add_control(
			'field_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-reviews' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'field_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['field'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'field_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['field'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'field_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['field'],
			)
		);

		$this->add_responsive_control(
			'field_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['field'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'fields_stars_style',
			array(
				'label'     => esc_html__( 'Stars Rating', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'fields_stars_size',
			array(
				'label'   => esc_html__( 'Stars Size', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 16,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['stars'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'fields_stars_color_empty',
			array(
				'label'  => esc_html__( 'Empty Stars Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['stars'] . ' .jet-review__stars-empty'=> 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'fields_stars_color_filled',
			array(
				'label'  => esc_html__( 'Filled Stars Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['stars'] . ' .jet-review__stars-filled'=> 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'fields_stars_gap',
			array(
				'label'   => esc_html__( 'Stars Gap', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['stars'] . ' i' => 'margin: 0 calc( {{SIZE}}px/2 );',
				),
			)
		);

		$this->add_control(
			'fields_progress_style',
			array(
				'label'     => esc_html__( 'Progress Styles', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'fields_progress_height',
			array(
				'label'   => esc_html__( 'Progress Height', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 30,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['progress_bar'] => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'fields_progress_canvas_color',
			array(
				'label'  => esc_html__( 'Progress Canvas Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['progress'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'fields_progress_canvas_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['progress'],
			)
		);

		$this->add_control(
			'fields_progress_bar_color',
			array(
				'label'  => esc_html__( 'Progress Bar Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['progress_bar'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'fields_progress_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['field'] . ' ' . $css_scheme['progress'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'fields_progress_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['field'] . ' ' . $css_scheme['progress'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'fields_progress_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['progress'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['field'] . ' ' . $css_scheme['progress'] . ' .jet-review__progress-bar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_summary_style',
			array(
				'label'      => esc_html__( 'Summary', 'jet-reviews' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'summary_title_style',
			array(
				'label'     => esc_html__( 'Summary Title', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'summary_title_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_title'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'summary_title_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['summary_title'],
			)
		);

		$this->add_control(
			'summary_content_style',
			array(
				'label'     => esc_html__( 'Summary Content', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'summary_content_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_content'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'summary_content_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_3,
				'selector' => '{{WRAPPER}} ' . $css_scheme['summary_content'],
			)
		);

		$this->add_control(
			'summary_box_style',
			array(
				'label'     => esc_html__( 'Summary Box', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'summary_bg_color',
			array(
				'label' => esc_html__( 'Background Color', 'jet-reviews' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'summary_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-reviews' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'left',
				'options' => array(
					'left'    => array(
						'title' => esc_html__( 'Left', 'jet-reviews' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-reviews' ),
						'icon'  => 'fa fa-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-reviews' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['summary'] => 'text-align: {{VALUE}};',
				),
				'classes' => 'jet-reviews-text-align-control',
			)
		);

		$this->add_responsive_control(
			'summary_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['summary'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'summary_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['summary'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'summary_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['summary'],
			)
		);

		$this->add_responsive_control(
			'summary_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['summary'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_review_summary_average_style',
			array(
				'label'      => esc_html__( 'Summary Average', 'jet-reviews' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'summary_average_legend_style',
			array(
				'label'     => esc_html__( 'Legend', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'summary_average_legend_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_legend'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'summary_average_legend_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['summary_legend'],
			)
		);

		$this->add_control(
			'summary_average_value_style',
			array(
				'label'     => esc_html__( 'Value', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'summary_average_value_color',
			array(
				'label'  => esc_html__( 'Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_2,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_value'] => 'color: {{VALUE}}',
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['progress_value'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'summary_average_value_typography',
				'scheme'   => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} ' . $css_scheme['summary_value'] . ', {{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['progress_value'],
			)
		);

		$this->add_control(
			'summary_average_bg_color',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-reviews' ),
				'type'      => Controls_Manager::COLOR,
				'separator' => 'before',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'summary_average_alignment',
			array(
				'label'   => esc_html__( 'Alignment', 'jet-reviews' ),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => array(
					'left'=> array(
						'title' => esc_html__( 'Start', 'jet-reviews' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-left' : 'eicon-h-align-right',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'jet-reviews' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right' => array(
						'title' => esc_html__( 'End', 'jet-reviews' ),
						'icon'  => ! is_rtl() ? 'eicon-h-align-right' : 'eicon-h-align-left',
					),
				),
			)
		);

		$this->add_responsive_control(
			'summary_average_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['summary_data'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'summary_average_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['summary_data'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'summary_average_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'placeholder'    => '1px',
				'selector'       => '{{WRAPPER}} ' . $css_scheme['summary_data'],
			)
		);

		$this->add_responsive_control(
			'summary_average_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'summary_stars_style',
			array(
				'label'     => esc_html__( 'Stars Rating', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'summary_stars_size',
			array(
				'label'   => esc_html__( 'Stars Size', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 16,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['stars'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'summary_stars_color_empty',
			array(
				'label'  => esc_html__( 'Empty Stars Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['stars'] . ' .jet-review__stars-empty'=> 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'summary_stars_color_filled',
			array(
				'label'  => esc_html__( 'Filled Stars Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['stars'] . ' .jet-review__stars-filled'=> 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'summary_stars_gap',
			array(
				'label'   => esc_html__( 'Stars Gap', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 0,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 0,
						'max' => 10,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['stars'] . ' i' => 'margin: 0 calc( {{SIZE}}px/2 );',
				),
			)
		);

		$this->add_control(
			'summary_progress_style',
			array(
				'label'     => esc_html__( 'Progress Styles', 'jet-reviews' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'summary_progress_height',
			array(
				'label'   => esc_html__( 'Progress Height', 'jet-reviews' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => array(
					'size' => 30,
					'unit' => 'px',
				),
				'range' => array(
					'px' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['progress_bar'] => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'summary_progress_canvas_color',
			array(
				'label'  => esc_html__( 'Progress Canvas Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['progress'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'           => 'summary_progress_canvas_border',
				'label'          => esc_html__( 'Border', 'jet-reviews' ),
				'selector'       => '{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['progress'],
			)
		);

		$this->add_control(
			'summary_progress_bar_color',
			array(
				'label'  => esc_html__( 'Progress Bar Color', 'jet-reviews' ),
				'type'   => Controls_Manager::COLOR,
				'scheme' => array(
					'type'  => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['progress_bar'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'summary_progress_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['summary_data'] . ' ' . $css_scheme['progress'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'summary_progress_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} '  . $css_scheme['summary_data'] . ' ' . $css_scheme['progress'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'summary_progress_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-reviews' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['summary_data'] . ' ' . $css_scheme['progress'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

	}

	protected function render() {

		$this->__context = 'render';

		$this->__open_wrap();

		$this->__prepare_data();

		if ( ! is_array( $this->__review_data ) ) {
			return false;
		}

		include $this->__get_global_template( 'index' );

		$this->structured_data();

		$this->__close_wrap();

	}

	/**
	 * Add structured data markup to review
	 *
	 * @return [type] [description]
	 */
	public function structured_data() {
		$settings = $this->get_settings();

		if ( empty( $settings['add_sdata'] ) ) {
			return;
		}

		$content_source = isset( $settings['content_source'] ) ? $settings['content_source'] : 'manually';

		switch ( $content_source ) {
			case 'manually':
				jet_reviews_tools()->render_structured_data( $this->get_sdata_from_settings() );
				break;

			case 'post-meta':
				jet_reviews_tools()->render_structured_data( $this->get_sdata_from_meta() );
				break;

			case 'wp-review':
				_e( 'Structured data supported only for Post Meta and Manually Input sources', 'jet-reviews' );
				break;
		}

	}

	public function get_sdata_from_settings() {

		$settings = $this->get_settings();

		$map = array(
			'item_name'     => 'sdata_item_name',
			'item_image'    => 'sdata_item_image',
			'item_desc'     => 'sdata_item_description',
			'review_date'   => 'sdata_review_date',
			'review_author' => 'sdata_review_author',
		);

		$result = array();

		foreach ( $map as $data_key => $settings_key ) {
			$result[ $data_key ] = $settings[ $settings_key ];
		}

		if ( ! empty( $result['item_image'] ) ) {

			$img_id  = absint( $result['item_image']['id'] );
			$img_src = wp_get_attachment_image_src( $img_id, 'full' );

			$result['item_image'] = array(
				'url'    => $img_src[0],
				'width'  => $img_src[1],
				'height' => $img_src[2],
			);

		}

		$result['item_rating'] = $this->get_review_rating();

		return $result;

	}

	public function get_sdata_from_meta() {

		$settings = $this->get_settings();
		$post_id  = ! empty( $settings['review_post_id'] ) ? absint( $settings['review_post_id'] ) : 0;

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$meta_map = array(
			'item_name'     => 'jet-review-data-name',
			'item_image'    => 'jet-review-data-image',
			'item_desc'     => 'jet-review-data-desc',
			'review_author' => 'jet-review-data-author-name',
		);

		$result = array();

		foreach ( $meta_map as $data_key => $meta_key ) {
			$result[ $data_key ] = get_post_meta( $post_id, $meta_key, true );
		}

		$result['review_date'] = get_the_date( 'c', $post_id );

		if ( ! empty( $result['item_image'] ) ) {

			$img_id  = absint( $result['item_image'] );
			$img_src = wp_get_attachment_image_src( $img_id, 'full' );

			$result['item_image'] = array(
				'url'    => $img_src[0],
				'width'  => $img_src[1],
				'height' => $img_src[2],
			);

		}

		$result['item_rating'] = $this->get_review_rating();

		return $result;
	}

	public function get_review_rating() {

		$average = $this->__get_total_average();

		$percent = isset( $average['percent'] ) ? $average['percent'] : 0;

		return round( ( ( $percent * 5 ) / 100 ), 2 );
	}

	/**
	 * Prepare reveiews items.
	 *
	 * @return [type] [description]
	 */
	public function __prepare_data() {

		$settings = $this->get_settings();

		$content_source = isset( $settings['content_source'] ) ? $settings['content_source'] : 'manually';

		switch ( $content_source ) {
			case 'manually':
				add_filter( 'jet-reviews/review-data', array( $this, '__set_manual_data' ), 10, 2 );
				break;

			case 'post-meta':
				add_filter( 'jet-reviews/review-data', array( $this, '__set_meta_data' ), 10, 2 );
				break;

			case 'wp-review':
				add_filter( 'jet-reviews/review-data', array( $this, '__set_wp_review_data' ), 10, 2 );
				break;

			case 'post-data':
				add_filter( 'jet-reviews/review-data', array( $this, '__set_post_data' ), 10, 2 );
				break;
		}

		$__primary_review_data = apply_filters( 'jet-reviews/review-data', [], $settings, $this );

		if ( empty( $__primary_review_data['review_fields'] ) ) {
			$this->__review_data = false;

			echo sprintf( '<h4>%s</h4>', esc_html__( 'Any Review Fields Not Found', 'jet-reviews' ) );

			return false;
		}

		$this->__primary_review_data = $__primary_review_data;

		$__review_data = [];

		switch ( $content_source ) {
			case 'manually':
				$__review_data[1] = $__primary_review_data;
				break;

			case 'post-meta':

				$post_id = $this->get_review_post_id();

				$post_reviews_data = get_post_meta( $post_id, 'jet-reviews-data', true );

				if ( ! empty( $post_reviews_data ) ) {

					// Sync
					$author_id = (int)get_post_field( 'post_author', $post_id );

					foreach ( $post_reviews_data as $key => $review_data ) {

						$review_fields = $review_data['review_fields'];

						$primary_review_fields = $this->__primary_review_data['review_fields'];

						foreach ( $primary_review_fields as $key => $field ) {

							if ( ! isset( $review_fields[ $key ] ) ) {
								continue;
							}

							$current_value = $review_fields[ $key ]['field_value'];

							if ( $__primary_review_data['user_id'] === $author_id ) {
								$current_value = $field['field_value'];
							}

							$review_fields[ $key ] = [
								'field_label' => $field['field_label'],
								'field_value' => $current_value,
								'field_max'   => $field['field_max'],
								'field_step'  => isset( $field['field_step'] ) ? $field['field_step'] : 1,
							];
						}

						$temp_review_data = [
							'user_id'        => $review_data['user_id'],
							'review_time'    => $review_data['review_time'],
							'review_date'    => $review_data['review_date'],
							'review_fields'  => $review_fields,
							'summary_title'  => $review_data['summary_title'],
							'summary_text'   => $review_data['summary_text'],
							'summary_legend' => $review_data['summary_legend'],
						];

						$__review_data[] = $temp_review_data;
					}
				}

				if ( 1 < count( $__review_data ) ) {
					$__review_data = $this->sort_review_by_time( $__review_data );
				}

				break;

			case 'wp-review':
				$__review_data[1] = $__primary_review_data;
				break;

			case 'post-data':

				$post_id = $this->get_review_post_id();

				$post_reviews_db_data = \Jet_Reviews\Reviews\Data::get_instance()->get_reviews_by_post_id( $post_id );

				foreach ( $post_reviews_db_data as $key => $review_data ) {

					$review_fields = maybe_unserialize( $review_data['rating_data'] );

					foreach ( $review_fields as $key => $field ) {

						$review_fields[ $key ] = array(
							'field_label' => $field['field_label'],
							'field_value' => $field['field_value'],
							'field_max'   => $field['field_max'],
							'field_step'  => isset( $field['field_step'] ) ? $field['field_step'] : 1,
						);
					}

					$temp_review_data = array(
						'user_id'        => $review_data['author'],
						'review_time'    => strtotime( $review_data['date'] ),
						'review_date'    => $review_data['date'],
						'review_fields'  => $review_fields,
						'summary_title'  => $review_data['title'],
						'summary_text'   => $review_data['content'],
						'summary_legend' => '',
					);

					$__review_data[] = $temp_review_data;
				}

			break;

		}

		$this->__review_data = $__review_data;
	}

	/**
	 * [sort_review_by_time description]
	 * @param  [type] $reviews [description]
	 * @return [type]          [description]
	 */

	public function sort_review_by_time( $reviews ) {

		usort( $reviews, function( $a, $b ) {
			return $b['review_time'] - $a['review_time'];
		} );

		return $reviews;
	}

	/**
	 * Get progress bar HTML markup
	 *
	 * @return string
	 */
	public function __get_progressbar( $value = 0, $max = 10, $label = 'above', $type = 'points' ) {

		$value_label = '';

		if ( 'inside' === $label ) {
			$value_label = ( 'points' === $type ) ? $value : round( ( 100 * $value ) / $max, 0 ) . '%';
		}

		return sprintf(
			'<div class="jet-review__progress"><div class="jet-review__progress-bar" style="width:%1$s%%"><div class="jet-review__progress-val">%2$s</div></div></div>',
			round( ( $value * 100 ) / $max ),
			$value_label
		);

	}

	/**
	 * Get stars rating HTML markup
	 *
	 * @return string
	 */
	public function __get_stars( $value = 0, $max = 10 ) {

		$width   = round( ( 100 * $value ) / $max, 3 );
		$stars_f = str_repeat( '<i class="fa fa-star" aria-hidden="true"></i>', 5 );
		$stars_e = str_repeat( '<i class="fa fa-star-o" aria-hidden="true"></i>', 5 );

		return sprintf(
			'<div class="jet-review__stars"><div class="jet-review__stars-filled" style="width:%1$s%%">%3$s</div><div class="jet-review__stars-empty" style="width:%2$s%%">%4$s</div><div class="jet-review__stars-adjuster">%4$s</div></div>',
			$width,
			100 - $width,
			$stars_f,
			$stars_e
		);

	}

	/**
	 * Calc average review value
	 *
	 * @return array
	 */
	public function __get_average( $review_data ) {

		$default = array(
			'valid'   => true,
			'val'     => 0,
			'max'     => 10,
			'percent' => 0,
		);

		$data   = $review_data;
		$fields = $data['review_fields'];

		if ( empty( $fields ) ) {
			return $default;
		}

		$all_max = array();
		$totals  = array();

		foreach ( $fields as $field ) {

			$val = isset( $field['field_value'] ) ? floatval( $field['field_value'] ) : 0;
			$max = isset( $field['field_max'] ) ? floatval( $field['field_max'] ) : 10;

			if ( ! $max ) {
				continue;
			}

			$totals[]  = round( ( 100 * $val ) / $max, 2 );
			$all_max[] = $max;

		}

		$average = round( array_sum( $totals ) / count( $totals ), 2 );
		$all_max = array_unique( $all_max );
		$valid   = 1 === count( $all_max );

		return [
			'valid'   => $valid,
			'val'     => ( $valid ? round( $average * ( $all_max[0] / 100 ), 2 ) : $average ),
			'max'     => ( $valid ? $all_max[0] : 100 ),
			'percent' => $average,
		];
	}

	public function __get_total_average() {
		$review_data = $this->__get_review_data();

		if ( empty( $review_data ) ) {
			return false;
		}

		$item_valid = [];
		$item_val = [];
		$item_max = [];
		$item_percent = [];

		$review_data_length = count( $review_data );

		foreach ( $review_data as $key => $review_item ) {

			$item_average = $this->__get_average( $review_item );

			$item_valid[]   = $item_average['valid'];
			$item_val[]     = $item_average['val'];
			$item_max[]     = $item_average['max'];
			$item_percent[] = $item_average['percent'];
		}

		$total_average = [
			'valid'   => 'false',
			'val'     => round( array_sum( $item_val ) / $review_data_length, 2 ),
			'max'     => round( array_sum( $item_max ) / $review_data_length, 2 ),
			'percent' => round( array_sum( $item_percent ) / $review_data_length, 2 ),
		];

		return $total_average;
	}

	/**
	 * Return required review fields list
	 *
	 * @return array
	 */
	public function __get_review_fields() {
		return array(
			'review_title',
			'review_fields',
			'summary_title',
			'summary_text',
			'summary_legend',
		);
	}

	/**
	 * Return review data
	 *
	 * @return array
	 */
	public function __get_review_data() {
		return $this->__review_data;
	}

	/**
	 * Set manually entered data if it selected in settings.
	 *
	 * @param  array $settings Widget settings.
	 * @return array
	 */
	public function __set_manual_data( $data, $settings ) {

		foreach ( $this->__get_review_fields() as $field ) {
			$data[ $field ] = isset( $settings[ $field ] ) ? $settings[ $field ] : false;
		}

		$data['user_id'] = 1;
		$data['review_time'] = 0;

		return $data;
	}

	/**
	 * Get review data from post meta
	 *
	 * @param  [type] $data     [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function __set_meta_data( $data, $settings ) {

		$meta_keys = array(
			'jet-review-title',
			'jet-review-items',
			'jet-review-summary-title',
			'jet-review-summary-text',
			'jet-review-summary-legend',
		);

		$post_id = $this->get_review_post_id();

		foreach ( array_combine( $this->__get_review_fields(), $meta_keys ) as $field => $meta_key ) {
			$meta_value = get_post_meta( $post_id, $meta_key, true );

			$data[ $field ] = $meta_value;
		}

		$data['user_id'] = $this->get_curent_user_id();
		$data['review_time'] = 0;
		$data['review_date'] = 0;

		$meta_update_check = get_post_meta( $post_id, 'jet-reviews-meta-update', true );

		if ( empty( $meta_update_check ) ) {
			$current[1] = $data;
			update_post_meta( $post_id, 'jet-reviews-data', $current );
			update_post_meta( $post_id, 'jet-reviews-meta-update', true );
		}

		return $data;

	}

	/**
	 * [__set_wp_review_data description]
	 * @param  [type] $data     [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function __set_wp_review_data( $data, $settings ) {

		$post_id = ! empty( $settings['review_post_id'] ) ? absint( $settings['review_post_id'] ) : 0;

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		$data['review_title']   = get_post_meta( $post_id, 'wp_review_heading', true );
		$data['summary_legend'] = '';
		$data['summary_title']  = get_post_meta( $post_id, 'wp_review_desc_title', true );
		$data['summary_text']   = get_post_meta( $post_id, 'wp_review_desc', true );

		$type = get_post_meta( $post_id, 'wp_review_type', true );
		$max  = 5;

		switch ( $type ) {
			case 'star':
				$max = 5;
				break;

			case 'percentage':
				$max = 100;
				break;

			case 'point':
				$max = 10;
				break;
		}

		$items = array();
		$meta  = get_post_meta( $post_id, 'wp_review_item', true );

		if ( ! empty( $meta ) ) {
			foreach ( $meta as $item ) {
				$items[] = array(
					'field_label' => isset( $item['wp_review_item_title'] ) ? $item['wp_review_item_title'] : '',
					'field_value' => isset( $item['wp_review_item_star'] ) ? $item['wp_review_item_star'] : 0,
					'field_max'   => $max,
				);
			}
		}

		$data['review_fields'] = $items;

		return $data;

	}

	/**
	 * Get review data from post meta
	 *
	 * @param  [type] $data     [description]
	 * @param  [type] $settings [description]
	 * @return [type]           [description]
	 */
	public function __set_post_data( $data, $settings ) {

		$data = array(
			'summary_title'  => $settings['summary_title'],
			'summary_text'   => $settings['summary_text'],
			'summary_legend' => $settings['summary_legend'],
		);

		$post_id = $this->get_review_post_id();

		$post_type = get_post_type( $post_id );

		$post_type_data = jet_reviews()->settings->get_post_type_data( $post_type );

		$review_type_data = \Jet_Reviews\Reviews\Data::get_instance()->get_review_type( $post_type_data['review_type'] );

		if ( ! $review_type_data ) {
			return false;
		}

		$review_raw_fields = maybe_unserialize( $review_type_data[0]['fields'] );

		foreach ( $review_raw_fields as $key => $field ) {
			$field = array(
				'field_label' => $field['label'],
				'field_value' => '',
				'field_max'   => $field['max'],
				'field_step'  => $field['step'],
			);

			$data['review_fields']['item-' . $key] = $field;
		}

		return $data;

	}

	public function __review_sources() {

		$default = array(
			'manually'  => esc_html__( 'Manually Input', 'jet-reviews' ),
			'post-meta' => esc_html__( 'Post Meta', 'jet-reviews' ),
			//'post-data' => esc_html__( 'Post Reviews', 'jet-reviews' ),
		);

		if ( $this->__has_wp_review() ) {
			$default['wp-review'] = esc_html__( 'WP Review Data', 'jet-reviews' );
		}

		return apply_filters( 'jet-reviews/widget/review-sources', $default );

	}

	public function is_user_can_add_review() {

		$settings = $this->get_settings();

		$content_source = isset( $settings['content_source'] ) ? $settings['content_source'] : 'manually';

		if ( 'manually' === $content_source || 'wp-review' === $content_source ) {
			return false;
		}

		$user    = wp_get_current_user();
		$user_id = $user->ID;

		if ( 0 === $user_id ) {

			if ( jet_reviews_tools()->is_demo_mode() ) {
				return true;
			}

			return false;
		}

		return true;
	}

	public function get_review_title() {
		$settings = $this->get_settings();

		$content_source = isset( $settings['content_source'] ) ? $settings['content_source'] : 'manually';

		switch ( $content_source ) {
			case 'manually':
			case 'wp-review':
				$title = $settings['review_title'];

				break;

			case 'post-meta':
				$post_id = $this->get_review_post_id();

				$title = get_post_meta( $post_id, 'jet-review-title', true );

				break;
		}

		if ( empty( $title ) ) {
			return false;
		}

		return $title;
	}

	public function get_review_post_id() {
		$settings = $this->get_settings();

		$post_id = ! empty( $settings['review_post_id'] ) ? absint( $settings['review_post_id'] ) : 0;

		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		return $post_id;
	}

	public function get_user_data_by_id( $user_id ) {
		$user = get_user_by( 'id', $user_id );

		return $user->data;
	}

	public function get_user_avatar( $user_mail ) {

		$avatar = get_avatar( $user_mail, 128 );

		return $avatar;
	}

	public function render_review_date( $review_data ) {

		if ( ! isset( $review_data['review_date'] ) || 0 == $review_data['review_date'] ) {
			return false;
		}

		$date = date( get_option( 'date_format' ), strtotime( $review_data['review_date'] ) );

		echo sprintf( '<div class="jet-review__user-date"><span>%s</span></div>', $date );
	}

	public function get_curent_user_id() {

		$current_user = wp_get_current_user();

		if ( 0 == $current_user->ID ) {
			return false;
		}

		return $current_user->ID;
	}

	public function get_curent_user_role() {
		$user_meta = get_userdata( $this->get_curent_user_id() );

		$user_roles = $user_meta->roles;

		return $user_roles;
	}

	/**
	 * Check if WP Review plugin is installed
	 *
	 * @return bool
	 */
	public function __has_wp_review() {
		return defined( 'WP_REVIEW_PLUGIN_VERSION' );
	}

}
