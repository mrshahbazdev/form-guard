<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings = Form_Guard::get_settings();
?>
<form method="post">
	<?php wp_nonce_field( 'fg_admin' ); ?>
	<input type="hidden" name="fg_action" value="save_settings">
	<table class="form-table">
		<tr>
			<th><label><?php esc_html_e( 'Global Recipient Email', 'form-guard' ); ?></label></th>
			<td><input type="email" name="fg_global_email" value="<?php echo esc_attr( $settings['global_email'] ); ?>" class="regular-text"></td>
		</tr>
	</table>
	<?php submit_button( __( 'Save Settings', 'form-guard' ) ); ?>
</form>
