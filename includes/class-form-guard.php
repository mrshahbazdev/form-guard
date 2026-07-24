<?php
/**
 * Form Guard core class.
 *
 * @package Form_Guard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Form_Guard
 */
class Form_Guard {

	const OPTION     = 'form_guard_settings';
	const FORMS_OPTION = 'form_guard_forms';

	/**
	 * Initialize.
	 */
	public static function init() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_init', array( __CLASS__, 'handle_actions' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'frontend_assets' ) );
		add_action( 'wp_ajax_fg_submit', array( __CLASS__, 'ajax_submit' ) );
		add_action( 'wp_ajax_nopriv_fg_submit', array( __CLASS__, 'ajax_submit' ) );
		add_shortcode( 'form_guard', array( __CLASS__, 'render_shortcode' ) );
		add_action( 'plugins_loaded', array( __CLASS__, 'maybe_create_table' ) );
	}

	/**
	 * Activation hook.
	 */
	public static function activate() {
		self::maybe_create_table();
	}

	/**
	 * Create entries table.
	 */
	public static function maybe_create_table() {
		global $wpdb;
		$table_name = $wpdb->prefix . FG_TABLE;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			form_id varchar(50) NOT NULL,
			data longtext NOT NULL,
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (id)
		) {$charset_collate};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Get settings.
	 *
	 * @return array
	 */
	public static function get_settings() {
		$defaults = array(
			'global_email' => get_option( 'admin_email' ),
		);
		$settings = get_option( self::OPTION, array() );
		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Get all forms.
	 *
	 * @return array
	 */
	public static function get_forms() {
		$forms = get_option( self::FORMS_OPTION, array() );
		return is_array( $forms ) ? $forms : array();
	}

	/**
	 * Get a single form.
	 *
	 * @param string $id Form id.
	 * @return array|null
	 */
	public static function get_form( $id ) {
		$forms = self::get_forms();
		return isset( $forms[ $id ] ) ? $forms[ $id ] : null;
	}

	/**
	 * Save forms.
	 *
	 * @param array $forms Forms.
	 */
	public static function save_forms( $forms ) {
		update_option( self::FORMS_OPTION, $forms );
	}

	/**
	 * Add admin menu.
	 */
	public static function add_menu() {
		add_management_page(
			esc_html__( 'Form Guard', 'form-guard' ),
			esc_html__( 'Form Guard', 'form-guard' ),
			'manage_options',
			'form-guard',
			array( __CLASS__, 'render_admin' )
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook Hook.
	 */
	public static function enqueue_assets( $hook ) {
		if ( 'tools_page_form-guard' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'fg-admin', FG_URL . 'assets/css/admin.css', array(), FG_VERSION );
		wp_enqueue_script( 'fg-admin', FG_URL . 'assets/js/admin.js', array( 'jquery' ), FG_VERSION, true );
	}

	/**
	 * Frontend assets.
	 */
	public static function frontend_assets() {
		wp_enqueue_style( 'fg-public', FG_URL . 'assets/css/public.css', array(), FG_VERSION );
		wp_register_script( 'fg-public', FG_URL . 'assets/js/public.js', array( 'jquery' ), FG_VERSION, true );
		wp_localize_script(
			'fg-public',
			'fg_public',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'fg_public_nonce' ),
			)
		);
		wp_enqueue_script( 'fg-public' );
	}

	/**
	 * Handle admin actions.
	 */
	public static function handle_actions() {
		if ( ! isset( $_POST['fg_action'] ) || ! isset( $_POST['_wpnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'fg_admin' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$action = sanitize_text_field( wp_unslash( $_POST['fg_action'] ) );

		if ( 'save_form' === $action ) {
			$id      = isset( $_POST['fg_form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['fg_form_id'] ) ) : '';
			$title   = isset( $_POST['fg_title'] ) ? sanitize_text_field( wp_unslash( $_POST['fg_title'] ) ) : '';
			$fields  = isset( $_POST['fg_fields'] ) ? self::sanitize_fields( wp_unslash( $_POST['fg_fields'] ) ) : array();
			$email   = isset( $_POST['fg_email'] ) ? sanitize_email( wp_unslash( $_POST['fg_email'] ) ) : '';
			$subject = isset( $_POST['fg_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['fg_subject'] ) ) : '';
			$message = isset( $_POST['fg_success'] ) ? sanitize_textarea_field( wp_unslash( $_POST['fg_success'] ) ) : '';

			$forms = self::get_forms();
			$forms[ $id ] = array(
				'id'      => $id,
				'title'   => $title,
				'fields'  => $fields,
				'email'   => $email,
				'subject' => $subject,
				'success' => $message,
			);
			self::save_forms( $forms );
			wp_safe_redirect( add_query_arg( array( 'tab' => 'forms', 'saved' => '1' ), admin_url( 'tools.php?page=form-guard' ) ) );
			exit;
		}

		if ( 'delete_form' === $action && isset( $_POST['fg_delete_id'] ) ) {
			$delete_id = sanitize_text_field( wp_unslash( $_POST['fg_delete_id'] ) );
			$forms     = self::get_forms();
			unset( $forms[ $delete_id ] );
			self::save_forms( $forms );
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . FG_TABLE, array( 'form_id' => $delete_id ), array( '%s' ) );
			wp_safe_redirect( add_query_arg( array( 'tab' => 'forms', 'deleted' => '1' ), admin_url( 'tools.php?page=form-guard' ) ) );
			exit;
		}

		if ( 'delete_entry' === $action && isset( $_POST['fg_entry_id'] ) ) {
			$entry_id = absint( $_POST['fg_entry_id'] );
			global $wpdb;
			$wpdb->delete( $wpdb->prefix . FG_TABLE, array( 'id' => $entry_id ), array( '%d' ) );
			wp_safe_redirect( add_query_arg( array( 'tab' => 'entries', 'entry_deleted' => '1' ), admin_url( 'tools.php?page=form-guard' ) ) );
			exit;
		}

		if ( 'save_settings' === $action && isset( $_POST['fg_global_email'] ) ) {
			$settings = self::get_settings();
			$settings['global_email'] = sanitize_email( wp_unslash( $_POST['fg_global_email'] ) );
			update_option( self::OPTION, $settings );
			wp_safe_redirect( add_query_arg( array( 'tab' => 'settings', 'saved' => '1' ), admin_url( 'tools.php?page=form-guard' ) ) );
			exit;
		}
	}

	/**
	 * Sanitize fields.
	 *
	 * @param mixed $fields Raw fields.
	 * @return array
	 */
	public static function sanitize_fields( $fields ) {
		if ( ! is_array( $fields ) ) {
			return array();
		}
		$clean = array();
		foreach ( $fields as $field ) {
			$label = isset( $field['label'] ) ? sanitize_text_field( wp_unslash( $field['label'] ) ) : '';
			$type  = isset( $field['type'] ) ? sanitize_text_field( wp_unslash( $field['type'] ) ) : 'text';
			$name  = isset( $field['name'] ) ? sanitize_title( wp_unslash( $field['name'] ) ) : sanitize_title( $label );
			$req   = ! empty( $field['required'] );
			$opts  = isset( $field['options'] ) ? sanitize_textarea_field( wp_unslash( $field['options'] ) ) : '';
			if ( $label ) {
				$clean[] = array(
					'label'    => $label,
					'type'     => in_array( $type, array( 'text', 'email', 'textarea', 'select', 'checkbox' ), true ) ? $type : 'text',
					'name'     => $name,
					'required' => $req,
					'options'  => $opts,
				);
			}
		}
		return $clean;
	}

	/**
	 * Render admin page.
	 */
	public static function render_admin() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission.', 'form-guard' ) );
		}

		$tab      = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'forms';
		$forms    = self::get_forms();
		$settings = self::get_settings();
		$edit_id  = isset( $_GET['edit'] ) ? sanitize_text_field( wp_unslash( $_GET['edit'] ) ) : '';
		$edit     = $edit_id ? self::get_form( $edit_id ) : null;

		include FG_DIR . 'views/admin.php';
	}

	/**
	 * Render shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string
	 */
	public static function render_shortcode( $atts ) {
		$atts = shortcode_atts( array( 'id' => '' ), $atts, 'form_guard' );
		$form = self::get_form( $atts['id'] );
		if ( ! $form ) {
			return '<p>' . esc_html__( 'Form not found.', 'form-guard' ) . '</p>';
		}

		ob_start();
		include FG_DIR . 'views/form.php';
		return ob_get_clean();
	}

	/**
	 * AJAX form submission.
	 */
	public static function ajax_submit() {
		check_ajax_referer( 'fg_public_nonce', 'nonce' );

		$form_id = isset( $_POST['fg_form_id'] ) ? sanitize_text_field( wp_unslash( $_POST['fg_form_id'] ) ) : '';
		$form    = self::get_form( $form_id );

		if ( ! $form ) {
			wp_send_json_error( array( 'message' => __( 'Form not found.', 'form-guard' ) ) );
		}

		// Honeypot.
		if ( ! empty( $_POST['fg_website'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Spam detected.', 'form-guard' ) ) );
		}

		$data    = array();
		$errors  = array();
		$body    = '';

		foreach ( $form['fields'] as $field ) {
			$name  = $field['name'];
			$value = isset( $_POST[ 'fg_' . $name ] ) ? wp_unslash( $_POST[ 'fg_' . $name ] ) : '';

			if ( is_array( $value ) ) {
				$value = array_map( 'sanitize_text_field', $value );
				$display_value = implode( ', ', $value );
			} else {
				if ( 'email' === $field['type'] ) {
					$value = sanitize_email( $value );
				} elseif ( 'textarea' === $field['type'] ) {
					$value = sanitize_textarea_field( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
				$display_value = $value;
			}

			if ( $field['required'] && ( is_array( $value ) ? empty( $value ) : '' === trim( $value ) ) ) {
				$errors[] = sprintf( __( '%s is required.', 'form-guard' ), $field['label'] );
			}

			$data[ $field['label'] ] = $display_value;
			$body .= $field['label'] . ": " . $display_value . "\n";
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( array( 'message' => implode( ' ', $errors ) ) );
		}

		// Save entry.
		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix . FG_TABLE,
			array(
				'form_id' => $form_id,
				'data'    => wp_json_encode( $data ),
			),
			array( '%s', '%s' )
		);

		// Send email.
		$to      = $form['email'] ? $form['email'] : $settings['global_email'];
		$subject = $form['subject'] ? $form['subject'] : __( 'New Form Submission', 'form-guard' );
		$headers = array( 'Content-Type: text/plain; charset=UTF-8', 'From: ' . get_option( 'admin_email' ) );

		wp_mail( $to, $subject, $body, $headers );

		wp_send_json_success( array( 'message' => $form['success'] ? $form['success'] : __( 'Thank you! Your message has been sent.', 'form-guard' ) ) );
	}

	/**
	 * Get entries for a form.
	 *
	 * @param string $form_id Form id.
	 * @return array
	 */
	public static function get_entries( $form_id = '' ) {
		global $wpdb;
		$table = $wpdb->prefix . FG_TABLE;
		if ( $form_id ) {
			return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE form_id = %s ORDER BY id DESC", $form_id ), ARRAY_A );
		}
		return $wpdb->get_results( "SELECT * FROM {$table} ORDER BY id DESC", ARRAY_A );
	}
}
