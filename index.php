<?php
/*
 * Plugin Name: UnderConstruction for Page
 * Description: This plugin helps you setup under construction content for each page so that your user can't see original content until it's done.
 * Author: Hyvikk
 * Author URI: https://hyvikk.com/
 * Version: 1.0.2
 * Requires: 3.0 or higher
 */

if (!defined('ABSPATH')) {
	exit;
}
// Exit if accessed directly

add_action('add_meta_boxes', 'hvk_ucfp_meta_box_add');

function hvk_ucfp_meta_box_add() {
	add_meta_box('hvk_ucfp-meta-box', 'Under Construction', 'hvk_ucfp_meta_box_cb', 'page', 'side', 'high');
}

function hvk_ucfp_meta_box_cb() {
	// $post is already set, and contains an object: the WordPress post
	global $post;
	$values = get_post_custom($post->ID);

	$check = isset($values['hvk_ucfp_meta_box_check'][0]) ? esc_attr($values['hvk_ucfp_meta_box_check'][0]) : '';

	// We'll use this nonce field later on when saving.
	wp_nonce_field('hvk_ucfp_meta_box_nonce', 'meta_box_nonce');
	?>


    <p>
        <input type="checkbox" id="hvk_ucfp_meta_box_check" name="hvk_ucfp_meta_box_check" <?php checked($check, 'on');?> />
        <label for="hvk_ucfp_meta_box_check">Under Construction</label>
    </p>
    <?php
}

add_action('save_post', 'hvk_ucfp_meta_box_save');

function hvk_ucfp_meta_box_save($post_id) {

	// Bail if we're doing an auto save
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	// if our nonce isn't there, or we can't verify it, bail
	if (!isset($_POST['meta_box_nonce']) || !wp_verify_nonce($_POST['meta_box_nonce'], 'hvk_ucfp_meta_box_nonce')) {
		return;
	}

	// if our current user can't edit this post, bail
	if (!current_user_can('edit_post')) {
		return;
	}

	// now we can actually save the data
	$allowed = array(
		'a' => array( // on allow a tags
			'href' => array(), // and those anchors can only have href attribute
		),
	);

	// This is purely my personal preference for saving check-boxes
	$chk = isset($_POST['hvk_ucfp_meta_box_check']) && $_POST['hvk_ucfp_meta_box_check'] ? 'on' : 'off';
	update_post_meta($post_id, 'hvk_ucfp_meta_box_check', $chk);
}

add_action('admin_menu', 'hvk_ucfp_construction_design');

function hvk_ucfp_construction_design() {

	add_menu_page('Construction Design', 'Construction Design', 'manage_options', 'hvk_ucfp_custom_construction_page', 'hvk_ucfp_custom_construction_page', 'dashicons-book-alt');

}

function hvk_ucfp_custom_construction_page() {
	require_once 'hvk_ucfp_custom_construction_page.php';
}

function hvk_ucfp_custom_style_and_css($hook) {

	//echo "<h3 align='center'>".$hook."</h3>";

	if ('toplevel_page_hvk_ucfp_custom_construction_page' == $hook) {
		wp_register_style('bootstrap_css', plugins_url('css/bootstrap.css', __FILE__));

		wp_enqueue_style('bootstrap_css');

		wp_register_script('bootstrap-jquery', plugins_url('js/bootstrap.js', __FILE__), '', null, '');

		wp_enqueue_script('bootstrap-jquery');

	}
}

add_action('admin_enqueue_scripts', 'hvk_ucfp_custom_style_and_css');

add_filter('the_content', 'hvk_ucfp_featured_image_before_content');

function hvk_ucfp_featured_image_before_content($content) {

	global $post;

	$values = get_post_custom($post->ID);

	$check = isset($values['hvk_ucfp_meta_box_check'][0]) ? esc_attr($values['hvk_ucfp_meta_box_check'][0]) : '';

	if (!current_user_can('administrator') && $check == 'on') {
		$content = get_option('un_theme1');
	}

	return html_entity_decode(stripcslashes($content));
}

add_action('wp_head', 'hvk_ucfp_featured_image');

function hvk_ucfp_featured_image() {
	global $post;
	$values = get_post_custom($post->ID);

	$check = isset($values['hvk_ucfp_meta_box_check'][0]) ? esc_attr($values['hvk_ucfp_meta_box_check'][0]) : '';

	if (!current_user_can('administrator') && $check == 'on') {?>
         <style>
            .post-thumbnail {display:none;}
        </style>
            <?php
}

}

function hvk_ucfp_script() {
	global $post;
	$values = get_post_custom($post->ID);

	$check = isset($values['hvk_ucfp_meta_box_check'][0]) ? esc_attr($values['hvk_ucfp_meta_box_check'][0]) : '';

	if (!current_user_can('administrator') && $check == 'on') {?>
        <script>
            jQuery(".post-thumbnail").html('');
        </script>
        <?php
}
}

add_action('wp_footer', 'hvk_ucfp_script');

function hvk_ucfp_plugin_activate() {

	$value = "";

	$value = '<p style="text-align: center; font-size: 22px; color:gray;"><strong>Page is currently Under Construction</strong></p>';

	update_option('un_theme1', esc_html($value));

}
register_activation_hook(__FILE__, 'hvk_ucfp_plugin_activate');