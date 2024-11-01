jQuery(document).ready(function($) {
    $(document).on('keyup', '#calc_shipping_postcode', function(){
        var cep = $(this).val();
        cep = cep.replace(/\D/g, '');

        if (cep.length > 5) {
            cep = cep.substr(0, 5) + '-' + cep.substr(5, 3);

            if (typeof mask.stateHidden !== 'undefined') {
                var state = get_state_from_cep( cep );
                if ( state ) {
                    $('#calc_shipping_state').val( state );
                    $('#calc_shipping_city').val('');
                } else {
                    $('.woocommerce-cart p#calc_shipping_state_field,'
                        + '.woocommerce-cart #calc_shipping_city_field,'
                        + '.woocommerce-cart #calc_shipping_country_field').attr('style', 'display: block!important');
                }
            }
        }

        if (cep.length > 9) {
            cep = cep.substr(0, 9);
        }
        $(this).val(cep);
    });
});

function get_state_from_cep( $cep ) {
    let range_ceps = {
        '01': 'SP',
        '02': 'SP',
        '03': 'SP',
        '04': 'SP',
        '05': 'SP',
        '06': 'SP',
        '07': 'SP',
        '08': 'SP',
        '09': 'SP',
        '10': 'SP',
        '11': 'SP',
        '12': 'SP',
        '13': 'SP',
        '14': 'SP',
        '15': 'SP',
        '16': 'SP',
        '17': 'SP',
        '18': 'SP',
        '19': 'SP',
        '20': 'RJ',
        '21': 'RJ',
        '22': 'RJ',
        '23': 'RJ',
        '24': 'RJ',
        '25': 'RJ',
        '26': 'RJ',
        '27': 'RJ',
        '28': 'RJ',
        '29': 'ES',
        '30': 'MG',
        '31': 'MG',
        '32': 'MG',
        '33': 'MG',
        '34': 'MG',
        '35': 'MG',
        '36': 'MG',
        '37': 'MG',
        '38': 'MG',
        '39': 'MG',
        '40': 'BA',
        '41': 'BA',
        '42': 'BA',
        '43': 'BA',
        '44': 'BA',
        '45': 'BA',
        '46': 'BA',
        '47': 'BA',
        '48': 'BA',
        '49': 'SE',
        '50': 'PE',
        '51': 'PE',
        '52': 'PE',
        '53': 'PE',
        '54': 'PE',
        '55': 'AL',
        '56': 'AL',
        '57': 'AL',
        '58': 'PB',
        '59': 'RN',
        '60': 'CE',
        '61': 'CE',
        '62': 'CE',
        '63': 'CE',
        '64': 'PI',
        '65': 'MA',
        '66': 'MA',
        '67': 'MA',
        '68': 'PA',
        '69': 'PA',
        '70': 'DF',
        '71': 'DF',
        '72': 'DF',
        '73': 'DF',
        '74': 'GO',
        '75': 'GO',
        '76': 'GO',
        '77': 'TO',
        '78': 'MT',
        '79': 'MS',
        '80': 'PR',
        '81': 'PR',
        '82': 'PR',
        '83': 'PR',
        '84': 'PR',
        '85': 'PR',
        '86': 'PR',
        '87': 'PR',
        '88': 'SC',
        '89': 'SC',
        '90': 'RS',
        '91': 'RS',
        '92': 'RS',
        '93': 'RS',
        '94': 'RS',
        '95': 'RS',
        '96': 'RS',
        '97': 'RS',
        '98': 'RS',
        '99': 'RS',
    };

    let state = false;

    if ( range_ceps[ $cep.substr( 0, 2 ) ] ) {
        state = range_ceps[ $cep.substr( 0, 2 ) ];
    }

    return state;
}