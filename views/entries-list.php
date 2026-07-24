<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_filter = isset( $_GET['form_id'] ) ? sanitize_text_field( wp_unslash( $_GET['form_id'] ) ) : '';
$entries     = Form_Guard::get_entries( $form_filter );
$forms       = Form_Guard::get_forms();
?>
<div class="fg-section">
	<form method="get" class="fg-filter">
		<input type="hidden" name="page" value="form-guard">
		<input type="hidden" name="tab" value="entries">
		<select name="form_id">
			<option value=""><?php esc_html_e( 'All Forms', 'form-guard' ); ?></option>
			<?php foreach ( $forms as $form ) : ?>
				<option value="<?php echo esc_attr( $form['id'] ); ?>" <?php selected( $form_filter, $form['id'] ); ?>><?php echo esc_html( $form['title'] ); ?></option>
			<?php endforeach; ?>
		</select>
		<button type="submit" class="button"><?php esc_html_e( 'Filter', 'form-guard' ); ?></button>
	</form>
</div>

<?php if ( empty( $entries ) ) : ?>
	<div class="fg-empty"><?php esc_html_e( 'No entries yet.', 'form-guard' ); ?></div>
<?php else : ?>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ID', 'form-guard' ); ?></th>
				<th><?php esc_html_e( 'Form', 'form-guard' ); ?></th>
				<th><?php esc_html_e( 'Data', 'form-guard' ); ?></th>
				<th><?php esc_html_e( 'Date', 'form-guard' ); ?></th>
				<th><?php esc_html_e( 'Action', 'form-guard' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $entries as $entry ) : ?>
				<?php $data = json_decode( $entry['data'], true ); ?>
				<tr>
					<td><?php echo esc_html( $entry['id'] ); ?></td>
					<td><?php echo esc_html( $entry['form_id'] ); ?></td>
					<td>
						<?php foreach ( $data as $key => $value ) : ?>
							<strong><?php echo esc_html( $key ); ?>:</strong> <?php echo esc_html( $value ); ?><br>
						<?php endforeach; ?>
					</td>
					<td><?php echo esc_html( $entry['created_at'] ); ?></td>
					<td>
						<form method="post" class="fg-inline" onsubmit="return confirm('<?php esc_attr_e( 'Delete entry?', 'form-guard' ); ?>');">
							<?php wp_nonce_field( 'fg_admin' ); ?>
							<input type="hidden" name="fg_action" value="delete_entry">
							<input type="hidden" name="fg_entry_id" value="<?php echo esc_attr( $entry['id'] ); ?>">
							<button type="submit" class="button"><?php esc_html_e( 'Delete', 'form-guard' ); ?></button>
						</form>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
