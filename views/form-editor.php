<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_new    = ! $edit || empty( $edit['id'] );
$form_id   = $is_new ? 'fg_' . wp_rand( 1000, 9999 ) : $edit['id'];
$title     = $is_new ? '' : $edit['title'];
$email     = $is_new ? '' : $edit['email'];
$subject   = $is_new ? '' : $edit['subject'];
$success   = $is_new ? '' : $edit['success'];
$fields    = $is_new || empty( $edit['fields'] ) ? array() : $edit['fields'];
?>
<div class="fg-editor">
	<form method="post">
		<?php wp_nonce_field( 'fg_admin' ); ?>
		<input type="hidden" name="fg_action" value="save_form">
		<input type="hidden" name="fg_form_id" value="<?php echo esc_attr( $form_id ); ?>">

		<table class="form-table">
			<tr>
				<th><label><?php esc_html_e( 'Form Title', 'form-guard' ); ?></label></th>
				<td><input type="text" name="fg_title" value="<?php echo esc_attr( $title ); ?>" class="regular-text" required></td>
			</tr>
			<tr>
				<th><label><?php esc_html_e( 'Recipient Email', 'form-guard' ); ?></label></th>
				<td><input type="email" name="fg_email" value="<?php echo esc_attr( $email ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label><?php esc_html_e( 'Email Subject', 'form-guard' ); ?></label></th>
				<td><input type="text" name="fg_subject" value="<?php echo esc_attr( $subject ); ?>" class="regular-text"></td>
			</tr>
			<tr>
				<th><label><?php esc_html_e( 'Success Message', 'form-guard' ); ?></label></th>
				<td><input type="text" name="fg_success" value="<?php echo esc_attr( $success ); ?>" class="regular-text"></td>
			</tr>
		</table>

		<h3><?php esc_html_e( 'Fields', 'form-guard' ); ?></h3>
		<div id="fg-fields-list" class="fg-fields-list">
			<?php foreach ( $fields as $index => $field ) : ?>
				<div class="fg-field-row">
					<input type="text" name="fg_fields[<?php echo esc_attr( $index ); ?>][label]" value="<?php echo esc_attr( $field['label'] ); ?>" placeholder="Label" required>
					<select name="fg_fields[<?php echo esc_attr( $index ); ?>][type]">
						<option value="text" <?php selected( 'text', $field['type'] ); ?>>Text</option>
						<option value="email" <?php selected( 'email', $field['type'] ); ?>>Email</option>
						<option value="textarea" <?php selected( 'textarea', $field['type'] ); ?>>Textarea</option>
						<option value="select" <?php selected( 'select', $field['type'] ); ?>>Select</option>
						<option value="checkbox" <?php selected( 'checkbox', $field['type'] ); ?>>Checkbox</option>
					</select>
					<input type="text" name="fg_fields[<?php echo esc_attr( $index ); ?>][options]" value="<?php echo esc_attr( $field['options'] ); ?>" placeholder="Options (for select/checkbox)" class="regular-text">
					<label><input type="checkbox" name="fg_fields[<?php echo esc_attr( $index ); ?>][required]" value="1" <?php checked( $field['required'] ); ?>> <?php esc_html_e( 'Required', 'form-guard' ); ?></label>
					<button type="button" class="button fg-remove-field"><?php esc_html_e( 'Remove', 'form-guard' ); ?></button>
				</div>
			<?php endforeach; ?>
		</div>
		<button type="button" class="button" id="fg-add-field"><?php esc_html_e( 'Add Field', 'form-guard' ); ?></button>

		<p class="submit">
			<button type="submit" class="button button-primary"><?php esc_html_e( 'Save Form', 'form-guard' ); ?></button>
			<a href="<?php echo esc_url( admin_url( 'tools.php?page=form-guard&tab=forms' ) ); ?>" class="button"><?php esc_html_e( 'Cancel', 'form-guard' ); ?></a>
		</p>
	</form>
</div>
