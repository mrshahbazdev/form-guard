<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<form class="fg-form" data-form-id="<?php echo esc_attr( $form['id'] ); ?>">
	<?php wp_nonce_field( 'fg_public_nonce', 'fg_public_nonce' ); ?>
	<input type="hidden" name="fg_form_id" value="<?php echo esc_attr( $form['id'] ); ?>">
	<input type="text" name="fg_website" class="fg-hp" tabindex="-1" autocomplete="off">

	<?php foreach ( $form['fields'] as $field ) : ?>
		<div class="fg-field">
			<label><?php echo esc_html( $field['label'] ); ?><?php echo $field['required'] ? ' <span class="fg-required">*</span>' : ''; ?></label>
			<?php if ( 'textarea' === $field['type'] ) : ?>
				<textarea name="fg_<?php echo esc_attr( $field['name'] ); ?>" <?php echo $field['required'] ? 'required' : ''; ?>></textarea>
			<?php elseif ( 'select' === $field['type'] ) : ?>
				<select name="fg_<?php echo esc_attr( $field['name'] ); ?>" <?php echo $field['required'] ? 'required' : ''; ?>>
					<option value=""><?php esc_html_e( 'Select...', 'form-guard' ); ?></option>
					<?php foreach ( array_map( 'trim', explode( "\n", $field['options'] ) ) as $opt ) : ?>
						<?php if ( $opt ) : ?>
							<option value="<?php echo esc_attr( $opt ); ?>"><?php echo esc_html( $opt ); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			<?php elseif ( 'checkbox' === $field['type'] ) : ?>
				<?php foreach ( array_map( 'trim', explode( "\n", $field['options'] ) ) as $opt ) : ?>
					<?php if ( $opt ) : ?>
						<label class="fg-check-label"><input type="checkbox" name="fg_<?php echo esc_attr( $field['name'] ); ?>[]" value="<?php echo esc_attr( $opt ); ?>" <?php echo $field['required'] ? 'required' : ''; ?>> <?php echo esc_html( $opt ); ?></label>
					<?php endif; ?>
				<?php endforeach; ?>
			<?php else : ?>
				<input type="<?php echo esc_attr( $field['type'] ); ?>" name="fg_<?php echo esc_attr( $field['name'] ); ?>" <?php echo $field['required'] ? 'required' : ''; ?>>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>

	<div class="fg-response" style="display:none;"></div>
	<button type="submit" class="button fg-submit"><?php esc_html_e( 'Submit', 'form-guard' ); ?></button>
</form>
