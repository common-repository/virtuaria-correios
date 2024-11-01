jQuery(document).ready(function($) {
	$('.generate-ticket, .generate-declaration-content').on('click', function(e) {
		e.preventDefault();
		let prepostContent = '.ticket';
		if ( $(this).hasClass('generate-declaration-content') ) {
			prepostContent = '.declare';
		}
		$(this).parent().find('.shipping-prepost' + prepostContent).fadeIn().prepend('<div class="close">X</div>');
	});

	$('.add-tracking-code').on('click', function(e) {
		e.preventDefault();
		$(this).parent().find('.new-trakking-code').fadeIn().prepend('<div class="close">X</div>');
	});

	$(document).on('click', '.close', function() {
		$(this).parent().fadeOut();
	});

	$(document).mouseup(function(e) {
		var ticket = $(".shipping-prepost");

		// if the target of the click isn't the container nor a descendant of the container
		if ( ! ticket.is(e.target) && ticket.has(e.target).length === 0 && ticket.is(':visible') ) 
		{
			ticket.fadeOut();
			$('.close').remove();
		}

		var trakking = $(".new-trakking-code");

		// if the target of the click isn't the container nor a descendant of the container
		if ( ! trakking.is(e.target) && trakking.has(e.target).length === 0 && trakking.is(':visible') ) 
		{
			trakking.fadeOut();
			$('.close').remove();
		}

		var products = $(".product-list");

		// if the target of the click isn't the container nor a descendant of the container
		if ( ! products.is(e.target) && products.has(e.target).length === 0 && products.is(':visible') ) 
		{
			products.fadeOut();
			$('.close').remove();
		}
	});

	$(document).on('click', 'a.print-ticket', function(e) {
		e.preventDefault();
		window.open($(this).attr('href'), '_blank');
	});

	$('.prepost.ticket').off().on('click', function(e) {
		let button_prepost = $(this);
		let shipping_prepost = $(this).parent();

		let nf_number = shipping_prepost.find('#nf-number').val();
		let nf_key = shipping_prepost.find('#nf-key').val();

		if ( nf_key || nf_number ) {
			$(this).parent().find('#correios_order_id').val($(this).data('orderid'));

			shipping_prepost.addClass('loading').append('<div class="spinner"></div>');
			shipping_prepost.find('.spinner').show();

			ajax_prepost(button_prepost, shipping_prepost);            
		} else {
			alert('Preencha pelo menos um dos campos com dados da NFe.');
			$('#nf-key,#nf-number').css('border', '1px solid red');
			e.preventDefault();
		}
		return false;
	});

	$('.prepost.declare').off().on('click', function(e) {
		let button_prepost = $(this);
		let shipping_prepost = $(this).parent();

		$(this).parent().find('#correios_order_id').val($(this).data('orderid'));

		shipping_prepost.addClass('loading').append('<div class="spinner"></div>');
		shipping_prepost.find('.spinner').show();

		ajax_prepost(button_prepost, shipping_prepost);
		return false;
	});

	$('.submit-trakking-code').off().on('click', function(e) {
		e.preventDefault();
		let button = $(this);
		let trakking = $(this).parent();

		trakking.addClass('loading').append('<div class="spinner"></div>');
		trakking.find('.spinner').show();

		$.ajax({
			url: setting.ajaxurl,
			type: 'POST',
			data: {
				action: 'add_trakking_code',
				order_id: button.parent().find('.trakking-order-id').val(),
				trakking_nonce: button.parent().find('input[name="trakking-nonce"]').val(),
				trakking_code: trakking.find('.input-trakking-code').val()
			},
			success: function(data) {
				if ( 'Fail' !== data ) {
					trakking.parent().parent().append(data);
					trakking.parent().remove();
				} else {
					alert('Falha ao adicionar o Código de Rastreamento. Tente novamente mais tarde.');
				}
			},
			error: function(data) {
				console.log(data);
			},
			complete: function() {
				trakking.fadeOut();
				trakking.find('.spinner').remove();
				trakking.removeClass('loading');
			}
		});
	});

	$('.see-trakking').on('click', function(e) {
		e.preventDefault();
		let icon = $(this);
		let trakking = $(this).parent().find('.trakking-order');

		trakking.addClass('loading').append('<div class="spinner"></div>');
		trakking.find('.spinner').show();

		trakking.fadeIn();
		if ( trakking.find('.close').length == 0 ) {
			trakking.prepend('<div class="close">X</div>');
		}

		$.ajax({
			url: setting.ajaxurl,
			type: 'POST',
			data: {
				action: 'trakking_order',
				order_id: icon.data('order-id'),
				trakking_nonce: icon.parent().find('input[name="trakking-order-nonce"]').val(),
			},
			success: function(data) {
				if ( 'Fail' !== data ) {
					trakking.append(data);
				} else {
					alert('Falha ao rastrear entrega do pedido. Tente novamente mais tarde.');
				}
			},
			error: function(data) {
				console.log(data);
			},
			complete: function() {
				trakking.find('.spinner').remove();
				trakking.removeClass('loading');
			}
		});
	});

	$('.see-products').on('click', function(e) {
		e.preventDefault();
		let products = $(this).parent().find('.product-list').fadeIn();
		if ( products.find('.close').length == 0 ) {
			products.append('<div class="close">X</div>');
		}
	});

	$('.select-method').on('change', function() {
		let select = $(this);
		if ( select.val() !== '' ) {
			select.prop('disabled', true);
			select.parent().find('.spinner').css('visibility', 'visible');
	 
			$.ajax({
				url: setting.ajaxurl,
				type: 'POST',
				data: {
					action: 'change_shipping_method',
					order_id: select.data('order'),
					shipping_method: select.val(),
					shipping_title: select.find('option:selected').text(),
					shipping_method_nonce: select.data('nonce')
				},
				success: function(data) {
					if ( data === 'success' ) {
						alert('Alteração efetuada com sucesso!');
						window.location.reload();
					} 
				},
				error: function(data) {
					console.log('Erro na alteração do método de envio: ' + data);
				},
				complete: function() {
					select.prop('disabled', false);
					select.parent().find('.spinner').css('visibility', 'hidden');
				}
			});
		}
	});
});

function ajax_prepost(button_prepost, shipping_prepost) {
	jQuery.ajax({
		url: setting.ajaxurl,
		type: 'POST',
		data: {
			action: 'create_prepost',
			correios_order_id: button_prepost.data('orderid'),
			correios_instance_id: shipping_prepost.find('#correios_instance_id').val(),
			create_prepost_nonce: shipping_prepost.parent().find('#create_prepost_nonce').val(),
			nf_number: shipping_prepost.find('#nf-number').val(),
			nf_key: shipping_prepost.find('#nf-key').val()
		},
		success: function(response) {
			console.log(response);
			if ( response.success === true && response.data.ticket_url != '') {
				if ( response.data.trakking_code != '' ) {
					let column_status = shipping_prepost.parent().parent()
					.find('.add-tracking-code, .tracking-code')
					.parent();

					shipping_prepost.parent().parent()
						.find('.add-tracking-code, .tracking-code')
						.remove();

					column_status
						.append('<a href="https://www.linkcorreios.com.br/?id=' + response.data.trakking_code + '" target="_blank" class="tracking-code">' + response.data.trakking_code + '</a>');
					
					shipping_prepost.parent().parent()
						.find('.status-a-enviar')
						.addClass('status-enviado')
						.removeClass('status-a-enviar')
						.html('✓ ENVIADO');
				}

				shipping_prepost.parent().find('.generate-ticket,.generate-declaration-content')
					.attr('target', '_blank')
					.html('Imprimir etiqueta')
					.attr('href', response.data.ticket_url)
					.addClass('print-ticket')
					.removeClass('generate-ticket');

				if (shipping_prepost.parent().find('.print-ticket').length > 1) {
					shipping_prepost.parent().find('.generate-declaration-content').remove();
				}

				shipping_prepost.parent().find('.shipping-prepost').remove();
				shipping_prepost.remove();
			} else {
				let error = '';

				if ( response.data ) {
					error = '\n- ' + response.data.join('\n- ') + '\n';
				}

				alert(
					'Falha ao gerar a etiqueta. '
					+ error
					+ 'Para mais informações consulte o log do plugin. '
				);
			}
		},
		error: function(data) {
			console.log(data);
		},
		complete: function() {
			shipping_prepost.find('.spinner').remove();
			shipping_prepost.removeClass('loading');
		}
	});
}

(function($){
	if ( $('.multi-ticket').length > 0 ) {
		window.open( $('.multi-ticket').attr('href') );
		$('.multi-ticket').remove();
	}
})(jQuery);