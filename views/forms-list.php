<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="fg-section">
	<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'forms', 'edit' => 'new' ), admin_url( 'tools.php?page=form-guard' ) ) ); ?>" class="button button-primary"><?php esc_html_e( 'Add New Form', 'form-guard' ); ?></a>
</div>

<?php if ( empty( $forms ) ) : ?>
	<div class="fg-empty"><?php esc_html_e( 'No forms yet. Create your first form.', 'form-guard' ); ?></div>
<?php else : ?>
	<div class="fg-grid">
		<?php foreach ( $forms as $form ) : ?>
			<div class="fg-card-item">
				<h3><?php echo esc_html( $form['title'] ); ?></h3>
				<code>[form_guard id="<?php echo esc_attr( $form['id'] ); ?>"]</code>
				<div class="fg-actions">
					<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'forms', 'edit' => $form['id'] ), admin_url( 'tools.php?page=form-guard' ) ) ); ?>" class="button"><?php esc_html_e( 'Edit', 'form-guard' ); ?></a>
					<form method="post" class="fg-inline" onsubmit="return confirm('<?php esc_attr_e( 'Delete this form and all its entries?', 'form-guard' ); ?>');">
						<?php wp_nonce_field( 'fg_admin' ); ?>
						<input type="hidden" name="fg_action" value="delete_form">
						<input type="hidden" name="fg_delete_id" value="<?php echo esc_attr( $form['id'] ); ?>">
						<button type="submit" class="button"><?php esc_html_e( 'Delete', 'form-guard' ); ?></button>
					</form>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
