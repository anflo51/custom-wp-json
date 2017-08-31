<?php
/*
Plugin Name: Custom WP JSON
Description: Custom API.
Author: Antonio Flores
Version: 0.1
*/

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'rest_api_init', 'custom_api_hooks' );

add_filter( 'rest_prepare_post', 'custom_fields_post', 10, 3 );

add_filter( 'rest_prepare_user', 'custom_fields_user', 10, 3 );

function custom_api_hooks() {

	register_api_field(
		'post',
		'intro',
		array(
			'get_callback' => 'return_intro',
		)
	);

	register_api_field(
		'post',
		'plaintitle',
		array(
			'get_callback' => 'return_plaintitle',
		)
	);

	register_api_field(
		'post',
		'plaintext',
		array(
			'get_callback' => 'return_plaintext',
		)
	);
}

function return_plaintitle( $object, $field_name, $request){
	return $object['title']['rendered'];
}

function return_intro( $object, $field_name, $request){
	return strip_tags(html_entity_decode($object['excerpt']['rendered']));
}

function return_plaintext( $object, $field_name, $request ) {
	return strip_tags( html_entity_decode( $object['content']['rendered'] ) );
}

function custom_fields_post( $data, $post, $request ) {
	$_data = $data->data;
	$params = $request->get_params();
	if ( ! isset( $params['id'] ) ) {
		unset( $_data['date'] );
		unset( $_data['date_gmt'] );
		unset( $_data['guid'] );
		unset( $_data['status'] );
		unset( $_data['modified'] );
		unset( $_data['modified_gmt'] );
		unset( $_data['type'] );
		unset( $_data['link'] );
		unset( $_data['comment_status'] );
		unset( $_data['ping_status'] );
		unset( $_data['sticky'] );
		unset( $_data['template'] );
		unset( $_data['format'] );
		unset( $_data['meta'] );
		unset( $_data['tags'] );
		unset( $_data['content'] );
		unset( $_data['title'] );
		unset( $_data['excerpt'] );
		unset( $_data['slug'] );
		unset( $_data['author'] );
		unset( $_data['featured_media'] );
	}

	$the_post = get_post( $post->ID );
	$the_author = get_user_by( 'ID', $the_post->post_author );
	$_data['author_name'] = $the_author->display_name;

	$the_description = get_the_author_meta( 'description' );
	$_data['author_description'] = $the_description;

	$image_id = get_post_thumbnail_id( $post->ID );
	$url = wp_get_attachment_image_src( $image_id );
	$_data['image_url'] = $url[0];

	$data->data = $_data;
	return $data;
}

function custom_fields_user( $data, $post, $request ) {
	$_data = $data->data;
	$params = $request->get_params();
	if ( ! isset( $params['id'] ) ) {
		unset( $_data['url'] );
		unset( $_data['link'] );
		unset( $_data['avatar_urls'] );
		unset( $_data['meta'] );
	}

	$data->data = $_data;
	return $data;
}