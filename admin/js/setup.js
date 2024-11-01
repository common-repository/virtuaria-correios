jQuery(document).ready(function ($) {
   $(document).on('click', '.ticket-disabled', function (e) {
      e.preventDefault();
      alert('A geração de etiquetas não está disponível no modo básico.');
      return false;
   });

   $('.tablinks').on('click', function (e) {
      e.preventDefault();
      if ( ! $(this).hasClass('ticket-disabled') ) {
         $('.tablinks').removeClass('active');
         let content = $(this).attr('class').replace('tablinks ', '');
         $(this).addClass('active');
         $('.form-table').fadeOut();
         $('.form-table.' + content).fadeIn().css('display', 'block');
         if ( 'entrega' == content ) {
            $('.button-primary').hide();
         } else {
            $('.button-primary').show();
         }
      }
   });

   if ( $('#woocommerce_virt_correios_easy_mode').is(':checked') ) {
      active_easy_mode( $, true );
   }

   $('#woocommerce_virt_correios_easy_mode').on('click', function(){
      active_easy_mode( $, $(this).is(':checked') );
   });
});

function active_easy_mode( $j, status ) {
   $j('#woocommerce_virt_correios_username').prop('disabled', status);
   $j('#woocommerce_virt_correios_password').prop('disabled', status);
   $j('#woocommerce_virt_correios_post_card').prop('disabled', status);
   $j('#woocommerce_virt_correios_enviroment').val('production').prop('disabled', status);
   $j('.navigation-tab .tablinks.ticket').toggleClass( 'ticket-disabled' );
   if ( status ) {
      // $j('.navigation-tab .tablinks.ticket').css({
      //    'pointer-events': 'none',
      //    'background-color': '#ddd'
      // }).append('<span style="color: red;display:block;font-size: 10px;position: absolute;margin-top: -3px;margin-left: 32px;">(Indisponível)</span>');
   } else {
      // $j('.navigation-tab .tablinks.ticket').css({
      //    'pointer-events': 'all',
      //    'background-color': '#fff'
      // }).find('span').remove();
   }
}

function openModal(modalId) {
   console.log(jQuery('#' + modalId).html());
   jQuery('#' + modalId).fadeIn();
   // document.getElementById(modalId).style.display = "block";
}

function closeModal(modalId) {
   jQuery('#' + modalId).fadeOut();
   // document.getElementById(modalId).style.display = "none";
}