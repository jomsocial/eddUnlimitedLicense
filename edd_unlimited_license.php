<?php
/**
 * Plugin Name: EDD Unlimited License
 * Plugin URI: http://peepso.com
 * Description: EDD Unlimited License
 * Version: 1.0
 * Author: peepso.com
 * Author URI: peepso.com
 * Text Domain: edd_unlimited_license 
 * License: 
 */
 
defined('ABSPATH') or die("No script kiddies please!");

function edd_update_unlimited_license_front($license_id, $expiration){
	$payment_id			= get_post_meta( $license_id, '_edd_sl_payment_id', true );
	$license_details 	= get_post_meta( $payment_id, '_edd_payment_meta', true );
	$license_product 	= get_post_meta( $license_id, '_edd_sl_download_id', true );	
	$unlimited_license 	= get_post_meta( $license_product, '_unlimited_license', true );
	//$price_id			= get_post_meta( $license_id, '_edd_sl_download_price_id', true );

	if(!empty($license_details)){
		foreach($license_details['downloads'] as $product){
			if($product['id'] != $license_product){
				continue;
			}

			if($unlimited_license == 1){
				$license_length			= '+20years';
				$expiration     		= strtotime( $license_length, strtotime( get_post_field( 'post_date', $license_id, 'raw' ) ) );
				update_post_meta( $license_id, '_edd_sl_expiration', $expiration );
				return;
			}

		}
	}
}
add_action( 'edd_sl_post_set_expiration', 'edd_update_unlimited_license_front', 99, 2 );


function edd_unlimited_update_non_expiring($post_id){
	if ( isset( $_REQUEST['_unlimited_license'] ) ) {
		update_post_meta( $post_id, '_unlimited_license', 1 );
	} else {
		update_post_meta( $post_id, '_unlimited_license', 0 );
	}
}
add_action( 'save_post', 'edd_unlimited_update_non_expiring', 12 );

function edd_unlimited_add_non_expiring($post){
	$defaults = array(
		'post_type'      => 'js_add_non_expiring',
		'paged'          => null,
		'post_status'    => array( 'active' )
	);

	$args = wp_parse_args( $defaults );
	
	$enabled   	= get_post_meta( $post->ID, '_unlimited_license', true ) ? true : false;
	echo '<table class="form-table">';
		echo '<tr>';
			echo '<td class="edd_field_type_text" colspan="2">';
				echo '<input type="checkbox" name="_unlimited_license" id="unlimited_license" value="1" ' . checked( true, $enabled, false ) . '/>&nbsp;';
				echo '<label for="unlimited_license">' . __( 'Check to set unlimited license period', 'edd_unlimited_license' ) . '</label>';
			echo '<td>';
		echo '</tr>';
	echo '</table>';		
}

/**
 * Add License Meta Box
 *
 * @since 1.0
 */
function edd_unlimited_add_non_expiring_meta_box() {
	global $post;

	add_meta_box( 'edd_unlimited_add_non_expiring', __( 'Unlimited license period', 'edd_unlimited_license' ), 'edd_unlimited_add_non_expiring', 'download', 'normal', 'low' );
}
add_action( 'add_meta_boxes', 'edd_unlimited_add_non_expiring_meta_box', 101 );