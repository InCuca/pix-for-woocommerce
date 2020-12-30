<?php

/**
 * Gateway class
 *
 * @package Pix_For_WooCommerce/Classes/Gateway
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Gateway.
 */
class WC_Pix_Gateway extends WC_Payment_Gateway
{

	/**
	 * Constructor for the gateway.
	 */
	public function __construct()
	{
		$this->domain             = 'woocommerce-pix';
		$this->id                 = 'pix_gateway';
		$this->icon               = apply_filters('woocommerce_offline_icon', '');
		$this->has_fields         = false;
		$this->method_title       = __('Pix', $this->domain);
		$this->method_description = __('Receba pagamentos via PIX', $this->domain);

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->title            = $this->get_option('title');
		$this->description      = $this->get_option('description');
		$this->instructions     = $this->get_option('instructions');
		$this->key     			= $this->get_option('key');
		$this->whatsapp     			= $this->get_option('whatsapp');

		//Load script files
		add_action( 'wp_enqueue_scripts', array( $this, 'wcpix_load_scripts'));

		// Actions
		add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
		add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));

		if (is_account_page()){
			add_action('woocommerce_order_details_after_order_table', array($this, 'order_page'));
		}
	}

	/**
	 * Load the script files.
	 */
	public function wcpix_load_scripts(){
		// load the main css scripts file
		wp_enqueue_style( 'wcpix-styles-css', plugins_url( '/css/styles.css', __FILE__ ) );

		// load the main js scripts file
		wp_enqueue_script( 'wcpix-main-js', plugins_url( '/js/main.js', __FILE__ ), array('jquery'));
	}

	/**
	 * Returns a bool that indicates if currency is amongst the supported ones.
	 *
	 * @return bool
	 */
	public function using_supported_currency()
	{
		return 'BRL' === get_woocommerce_currency();
	}

	/**
	 * Get WhatsApp.
	 *
	 * @return string
	 */
	public function get_whatsapp()
	{
		return $this->whatsapp;
	}

	/**
	 * Get key.
	 *
	 * @return string
	 */
	public function get_key()
	{
		return $this->key;
	}

	/**
	 * Returns a value indicating the the Gateway is available or not. It's called
	 * automatically by WooCommerce before allowing customers to use the gateway
	 * for payment.
	 *
	 * @return bool
	 */
	public function is_available()
	{
		// Test if is valid for use.
		$available = 'yes' === $this->get_option('enabled') && '' !== $this->get_whatsapp() && '' !== $this->get_key() && $this->using_supported_currency();

		return $available;
	}

	/**
	 * Get log.
	 *
	 * @return string
	 */
	protected function get_log_view()
	{
		if (defined('WC_VERSION') && version_compare(WC_VERSION, '2.2', '>=')) {
			return '<a href="' . esc_url(admin_url('admin.php?page=wc-status&tab=logs&log_file=' . esc_attr($this->id) . '-' . sanitize_file_name(wp_hash($this->id)) . '.log')) . '">' . __('System Status &gt; Logs', 'woocommerce-pix') . '</a>';
		}

		return '<code>woocommerce/logs/' . esc_attr($this->id) . '-' . sanitize_file_name(wp_hash($this->id)) . '.txt</code>';
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled'              => array(
				'title'   => __('Habilitar/Desabilitar', 'woocommerce-pix'),
				'type'    => 'checkbox',
				'label'   => __('Habilitar Pix', 'woocommerce-pix'),
				'default' => 'yes',
			),
			'title'                => array(
				'title'       => __('Título', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Representa o título visível para o usuário comprador', 'woocommerce-pix'),
				'desc_tip'    => true,
				'default'     => __('Pix', 'woocommerce-pix'),
			),
			'description'          => array(
				'title'       => __('Descrição', 'woocommerce-pix'),
				'type'        => 'textarea',
				'description' => __('Representa a descrição que o usuário verá na tela de checkout', 'woocommerce-pix'),
				'default'     => __('Ao finalizar a compra, iremos gerar o código Pix para pagamento na próxima tela e disponibilizar um número WhatsApp para você compartilhar o seu comprovante.', 'woocommerce-pix'),
			),
			'integration'          => array(
				'title'       => __('Integração', 'woocommerce-pix'),
				'type'        => 'title',
				'description' => '',
			),
			'key'                => array(
				'title'       => __('Chave Pix', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Por favor, informe sua chave PIX. Ela é necessária para poder processar os pagamentos.', 'woocommerce-pix'),
				'default'     => '',
			),
			'whatsapp'                => array(
				'title'       => __('WhatsApp para contato', 'woocommerce-pix'),
				'type'        => 'text',
				'description' => __('Seu número de WhatsApp será informado ao cliente para compartilhar o comprovante de pagamento. Modelo: 5548999999999', 'woocommerce-pix'),
				'default'     => '',
			),
			'instructions' => array(
				'title'       => __('Instruções', 'woocommerce-pix'),
				'type'        => 'textarea',
				'description' => __('Instruções na página de obrigado pela compra', 'woocommerce-pix'),
				'default'     => 'Utilize o seu aplicativo favorito do Pix para ler o QR Code ou copiar o código abaixo e efetuar o pagamento.',
			),
		);
	}

	/**
	 * Admin page.
	 */
	public function admin_options()
	{

		include dirname(__FILE__) . '/admin/views/html-admin-page.php';
	}

	/**
	 * Send email notification.
	 *
	 * @param string $subject Email subject.
	 * @param string $title   Email title.
	 * @param string $message Email message.
	 */
	protected function send_email($subject, $title, $message)
	{
		$mailer = WC()->mailer();

		$mailer->send(get_option('admin_email'), $subject, $mailer->wrap_message($title, $message));
	}

	/**
	 * Payment fields.
	 */
	public function payment_fields()
	{

		$description = $this->get_description();
		if ($description) {
			echo wpautop(wptexturize($description)); // WPCS: XSS ok.
		}
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param  int $order_id Order ID.
	 * @return array
	 */
	public function process_payment($order_id)
	{
		$order = wc_get_order($order_id);

		// Mark as on-hold (we're awaiting the payment)
		$order->update_status('on-hold', __('Awaiting offline payment', $this->domain));

		// Remove cart
		WC()->cart->empty_cart();

		// Reduce stock for billets.
		if (function_exists('wc_reduce_stock_levels')) {
			wc_reduce_stock_levels($order_id);
		}

		// Return thankyou redirect
		return array(
			'result'     => 'success',
			'redirect'    => $this->get_return_url($order)
		);
	}

	/**
	 * Render Pix code.
	 *
	 * @param int $order_id Order ID.
	 */
	public function render_pix($order_id)
	{
		$order = wc_get_order($order_id);
		if($order->payment_method != 'pix_gateway')
		{
			return;
		}

		$pix = $this->generate_pix($order_id);
		if ($this->instructions) {
			echo wpautop(wptexturize($this->instructions));
		}
		if (!empty($pix)) {
			?>
			<div class="wcpix-container">
				<input type="hidden" value="<?php echo $pix['link']; ?>" id="copiar">
				<img  style="cursor:pointer; display: initial;" class="wcpix-img-copy-code" onclick="copyCode()" src="<?php echo $pix['image']; ?>" alt="QR Code" />
				<br><button class="button wcpix-button-copy-code" onclick="copyCode()"><?php echo __('Clique aqui para copiar o Código', 'woocommerce-pix'); ?> </button><br>
				<div class="wcpix-response-output inactive" aria-hidden="true" style=""><?php echo __('O código foi copiado para a área de transferência.', 'woocommerce-pix'); ?></div>
			</div>
			<?php
			if ($this->whatsapp) {
				echo '<br>' . __('Você pode compartilhar conosco o comprovante via WhatsApp.', 'woocommerce-pix') .' <a target="_blank" href=" https://wa.me/'.$this->whatsapp.'?text=Segue%20meu%20comprovante">clicando aqui.</a>';
			}
		}
	}
	/**
	 * Order Page message.
	 *
	 * @param int $order_id Order ID.
	 */
	public function order_page($order_id)
	{
		return $this->render_pix($order_id);
	}

	/**
	 * Thank You page message.
	 *
	 * @param int $order_id Order ID.
	 */
	public function thankyou_page($order_id)
	{
		return $this->render_pix($order_id);
	}

	public function generate_pix($order_id)
	{
		$order = wc_get_order($order_id);
		$pix = new ICPFW_QRCode();
		$pix->chave($this->key);
		$pix->valor($order->total);
		$pix->cidade($order->city);
		$pix->pais($order->country);
		$pix->moeda(986); // Real brasileiro (BRL) - Conforme ISO 4217: https://pt.wikipedia.org/wiki/ISO_4217
		$pix->txId($order->order_key);
		$link = $pix->toCode();
		$image = $pix->toImage();
		$pix = array(
			'image' => $image,
			'link' => $link,
		);
		return $pix;
	}
}
