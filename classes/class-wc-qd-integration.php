<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_QD_Integration extends WC_Integration {

	public static $api_token = null;
	public static $api_url = null;
	public static $receipts_threshold = 0;
	public static $autosend_invoices = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'quaderno';
		$this->method_title       = 'Quaderno';
		$this->method_description = __( sprintf( 'Automatically send customizable invoices and receipts with every order in your store. Comply with local rules in US, Canada, Australia, New Zealand, Singapore, and the European Union.%sNote: You need a %sQuaderno account%s for this extension to work.', '<br>', '<a href="' . WooCommerce_Quaderno::QUADERNO_URL . '/signup" target="_blank">', '</a>' ), 'woocommerce-quaderno' );

		// Load admin form
		$this->init_form_fields();

		// Load settings
		$this->init_settings();

		self::$api_token = $this->get_option( 'api_token' );
		self::$api_url  = $this->get_option( 'api_url' );
		self::$receipts_threshold  = $this->get_option( 'receipts_threshold' );
		self::$autosend_invoices  = $this->get_option( 'autosend_invoices' );

		// Hooks
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'woocommerce_update_options_integration_quaderno', array( $this, 'process_admin_options' ) );

		if ( empty( self::$api_token ) || empty( self::$api_url ) ) {
			add_action( 'admin_notices', array( $this, 'settings_notice' ) );
		}
	}
	
	/**
	 * Init integration form fields
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'api_token' => array(
				'title'       => __( 'Private key', 'woocommerce-quaderno' ),
				'description' => __( 'Get this token from your Quaderno account.', 'woocommerce-quaderno' ),
				'type'        => 'text'
			),
			'api_url'  => array(
				'title'       => __( 'API URL', 'woocommerce-quaderno' ),
				'description' => __( 'Get this URL from your Quaderno account.', 'woocommerce-quaderno' ),
				'type'        => 'text'
			),
			'receipts_threshold'  => array(
				'title'       => __( 'Receipts threshold', 'woocommerce-quaderno' ),
				'description' => __( 'All purchases under this threshold will generate a sales receipt, instead of an invoice.', 'woocommerce-quaderno' ),
				'type'        => 'text'
			),
			'autosend_invoices' => array(
				'title'       => __( 'Delivery', 'woocommerce-quaderno' ),
				'label'       => __( 'Autosend sales receipts and invoices', 'woocommerce-quaderno' ),
				'description' => __( 'Check this to automatically send your sales receipts and invoices.', 'woocommerce-quaderno' ),
				'type'        => 'checkbox'
			)
		);
	}

	/**
	 * Settings prompt
	 */
	public function settings_notice() {
		if ( ! empty( $_GET['tab'] ) && 'integration' === $_GET['tab'] ) {
			return;
		}
		?>
		<div id="message" class="updated woocommerce-message">
			<p><?php _e( '<strong>Quaderno</strong> is almost ready &#8211; Please configure your API keys to start creating automatic invoices.', 'woocommerce-quaderno' ); ?></p>

			<p class="submit"><a
					href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=integration&section=quaderno' ); ?>"
					class="button-primary"><?php _e( 'Settings', 'woocommerce-quaderno' ); ?></a></p>
		</div>
	<?php
	}
}