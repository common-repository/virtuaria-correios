<?php
/**
 * Correios Shipping Method.
 *
 * @package virtuaria.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Virtuaria_Correios_Shipping' ) ) {
	/**
	 * Class definition.
	 */
	abstract class Virtuaria_Correios_Shipping extends WC_Shipping_Method {
		/**
		 * Handle log enable.
		 *
		 * @var WC_logger
		 */
		protected $log;

		/**
		 * Service code.
		 *
		 * @var int
		 */
		protected $code;

		/**
		 * CEP origin.
		 *
		 * @var string
		 */
		protected $origin;

		/**
		 * Username.
		 *
		 * @var string
		 */
		protected $post_card;

		/**
		 * Username.
		 *
		 * @var string
		 */
		protected $password;

		/**
		 * Service cod.
		 *
		 * @var string
		 */
		protected $service_cod;

		/**
		 * Enviroment.
		 *
		 * @var string
		 */
		protected $enviroment;

		/**
		 * Extra time.
		 *
		 * @var string
		 */
		protected $additional_time;

		/**
		 * Receipt notice service.
		 *
		 * @var string
		 */
		protected $receipt_notice;

		/**
		 * Own hands service.
		 *
		 * @var string
		 */
		protected $own_hands;

		/**
		 * Min height.
		 *
		 * @var string
		 */
		protected $minimum_height;

		/**
		 * Min width.
		 *
		 * @var string
		 */
		protected $minimum_width;

		/**
		 * Min length.
		 *
		 * @var string
		 */
		protected $minimum_length;

		/**
		 * Min weight.
		 *
		 * @var string
		 */
		protected $minimum_weight;

		/**
		 * Extra weight.
		 *
		 * @var string
		 */
		protected $extra_weight;

		/**
		 * Declare value.
		 *
		 * @var string
		 */
		protected $declare_value;

		/**
		 * Username.
		 *
		 * @var string
		 */
		protected $username;

		/**
		 * API.
		 *
		 * @var Virtuaria_Correios_API
		 */
		protected $api;

		/**
		 * Global settings.
		 *
		 * @var array
		 */
		protected $correios_settings;

		/**
		 * Constructor for shipping class
		 *
		 * @param int $instance_id Shipping zone instance ID.
		 * @access public
		 * @return void
		 */
		public function __construct( $instance_id = 0 ) {
			$this->instance_id        = absint( $instance_id );
			$this->method_description = __(
				'Permite enviar suas mercadorias através da transportadora correios.',
				'virtuaria-correios'
			);
			$this->supports           = array(
				'shipping-zones',
				'instance-settings',
			);

			$this->correios_settings = Virtuaria_WPMU_Correios_Settings::get_settings();
			// Load the form fields.
			$this->init_form_fields();

			// Define user set variables.
			$this->enabled = $this->get_option( 'enabled' );
			$this->title   = $this->get_option( 'title' );

			$this->origin          = $this->get_option( 'origin' );
			$this->username        = $this->get_setting_value( 'username' );
			$this->password        = $this->get_setting_value( 'password' );
			$this->post_card       = $this->get_setting_value( 'post_card' );
			$this->enviroment      = $this->get_setting_value( 'enviroment', 'production' );
			$this->service_cod     = $this->get_option( 'service_cod' );
			$this->additional_time = $this->get_option( 'additional_time', 0 );
			$this->fee             = $this->get_option( 'fee', 0 );
			$this->receipt_notice  = $this->get_option( 'receipt_notice' );
			$this->own_hands       = $this->get_option( 'own_hands' );
			$this->declare_value   = $this->get_option( 'declare_value' );
			$this->minimum_height  = $this->get_option( 'minimum_height' );
			$this->minimum_width   = $this->get_option( 'minimum_width' );
			$this->minimum_length  = $this->get_option( 'minimum_length' );
			$this->extra_weight    = $this->get_option( 'extra_weight' );
			$this->minimum_weight  = $this->get_option( 'minimum_weight' );

			// Active logs.
			if ( 'yes' === $this->get_setting_value( 'debug' ) ) {
				$this->log = new WC_Logger();
			}

			$this->api = new Virtuaria_Correios_API(
				isset( $this->log ) ? $this->log : null,
				$this->enviroment
				// 'sandbox'
			);

			add_action(
				'woocommerce_update_options_shipping_' . $this->id,
				array( $this, 'process_admin_options' )
			);
		}

		/**
		 * Define settings field for this shipping.
		 *
		 * @return void
		 */
		public function init_form_fields() {
			$method_settings = get_option( 'woocommerce_' . $this->id . '_' . $this->instance_id . '_settings' );

			$correios_fields = array(
				'enabled'            => array(
					'title'       => __( 'Habilitar', 'virtuaria-correios' ),
					'type'        => 'checkbox',
					'description' => __( 'Habilita a entrega com Correios.', 'virtuaria-correios' ),
					'default'     => 'yes',
					'desc_tip'    => true,
				),
				'title'              => array(
					'title'       => __( 'Título', 'virtuaria-correios' ),
					'type'        => 'text',
					'description' => __( 'Título que será exibido na loja.', 'virtuaria-correios' ),
					'default'     => 'Correios',
					'desc_tip'    => true,
				),
				'enviroment_options' => array(
					'title'   => __( 'Comportamento', 'virtuaria-correios' ),
					'type'    => 'title',
					'default' => '',
				),
				'origin'             => array(
					'title'       => __( 'CEP de Origem', 'virtuaria-correios' ),
					'type'        => 'text',
					'description' => __( 'CEP de origem da Mercadoria. Utilizado no cálculo do frete.', 'virtuaria-correios' ),
					'default'     => '',
					'desc_tip'    => true,
				),
				'service_cod'        => array(
					'title'       => __( 'Código do Serviço', 'virtuaria-correios' ),
					'type'        => 'cod_service',
					'description' => __( 'Código que identifica o serviço nos correios, seguem os códigos mais comuns: 03220 - SEDEX CONTRATO AG e 03298 - PAC CONTRATO AG. Lembrando que cada contrato pode ter um código específico, por isso, recomendamos que faça contato com os Correios para confirmar.', 'virtuaria-correios' ),
					'default'     => $this->code,
				),
				'additional_time'    => array(
					'title'       => __( 'Dias Adicionais', 'virtuaria-correios' ),
					'type'        => 'text',
					'description' => __( 'Acrescenta tempo extra a estimativa de entrega fornecida pelos correios.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'default'     => '0',
					'placeholder' => '0',
				),
				'fee'                => array(
					'title'       => __( 'Taxa de Manuseio', 'virtuaria-correios' ),
					'type'        => 'price',
					'description' => __( 'Informe valor extra (R$) para ser acrescido ao preço da entrega. Deixe em branco para desativar.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'placeholder' => '0.00',
					'default'     => '0',
				),
				'shipping_custom'    => array(
					'title'   => __( 'Ajustes de Frete', 'virtuaria-correios' ),
					'type'    => 'title',
					'default' => '',
				),
				'hide_delivery_time' => array(
					'title'       => __( 'Ocultar previsão de entrega?', 'virtuaria-correios' ),
					'label'       => __( 'Marque para não exibir a previsão de entrega.', 'virtuaria-correios' ),
					'type'        => 'checkbox',
					'description' => __( 'Controla a exibição da estimativa de tempo de entrega.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'default'     => 'no',
				),
				'object_type'        => array(
					'title'       => __( 'Tipo do Objeto', 'virtuaria-correios' ),
					'label'       => __( 'Define o tipo de objeto que será postado, permitindo o uso de serviços que requerem um tipo especial de objeto. Ex: Impresso Normal requer objetos do tipo "Envelope". Observação: Para o tipo Envelope, as dimensões são desconsideradas no cálculo do frete.', 'virtuaria-correios' ),
					'type'        => 'select',
					'description' => __( 'Define o tipo de objeto que será postado, permitindo o uso de serviços que requerem um tipo especial de objeto. Ex: Impresso Normal requer objetos do tipo "Envelope". Observação: Para o tipo Envelope, as dimensões são desconsideradas no cálculo do frete.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select',
					'default'     => '2',
					'options'     => array(
						'1' => __( 'Envelope', 'virtuaria-correios' ),
						'2' => __( 'Pacote', 'virtuaria-correios' ),
					),
				),
				'discounts'          => array(
					'title'       => __( 'Valor Mínimo para Desconto no Frete', 'virtuaria-correios' ),
					'label'       => __( 'Define valor mínimo e percentual de desconto a ser aplicado no frete.', 'virtuaria-correios' ),
					'type'        => 'discount',
					'description' => __( 'Define valor mínimo e percentual de desconto a ser aplicado no frete. <b>Atenção: </b> Os descontos não serão aplicados quando a estimativa de frete for alterada por "Ajustar o valor do frete com base nas categorias de produtos".', 'virtuaria-correios' ),
					'desc_tip'    => false,
				),
				'optional_services'  => array(
					'title'       => __( 'Serviços Opcionais', 'virtuaria-correios' ),
					'type'        => 'title',
					'description' => __( 'Torne sua integração mais completo com estes serviços dos Correios. Alguns serviços possuem limitações, recomandamos consultar a <a href="https://www.correios.com.br/enviar/servicos-adicionais" target="_blank">documentação</a> dos Correios para mais informações.', 'virtuaria-correios' ),
					'default'     => '',
				),
				'receipt_notice'     => array(
					'title'       => __( 'Aviso de Recebimento', 'virtuaria-correios' ),
					'type'        => 'checkbox',
					'label'       => __( 'Habilitar', 'virtuaria-correios' ),
					'description' => __( 'Isso controla se os custos do serviço de Aviso de Recebimento devem ser adicionados.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'default'     => 'no',
				),
				'own_hands'          => array(
					'title'       => __( 'Mãos Próprias', 'virtuaria-correios' ),
					'type'        => 'checkbox',
					'label'       => __( 'Habilitar mãos próprias', 'virtuaria-correios' ),
					'description' => __( 'Isso controla se os custos do serviço Mãos Próprias devem ser adicionados ao valor do frete.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'default'     => 'no',
				),
				'declare_value'      => array(
					'title'       => __( 'Declarar Valor para Seguro', 'virtuaria-correios' ),
					'type'        => 'select',
					'label'       => __( 'Habilitar valor declarado', 'virtuaria-correios' ),
					'description' => __( 'Isto controla se o preço do pacote deve ser declarado para fins de Seguro.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select',
					'default'     => '',
					'options'     => array(
						''    => __( 'Não declarar declare', 'virtuaria-correios' ),
						'019' => __( '(019) Valor Declarado Nacional Premium e Expresso (use para SEDEX)', 'virtuaria-correios' ),
						'064' => __( '(064) Valor Declarado Nacional Standard (use para PAC)', 'virtuaria-correios' ),
						'065' => __( '(065) Valor Declarado Correios Mini Envios (use para SEDEX Mini)', 'virtuaria-correios' ),
						'075' => __( '(075) Valor Declarado Expresso RFID (SEDEX)', 'virtuaria-correios' ),
						'076' => __( '(076) Valor Declarado Standard RFID (PAC)', 'virtuaria-correios' ),
					),
				),
				'register_type'      => array(
					'title'       => __( 'Tipo de Registro', 'virtuaria-correios' ),
					'type'        => 'select',
					'description' => __( 'Controla o tipo de impresso a ser enviado pelos Correios. Válido somente para o impresso ( 20117 ).', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'class'       => 'wc-enhanced-select',
					'default'     => '004',
					'options'     => array(
						'004' => __( '(004) Módico a Faturar', 'virtuaria-correios' ),
						'025' => __( '(025) Nacional', 'virtuaria-correios' ),
					),
				),
				'min_value_declared' => array(
					'title'       => __( 'Valor Mínimo para Seguro', 'virtuaria-correios' ),
					'type'        => 'price',
					'description' => __( 'Deixe em branco para aplicar a qualquer valor. Os Correios não permitem aplicar o seguro a valores muito baixos. Para mais informações, consulte os Correios.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'default'     => '27',
					'placeholder' => '0.00',
				),
				'dimensions_section' => array(
					'title'   => __( 'Ajustes de Dimensões', 'virtuaria-correios' ),
					'type'    => 'title',
					'default' => '',
				),
				'dimensions_type'    => array(
					'title'       => __( 'Tipo de Dimensões', 'virtuaria-correios' ),
					'type'        => 'select',
					'description' => __( 'Selecione o tipo de dimensões para os pacotes de envio.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'options'     => array(
						'package' => __( 'Por pedido', 'virtuaria-correios' ),
						'product' => __( 'Por produto', 'virtuaria-correios' ),
					),
					'default'     => ( ! isset( $method_settings['origin'] ) && ! isset( $method_settings['dimensions_type'] ) )
						? 'package'
						: 'product',
				),
				'minimum_height'     => array(
					'title'       => __( 'Altura Mínima (cm)', 'virtuaria-correios' ),
					'type'        => 'number',
					'description' => __( 'Altura mínima de seus pacotes de remessa. Os Correios precisam de no mínimo 2cm.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'default'     => '2',
				),
				'minimum_width'      => array(
					'title'       => __( 'Largura Mínima (cm)', 'virtuaria-correios' ),
					'type'        => 'number',
					'description' => __( 'Largura mínima de seus pacotes de remessa. Os Correios precisam de no mínimo 11cm.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'default'     => '11',
				),
				'minimum_length'     => array(
					'title'       => __( 'Comprimento Mínimo Length (cm)', 'virtuaria-correios' ),
					'type'        => 'number',
					'description' => __( 'Comprimento mínimo de seus pacotes de remessa. Os Correios precisam de no mínimo 16cm.', 'virtuaria-correios' ),
					'desc_tip'    => true,
					'default'     => '16',
				),
				'weight_section'     => array(
					'title'       => __( 'Ajustes no Peso', 'virtuaria-correios' ),
					'type'        => 'title',
					'description' => __( 'Medida mínima para o peso de seus pacotes.', 'virtuaria-correios' ),
					'default'     => '',
				),
				'minimum_weight'     => array(
					'title'             => __( 'Peso Mínimo (kg)', 'virtuaria-correios' ),
					'type'              => 'number',
					'description'       => __( 'Peso mínimo em quilogramas para os produtos de seu pacote de remessa. Aceita fração, exemplo 0,5 para indicar meio Kg.', 'virtuaria-correios' ),
					'desc_tip'          => true,
					'default'           => '0.050',
					'custom_attributes' => array(
						'step' => '0.01',
					),
				),
				'extra_weight'       => array(
					'title'             => __( 'Peso Extra (kg)', 'virtuaria-correios' ),
					'type'              => 'extra_weight',
					'description'       => __( 'Acrescenta um valor extra ao peso calculado, referente a embalagem do produto ou pedido.', 'virtuaria-correios' ),
					'desc_tip'          => true,
					'default'           => '0',
					'custom_attributes' => array(
						'step' => '0.01',
					),
				),
			);

			$ideal_position = 8;
			if ( isset( $this->correios_settings['serial'], $this->correios_settings['category_price'], $this->correios_settings['authenticated'] )
				&& $this->correios_settings['serial']
				&& 'yes' === $this->correios_settings['category_price']
				&& $this->correios_settings['authenticated'] ) {
				$correios_fields = array_slice( $correios_fields, 0, $ideal_position )
				+ array(
					'cond_special' => array(
						'title'       => __( 'Ajustar o valor do frete com base nas categorias de produtos', 'virtuaria-correios' ),
						'type'        => 'cond_special',
						'description' => __( 'Permite ajustar (R$) o valor do frete exibido aos clientes com base nas categorias dos produtos no carrinho. Por exemplo, você pode acrescentar um valor no frete para produtos frágeis, pois terá que embalar de forma especial. O ajuste também pode ser um desconto, sendo que nunca será exibido para o cliente um frete menor que zero.', 'virtuaria-correios' ),
						'desc_tip'    => true,
						'default'     => '',
					),
				) + array_slice( $correios_fields, $ideal_position );
			}

			$this->instance_form_fields = $correios_fields;
		}

		/**
		 * Calculate_shipping function.
		 *
		 * @access public
		 * @param array $package the packacge.
		 * @return void
		 */
		public function calculate_shipping( $package = array() ) {
			// if ( ! isset( $_REQUEST['woocommerce-shipping-calculator-nonce'] )
			// 	&& (
			// 		! isset( $_REQUEST['wc-ajax'] )
			// 		|| 'update_order_review' !== $_REQUEST['wc-ajax']
			// 	)
			// 	&& ! isset( $package['is_product_page'] )
			// 	&& (
			// 		! isset( $this->correios_settings['optimize_add_cart'] )
			// 		|| 'yes' !== $this->correios_settings['optimize_add_cart']
			// 	)
			// ) {
			// 	return;
			// }

			// Check if valid to be calculeted.
			if ( '' === $package['destination']['postcode']
				|| 'BR' !== $package['destination']['country']
				|| 8 > strlen( $package['destination']['postcode'] ) ) {
				return;
			}

			$dimensions = $this->get_package_dimensions( $package );

			if ( ! isset( $dimensions['length'] )
				|| ! isset( $dimensions['width'] )
				|| ! isset( $dimensions['height'] )
				|| ! isset( $dimensions['weight'] ) ) {
				if ( isset( $this->log ) ) {
					$this->log->add(
						'virtuaria-correios',
						'Dimensões do pacote ausentes ao calcular frete. ' . wp_json_encode( $dimensions ),
						WC_Log_Levels::ERROR
					);
				}
				return;
			}

			$contract = get_transient( 'virtuaria_correios_contract' );
			$contract = $contract ? $contract : get_option( 'virtuaria_correios_contract' );

			if ( isset( $this->correios_settings['global'] )
				&& $this->correios_settings['global']
				&& is_multisite() ) {
				switch_to_blog( get_main_site_id() );
				$contract = get_option( 'virtuaria_correios_contract' );
				restore_current_blog();
			}

			if ( ! isset( $this->correios_settings['easy_mode'] )
				&& ( ! isset( $contract['cartaoPostagem']['dr'] )
				|| ! isset( $contract['cartaoPostagem']['contrato'] ) ) ) {
				if ( isset( $this->log ) ) {
					$this->log->add(
						'virtuaria-correios',
						'Informações do cartão de postagem ausentes ao calcular frete.',
						WC_Log_Levels::ERROR
					);
				}
				return;
			}

			if ( ! isset( $package['contents_cost'] )
				|| ! $package['contents_cost']
				&& isset( WC()->cart ) ) {
				$package['contents_cost'] = WC()->cart->get_cart_contents_total();
			}

			$data = array(
				'username'    => $this->username,
				'password'    => $this->password,
				'post_card'   => $this->post_card,
				'origin'      => isset( $package['origin_cep'] )
					? preg_replace( '/\D/', '', $package['origin_cep'] )
					: preg_replace( '/\D/', '', $this->origin ),
				'destination' => preg_replace( '/\D/', '', $package['destination']['postcode'] ),
				'height'      => round( $dimensions['height'] ),
				'width'       => round( $dimensions['width'] ),
				'length'      => round( $dimensions['length'] ),
				'weight'      => round( $dimensions['weight'] ),
				'service'     => $this->service_cod,
				'nuContrato'  => isset( $contract['cartaoPostagem']['contrato'] )
					? $contract['cartaoPostagem']['contrato']
					: '',
				'nuDR'        => isset( $contract['cartaoPostagem']['dr'] )
					? $contract['cartaoPostagem']['dr']
					: '',
				'product'     => $this->title,
				'show_errors' => isset( $this->correios_settings['error_message'] )
					? true
					: false,
				'object_type' => $this->get_option( 'object_type', '2' ),
			);

			if ( isset( $this->correios_settings['easy_mode'] ) ) {
				$data['easy_mode'] = 'yes';
			}

			$min_declared_value = floatval( $this->get_option( 'min_value_declared' ) );
			if ( '' !== $this->declare_value
				&& ( ! $min_declared_value
				|| $min_declared_value <= $package['contents_cost'] ) ) {
				$data['vlDeclarado']          = $package['contents_cost'];
				$data['servicosAdicionais'][] = $this->declare_value;
			}

			if ( 'yes' === $this->receipt_notice ) {
				$data['servicosAdicionais'][] = '001';
			}

			if ( 'yes' === $this->own_hands ) {
				$data['servicosAdicionais'][] = '002';
			}

			if ( in_array( $this->service_cod, array( '03328', '03212' ), true )
				/*&& isset( $this->correios_settings['serial'], $this->correios_settings['authenticated'] )
				&& $this->correios_settings['serial']
				&& $this->correios_settings['authenticated']*/ ) {
				$data['servicosAdicionais'][] = '057';
			}

			if ( '20117' === $this->service_cod ) {
				$data['servicosAdicionais'][] = $this->get_option( 'register_type', '004' );
			}

			$estimate = $this->api->get_shipping_cost( $data );

			if ( $estimate ) {
				$delivery_time = $this->api->get_shipping_deadline( $data );

				$estimate = str_replace(
					array( ',', '.' ),
					'',
					$estimate
				);

				$title = $this->title;

				$cond_special = json_decode(
					$this->get_option( 'cond_special' ),
					true
				);

				$applied_cond_special = false;
				if ( $cond_special
					&& $estimate
					&& isset( $this->correios_settings['serial'], $this->correios_settings['category_price'], $this->correios_settings['authenticated'] )
					&& $this->correios_settings['serial']
					&& 'yes' === $this->correios_settings['category_price']
					&& $this->correios_settings['authenticated'] ) {
					foreach ( $cond_special as $key => $cond ) {
						if ( $this->cond_match( $cond, $package ) ) {
							switch ( $cond['condition'] ) {
								case 'increase':
									$estimate            += floatval(
										$cond['condition_value']
									) * 100;
									$applied_cond_special = true;
									break;
								case 'fix':
									$estimate             = floatval(
										$cond['condition_value']
									) * 100;
									$applied_cond_special = true;
									break;
								case 'decrease':
									$estimate            -= floatval(
										$cond['condition_value']
									) * 100;
									$applied_cond_special = true;
									break;
							}
							break;
						}
					}

					if ( $estimate <= 0 ) {
						$estimate = 0;
						$title   .= __( ' (Grátis)', 'virtuaria-correios' );
					}
				}

				$discounts = $this->get_option( 'discounts' );
				if ( $discounts
					&& ! $applied_cond_special
					&& $estimate > 0 ) {
					$discounts = json_decode( $discounts, true );
					usort(
						$discounts,
						function ( $a, $b ) {
							if ( $a['min_value'] == $b['min_value'] ) {
								return 0;
							}
							return $a['min_value'] < $b['min_value']
								? 1
								: -1;
						}
					);

					foreach ( $discounts as $discount ) {
						if ( floatval( $discount['min_value'] ) > 0
							&& floatval( $package['contents_cost'] ) >= $discount['min_value'] ) {
							$discount_value = 0;
							if ( isset( $discount['fixed_value'] )
								&& floatval( $discount['fixed_value'] ) > 0 ) {
								$discount_value = floatval(
									str_replace(
										',',
										'.',
										$discount['fixed_value']
									)
								) * 100; // Convert to interger.
							} elseif ( intval( $discount['percent'] ) > 0 ) {
								$discount_value = round( $estimate * ( $discount['percent'] / 100 ) );
							}

							if ( $discount_value > 0 ) {
								$estimate -= $discount_value;
								if ( $estimate <= 0 ) {
									if ( ! isset( $this->correios_settings['hide_free_shipping_notice'] )
										|| 'yes' !== $this->correios_settings['hide_free_shipping_notice'] ) {
										wc_add_notice( "<b>$title:</b>&nbsp; Parabéns! Você recebeu frete grátis." );
									}
								} else {
									$discount_applied = isset( $discount['fixed_value'] ) && floatval( $discount['fixed_value'] ) > 0
										? 'R$ ' . number_format( $discount['fixed_value'], 2, ',', '.' )
										: $discount['percent'] . '%';
									wc_add_notice( "<b>$title:</b>&nbsp; Você recebeu desconto de $discount_applied no frete." );
								}
								break;
							}
						}
					}
				}

				if ( $estimate <= 0 ) {
					$estimate = 0;
					$title   .= __( ' (Grátis)', 'virtuaria-correios' );
				}

				$meta_delivery = array();
				if ( $delivery_time && 'yes' !== $this->get_option( 'hide_delivery_time', 'no' ) ) {
					$meta_delivery = array(
						'_delivery_time' => ( $delivery_time + $this->additional_time ),
					);
				}

				$rate = apply_filters(
					$this->id . '_rate',
					array(
						'id'        => $this->id . ':' . $this->instance_id,
						'label'     => $title,
						'cost'      => ( $estimate / 100 ) + ( $this->fee > 0 ? floatval( $this->fee ) : 0 ),
						'package'   => $package,
						'meta_data' => $meta_delivery,
					),
					$this->instance_id,
					$package
				);

				// Add rate to Virtuaria.
				$this->add_rate( $rate );

				$method_info = WC()->session->get( 'virtuaria_correios_methods', array() );

				$method_info[ $this->service_cod ] = array(
					'title'       => $this->title,
					'dimensions'  => wp_json_encode( $dimensions ),
					'total'       => ( $estimate / 100 ) + ( $this->fee > 0 ? floatval( $this->fee ) : 0 ),
					'services'    => isset( $data['servicosAdicionais'] )
						? wp_json_encode( $data['servicosAdicionais'] )
						: array(),
					'service_cod' => $this->service_cod,
				);

				WC()->session->set(
					'virtuaria_correios_methods',
					$method_info
				);
			}
		}

		/**
		 * Get package weight.
		 *
		 * @param  array $package Shipping package.
		 *
		 * @return float|bool
		 */
		public function get_package_dimensions( $package ) {
			$count  = 0;
			$weight = array();
			$height = array();
			$width  = array();
			$length = array();

			$extra_weight = json_decode(
				$this->extra_weight,
				true
			);

			foreach ( $package['contents'] as $value ) {
				$product = $value['data'];
				$qty     = $value['quantity'];

				if ( $qty > 0 && $product->needs_shipping() ) {
					$product_weight = max(
						wc_get_weight(
							(float) $product->get_weight(),
							'kg'
						),
						floatval(
							str_replace(
								',',
								'.',
								$this->minimum_weight
							)
						)
					);

					if ( ( ! isset( $extra_weight['type'] )
						|| 'order' !== $extra_weight['type'] )
						&& isset( $extra_weight['weight'] ) ) {
						$product_weight += floatval(
							str_replace(
								',',
								'.',
								$extra_weight['weight']
							)
						);
					}

					$product_height = wc_get_dimension(
						(float) $product->get_height(),
						'cm'
					);
					$product_width  = wc_get_dimension(
						(float) $product->get_width(),
						'cm'
					);
					$product_length = wc_get_dimension(
						(float) $product->get_length(),
						'cm'
					);

					if ( 'product' === $this->get_option( 'dimensions_type' ) ) {
						$product_height = max(
							$product_height,
							$this->minimum_height
						);
						$product_width  = max(
							$product_width,
							$this->minimum_width
						);
						$product_length = max(
							$product_length,
							$this->minimum_length
						);
					}

					$height[ $count ] = $product_height;
					$width[ $count ]  = $product_width;
					$length[ $count ] = $product_length;
					$weight[ $count ] = $product_weight;

					if ( $qty > 1 ) {
						$n = $count;
						for ( $i = 0; $i < $qty; $i++ ) {
							$height[ $n ] = $product_height;
							$width[ $n ]  = $product_width;
							$length[ $n ] = $product_length;
							$weight[ $n ] = $product_weight;
							++$n;
						}
						$count = $n;
					}

					++$count;
				}
			}

			if ( empty( $height )
				|| empty( $width )
				|| empty( $length ) ) {
				return array();
			}

			// Verifica se existe mais um produto no carrinho.
			if ( $count > 1 ) {
				$cubage = $this->get_optimized_cubage(
					array_values( $height ),
					array_values( $width ),
					array_values( $length ),
				);
			} elseif ( 1 === $count ) {
				$cubage = array(
					'height' => $height[0],
					'width'  => $width[0],
					'length' => $length[0],
					'weight' => $weight[0],
				);
			}

			if ( 'package' === $this->get_option( 'dimensions_type' ) ) {
				$cub_height = max(
					isset( $cubage['height'] )
						? $cubage['height']
						: 0,
					$this->minimum_height
				);
				$cub_width  = max(
					isset( $cubage['width'] )
						? $cubage['width']
						: 0,
					$this->minimum_width
				);
				$cub_length = max(
					isset( $cubage['length'] )
						? $cubage['length']
						: 0,
					$this->minimum_length
				);

				$cubage = array(
					'height' => $cub_height,
					'width'  => $cub_width,
					'length' => $cub_length,
				);
			}

			if ( $cubage ) {
				$cubage['weight'] = array_sum( $weight ) * 1000; // Convert kg to g.
				$cubage['height'] = number_format( $cubage['height'], 1 );
				$cubage['width']  = number_format( $cubage['width'], 1 );
				$cubage['length'] = number_format( $cubage['length'], 1 );

				if ( isset( $extra_weight['type'] )
					&& 'order' === $extra_weight['type'] ) {
					$cubage['weight'] += floatval(
						str_replace(
							',',
							'.',
							$extra_weight['weight']
						)
					) * 1000; // Convert kg to g.
				}

				return $cubage;
			}

			return array();
		}


		/**
		 * Calculates the cubage of all products.
		 *
		 * @param  array $height Package height.
		 * @param  array $width  Package width.
		 * @param  array $length Package length.
		 *
		 * @return int
		 */
		protected function cubage_total( $height, $width, $length ) {
			// Sets the cubage of all products.
			$total       = 0;
			$total_items = count( $height );

			for ( $i = 0; $i < $total_items; $i++ ) {
				$total += $height[ $i ] * $width[ $i ] * $length[ $i ];
			}

			return $total;
		}

		/**
		 * Get the max values.
		 *
		 * @param  array $height Package height.
		 * @param  array $width  Package width.
		 * @param  array $length Package length.
		 *
		 * @return array
		 */
		protected function get_max_values( $height, $width, $length ) {
			$find = array(
				'height' => $height ? max( $height ) : 0,
				'width'  => $width ? max( $width ) : 0,
				'length' => $length ? max( $length ) : 0,
			);

			return $find;
		}

		/**
		 * Calculates the square root of the scaling of all products.
		 *
		 * @param  array $height     Package height.
		 * @param  array $width      Package width.
		 * @param  array $length     Package length.
		 * @param  array $max_values Package bigger values.
		 *
		 * @return float
		 */
		protected function calculate_root( $height, $width, $length, $max_values ) {
			$cubage_total = $this->cubage_total( $height, $width, $length );
			$root         = 0;
			$biggest      = max( $max_values );

			if ( 0 !== $cubage_total && 0 < $biggest ) {
				// Dividing the value of scaling of all products.
				// With the measured value of greater.
				$division = $cubage_total / $biggest;
				// Total square root.
				$root = round( sqrt( $division ), 1 );
			}

			return $root;
		}

		/**
		 * Sets the final cubage.
		 *
		 * @param array $height Package height.
		 * @param array $width  Package width.
		 * @param array $length Package length.
		 *
		 * @return array
		 */
		protected function get_cubage( $height, $width, $length ) {
			$cubage     = array();
			$max_values = $this->get_max_values( $height, $width, $length );
			$root       = $this->calculate_root( $height, $width, $length, $max_values );
			$greatest   = array_search( max( $max_values ), $max_values, true );

			switch ( $greatest ) {
				case 'height':
					$cubage = array(
						'height' => max( $height ),
						'width'  => $root,
						'length' => $root,
					);
					break;
				case 'width':
					$cubage = array(
						'height' => $root,
						'width'  => max( $width ),
						'length' => $root,
					);
					break;
				case 'length':
					$cubage = array(
						'height' => $root,
						'width'  => $root,
						'length' => max( $length ),
					);
					break;

				default:
					$cubage = array(
						'height' => 0,
						'width'  => 0,
						'length' => 0,
					);
					break;
			}

			return $cubage;
		}

		/**
		 * Get a list of Brazilian states and their abbreviations.
		 *
		 * @return array List of state abbreviations and their corresponding names
		 */
		private function get_states() {
			return array(
				'AC' => 'Acre',
				'AL' => 'Alagoas',
				'AP' => 'Amapa',
				'AM' => 'Amazonas',
				'BA' => 'Bahia',
				'CE' => 'Ceara',
				'DF' => 'Distrito Federal',
				'ES' => 'Espirito Santo',
				'GO' => 'Goias',
				'MA' => 'Maranhao',
				'MT' => 'Mato Grosso',
				'MS' => 'Mato Grosso do Sul',
				'MG' => 'Minas Gerais',
				'PA' => 'Pará',
				'PB' => 'Paraiba',
				'PR' => 'Paraná',
				'PE' => 'Pernambuco',
				'PI' => 'Piaui',
				'RJ' => 'Rio de Janeiro',
				'RN' => 'Rio Grande do Norte',
				'RS' => 'Rio Grande do Sul',
				'RO' => 'Rondônia',
				'RR' => 'Roraima',
				'SC' => 'Santa Catarina',
				'SP' => 'São Paulo',
				'SE' => 'Sergipe',
				'TO' => 'Tocantins',
			);
		}

		/**
		 * Temp get value.
		 *
		 * @param string $key the key.
		 */
		public function __get( $key ) {
			return isset( $this->$key ) ? $this->$key : null;
		}


		/**
		 * Get a setting value by key.
		 *
		 * @param string $key setting key.
		 * @return string|null
		 */
		public function get_setting( $key ) {
			$option = isset( $this->correios_settings[ $key ] )
				? $this->correios_settings[ $key ]
				: '';

			return $option;
		}

		/**
		 * Get a setting value by key.
		 *
		 * @param string $key setting key.
		 * @param string $default default value.
		 * @return string|null
		 */
		private function get_setting_value( $key, $default = '' ) {
			return isset( $this->correios_settings[ $key ] )
				? $this->correios_settings[ $key ]
				: $default;
		}

		/**
		 * Generate HTML for a special condition based on key and data.
		 *
		 * @param string $key  title from field.
		 * @param array  $data field args.
		 * @return Some_Return_Value
		 */
		public function generate_cond_special_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );

			$option = json_decode( $this->get_option( $key ), true );

			$has_value = false;
			if ( ! $option ) {
				$option[] = array(
					'condition'       => 'increase',
					'condition_value' => '',
					'category'        => 0,
				);
			} else {
				$has_value = true;
			}

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>">
						<?php echo esc_html( $data['title'] ); ?>
					</label>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( $data['type'] ); ?>">
					<input type="hidden" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" />

					<?php
					$size = count( $option );
					foreach ( $option as $key => $value ) :
						?>
						<div class="<?php echo $has_value ? 'original' : ''; ?>">
							<select name="condition" class="special-combo condition" <?php echo ! $has_value ? 'disabled' : ''; ?>>
								<option value="increase" <?php selected( 'increase', $value['condition'] ); ?>>Aumentar</option>
								<option value="fix" <?php selected( 'fix', $value['condition'] ); ?>>Fixar</option>
								<option value="decrease" <?php selected( 'decrease', $value['condition'] ); ?>>Reduzir</option>
							</select>
							<input
								type="number"
								name="condition_value"
								min="0"
								<?php echo ! $has_value ? 'disabled' : ''; ?>
								class="special-combo cost"
								placeholder="Valor"
								value="<?php echo esc_attr( $value['condition_value'] ); ?>"
								step="0.01" />
							<div class="tabs-panel special-combo dropdown">
								<ul data-wp-lists="list:product_cat" class="categorychecklist form-no-clear">
									<?php
									wp_dropdown_categories(
										array(
											'taxonomy'      => 'product_cat',
											'hide_if_empty' => true,
											'hierarchical'  => 1,
											'orderby'       => 'title',
											'selected'      => $value['category'],
										)
									);
									?>
								</ul>
							</div>
							<button class="remove-condition button button-primary" <?php echo ! $has_value ? 'disabled' : ''; ?>>-</button>
							<button class="new-condition button button-primary">+</button>
						</div>
						<?php
					endforeach;
					echo '<small style="display:block;margin-top:20px" class="desc">'
						. wp_kses_post( $data['description'] ) . '</small>';
					?>
				</td>
			</tr>
			<style>
				.woocommerce .forminp-cond_special .special-combo,
				.woocommerce .forminp-cond_special .special-combo #cat {
					width: auto;
				}
				.woocommerce .forminp-cond_special .special-combo.cost {
					width: 90px;
				}
				#wpbody .woocommerce .forminp-cond_special .special-combo {
					display: inline-block;
					vertical-align: middle;
					height: 34px;
				}
				.forminp-cond_special .remove-condition {
					padding: 2px 12px;
				}
				.forminp-cond_special .new-condition {
					padding: 2px 10px;
				}
				.forminp-cond_special .categorychecklist {
					margin: 0;
				}
				.forminp-cond_special .original {
					margin-top: 10px;
				}
			</style>
			<script>
				jQuery( document ).ready( function( $ ) {
					if ( $('.forminp-cond_special .remove-condition').prop('disabled') ) {
						$('.forminp-cond_special').find('select').prop('disabled', true);
					}
					$( document ).on( 'click', '.new-condition', function(e) {
						e.preventDefault();
						if ( ! $(this).parent().hasClass('original') ) {
							$(this).parent().addClass('original')
								.find('.remove-condition').prop('disabled', false)
								.parent().find('select').prop('disabled', false)
								.parent().find('input').prop('disabled', false);
						} else {
							let clone = $( this ).parent().clone().addClass('duplicate');
							$( this ).parent().after( clone );
						}
					});

					$( document ).on( 'click', '.remove-condition', function(e) {
						e.preventDefault();
						if ( $('.forminp-cond_special .original').length > 1 ) {
							$( this ).parent().remove();
						} else {
							$('.forminp-cond_special .original').removeClass('original')
								.find('.remove-condition').prop('disabled', true)
								.parent().find('select').prop('disabled', true)
								.parent().find('input').prop('disabled', true);
						}
					});

					$('#mainform').on('submit', function(e){
						let options = [];
						$('.forminp-cond_special .original').each(function(i,v){
							options.push(
								{
									'condition': $(v).find('.condition').val(),
									'condition_value': $(v).find('.cost').val(),
									'category': $(v).find('#cat').val()
								}
							);
						});
						$('#<?php echo esc_attr( $field_key ); ?>').val(JSON.stringify(options));
					});
				})
			</script>
			<?php
			return ob_get_clean();
		}

		/**
		 * Test if condition is matched.
		 *
		 * @param  array $condition Condition.
		 * @param  array $package   Package.
		 * @return bool
		 */
		public function cond_match( $condition, $package ) {
			foreach ( $package['contents'] as $value ) {
				$product    = $value['data'];
				$categories = $product->get_category_ids();

				if ( 'variation' === $product->get_type() ) {
					$parent = wc_get_product( $product->get_parent_id() );
					if ( $parent ) {
						$categories = $parent->get_category_ids();
					}
				}

				if ( in_array( $condition['category'], $categories ) ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * Generate HTML for a code service list on key and data.
		 *
		 * @param string $key  title from field.
		 * @param array  $data field args.
		 * @return Some_Return_Value
		 */
		public function generate_cod_service_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );

			$option = $this->get_option( $key );

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>">
						<?php echo esc_html( $data['title'] ); ?>
						<span class="woocommerce-help-tip" data-tip="<?php echo esc_html( $data['description'] ); ?>"></span>
					</label>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( $data['type'] ); ?>">
					<?php
					if ( isset( $this->correios_settings['services_list'] )
						&& $this->correios_settings['services_list'] ) {
						$services = array(
							'' => __( 'Selecione o Serviço', 'virtuaria-correios' ),
						);

						foreach ( $this->correios_settings['services_list'] as $service ) {
							$services[ $service['codigo'] ] = $service['codigo'] . ' - ' . $service['descricao'];
						}
						?>
						<select
							class="wc-enhanced-select"
							name="<?php echo esc_attr( $field_key ); ?>"
							id="<?php echo esc_attr( $field_key ); ?>">
							<?php
							foreach ( $services as $index => $service ) :
								?>
								<option	value="<?php echo esc_attr( $index ); ?>" <?php selected( $option, $index ); ?>>
									<?php echo esc_html( $service ); ?>
								</option>
								<?php
							endforeach;
							?>
						</select>
						<script type="text/javascript">
							jQuery(function($) {
								$('#<?php echo esc_attr( $field_key ); ?>').select2({
									placeholder: '<?php esc_attr_e( 'Digite para buscar um serviço de entrega', 'virtuaria-correios' ); ?>',
									allowClear: true,
								});
							});
						</script>
						<?php
					} else {
						?>
						<input
							type="text"
							class="input-text regular-input "
							name="<?php echo esc_attr( $field_key ); ?>"
							value="<?php echo esc_attr( $option ); ?>"
							id="<?php echo esc_attr( $field_key ); ?>" />
						<?php
					}
					?>
				</td>
			</tr>
			<?php
			return ob_get_clean();
		}

		/**
		 * Generate HTML for a special condition based on key and data.
		 *
		 * @param string $key  title from field.
		 * @param array  $data field args.
		 * @return Some_Return_Value
		 */
		public function generate_discount_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );

			$option = json_decode( $this->get_option( $key ), true );

			$has_value = false;
			if ( ! $option ) {
				$option[] = array(
					'min_value'   => '',
					'percent'     => 0,
					'fixed_value' => '',
				);
			} else {
				$has_value = true;
			}

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>">
						<?php echo esc_html( $data['title'] ); ?>
					</label>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( $data['type'] ); ?>">
					<input type="hidden" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" />

					<?php
					$size = count( $option );
					foreach ( $option as $key => $value ) :
						?>
						<div class="<?php echo $has_value ? 'original' : ''; ?>">
							<input
								type="number"
								name="min_value"
								min="0"
								<?php echo ! $has_value ? 'disabled' : ''; ?>
								class="discount-combo cost"
								placeholder="Valor Mínimo (R$)"
								value="<?php echo esc_attr( $value['min_value'] ); ?>"
								step="0.01" />
							<select name="discount" class="discount-combo percent" <?php echo ! $has_value ? 'disabled' : ''; ?>>
								<option>Desconto (%)</option>
								<?php
								$percents = range( 10, 100, 10 );
								foreach ( $percents as $percent ) :
									printf(
										'<option %1$s value="%2$s">%3$s%% %4$s</option>',
										selected( $value['percent'], $percent, false ),
										esc_attr( $percent ),
										esc_html( $percent ),
										100 == $percent ? ' (Grátis)' : ''
									);
								endforeach;
								printf(
									'<option %1$s value="fixed">Valor Fixo (R$)</option>',
									selected( $value['percent'], 'fixed', false ),
								);
								?>
							</select>
							<input
								type="number"
								name="fixed_value"
								class="fixed_value discount-combo <?php echo ! isset( $value['fixed_value'] ) || ! $value['fixed_value'] ? 'hidden' : ''; ?>"
								placeholder="Ex: 2.50"
								value="<?php echo isset( $value['fixed_value'] ) ? esc_attr( $value['fixed_value'] ) : ''; ?>"
								min="1"
								step="0.01" />
							<button class="remove-discount button button-primary" <?php echo ! $has_value ? 'disabled' : ''; ?>>-</button>
							<button class="new-discount button button-primary">+</button>
						</div>
						<?php
					endforeach;
					echo '<small style="display:block;margin-top:20px" class="desc">'
						. wp_kses_post( $data['description'] ) . '</small>';
					?>
				</td>
			</tr>
			<style>
				.woocommerce .forminp-discount .discount-combo {
					width: auto;
				}
				.woocommerce .forminp-discount .discount-combo.cost {
					width: 160px;
				}
				#wpbody .woocommerce .forminp-discount .discount-combo {
					display: inline-block;
					vertical-align: middle;
					height: 34px;
				}
				.forminp-discount .new-discount {
					padding: 2px 10px;
				}
				.forminp-discount .categorychecklist {
					margin: 0;
				}
				.forminp-discount .original {
					margin-top: 10px;
				}
				.original .button-primary.remove-discount {
					padding: 2px 12px;
				}
				.woocommerce_page_wc-settings h3.wc-settings-sub-title {
					font-size: 20px;
					margin-top: 40px;
				}
				#wpbody .woocommerce .forminp-discount .fixed_value.hidden {
					display: none;
				}
				#wpbody .woocommerce .forminp-discount .fixed_value {
					max-width: 100px;
				}
				#woocommerce_virtuaria-correios-sedex_dimensions_section + .form-table tbody tr:first-child th {
					visibility: hidden;
					width: 0;
					display: none;
				}
				#woocommerce_virtuaria-correios-sedex_dimensions_section + .form-table {
					table-layout: auto;
				}
				#woocommerce_virtuaria-correios-sedex_dimensions_section + .form-table tbody tr:first-child td {
					max-width: 200px;
					padding: 0;
				}
				select#woocommerce_virtuaria-correios-sedex_dimensions_type {
					width: 200px;
				}
			</style>
			<script>
				jQuery( document ).ready( function( $ ) {
					if ( $('.forminp-discount .remove-discount').prop('disabled') ) {
						$('.forminp-discount').find('select').prop('disabled', true);
					}
					$( document ).on( 'click', '.new-discount', function(e) {
						e.preventDefault();
						if ( ! $(this).parent().hasClass('original') ) {
							$(this).parent().addClass('original')
								.find('.remove-discount').prop('disabled', false)
								.parent().find('select').prop('disabled', false)
								.parent().find('input').prop('disabled', false);
						} else {
							let clone = $( this ).parent().clone().addClass('duplicate');
							$( this ).parent().after( clone );
						}			
					});

					$( document ).on( 'click', '.remove-discount', function(e) {
						e.preventDefault();
						if ( $('.forminp-discount .original').length > 1 ) {
							$( this ).parent().remove();
						} else {
							$('.forminp-discount .original').removeClass('original')
								.find('.remove-discount').prop('disabled', true)
								.parent().find('select').prop('disabled', true)
								.parent().find('input').prop('disabled', true);
						}
					});

					$(document).on('change', '.discount-combo.percent', function() {
						if ( $(this).val() == 'fixed' ) {
							$(this).parent().find('.fixed_value').removeClass('hidden');
						} else {
							if ( ! $(this).parent().find('.fixed_value').hasClass('hidden') ) {
								$(this).parent().find('.fixed_value').addClass('hidden');
							}
						}
					});

					$('#mainform').on('submit', function(e){
						let options = [];
						$('.forminp-discount .original').each(function(i,v){
							options.push(
								{
									'min_value': $(v).find('.cost').val(),
									'percent': $(v).find('.percent').val(),
									'fixed_value': $(v).find('.fixed_value:visible').val()
								}
							);
						});
						$('#<?php echo esc_attr( $field_key ); ?>').val(JSON.stringify(options));
					});
				})
			</script>
			<?php
			return ob_get_clean();
		}

		/**
		 * Get min to free shipping.
		 */
		public function get_min_to_free_shipping() {
			$discounts = $this->get_option( 'discounts' );
			if ( $discounts ) {
				$discounts = json_decode( $discounts, true );
				foreach ( $discounts as $discount ) {
					if ( '100' === $discount['percent'] ) {
						return $discount['min_value'];
					}
				}
			}
			return false;
		}

		/**
		 * Generate HTML for a special condition based on key and data.
		 *
		 * @param string $key  title from field.
		 * @param array  $data field args.
		 * @return Some_Return_Value
		 */
		public function generate_extra_weight_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );

			$option = json_decode( $this->get_option( $key ), true );

			if ( ! $option ) {
				$option = array(
					'weight' => 0,
					'type'   => 'product',
				);
			}

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>">
						<?php echo esc_html( $data['title'] ); ?>
					</label>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( $data['type'] ); ?>">
					<input
						type="hidden"
						name="<?php echo esc_attr( $field_key ); ?>"
						id="<?php echo esc_attr( $field_key ); ?>" />

					<input
						type="number"
						name="extra_weight"
						min="0"
						class="extra_weight"
						placeholder="0.200"
						value="<?php echo esc_attr( $option['weight'] ); ?>"
						step="0.001" />

					<select name="extra_type" class="extra-type">
						<option value="product" <?php selected( 'product', $option['type'] ); ?>>Por Produto</option>
						<option value="order" <?php selected( 'order', $option['type'] ); ?>>Por Pedido</option>
					</select>
					<small style="display: block;margin-top: 5px;"><?php echo esc_html( $data['description'] ); ?></small>
				</td>
			</tr>
			<style>
				.woocommerce table.form-table select.extra-type {
					max-width: 140px;
					line-height: 28px;
					margin-left: 10px;
				}
				.woocommerce .form-table .extra_weight {
					max-width: 247px;
				}
			</style>
			<script>
				jQuery(document).ready(function($) {
					$('#mainform').on('submit', function(e){
						let options = {
							'weight': $('.forminp-extra_weight .extra_weight').val(),
							'type': $('.forminp-extra_weight .extra-type').val()
						};
						$('#<?php echo esc_attr( $field_key ); ?>').val(JSON.stringify(options));
					});
				});
			</script>
			<?php
			return ob_get_clean();
		}

		/**
		 * Calculates the optimized cubage of all products.
		 *
		 * @param  array $heights Array of product heights.
		 * @param  array $widths  Array of product widths.
		 * @param  array $lengths Array of product lengths.
		 * @return array Optimized dimensions of the package.
		 */
		protected function get_optimized_cubage( $heights, $widths, $lengths ) {
			// Verifica se os arrays têm o mesmo número de elementos.
			if ( count( $heights ) !== count( $widths ) || count( $widths ) !== count( $lengths ) ) {
				return false;
			}

			// Agrupa os produtos por dimensões e conta as quantidades.
			$product_groups = array();
			$total_products = count( $heights );

			for ( $i = 0; $i < $total_products; $i++ ) {
				$key = $heights[ $i ] . 'x' . $widths[ $i ] . 'x' . $lengths[ $i ];
				if ( isset( $product_groups[ $key ] ) ) {
					$product_groups[ $key ]['quantity'] += 1;
				} else {
					$product_groups[ $key ] = array(
						'height'   => $heights[ $i ],
						'width'    => $widths[ $i ],
						'length'   => $lengths[ $i ],
						'quantity' => 1,
					);
				}
			}

			// Inicializa as dimensões da embalagem.
			$package_height = 0;
			$package_width  = 0;
			$package_length = 0;

			foreach ( $product_groups as $group ) {
				// Calcula o arranjo ótimo para o grupo.
				$arrangement = $this->calculateOptimalArrangement(
					$group['quantity'],
					$group['width'],
					$group['length']
				);

				// Calcula as dimensões ocupadas pelo grupo.
				$group_width  = $arrangement['columns'] * $group['width'];
				$group_length = $arrangement['rows'] * $group['length'];
				$group_height = $group['height'];

				// Atualiza as dimensões máximas da camada atual.
				$package_width  = max( $package_width, $group_width );
				$package_length = max( $package_length, $group_length );
				$package_height = max( $package_height, $group_height );
			}

			$margin = 2; // espaçamento da caixa em cm.
			// Atualiza as dimensões da embalagem.
			$package_height += $margin;
			$package_width  += $margin;
			$package_length += $margin;

			// Retorna as dimensões calculadas da embalagem.
			return array(
				'height' => $package_height,
				'width'  => $package_width,
				'length' => $package_length,
			);
		}

		/**
		 * Calcula o arranjo ótimo (número de linhas e colunas) para um grupo de produtos.
		 *
		 * @param int $quantity Quantidade de produtos no grupo.
		 * @param int $width    Largura de um produto.
		 * @param int $length   Comprimento de um produto.
		 *
		 * @return array Associativo com 'rows' e 'columns'.
		 */
		private function calculateOptimalArrangement( $quantity, $width, $length ) {
			$best_rows     = 1;
			$best_columns  = $quantity;
			$min_perimeter = ( $width * $best_columns ) + ( $length * $best_rows );

			// Tenta encontrar o arranjo com o menor perímetro (para minimizar dimensões).
			for ( $rows = 1; $rows <= $quantity; $rows++ ) {
				$columns   = ceil( $quantity / $rows );
				$perimeter = ( $width * $columns ) + ( $length * $rows );
				if ( $perimeter < $min_perimeter ) {
					$min_perimeter = $perimeter;
					$best_rows     = $rows;
					$best_columns  = $columns;
				}
			}

			return array(
				'rows'    => $best_rows,
				'columns' => $best_columns,
			);
		}
	}
}
