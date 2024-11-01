<?php
/**
 * Handle Correios Pré Post.
 *
 * @package virtuaria.
 */

defined( 'ABSPATH' ) || exit;

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

/**
 * Pre-Post Correios.
 */
class Virtuaria_Correios_Prepost {
	use Virtuaria_Correios_PrePost_Functions;

	/**
	 * Forbidden order status to be preposted.
	 *
	 * @var array
	 */
	private $forbidden_order_status = array(
		'pending',
		'cancelled',
		'failed',
	);

	/**
	 * Initialize functions.
	 */
	public function __construct() {
		add_action(
			'init',
			array( $this, 'enqueue_actions_metabox' ),
			20
		);

		add_action(
			'woocommerce_process_shop_order_meta',
			array( $this, 'create_prepost' )
		);

		add_action(
			'woocommerce_admin_order_actions_end',
			array( $this, 'preposted_order_actions_icon' )
		);
	}

	/**
	 * A function to enqueue actions metabox.
	 */
	public function enqueue_actions_metabox() {
		add_action(
			'add_meta_boxes_' . $this->get_meta_boxes_screen(),
			array( $this, 'prepost_meta_box' ),
		);
	}

	/**
	 * Add meta box.
	 */
	public function prepost_meta_box() {
		add_meta_box(
			'virtuaria-correios-prepost',
			'Correios Etiqueta',
			array( $this, 'prepost_meta_box_content' ),
			$this->get_meta_boxes_screen(),
			'side',
			'high'
		);

		add_meta_box(
			'virtuaria-correios-prepost-content-declare',
			'Correios Declaração de Conteúdo',
			array( $this, 'declare_meta_box_content' ),
			$this->get_meta_boxes_screen(),
			'side',
			'high'
		);
	}

	/**
	 * A function to display meta box content for pre-postage creation.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function prepost_meta_box_content( $post ) {
		$order = $post instanceof WC_Order
			? $post
			: wc_get_order( $post->ID );

		if ( $order
			&& ! in_array(
				$order->get_status(),
				$this->forbidden_order_status,
				true
			)
		) {
			$shipping_method = isset( array_values( $order->get_shipping_methods() )[0] )
				? array_values( $order->get_shipping_methods() )[0]
				: false;

			$methods_allowed = array(
				'virtuaria-correios-sedex',
				'virtuaria-correios-pac',
			);
			if ( $shipping_method instanceof WC_Order_Item_Shipping
				&& in_array( $shipping_method->get_method_id(), $methods_allowed, true ) ) {
				?>
				<div class="shipping-prepost ticket">
					<p>
						Preencha <b>um dos campos</b> abaixo:
					</p>
					<p>
						<input type="text" name="nf_number" id="nf-number" placeholder="Número da nota fiscal" />
					</p>
					<p>
						<input type="text" name="nf_key" id="nf-key" placeholder="Chave da nota fiscal" maxlength="44" />
					</p>
					<button
						class="prepost button button-primary ticket"
						id="prepost-<?php echo esc_attr( $order->get_id() ); ?>"
						data-orderid="<?php echo esc_attr( $order->get_id() ); ?>"
						data-instanceid="<?php echo esc_attr( $shipping_method->get_instance_id() ); ?>"
						style="margin-bottom: 10px;"
						data-nfkey=""
						data-nfnumber=")">
							Criar Etiqueta ( Pré-Postagem )
					</button>
					<small
						style="display:block;
						background-color: #f8f8f8;
						border-left: 4px solid #ffca28;
						padding: 10px;
						font-size: 0.9em;
						color: #555;
						margin-bottom: 20px;" class="avisodeclaracao">
						Cria etiqueta de pré-postagem com informações da nota fiscal. O link da etiqueta ( pré-postagem ) será salvo nas notas do pedido.
					</small>
					<input type="hidden" name="correios_order_id_ticket" id="correios_order_id"/>
					<input type="hidden" name="correios_instance_id" id="correios_instance_id" value="<?php echo esc_attr( $shipping_method->get_instance_id() ); ?>" />
					<script>
						jQuery(document).ready(function($) {
							$('#prepost-<?php echo esc_attr( $order->get_id() ); ?>.ticket').on('click', function(e) {
								if ( $('#nf-key').val() || $('#nf-number').val() ) {
									console.log('clicked');
									$(this).parent().find('#correios_order_id').val('<?php echo esc_attr( $order->get_id() ); ?>');
								} else {
									alert('Preencha pelo menos um dos campos com dados da NFe.');
									$('#nf-key,#nf-number').css('border', '1px solid red');
									e.preventDefault();
								}
							});
							$('#nf-key').on('keydown', function() {
								$(this).val($(this).val().replace(/\D/, ''));
							});
							$('#nf-number').on('keydown', function() {
								$(this).val($(this).val().replace(/\D/, ''));
							});

						});
					</script>
				</div>
				<?php
				wp_nonce_field( 'create_prepost', 'create_prepost_nonce' );

				return;
			}
		}

		printf(
			esc_html(
				__(
					'A emissão de pré-postagens não está disponível para pedidos pendentes, falha ou cancelados. Somente pedidos cujo método de envio seja Virtuaria Correios serão aceitos.',
					'virtuaria-correios'
				)
			)
		);
	}

	/**
	 * A function to display meta box content for pre-postage declare content creation.
	 *
	 * @param WP_Post $post The post object.
	 */
	public function declare_meta_box_content( $post ) {
		$order = $post instanceof WC_Order
			? $post
			: wc_get_order( $post->ID );

		if ( $order
			&& ! in_array(
				$order->get_status(),
				$this->forbidden_order_status,
				true
			)
		) {
			$shipping_method = isset( array_values( $order->get_shipping_methods() )[0] )
				? array_values( $order->get_shipping_methods() )[0]
				: false;

			$methods_allowed = array(
				'virtuaria-correios-sedex',
				'virtuaria-correios-pac',
			);
			if ( $shipping_method instanceof WC_Order_Item_Shipping
				&& in_array( $shipping_method->get_method_id(), $methods_allowed, true ) ) {
				?>
				<div class="shipping-prepost declare">
					<button
						class="prepost button button-primary declare"
						id="prepost-<?php echo esc_attr( $order->get_id() ); ?>"
						data-orderid="<?php echo esc_attr( $order->get_id() ); ?>"
						data-instanceid="<?php echo esc_attr( $shipping_method->get_instance_id() ); ?>"
						style="margin-bottom: 10px;">
						Criar Etiqueta ( Sem Nota Fiscal )
					</button>
					<small
						style="display:block;
						background-color: #f8f8f8;
						border-left: 4px solid #ffca28;
						padding: 10px;
						font-size: 0.9em;
						color: #555;
						margin-bottom: 20px;" class="avisodeclaracao">
						Cria etiqueta com <strong>Declaração de Conteúdo</strong>. As informações completas da declaração de conteúdo poderão ser consultadas via painel <a href="https://empresas.correios.com.br" target="_blank">Correios Empresas</a>.
					</small>
					<input type="hidden" name="correios_order_id" id="correios_order_id" />
					<input type="hidden" name="correios_instance_id" id="correios_instance_id" value="<?php echo esc_attr( $shipping_method->get_instance_id() ); ?>" />
					<script>
						jQuery(document).ready(function($) {
							$('#prepost-<?php echo esc_attr( $order->get_id() ); ?>.declare').on('click', function(e) {
								$(this).parent().find('#correios_order_id').val('<?php echo esc_attr( $order->get_id() ); ?>');
								$('#prepost-<?php echo esc_attr( $order->get_id() ); ?>.ticket').find('#nf-key').val('');
								$('#prepost-<?php echo esc_attr( $order->get_id() ); ?>.ticket').find('#nf-number').val('');
							});
						});
					</script>
				</div>
				<?php
				wp_nonce_field( 'create_prepost', 'create_prepost_nonce' );

				return;
			}
		}

		printf(
			esc_html(
				__(
					'A emissão de pré-postagens não está disponível para pedidos pendentes, falha ou cancelados. Somente pedidos cujo método de envio seja Virtuaria Correios serão aceitos',
					'virtuaria-correios'
				)
			)
		);
	}

	/**
	 * Create prepost data for the given post ID.
	 *
	 * @param int $post_id The ID of the post.
	 * @return bool
	 */
	public function create_prepost( $post_id ) {
		$order = wc_get_order( $post_id );

		if ( $order
			&& isset(
				$_POST['create_prepost_nonce'],
				$_POST['correios_instance_id'],
			)
			&& (
				isset( $_POST['correios_order_id_ticket'] )
				|| isset( $_POST['correios_order_id'] )
			)
			&& is_admin()
			&& wp_verify_nonce(
				sanitize_text_field(
					wp_unslash(
						$_POST['create_prepost_nonce']
					)
				),
				'create_prepost'
			)
			&& (
				intval( $_POST['correios_order_id'] ) > 0
				|| intval( $_POST['correios_order_id_ticket'] ) > 0
			)
			&& intval( $_POST['correios_instance_id'] )
		) {
			$instance_id = sanitize_text_field( wp_unslash( $_POST['correios_instance_id'] ) );
			$shipping    = new Virtuaria_Correios_Sedex( $instance_id );

			if ( ! $shipping ) {
				$order->add_order_note(
					__( 'Virtuaria Correios: Pré-Postagem não criada. Método de entrega não disponível.', 'virtuaria-correios' )
				);
				return;
			}

			$api = new Virtuaria_Correios_API(
				$shipping->log ? $shipping->log : null,
				$shipping->enviroment
			);

			$args    = $this->get_formatted_prepost( $order, $shipping );
			$prepost = $api->create_prepost( $args );

			if ( $prepost ) {
				$gen_label = $api->generate_label(
					array(
						'idCorreios'           => $prepost['idCorreios'],
						'numeroCartaoPostagem' => $prepost['numeroCartaoPostagem'],
						'tipoRotulo'           => 'P',
						'formatoRotulo'        => 'ET',
						'imprimeRemetente'     => 'S',
						'idsPrePostagem'       => array(
							$prepost['id'],
						),
						'layoutImpressao'      => 'PADRAO',
						'username'             => $shipping->username,
						'password'             => $shipping->password,
						'post_card'            => $shipping->post_card,
					)
				);

				if ( $gen_label ) {
					sleep( 2 ); // Delay to waiting for the API generation Label from idRecibo.
					$label = $api->get_label(
						array(
							'username'  => $shipping->username,
							'password'  => $shipping->password,
							'post_card' => $shipping->post_card,
							'idRecibo'  => $gen_label,
						)
					);

					if ( $label ) {
						$order->update_meta_data( '_virtuaria_correios_ticket_label', $label );

						$file_url = $this->upload_label( $label, $order->get_id() );

						$order->update_meta_data( '_virtuaria_correios_ticket_url', $file_url );
						$order->update_meta_data(
							'_virtuaria_correios_ticket_nfe_key',
							isset( $args['chaveNFe'] )
								? $args['chaveNFe']
								: false
						);
						$order->update_meta_data(
							'_virtuaria_correios_ticket_nfe_number',
							isset( $args['numeroNotaFiscal'] )
								? $args['numeroNotaFiscal']
								: false
						);

						if ( isset( $prepost['codigoObjeto'] ) ) {
							$order->update_meta_data(
								'_virt_correios_trakking_code',
								$prepost['codigoObjeto']
							);

							$order->add_order_note(
								sprintf(
									/* translators: %1$s: trakking code. */
									__( 'Virtuaria Correios: Novo código de rastreamento (%1$s) adicionado ao pedido.', 'virtuaria-correios' ),
									$prepost['codigoObjeto']
								),
								false,
								true
							);
						}

						$order->save();

						if ( function_exists( '\\order\\limit_characters_order_note' ) ) {
							remove_filter(
								'woocommerce_new_order_note_data',
								'\\order\\limit_characters_order_note'
							);
						}
						$order->add_order_note(
							sprintf(
								/* translators: %1$s: chave da NFe, %2$s: número da NFe, %3$s: ticket link. */
								__(
									'Virtuaria Correios: Sua etiqueta foi gerada com sucesso.<br><br>Chave da NFe: %1$s<br><br>Número da NFe: %2$s<br><br><a href="%3$s" target="_blank">Imprimir etiqueta</a>',
									'virtuaria-correios'
								),
								isset( $args['chaveNFe'] ) ? $args['chaveNFe'] : 'Não informado',
								isset( $args['numeroNotaFiscal'] ) ? $args['numeroNotaFiscal'] : 'Não informado',
								$file_url
							),
							0,
							true
						);

						if ( function_exists( '\\order\\limit_characters_order_note' ) ) {
							add_filter(
								'woocommerce_new_order_note_data',
								'\\order\\limit_characters_order_note'
							);
						}

						return true;
					}
				}
			}

			if ( function_exists( '\\order\\limit_characters_order_note' ) ) {
				remove_filter(
					'woocommerce_new_order_note_data',
					'\\order\\limit_characters_order_note'
				);
			}

			$erros = get_transient( 'virtuaria_correios_prepost_error' );
			$order->add_order_note(
				sprintf(
					/* translators: %1$s: errors, %2$s: ticket link, %3$s: chave da NFe, %4$s: número da NFe. */
					__( 'Virtuaria Correios: Falha ao gerar etiqueta de postagem: %1$s Consulte o %2$s para mais informações.<br><br>Chave da NFe: %3$s<br><br>Número da NFe: %4$s', 'virtuaria-correios' ),
					'<br><br>-' . ( $erros && is_array( $erros ) ? implode( '<br>-', $erros ) : '' ) . '</b><br><br>',
					'<a target="_blank" href="' . admin_url( 'admin.php?page=wc-status&tab=logs&source=virtuaria-correios' ) . '">log</a>',
					isset( $args['chaveNFe'] ) ? $args['chaveNFe'] : 'Não informado',
					isset( $args['numeroNotaFiscal'] ) ? $args['numeroNotaFiscal'] : 'Não informado',
				),
				0,
				true
			);

			if ( function_exists( '\\order\\limit_characters_order_note' ) ) {
				add_filter(
					'woocommerce_new_order_note_data',
					'\\order\\limit_characters_order_note'
				);
			}

			return false;
		}
	}

	/**
	 * Upload label and generate link to print.
	 *
	 * @param string $label    the label.
	 * @param string $filename file name.
	 */
	public function upload_label( $label, $filename ) {
		$uploads_dir = trailingslashit( wp_upload_dir()['basedir'] ) . 'virtuaria_correios';
		wp_mkdir_p( $uploads_dir );

		$bin = base64_decode( $label, true );

		$file_path = $uploads_dir . '/' . $filename . '.pdf';

		WP_Filesystem();
		global $wp_filesystem;

		$wp_filesystem->put_contents( $file_path, $bin );

		return wp_upload_dir()['baseurl'] . '/virtuaria_correios/' . $filename . '.pdf';
	}

	/**
	 * Retrieve the screen ID for meta boxes.
	 *
	 * @return string
	 */
	private function get_meta_boxes_screen() {
		return class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' )
			&& wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
			&& function_exists( 'wc_get_page_screen_id' )
			? wc_get_page_screen_id( 'shop-order' )
			: 'shop_order';
	}

	/**
	 * Display icon in order who generated the prepost.
	 *
	 * @param wc_order $order order.
	 */
	public function preposted_order_actions_icon( $order ) {
		$ticket_url = $order->get_meta( '_virtuaria_correios_ticket_url' );
		if ( $ticket_url ) {
			echo '<a href="' . esc_url( $ticket_url );
			echo '" target="_blank" style="display:inline-block;vertical-align:middle;margin-left:3px;border:1px solid #2271b1;border-radius:3px;line-height:initial;">';
			echo '<img src="' . esc_url( VIRTUARIA_CORREIOS_URL );
			echo 'admin/images/ticket.png" alt="Etiqueta de entrega gerada"';
			echo ' title="Etiqueta de entrega gerada" width="26" height="26" />';
			echo '</a>';
		}
	}
}

new Virtuaria_Correios_PrePost();