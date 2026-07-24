<?php
/**
 * Admin view.
 *
 * @package Form_Guard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$forms   = Form_Guard::get_forms();
$entries = Form_Guard::get_entries();
$stats   = array(
	'total_forms'  => count( $forms ),
	'total_entries' => count( $entries ),
);
?>
<div class="wrap fg-wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php if ( isset( $_GET['saved'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Saved successfully.', 'form-guard' ); ?></p></div>
<?php endif; ?>
	<?php if ( isset( $_GET['deleted'] ) ) : ?>
		<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Form deleted.', 'form-guard' ); ?></p></div>
<?php endif; ?>

	<div class="fg-summary">
		<div class="fg-card">
			<span class="fg-label"><?php esc_html_e( 'Total Forms', 'form-guard' ); ?></span>
			<span class="fg-value"><?php echo esc_html( number_format_i18n( $stats['total_forms'] ) ); ?></span>
		</div>
		<div class="fg-card">
			<span class="fg-label"><?php esc_html_e( 'Total Entries', 'form-guard' ); ?></span>
			<span class="fg-value"><?php echo esc_html( number_format_i18n( $stats['total_entries'] ) ); ?></span>
		</div>
	</div>

	<div class="fg-tabs">
		<a href="<?php echo esc_url( add_query_arg( 'tab', 'forms' ) ); ?>" class="fg-tab <?php echo 'forms' === $tab ? 'active' : ''; ?>"><?php esc_html_e( 'Forms', 'form-guard' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( 'tab', 'entries' ) ); ?>" class="fg-tab <?php echo 'entries' === $tab ? 'active' : ''; ?>"><?php esc_html_e( 'Entries', 'form-guard' ); ?></a>
		<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>" class="fg-tab <?php echo 'settings' === $tab ? 'active' : ''; ?>"><?php esc_html_e( 'Settings', 'form-guard' ); ?></a>
	</div>

	<?php if ( 'forms' === $tab ) : ?>
		<?php if ( $edit || isset( $_GET['edit'] ) ) : ?>
			<?php include FG_DIR . 'views/form-editor.php'; ?>
		<?php else : ?>
			<?php include FG_DIR . 'views/forms-list.php'; ?>
	<?php endif; ?>
	<?php elseif ( 'entries' === $tab ) : ?>
		<?php include FG_DIR . 'views/entries-list.php'; ?>
	<?php else : ?>
		<?php include FG_DIR . 'views/settings-page.php'; ?>
<?php endif; ?>
</div>
