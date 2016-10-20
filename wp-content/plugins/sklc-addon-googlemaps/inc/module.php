<?php
	
	/**
	 * Register Module
	 */

	add_action('dslc_hook_register_modules',
		 create_function('', 'return dslc_register_module( "SKLC_GMaps_Module" );')
	);

	/**
	 * Module Class
	 */

	class SKLC_GMaps_Module extends DSLC_Module {

		var $module_id;
		var $module_title;
		var $module_icon;
		var $module_category;

		/**
		 * Construct
		 */

		function __construct() {

			$this->module_id = 'SKLC_GMaps_Module';
			$this->module_title = __( 'Google Maps', 'sklc_gmaps_string' );
			$this->module_icon = 'picture';
			$this->module_category = 'elements';

		}

		/**
		 * Options
		 */

		function options() {	

			$options = array(

				array(
					'label' => __( 'Address', 'sklc_gmaps_string' ),
					'id' => 'address',
					'std' => '11 Nemanjina, Beograd, Serbia',
					'type' => 'text',
				),
				array(
					'label' => __( 'Custom Marker IMG', 'sklc_gmaps_string' ),
					'id' => 'custommarker',
					'std' => '',
					'type' => 'image',
				),
				array(
					'label' => __( 'Mousewheel Zooming', 'sklc_gmaps_string' ),
					'id' => 'zooming',
					'std' => 'enabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'sklc_gmaps_string' ),
							'value' => 'enabled'
						),
						array(
							'label' => __( 'Disabled', 'sklc_gmaps_string' ),
							'value' => 'disabled'
						),
					),
				),
				array(
					'label' => __( 'Doubleclick Zooming', 'sklc_gmaps_string' ),
					'id' => 'dblzooming',
					'std' => 'enabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'sklc_gmaps_string' ),
							'value' => 'enabled'
						),
						array(
							'label' => __( 'Disabled', 'sklc_gmaps_string' ),
							'value' => 'disabled'
						),
					)
				),
				array(
					'label' => __( 'Dragging', 'sklc_gmaps_string' ),
					'id' => 'dragging',
					'std' => 'enabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'sklc_gmaps_string' ),
							'value' => 'enabled'
						),
						array(
							'label' => __( 'Disabled', 'sklc_gmaps_string' ),
							'value' => 'disabled'
						),
					)
				),
				array(
					'label' => __( 'Map UI', 'sklc_gmaps_string' ),
					'id' => 'mapui',
					'std' => 'enabled',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Enabled', 'sklc_gmaps_string' ),
							'value' => 'enabled'
						),
						array(
							'label' => __( 'Disabled', 'sklc_gmaps_string' ),
							'value' => 'disabled'
						),
					)
				),
				array(
					'label' => __( 'Type', 'sklc_gmaps_string' ),
					'id' => 'maptype',
					'std' => 'ROADMAP',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Hybrid', 'sklc_gmaps_string' ),
							'value' => 'HYBRID'
						),
						array(
							'label' => __( 'Roadmap', 'sklc_gmaps_string' ),
							'value' => 'ROADMAP'
						),
						array(
							'label' => __( 'Satellite', 'sklc_gmaps_string' ),
							'value' => 'SATELLITE'
						),
						array(
							'label' => __( 'Terrain', 'sklc_gmaps_string' ),
							'value' => 'TERRAIN'
						),
					)
				),
				array(
					'label' => __( 'Height', 'sklc_gmaps_string' ),
					'id' => 'height',
					'std' => '400',
					'type' => 'text',
				),
				array(
					'label' => __( 'Zoom', 'sklc_gmaps_string' ),
					'help' => 'Set a value from 1 to 21. Bigger the number bigger the zoom.',
					'id' => 'zoom',
					'std' => '15',
					'type' => 'text',
				),

				/**
				 * Styling
				 */

				array(
					'label' => __( ' BG Color', 'sklc_gmaps_string' ),
					'id' => 'css_main_bg_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Image', 'sklc_gmaps_string' ),
					'id' => 'css_main_bg_img',
					'std' => '',
					'type' => 'image',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-image',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Image Repeat', 'sklc_gmaps_string' ),
					'id' => 'css_main_bg_img_repeat',
					'std' => 'repeat',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Repeat', 'sklc_gmaps_string' ),
							'value' => 'repeat',
						),
						array(
							'label' => __( 'Repeat Horizontal', 'sklc_gmaps_string' ),
							'value' => 'repeat-x',
						),
						array(
							'label' => __( 'Repeat Vertical', 'sklc_gmaps_string' ),
							'value' => 'repeat-y',
						),
						array(
							'label' => __( 'Do NOT Repeat', 'sklc_gmaps_string' ),
							'value' => 'no-repeat',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-repeat',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Image Attachment', 'sklc_gmaps_string' ),
					'id' => 'css_main_bg_img_attch',
					'std' => 'scroll',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Scroll', 'sklc_gmaps_string' ),
							'value' => 'scroll',
						),
						array(
							'label' => __( 'Fixed', 'sklc_gmaps_string' ),
							'value' => 'fixed',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-attachment',
					'section' => 'styling',
				),
				array(
					'label' => __( 'BG Image Position', 'sklc_gmaps_string' ),
					'id' => 'css_main_bg_img_pos',
					'std' => 'top left',
					'type' => 'select',
					'choices' => array(
						array(
							'label' => __( 'Top Left', 'sklc_gmaps_string' ),
							'value' => 'left top',
						),
						array(
							'label' => __( 'Top Right', 'sklc_gmaps_string' ),
							'value' => 'right top',
						),
						array(
							'label' => __( 'Top Center', 'sklc_gmaps_string' ),
							'value' => 'Center Top',
						),
						array(
							'label' => __( 'Center Left', 'sklc_gmaps_string' ),
							'value' => 'left center',
						),
						array(
							'label' => __( 'Center Right', 'sklc_gmaps_string' ),
							'value' => 'right center',
						),
						array(
							'label' => __( 'Center', 'sklc_gmaps_string' ),
							'value' => 'center center',
						),
						array(
							'label' => __( 'Bottom Left', 'sklc_gmaps_string' ),
							'value' => 'left bottom',
						),
						array(
							'label' => __( 'Bottom Right', 'sklc_gmaps_string' ),
							'value' => 'right bottom',
						),
						array(
							'label' => __( 'Bottom Center', 'sklc_gmaps_string' ),
							'value' => 'center bottom',
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'background-position',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Color', 'sklc_gmaps_string' ),
					'id' => 'css_main_border_color',
					'std' => '',
					'type' => 'color',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-color',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Width', 'sklc_gmaps_string' ),
					'id' => 'css_main_border_width',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-width',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Borders', 'sklc_gmaps_string' ),
					'id' => 'css_main_border_trbl',
					'std' => 'top right bottom left',
					'type' => 'checkbox',
					'choices' => array(
						array(
							'label' => __( 'Top', 'sklc_gmaps_string' ),
							'value' => 'top'
						),
						array(
							'label' => __( 'Right', 'sklc_gmaps_string' ),
							'value' => 'right'
						),
						array(
							'label' => __( 'Bottom', 'sklc_gmaps_string' ),
							'value' => 'bottom'
						),
						array(
							'label' => __( 'Left', 'sklc_gmaps_string' ),
							'value' => 'left'
						),
					),
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-style',
					'section' => 'styling',
				),
				array(
					'label' => __( 'Border Radius - Top', 'sklc_gmaps_string' ),
					'id' => 'css_main_border_radius_top',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-top-left-radius,border-top-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Border Radius - Bottom', 'sklc_gmaps_string' ),
					'id' => 'css_main_border_radius_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'border-bottom-left-radius,border-bottom-right-radius',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Margin Bottom', 'sklc_gmaps_string' ),
					'id' => 'css_margin_bottom',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'margin-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Vertical', 'sklc_gmaps_string' ),
					'id' => 'css_main_padding_vertical',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'padding-top,padding-bottom',
					'section' => 'styling',
					'ext' => 'px',
				),
				array(
					'label' => __( 'Padding Horizontal', 'sklc_gmaps_string' ),
					'id' => 'css_main_padding_horizontal',
					'std' => '0',
					'type' => 'slider',
					'refresh_on_change' => false,
					'affect_on_change_el' => '.sklc-gmaps-wrapper',
					'affect_on_change_rule' => 'padding-left,padding-right',
					'section' => 'styling',
					'ext' => 'px',
				),

			);

			$options = array_merge( $options, $this->shared_options('animation_options') );
			$options = array_merge( $options, $this->presets_options() );

			return apply_filters( 'dslc_module_options', $options, $this->module_id );

		}

		/**
		 * Output
		 */

		function output( $options ) {

			/* Module Start */
			$this->module_start( $options );

				$coordinates = sklc_address_to_coordinates( $options['address'] );

				if ( $coordinates == 'zero_results' || $coordinates == 'invalid_request' || $coordinates == 'error' ) {
					echo _e( 'Could not find this address. Check it on Google Maps website to make sure it is correct.', 'sklc_gmaps_string');
				} elseif ( $coordinates == 'no_load' ) {
					echo _e( 'Google API is not responding at the moment. Please try again shortly', 'sklc_gmaps_string' );
				} else {
				
					?>
					<div class="sklc-gmaps-wrapper">
						<div class="sklc-gmaps" 
							data-lat="<?php echo $coordinates['lat']; ?>" 
							data-lng="<?php echo $coordinates['lng']; ?>" 
							data-zooming="<?php echo $options['zooming']; ?>"
							data-dblzooming="<?php echo $options['dblzooming']; ?>"
							data-dragging="<?php echo $options['dragging']; ?>"
							data-mapui="<?php echo $options['mapui']; ?>"
							data-maptype="<?php echo $options['maptype']; ?>"
							data-zoom="<?php echo $options['zoom']; ?>"
							data-custom-marker="<?php echo $options['custommarker']; ?>"
							style="width: 100%; height: <?php echo $options['height']; ?>px;"></div>
					</div><!-- .sklc-gmaps-wrapper -->
					<?php

				}

			/* Module End */
			$this->module_end( $options );

		}

	}