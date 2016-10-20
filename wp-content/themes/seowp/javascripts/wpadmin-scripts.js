/*
 * Theme back-end JavaScript
 *
 * -------------------------------------------------------------------
 *
 * DESCRIPTION:
 *
 * Custom JavaScript used to improve/extend some bundled plugins UI,
 * run actions for theme installation wizard
 *
 * @package    SEOWP WordPress Theme
 * @author     Vlad Mitkovsky <info@lumbermandesigns.com>
 * @copyright  2014 Lumberman Designs
 * @license    GNU GPL, Version 3
 * @link       http://themeforest.net/user/lumbermandesigns
 *
 * -------------------------------------------------------------------
 *
 * Send your ideas on code improvement or new hook requests using
 * contact form on http://themeforest.net/user/lumbermandesigns
 */

(function ($) {
	"use strict";

	jQuery(document).ready(function ($) {

		// Helper function to get the value from URL Parameter
		var QueryString = function () {
		  // This function is anonymous, is executed immediately and
		  // the return value is assigned to QueryString!
		  var query_string = {};
		  var query = window.location.search.substring(1);
		  var vars = query.split("&");
		  for (var i=0;i<vars.length;i++) {
			 var pair = vars[i].split("=");
				// If first entry with this name
			 if (typeof query_string[pair[0]] === "undefined") {
				query_string[pair[0]] = pair[1];
				// If second entry with this name
			 } else if (typeof query_string[pair[0]] === "string") {
				var arr = [ query_string[pair[0]], pair[1] ];
				query_string[pair[0]] = arr;
				// If third or later entry with this name
			 } else {
				query_string[pair[0]].push(pair[1]);
			 }
		  }
			 return query_string;
		} ();

		/* Hide theme quick setup block on "Hide this message" button click */
		if (QueryString.hide_quicksetup) {
			$(".lumberman-message.quick-setup").hide();
		}


		/**
		* ----------------------------------------------------------------------
		* Mega Main Menu item alignment control
		* TODO: move this block into a separate file /inc/plugin-integrations/megamainmenu/megamainmenu-wpadmin.js
		*/

		var alignmentSelector = '';
		alignmentSelector += '<div class="clearboth"></div>';
		alignmentSelector += '<div class="bootstrap">';
		alignmentSelector += '	<div class="menu-item-align option row menu-item-124item_style select_type">';

		alignmentSelector += '		<div class="option_header col-md-3 col-sm-12">';
		alignmentSelector += '			<div class="descr">';
		alignmentSelector += '				Menu item alignment';
		alignmentSelector += '			</div><!-- class="descr" -->';
		alignmentSelector += '		</div>';

		alignmentSelector += '		<div class="option_field col-md-9 col-sm-12">';
		alignmentSelector += '			<select name="lbmn_menu-item-align" class="col-xs-12 form-control input-sm">';
		alignmentSelector += '				<option value="">Default</option>';
		alignmentSelector += '				<option value="menu-align-left">Left</option>';
		alignmentSelector += '				<option value="menu-align-right">Right</option>';
		alignmentSelector += '			</select>';
		// alignmentSelector += '<input type="text" class="widefat code edit-menu-item-classes" name="syntetic-menu-item-classes" value="" />';//style="display:none"
		alignmentSelector += '		</div>';

		alignmentSelector += '		<div class="col-xs-12">';
		alignmentSelector += '			<div class="h_separator">';
		alignmentSelector += '			</div><!-- class="h_separator" -->';
		alignmentSelector += '		</div>';

		alignmentSelector += '	</div>';
		alignmentSelector += '</div>';


		$(".nav-menus-php .menu-item-depth-0").each(function(index, el) {
			$(".menu-item-settings .bootstrap", el).eq('1').before(alignmentSelector);
		});

		// on page load update menu align dropdown value according to menu item class
		$("select[name=lbmn_menu-item-align]").each(function(index, val) {
			var align_selector    = $(this);
			var menu_class_field  = $(this).parents('.menu-item-settings').find('.edit-menu-item-classes');
			var current_class_val = menu_class_field.val();
			var align_class       = current_class_val.match(/menu-align-left|menu-align-right/g, '');

			if ( align_class == 'menu-align-left' ) {
				align_selector.find('option[value="menu-align-left"]').attr('selected', 'selected');
			} else if ( align_class == 'menu-align-right' ) {
				align_selector.find('option[value="menu-align-right"]').attr('selected', 'selected');
			}

		});

		// on menu align dropdown change update class field
		$("select[name=lbmn_menu-item-align]").live('change', function(event) {
			var selected_value    = $(this).find('option:selected').val();
			var menu_class_field  = $(this).parents('.menu-item-settings').find('.edit-menu-item-classes');
			var current_class_val = menu_class_field.val();
			var cleaned_classes   = current_class_val.replace(/menu-align-left|menu-align-right/g, '');

			if (cleaned_classes != null) {
				selected_value = cleaned_classes + ' ' + selected_value;
				selected_value = selected_value.replace(/\s+/g, ' ');
			}

			menu_class_field.val(selected_value);
		});


		/**
		* ----------------------------------------------------------------------
		* TGM plugin activation page improvements
		*/
		if ( $('body').hasClass('appearance_page_install-required-plugins') ) {
			// we are on the istall required plugins page

			$('#the-list tr').each(function(index, el) {
				var currentRow = $(this);

				// Mark row with .plugin-required class
				if ( $(currentRow).find('.type').text() == 'Required' ) {
					$(currentRow).addClass('required');
				}

				// Mark row with .plugin-notinstalled class
				if ( $(currentRow).find('.status').text() == 'Not Installed' ) {
					$(currentRow).addClass('notinstalled');
				}

				// Mark row with .plugin-notinstalled class
				if ( $(currentRow).find('.status').text() == 'Not Updated' ) {
					$(currentRow).addClass('update');
				}

				// Mark row with .plugin-notactive class
				if ( $(currentRow).find('.status').text() == 'Installed But Not Activated' ) {
					$(currentRow).addClass('inactive');
				}

				// Mark row with .plugin-active class
				if ( $(currentRow).find('.status').text() == 'Active' ) {
					$(currentRow).addClass('active');
				}
			});

			// Duplicate action link next to "Status" column
			$('.row-actions a').each(function(index, el) {
				var statusColumns = $(this).closest('tr').find('.status');
				$(this).clone().addClass('button button-primary').appendTo( statusColumns );
			});

			// Remove source column, it only confuse users
			$('.column-source').hide();
			$('.column-version').hide();

			/**
			 * ----------------------------------------------------------------------
			 * Auto plugins installation
			 */

			if (QueryString.autoinstall) {
				$('.check-column input').attr('checked','checked').change();
				$('select[name=action]').find('option').removeAttr('selected');
				$('select[name=action] option[value=tgmpa-bulk-install]').attr('selected','selected');
				$('select[name=action]').change();
				$('input#doaction').trigger('click');

			}
		}

		// Change TGMPA notification links into buttons

		$("#setting-error-tgmpa a").each(function() {
			if( ($(this).text() == 'Begin updating plugin') ||
				 ($(this).text() == 'Begin updating plugins') ||
				 ($(this).text() == 'Begin installing plugin') ||
				 ($(this).text() == 'Begin installing plugins') ||
				 ($(this).text() == 'Begin activating plugin') ||
				 ($(this).text() == 'Begin activating plugins')
			) {
				$(this).addClass('button button-primary');
			}
		});


		/**
		 * ----------------------------------------------------------------------
		 * Theme installation wizard action:
		 * 1. Plugins installation
		 */

		var window_hash = window.location.hash.substr(1);
		$("#do_plugins-install").click(function() {
			// disable spinner
			$("#theme-setup-step-1").addClass('loading');
		});

		// Check if all plugins were installed manually
		// If all installed: redirect to themes.php with url var set
		if ( window_hash === 'checkifallinstalled' ) {
			if ( $(".wp-list-table.plugins tr.inactive").length < 1 ) {
				window.location.replace(location.protocol + '//' + location.host + location.pathname+"?plugins=installed");
			}
		}


		/**
		 * ----------------------------------------------------------------------
		 * Theme installation wizard action:
		 * 2. Configure basic settings
		 */

		$("#do_basic-config").click(function(event) {
			event.preventDefault();

			// Do not run multiply times
			if ( ! $("#theme-setup-step-2").hasClass('step-completed') ) {
				// Do not run before step 1
				if ( $("#theme-setup-step-1").hasClass('step-completed') ) {

					$("#theme-setup-step-2").addClass('loading');
					ajax_importcontent_part('basic-templates');

				} else {
					$( "#theme-setup-step-1" ).effect( "bounce", 1000);
				}
			}
		});

		// Show error log functionality
		$('.show-error-log').on('click', function(event) {
			event.preventDefault();
			/* Act on the event */
			$(".error-log-window").show();
		});


		/**
		 * ----------------------------------------------------------------------
		 * Theme installation wizard action:
		 * 3. Import demo content
		 */

		$("#do_demo-import").click(function(event) {
			event.preventDefault();

			// Do not run before step 1
			if ( $("#theme-setup-step-1").hasClass('step-completed') ) {
				// Do not run before step 2
				if ( $("#theme-setup-step-2").hasClass('step-completed') ) {
					$("#theme-setup-step-3").addClass('loading');
					ajax_importcontent_part();
				} else {
					$( "#theme-setup-step-2" ).effect( "bounce", 1000);
				}
			} else {
				$( "#theme-setup-step-1" ).effect( "bounce", 1000);
			}
		});



		/**
		 * ----------------------------------------------------------------------
		 * LiveComposer Settings Page
		 * Warning to use only letters and numbers in the sidebar name
		 */

		// if current admin screens is Live Composer > Widgets Module
		if ( $("body").hasClass('live-composer_page_dslc_plugin_options_widgets_m') ) {
			$(".dslca-plugin-opts-list-wrap .dslca-plugin-opts-list-add-hook").on('click', function(event) {
				// event.preventDefault();
				/* Act on the event */
				$(this).before(
					"<p style=' width: 300px; margin-bottom: 20px; font-size: 13px; color: #CF522A;'> Only letters, numbers and spaces may be used in sidebar names. </p>"
				);
			});

		}

		// On Live Composer settings page
		// hide "Archives Settings" and "Tutorials" tabs
		$("a[href='?page=dslc_plugin_options_tuts'] ").hide();


		/**
		 * ----------------------------------------------------------------------
		 * Hide unwanted metaboxes on edit screen
		 */

		if ( $("body").hasClass('wp-admin') ) {
			// Hide Mega Main Options metabox
			$(".postbox#mm_general").hide();

			// For pages only
			if ( $("body").hasClass('post-type-page') ) {
				// Hide discussion metabox
				$(".postbox#commentstatusdiv").hide();
			}
		}


		/**
		 * ----------------------------------------------------------------------
		 * Update menus screen if Mega Menu not initialized
		 * (to solve bug when mega menu breaks on the first edit )
		 */

		if ( $("body").hasClass('nav-menus-php') ) {
			// If "Demo Mega Menu (Header)" selected
			if ( $(".manage-menus select#menu option[selected='selected']").text().indexOf("Demo Mega Menu (Header)") != -1 ) {
				if ( $("#menu-management .menu-item .background_image_type").length == 0 ) {
					location.reload(true);
				}
			}
		}

		/**
		 * ----------------------------------------------------------------------
		 * Yoast SEO + Live Composer = integration
		 * https://github.com/Yoast/YoastSEO.js
		 *
		 * With help of the code below Yoast SEO plugin will analyse
		 * 'dslc_html_content' custom field, the same way as the content form WP editor.
		 *
		 */

		if (typeof YoastSEO != "undefined") {

			var YoastLovesLC = function() {
				YoastSEO.app.registerPlugin( 'yoastLovesLC', {status: 'ready'} );

			 /**
				* @param modification    {string}    The name of the filter
				* @param callable        {function}  The callable
				* @param pluginName      {string}    The plugin that is registering the modification.
				* @param priority        {number}    (optional) Used to specify the order in which the callables
				*                                    associated with a particular filter are called. Lower numbers
				*                                    correspond with earlier execution.
				*/

				YoastSEO.app.registerModification( 'content', this.lbmnContentModification, 'yoastLovesLC', 99 );
			}

			/**
			 * Adds some text to the data...
			 *
			 * @param data The data to modify
			 */
			YoastLovesLC.prototype.lbmnContentModification = function(data) {

				var pageContent = '';

				if (typeof lbmnData.currentPageContent !== 'undefined') {
					if ( lbmnData.currentPageContent.length) {
						pageContent = lbmnData.currentPageContent;
					}
				}

				if ( pageContent.length ) {
					return pageContent;
				} else {
					return data;
				}

			};

			new YoastLovesLC();

		}


	}); // document.ready


	/**
	 * ----------------------------------------------------------------------
	 * Special Functions That Called from TGMPA php file to update
	 * installer required plugins status
	 */

	// This function will be caled on the end of plugin installation from
	// hidden 'iframe-plugins-install' iframe
	window.pluginsInstalledSuccessfully = function () {
		// disable spinner
		$("#theme-setup-step-1").removeClass('loading');
		// mark installer step as completed
		$(".lumberman-message.quick-setup .step-plugins").addClass("step-completed");
		// hide standard TGMPA notice
		$("#setting-error-tgmpa").hide();
	}

	window.pluginsInstallFailed = function () {
		// disable spinner
		$("#theme-setup-step-1").removeClass('loading');
		// show error message
		$(".lumberman-message.quick-setup .step-plugins .error").css("display","inline-block");
	}



	function ajax_importcontent_part(content_type, step_current, step_desc, step_current_no, steps_total){
		// sets defaults
		content_type = typeof content_type !== 'undefined' ?  content_type : 'alldemocontent';
		step_current = typeof step_current !== 'undefined' ?  step_current : '';
		step_desc = typeof step_desc !== 'undefined' ?  step_desc : '';
		step_current_no = typeof step_current_no !== 'undefined' ?  step_current_no : 0;
		steps_total = typeof steps_total !== 'undefined' ?  steps_total : 100;

		var progressbar_position = ( parseInt(step_current_no) + 2) * (100 / steps_total); // 100% / 20steps = 5% per step

		// var progressbar_position = (parseInt(step_current)+1) * 5; // 100% / 20steps = 5% per step
		var currentdate = new Date();
		console.info( "--------------------" );
		console.info( 'current part id: ' + step_current );
		console.info( 'current part no: ' + step_current_no );
		console.info( 'current part desc: ' + step_desc );
		console.info( 'steps_total: ' + steps_total );

		var step_css_class = '.step-demoimport';
		if ( content_type == 'basic-templates' ) {
			step_css_class = '.step-basic_config';
		}

		$(step_css_class + " .import-progress-desc").text(step_desc);

		console.info( currentdate.getHours() + ":"
					 + currentdate.getMinutes() + ":"
					 + currentdate.getSeconds() + "  >>  "
					 + step_current);

		if(step_current)
			step_current = '&importcontent_step_current_id=' + step_current;
		else
			step_current = '&importcontent_step_current_id';

		$.ajax({
			cache: false,
			url: location.protocol + '//' + location.host + location.pathname + "?importcontent=" + content_type + step_current,
			success: function(response){

				$(step_css_class + ' .progress-indicator').css('width', progressbar_position + '%');

				if ( $(response).find('#importcontent_step_next_id').length > 0 ) {
					console.info( 'part' + step_current );

					if ( $(response).find(".ajax-request-error").length > 0 ) {
						$(".lumberman-message.quick-setup .step-basic_config .error").css('display', 'inline-block');
						$(".lumberman-message.quick-setup").after('<div class="error-log-window" style="display:none"></div>');
						$(".error-log-window").append( $(response).find(".ajax-log") );
						$('.lumberman-message.quick-setup .step-basic_config .error-log-window').css('display','inline-block');

						// config process succeeded
					}else{
						ajax_importcontent_part( content_type, $(response).find('#importcontent_step_next_id').val(), $(response).find('#importcontent_step_current_descr').val(), $(response).find('#importcontent_step_current_no').val(), $(response).find('#importcontent_steps_total').val() );
					}

				// no #importcontent_step_next_id field in the content
				} else {
					$(step_css_class).removeClass('loading');
					// config process failed
					if ( $(response).find(".ajax-request-error").length > 0 ) {
						$(".lumberman-message.quick-setup .step-basic_config .error").css('display', 'inline-block');
						$(".lumberman-message.quick-setup").after('<div class="error-log-window" style="display:none"></div>');
						$(".error-log-window").append( $(response).find(".ajax-log") );
						$('.lumberman-message.quick-setup .step-basic_config .error-log-window').css('display','inline-block');

						// config process succeeded
					} else {
						$(step_css_class).addClass("step-completed");
						// update option "LBMN_THEME_NAME . '_democontent_imported'"
						// with 'true' value
						$.ajax({
							cache: false,
							url: location.protocol + '//' + location.host + location.pathname + "?demoimport=" + content_type + "completed"
						});
					}
				}
			}
		}); //ajax
	}

	/**
	 * ----------------------------------------------------------------------
	 * Call Ajax action to dismiss the admin notice
	 */
	jQuery(document).on( 'click', '.lbmn-notice .notice-dismiss', function(event) {

		var notice_id = event.target.parentNode.id;
		var nonce = event.target.parentNode.getAttribute("data-nonce");

		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'lbmn_dismiss_notice',
				nonce: nonce,
				notice_id: notice_id,
			}
		})
	})

	/**
	 * ----------------------------------------------------------------------
	 * Remove Rankie License Box
	 */
	if ( $('body').hasClass('wp-rankie_page_wp_rankie_settings') ) {
		$('.wp-rankie_page_wp_rankie_settings .metabox-holder .postbox').last().hide();
		$('.wp-rankie_page_wp_rankie_settings #postbox-container-1').css('min-width','800px');
	}

})(jQuery);