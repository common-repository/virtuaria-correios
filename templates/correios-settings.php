<?php
/**
 * Template Correios Multsite settings.
 *
 * @package virtuaria/integrations/correios.
 */

defined( 'ABSPATH' ) || exit;

$states = array(
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

$options = Virtuaria_WPMU_Correios_Settings::get_settings();

if ( ! isset( $options['username'] )
	&& ! class_exists( 'Extra_Checkout_Fields_For_Brazil' ) ) {
	$options['activate_checkout'] = 'yes';
}

?>

<h1 class="main-title">Virtuaria Correios</h1>

<form action="" method="post" id="mainform" class="main-setting">
	<div class="navigation-tab">
		<a class="tablinks integration active" href="#">Integração</a>
		<a class="tablinks ticket" href="#">Etiquetas</a>
		<a class="tablinks checkout" href="#">Checkout</a>
		<a class="tablinks entrega" href="#">Instruções</a>
		<a class="tablinks premium" href="#">Premium</a>
	</div>
	<table class="form-table integration">
		<tbody>
			<?php
			if ( is_multisite() && function_exists( 'is_main_site' ) && is_main_site() ) :
				?>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_virt_correios_enabled">Habilitar Configuração Global</label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span>Habilitar Configuração Global</span></legend>
							<input
								type="checkbox"
								name="woocommerce_virt_correios_enabled"
								id="woocommerce_virt_correios_enabled"
								value="yes"
								<?php isset( $options['enabled'] ) ? checked( $options['enabled'], 'yes' ) : ''; ?> />
							<p class="description">
								Habilita configuração global das informações de acesso a API e geração de etiquetas.
							</p>
						</fieldset>
					</td>
				</tr>
				<?php
			endif;
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_easy_mode">Modo Básico</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Modo Básico</span></legend>
						<input type="checkbox" style="display: inline;"
							name="woocommerce_virt_correios_easy_mode"
							id="woocommerce_virt_correios_easy_mode"
							value="yes"
							<?php isset( $options['easy_mode'] ) ? checked( $options['easy_mode'], 'yes' ) : ''; ?> />
						<p class="description" style="display: inline;">
							Permite realizar cotações de frete <b>sem a necessidade de um contrato com os Correios</b>. Vale ressaltar que apenas os métodos SEDEX ( 03220 ) e PAC (03298) estarão disponíveis. Etiquetas de entrega não podem ser geradas no Modo Básico. Os valores serão calculadas com base na modalidade de pagamento à vista, devendo ser confirmadas no ato da postagem.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_username">Login</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Login</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_username"
							id="woocommerce_virt_correios_username"
							value="<?php echo isset( $options['username'] ) ? esc_attr( $options['username'] ) : ''; ?>" />
						<p class="description">
							Login cadastrado no painel Meus Correios.
							<a href="#" onclick="openModal('loginVideoModal'); return false;">Veja como encontrar no portal CAS<i class="videoicon dashicons dashicons-video-alt3"></i></a>.
							<a href="#" onclick="openModal('loginVideoModal2'); return false;">Veja como encontrar no portal Empresas<i class="videoicon dashicons dashicons-video-alt3"></i></a>.

						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_password">Código de acesso</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Código de acesso</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_password"
							id="woocommerce_virt_correios_password"
							value="<?php echo isset( $options['password'] ) ? esc_attr( $options['password'] ) : ''; ?>" />
						<p class="description">
							Código de acesso gerado no portal do serviço Meus Correios.
							<a href="#" onclick="openModal('accessCodeVideoModal'); return false;">Veja como encontrar<i class="videoicon dashicons dashicons-video-alt3"></i></a>
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_post_card">Cartão de Postagem</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Cartão de Postagem</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_post_card"
							id="woocommerce_virt_correios_post_card"
							value="<?php echo isset( $options['post_card'] ) ? esc_attr( $options['post_card'] ) : ''; ?>" />
						<p class="description">
							Recurso utilizado para acesso a APIs com autorização pelo cartão de postagem.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_enviroment">Ambiente</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Ambiente</span></legend>
						<select
							name="woocommerce_virt_correios_enviroment"
							id="woocommerce_virt_correios_enviroment">
							<option
							<?php
							if ( isset( $options['enviroment'] ) ) {
								echo selected( 'sandbox', $options['enviroment'], false );
							}
							?>
							value="sandbox">Testes</option>
							<option
							<?php
							if ( isset( $options['enviroment'] ) ) {
								echo selected( 'production', $options['enviroment'], false );
							}
							?>
							value="production">Produção</option>
						</select>
						<p class="description">
							Modo de execução da integração com correios.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc section">
					Recursos Avançados
					<small style="display: block;font-weight: normal;font-size: 15px;">
						Configura opções avançandas do plugin.
					</small>
				</th>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_automatic_fill">Preenchimento automático de endereços</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Preenchimento automático de endereços</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_automatic_fill"
							id="woocommerce_virt_correios_automatic_fill"
							value="yes"
							<?php isset( $options['automatic_fill'] ) ? checked( $options['automatic_fill'], 'yes' ) : ''; ?> />
						<p class="description">
							Habilita o preenchimento automático de endereços com base no CEP informado durante o checkout.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_calc_in_product">Calculadora de frete na página do produto</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Calculadora de frete na página do produto</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_calc_in_product"
							id="woocommerce_virt_correios_calc_in_product"
							value="yes"
							<?php isset( $options['calc_in_product'] ) ? checked( $options['calc_in_product'], 'yes' ) : ''; ?> />
						<p class="description">
							Exibir calculadora de frete da na página do produto.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_calc_in_product">Shortcode para Calculadora</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Shortcode para Calculadora</span></legend>
						<p class="description">
							Use o shortcode <b>[virtuaria_correios_calculadora]</b> para exibir a calculadora de frete com mais flexibilidade na página do produto. Para evitar exibição duplicada, desative a opção <b>"Calculadora de frete na página do produto"</b>.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_parcel_tracking">Rastreamento de encomendas</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Rastreamento de encomendas</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_parcel_tracking"
							id="woocommerce_virt_correios_parcel_tracking"
							value="yes"
							<?php isset( $options['parcel_tracking'] ) ? checked( $options['parcel_tracking'], 'yes' ) : ''; ?> />
						<p class="description">
							Exibe o rastreamento de do pedido no painel do cliente e painel de edição do pedido para o lojista.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_devolutions">Solicitação de devolução</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Solicitação de devolução</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_devolutions"
							id="woocommerce_virt_correios_devolutions"
							value="yes"
							<?php isset( $options['devolutions'] ) ? checked( $options['devolutions'], 'yes' ) : ''; ?> />
						<p class="description">
							Ative para que seus clientes possam solicitar devoluções de produtos diretamente no painel de pedidos do cliente. Sempre que uma devolução é solicitada, uma notificação por e-mail será enviada ao gestor da loja.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_display_cart_fields">Exibir campos do carrinho</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Exibir campos do carrinho</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_display_cart_fields"
							id="woocommerce_virt_correios_display_cart_fields"
							value="yes"
							<?php isset( $options['display_cart_fields'] ) ? checked( $options['display_cart_fields'], 'yes' ) : ''; ?> />
						<p class="description">
							Marque para exibir os campos <b>País, Estado e Cidade</b> na calculadora de frete da página do carrinho.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_hide_free_shipping_notice">Esconder Mensagem de Frete Grátis</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Esconder Mensagem de Frete Grátis</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_hide_free_shipping_notice"
							id="woocommerce_virt_correios_hide_free_shipping_notice"
							value="yes"
							<?php isset( $options['hide_free_shipping_notice'] ) ? checked( $options['hide_free_shipping_notice'], 'yes' ) : ''; ?> />
						<p class="description">
							Marque para ocultar a mensagem exibida ao cliente quando a condição para frete grátis for atingida.
						</p>
					</fieldset>
				</td>
			</tr>
			<!-- <tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_optimize_add_cart">Desativar aceleração no Adicionar ao Carrinho</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Desativar aceleração no Adicionar ao Carrinho</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_optimize_add_cart"
							id="woocommerce_virt_correios_optimize_add_cart"
							value="yes"
							/>
						<p class="description">
							Marque para desativar a otimização que acelera o processo de adição de produtos ao carrinho.
						</p>
					</fieldset>
				</td>
			</tr> -->
			<tr valign="top">
				<th scope="row" class="titledesc section">
					Depuração
				</th>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_debug">Debug</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Debug</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_debug"
							id="woocommerce_virt_correios_debug"
							value="yes"
							<?php isset( $options['debug'] ) ? checked( $options['debug'], 'yes' ) : ''; ?> />
						<p class="description">
							Log para depuração de problemas.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_error_message">Mensagens dos Correios</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Mensagens dos Correios</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_error_message"
							id="woocommerce_virt_correios_error_message"
							value="yes"
							<?php isset( $options['error_message'] ) ? checked( $options['error_message'], 'yes' ) : ''; ?> />
						<p class="description">
							Exibe descritivo de problemas na cotação de frete nas áreas abertas de sua loja.
						</p>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table ticket hidden">
		<tbody>
		<tr valign="top">
				<th scope="row" class="titledesc section">
					Pré-Postagem
					<small style="display: block;font-weight: normal;font-size: 15px;">
						Define os dados usados na emissão da pré-postagem de encomendas.
					</small>
				</th>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_full_name">Nome Completo</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Nome Completo</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_full_name"
							id="woocommerce_virt_correios_full_name"
							maxlength="50"
							value="<?php echo isset( $options['full_name'] ) ? esc_attr( $options['full_name'] ) : ''; ?>" />
						<p class="description">
							Nome completo do remetente dos pacotes.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_ddd">Código de área (DDD)</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Código de área (DDD)</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_ddd"
							id="woocommerce_virt_correios_ddd"
							maxlength="2"
							value="<?php echo isset( $options['ddd'] ) ? esc_attr( $options['ddd'] ) : ''; ?>" />
						<p class="description">
							Código de área (DDD) do remetente dos pacotes.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_fone">Celular</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Celular</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_fone"
							id="woocommerce_virt_correios_fone"
							maxlength="9"
							value="<?php echo isset( $options['fone'] ) ? esc_attr( $options['fone'] ) : ''; ?>" />
						<p class="description">
							Número do celular do remetente. Sem o Código de área (DDD).
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_email">E-mail</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>E-mail</span></legend>
						<input
							type="email"
							name="woocommerce_virt_correios_email"
							id="woocommerce_virt_correios_email"
							value="<?php echo isset( $options['email'] ) ? esc_attr( $options['email'] ) : ''; ?>" />
						<p class="description">
							E-mail do remetente.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_cpfcnpj">CPF / CNPJ</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>CPF / CNPJ</span></legend>
						<input
							type="number"
							step="1"
							name="woocommerce_virt_correios_cpfcnpj"
							id="woocommerce_virt_correios_cpfcnpj"
							maxlength="14"
							value="<?php echo isset( $options['cpfcnpj'] ) ? esc_attr( $options['cpfcnpj'] ) : ''; ?>" />
						<p class="description">
							Documento de identificação do remetente. Informe o CPF ou CNPJ, somente números são aceitos.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_origin">CEP</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>CEP</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_origin"
							id="woocommerce_virt_correios_origin"
							maxlength="8"
							value="<?php echo isset( $options['origin'] ) ? esc_attr( $options['origin'] ) : ''; ?>" />
						<p class="description">
							Código de postal (CEP) do remetente dos pacotes ( somente números ).
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_logradouro">Logradouro</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Logradouro</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_logradouro"
							id="woocommerce_virt_correios_logradouro"
							maxlength="50"
							value="<?php echo isset( $options['logradouro'] ) ? esc_attr( $options['logradouro'] ) : ''; ?>" />
						<p class="description">
							Logradouro do remetente. Máximo de 50 caracteres.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_numero">Número</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Número</span></legend>
						<input
							type="number"
							step="1"
							name="woocommerce_virt_correios_numero"
							id="woocommerce_virt_correios_numero"
							maxlength="6"
							value="<?php echo isset( $options['numero'] ) ? esc_attr( $options['numero'] ) : ''; ?>" />
						<p class="description">
							Número do logradouro do remetente. Máximo de 6 caracteres.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_complemento">Complemento ( Opcional )</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Complemento ( Opcional )</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_complemento"
							id="woocommerce_virt_correios_complemento"
							maxlength="30"
							value="<?php echo isset( $options['complemento'] ) ? esc_attr( $options['complemento'] ) : ''; ?>" />
						<p class="description">
							Complemento do logradouro do remetente. Máximo de 30 caracteres.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_bairro">Bairro</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Bairro</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_bairro"
							id="woocommerce_virt_correios_bairro"
							maxlength="30"
							value="<?php echo isset( $options['bairro'] ) ? esc_attr( $options['bairro'] ) : ''; ?>" />
						<p class="description">
							Bairro do remetente. Máximo de 30 caracteres.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_cidade">Cidade</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Cidade</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_cidade"
							id="woocommerce_virt_correios_cidade"
							maxlength="30"
							value="<?php echo isset( $options['cidade'] ) ? esc_attr( $options['cidade'] ) : ''; ?>" />
						<p class="description">
						Cidade do remetente. Máximo de 30 caracteres.
						</p>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_estado">Estado</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Estado</span></legend>
						<select
							name="woocommerce_virt_correios_estado"
							id="woocommerce_virt_correios_estado">
							<option value="">--Selecione--</option>
							<?php
							foreach ( $states as $key => $value ) {
								printf(
									'<option value="%s" %s>%s</option>',
									esc_attr( $key ),
									$options['estado'] === $key ? 'selected' : '',
									esc_html( $value )
								);
							}
							?>
						</select>
						<p class="description">
							Estado (UF) do remetente.
						</p>
					</fieldset>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table entrega hidden" style="margin-top:20px; margin-bottom:50px; margin-left: 20px;">
		<tbody>
			<tr valign="top">
				<td><h2><i class="dashicons dashicons-archive"></i> Configurar Entrega</h2></td>
			</tr>
			<tr valign="top">
				<td><h4>✅ 1. Selecione uma "Área de Entrega";</h4></td>
			</tr>
			<tr valign="top">
				<td><h4>✅ 2. Clique em "Adicionar método de entrega";</h4></td>
			</tr>
		
			<tr valign="top">
				<td><h4>✅ 3. Selecione "Virtuaria Correios" e clique em "Adicionar";</h4></td>
			</tr>
			<tr valign="top">
				<td><h4>✅ 4. Após "Adicionar", você poderá definir um título e  escolher um dos produtos dos correios (PAC, SEDEX, etc)</h4></td>
			</tr>
			<tr valign="top">
				<td><h4>✅ 5. Caso a forma de entrega não aparece no carrinho ou checkout de sua loja virtual, veja como Ativar o modo <a class="classic-video" href="#" onclick="openModal('classicModeModal'); return false;">Shortcode<i class="videoicon dashicons dashicons-video-alt3"></i></a>.</h4></td>
			</tr>
			<tr valign="top">
				<td><h4><a href="/wp-admin/admin.php?page=wc-settings&tab=shipping">Clique para Adicionar um Método de Entrega</a></h4></td>
			</tr>
			<tr>
				<td>
					<hr class="separator" />
					<h2><i class="videoicon dashicons dashicons-video-alt3"></i> Visão Geral</h2>
					<iframe width="600" height="400" src="https://www.youtube.com/embed/oy0H-KOh3Gc?si=P3mxRxPK6GGxrxfR" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
				</td>
			</tr>
			<tr>
				<td>
					<hr class="separator" />
					<h2><i class="dashicons dashicons-menu-alt3"></i> Dúvidas Frequentes</h2>
					<ol class="frequently-questions">
						<li>
							✅ Etiquetas e Declaração de Conteúdo
							<ul class="ticket faq">
								<li>É obrigatório configurar um contrato para gerar;</li>
								<li>Está incluso na versão gratuita do plugin, porém na versão Premium existe a facilidade de gerar mais rapidamente via tela Entregas</li>
								<li>É possível regerar Etiquetas ou Declaração para um mesmo pedido, porém apenas via tela de Detalhes do Pedido;</li>
								<li>Para gerar etiqueta é obrigatório incluir o número ou a chave da nota fiscal. Um dos dois é suficiente;</li>
								<li>Caso não deseje informar os dados da nota, use o link Gerar Declaração de Conteúdo.</li>
							</ul>
						</li>
						<li>
							✅ Calculadora na Página do Produto
							<ul class="calc-product faq">
								<li>Funciona com produtos simples ou variáveis;</li>
								<li>O cálculo é realizado com base no produto em exibição, não nos produtos que estão no carrinho;</li>
								<li>Opcionalmente, é possível usar o shortcode virtuaria_correios_calculadora para incluir a calculadora de forma mais flexível.</li>
							</ul>
						</li>
						<li>
							✅ Modo Básico
							<ul class="basic-mode faq">
								<li>Faz a contação do frete e prazo de entrega sem necessidade de contrato com os correios;</li>
								<li>Quando ativo, não permite gerar etiquetas ou declarações de conteúdo.</li>
							</ul>
						</li>
					</ol>
					
				</td>
			</tr>
		</tbody>
	</table>
	<table class="form-table checkout hidden">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc section">
					Campos Extras no Checkout
				</th>
			</tr>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_activate_checkout">Marque para Ativar</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Marque para Ativar</span></legend>
						<input
							type="checkbox"
							name="woocommerce_virt_correios_activate_checkout"
							id="woocommerce_virt_correios_activate_checkout"
							value="yes"
							<?php isset( $options['activate_checkout'] ) ? checked( $options['activate_checkout'], 'yes' ) : ''; ?> />
						<p class="description">
							Campos que normalmente são usados para integração com plataformas diversas do Brasil, como por exemplo plataformas de logística ou pagamentos.
							<h3>Destaques</h3>
							<ul class="features">
								<li>Substitui o plugin Brazilian Market on Woocommerce</li>
								<li>Preenche campos do checkout somente com a edição do CEP</li>
								<li>Preenchimento automático dos campos do checkout, funciona no checkout modo classico e blocos</li>
							</ul>
						</p>
					</fieldset>
				</td>
			</tr>
			<?php
			if ( isset( $options['activate_checkout'] ) ) {
				do_action( 'virtuaria_correios_setting_extra_fields' );
			}
			?>
		</tbody>
	</table>
	<table class="form-table premium hidden">
		<tbody>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="woocommerce_virt_correios_serial">Código de Licença</label>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span>Código de Licença</span></legend>
						<input
							type="text"
							name="woocommerce_virt_correios_serial"
							id="woocommerce_virt_correios_serial"
							value="<?php echo isset( $options['serial'] ) ? esc_attr( $options['serial'] ) : ''; ?>" />
						<p class="description">
							Informe o código de licença para ter acesso a todos os recursos <b>premium</b> do plugin.
						</p>
					</fieldset>
				</td>
			</tr>
			<?php
			if ( ! isset( $options['serial'], $options['authenticated'] )
				|| ! $options['serial']
				|| ! $options['authenticated'] ) :
				?>
				<tr valign="top">
					<th scope="row" class="titledesc">
						Status
					</th>
					<td>
						<p class="description">
							<b><span style="color:red">Desativado</span></b><br>
							Você ainda não possui um Código de Licença válido. É possível adquirir através do link <a href="https://virtuaria.com.br/loja/virtuaria-correios/" target="_blank">https://virtuaria.com.br/loja/virtuaria-correios</a>. Em caso de dúvidas, entre em contato com o suporte via e-mail <a href="mailto:integracaocorreios@virtuaria.com.br">integracaocorreios@virtuaria.com.br</a>.
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc" style="width: 0;">
						
					</th>
					<td>
						<div class="premium-disabled form-table hidden premium">
							<h2>Recursos Premium</h2>
							<p class="description">
								Com nossa versão premium, você terá acesso a funcionalidades avançadas que vão melhorar a gestão dos envios. Um plugin confiável e poderoso, capaz de transformar a gestão logística do seu e-commerce. Invista no nosso plugin premium e maximize o potencial do seu e-commerce! Confira abaixo a lista de recursos disponíveis: 
							</p>
							<ul>
								<li><h3>💡 Preço por Categoria</h3>Ajusta os custos do frete com base nas categorias dos produtos, aumentando, diminuindo ou fixando preços, conforme a necessidade. Essa funcionalidade permite uma abordagem flexível para gerenciar os custos de envio de acordo com o perfil dos produtos.</li>
								<li><h3>📊 Barra de Progresso para Frete Grátis</h3>Incentiva compras de maior valor ao mostrar o quanto falta para alcançar o frete grátis. Proporciona uma visualização clara e motivadora do progresso em direção ao frete grátis, com uma barra de progresso visível no checkout e carrinho.</li>
								<li><h3>🎯 Shortcode [progress_free_shipping]</h3>Flexibilidade é a chave, e com este shortcode, você pode exibir a barra de progresso para frete grátis em qualquer lugar do seu site. Seja na página inicial, em páginas de produtos específicos ou até mesmo em campanhas promocionais, essa ferramenta permite uma integração fluida e adaptável ao layout do seu site.</li>
								<li><h3>🚚 Esconder Métodos de Entrega</h3>Simplifique o processo de escolha do cliente ao oferecer frete grátis. Quando o método de envio gratuito está disponível, essa função oculta automaticamente todos os outros métodos de entrega, garantindo uma experiência de compra mais direta e intuitiva.</li>
								<li><h3>✨ Frete Grátis</h3>O frete grátis do plugin permite que os métodos de envio dos Correios tenham um custo zero quando o valor mínimo para obtenção do frete grátis, configurado pelo usuário, é alcançado.</li>
								<li><h3>🏷️ Gerenciamento de Etiquetas no Relatório de Entregas</h3> Otimize a gestão logística de sua loja virtual com o gerenciamento de etiquetas no relatório de entregas. Assim é possível gerar e imprimir etiquetas de envio diretamente do relatório de entrega de forma ágil e eficiente.</li>
								<li>
									<h2>🌟  Seja Premium</h2>
									Entre em contato conosco e garanta uma experiência otimizada para sua loja virtual.<br>
									E-mail: <a href="mailto:sejapremium@virtuaria.com.br">sejapremium@virtuaria.com.br</a><br>
									WhatsApp: +55 79 999312134
								</li>
								<li class="gallery">
									<ul class="premium-prints">
										<li class="print">
											<a href="<?php echo esc_attr( VIRTUARIA_CORREIOS_URL ); ?>admin/images/print-01.jpg" target="_blank">
												<img src="<?php echo esc_attr( VIRTUARIA_CORREIOS_URL ); ?>admin/images/print-01.jpg" alt="Print" />
												<h3 class="description">Gestão de Etiquetas no Relatório de Entregas</h3>
											</a>
										</li>
										<li class="print">
											<a href="<?php echo esc_attr( VIRTUARIA_CORREIOS_URL ); ?>admin/images/print-02.jpg" target="_blank">
												<img src="<?php echo esc_attr( VIRTUARIA_CORREIOS_URL ); ?>admin/images/print-02.jpg" alt="Print" />
												<h3 class="description">Ajuste de Preço por Categoria</h3>
											</a>
										</li>
										<li class="print">
											<a href="<?php echo esc_attr( VIRTUARIA_CORREIOS_URL ); ?>admin/images/print-03.jpg" target="_blank">
												<img src="<?php echo esc_attr( VIRTUARIA_CORREIOS_URL ); ?>admin/images/print-03.jpg" alt="Print" />
												<h3 class="description">Barra de Progresso para Frete GRATIS</h3>
											</a>
										</li>
									</ul>
								</li>
							</ul>
						</div>
					</td>
				<?php
			else :
				?>
				<tr valign="top">
					<th scope="row" class="titledesc section">
						Recursos Premium
					</th>
					<td>
						<p class="description">
							<b>Status: <span style="color:green">Ativado</span></b><br>
							Você possui uma chave de acesso válida. Em caso de dúvidas, entre em contato com o suporte via e-mail <a href="mailto:integracaocorreios@virtuaria.com.br">integracaocorreios@virtuaria.com.br</a>.
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_virt_correios_category_price">Preço por categoria</label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span>Preço por categoria</span></legend>
							<input
								type="checkbox"
								name="woocommerce_virt_correios_category_price"
								id="woocommerce_virt_correios_category_price"
								value="yes"
								<?php isset( $options['category_price'] ) ? checked( 'yes', $options['category_price'] ) : ''; ?> />
							<p class="description">
								Permite aumentar, diminuir ou fixar o preço de frete para produtos das categorias selecionadas no método de entrega.
							</p>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_virt_correios_progress_free">Barra de progresso para frete grátis</label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span>Barra de progresso para frete grátis</span></legend>
							<input
								type="checkbox"
								name="woocommerce_virt_correios_progress_free"
								id="woocommerce_virt_correios_progress_free"
								value="yes"
								<?php isset( $options['progress_free'] ) ? checked( 'yes', $options['progress_free'] ) : ''; ?> />
							<p class="description">
								Ao definir <b>"Valor Mínimo para Desconto no Frete"</b> com percentual <b>100% (Grátis)</b>, exibe na página de checkout e carrinho, valor que o cliente precisa adicionar para obter frete grátis. <b>Atenção: </b> A barra de frete grátis não é exibida quando a estimativa de frete for alterada por "Ajustar o valor do frete com base nas categorias de produtos".
							</p>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_virt_correios_hide_shipping">Esconder Métodos de entrega</label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span>Esconder Métodos de entrega</span></legend>
							<input
								type="checkbox"
								name="woocommerce_virt_correios_hide_shipping"
								id="woocommerce_virt_correios_hide_shipping"
								value="yes"
								<?php isset( $options['hide_shipping'] ) ? checked( 'yes', $options['hide_shipping'] ) : ''; ?> />
							<p class="description">
								Quando o método frete grátis estiver disponível, esconde todos os demais métodos de entrega.
							</p>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_virt_correios_shorcode">Shortcode</label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span>Shortcode</span></legend>
							<p class="description">
								Flexibilidade é a chave, e com este shortcode <b>[progress_free_shipping]</b>, você pode exibir a barra de progresso para frete grátis em qualquer lugar do seu site. Seja na página inicial, em páginas de produtos específicos ou até mesmo em campanhas promocionais, essa ferramenta permite uma integração fluida e adaptável ao layout do seu site.
							</p>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="woocommerce_virt_correios_shipping_screen">Gerenciar Etiquetas no Relatório de Entregas</label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span>Gerenciar Etiquetas no Relatório de Entregas</span></legend>
							<p class="description">
								Otimize a gestão logística de sua loja virtual com o gerenciamento de etiquetas no relatório de entregas. Assim é possível gerar e imprimir etiquetas de envio diretamente do relatório de entrega de forma ágil e eficiente.
							</p>
						</fieldset>
					</td>
				</tr>
				<?php
			endif;
			?>
		</tbody>
	</table>
	<div id="loginVideoModal" class="modal" style="display: none;">
		<div class="modal-content">
			<span class="close" onclick="closeModal('loginVideoModal')">×</span>
			<video width="560" height="315" controls="">
				<source src="https://virtuaria-plugins.s3.sa-east-1.amazonaws.com/correios/VirtuariaLoginCorreios.mp4" type="video/mp4">
				Seu navegador não suporta o elemento de vídeo.
			</video>
		</div>
	</div>

	<div id="loginVideoModal2" class="modal" style="display: none;">
		<div class="modal-content">
			<span class="close" onclick="closeModal('loginVideoModal2')">×</span>
			<video width="560" height="315" controls="">
				<source src="https://virtuaria-plugins.s3.sa-east-1.amazonaws.com/correios/VirtuariaLoginCorreiosEmpresas.mp4" type="video/mp4">
				Seu navegador não suporta o elemento de vídeo.
			</video>
		</div>
	</div>

	<div id="accessCodeVideoModal" class="modal" style="display: none;">
		<div class="modal-content">
			<span class="close" onclick="closeModal('accessCodeVideoModal')">×</span>
			<video width="560" height="315" controls="">
				<source src="https://virtuaria-plugins.s3.sa-east-1.amazonaws.com/correios/VirtuariaGerarTokenCorreios.mp4" type="video/mp4">
				Seu navegador não suporta o elemento de vídeo.
			</video>
		</div>
	</div>

	<div id="classicModeModal" class="modal" style="display: none;">
		<div class="modal-content">
			<span class="close" onclick="closeModal('classicModeModal')">×</span>
			<iframe width="560" height="315" src="https://www.youtube.com/embed/0Z18Htrg_Fs?si=1auXHALlHZ6xwes-" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
		</div>
	</div>

	<?php wp_nonce_field( 'update-correios-settings', 'correios_nonce' ); ?>
	<input
		type="submit"
		class="button button-primary"
		value="<?php esc_attr_e( 'Salvar alterações', 'virtuaria-correios' ); ?>">
</form>
<p class="description" style="margin-top: 20px; font-size: 15px; margin-bottom: -5px;">
	Alguns serviços possuem limitações relacionadas a preço, peso e dimensão e podem não estar disponíveis para todos os seus produtos. Para mais informações, consulte a documentação dos <a href="https://www.correios.com.br/enviar/servicos-adicionais" target="_blank">Correios</a>.
</p>
