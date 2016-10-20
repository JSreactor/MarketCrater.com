<?php

/*
 * Add Style and JS
 */

function lbmn_load_custom_wp_admin_style_js() {

		wp_register_style( 'lbmn_custom_wp_admin_css', get_template_directory_uri() . '/inc/themeinstallation-activate/theme-activation.css', false, '1.0.14' );
		wp_enqueue_style( 'lbmn_custom_wp_admin_css' );

		wp_register_script( 'lbmn_custom_wp_admin_script_validate', get_template_directory_uri() . '/inc/themeinstallation-activate/theme-activation.js', array( 'jquery') );
		wp_enqueue_script( 'lbmn_custom_wp_admin_script_validate' );
		wp_localize_script( 'lbmn_custom_wp_admin_script_validate', 'lbmnajax', array( 'nonce' => wp_create_nonce( 'lbmnajax' ) ) );

}
add_action( 'admin_print_scripts-themes.php', 'lbmn_load_custom_wp_admin_style_js' );

/*
 * Active Campaign
 */

add_action( 'wp_ajax_lbmn_activecampaign_email', 'lbmn_ajax_check_email' );
function lbmn_ajax_check_email(){

	// Check Nonce
	if ( !wp_verify_nonce( $_POST['security']['nonce'], 'lbmnajax' ) ) {
		wp_die('NO');
	}

	// Access permissions
	if ( !current_user_can( 'install_themes' ) ) {
		wp_die('You do not have rights!');
	}

	if ( get_option('lbmn_user') == false ) {
		$email = sanitize_email( $_POST["email"] );
		$name = sanitize_text_field( $_POST["name"] );
		$lbmn_four_step = array(
			'email' => $email,
			'name' => $name,
		);
		add_option( 'lbmn_user', $lbmn_four_step );
	} else {
		$options_val = get_option('lbmn_user');
		$email = sanitize_email( $_POST["email"] );
		$name = sanitize_text_field( $_POST["name"] );
		$options_val = $options_val + array( 'email' => $email, 'name' => $name );
		update_option('lbmn_user',$options_val);
	}

	wp_die();

}

/*
 * Purchase AJAX
 */

add_action( 'wp_ajax_lbmn_code_action', 'lbmn_ajax_check_purchase_code' );
function lbmn_ajax_check_purchase_code(){

	// Check Nonce
	if ( !wp_verify_nonce( $_POST['security']['nonce'], 'lbmnajax' ) ) {
		wp_die('NO');
	}

	// Access permissions
	if ( !current_user_can( 'install_themes' ) ) {
	   wp_die('You do not have rights!');
	}

	if ( get_option('lbmn_user') == false ) {
		$purchase_code = sanitize_key( $_POST["formData"] );
		$array = array( 'purchase_code' => $purchase_code );
		add_option( 'lbmn_user', $array );
	} else {
		$options_val = get_option('lbmn_user');
		$purchase_code = sanitize_key( $_POST["formData"] );
		$options_val = $options_val + array( 'purchase_code' => $purchase_code );
		update_option('lbmn_user',$options_val);
	}

	wp_die();
}

?>
