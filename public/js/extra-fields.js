jQuery(document).ready(function($) {
    if ($('#billing_person_type').length) {
        $('#billing_cnpj_field, #billing_ie_field, #billing_company_field').hide();
    }

    handle_person_type( $('#billing_person_type') );

    $('#billing_person_type').change(function() {
       handle_person_type( $(this) );
    });

    function handle_person_type( elem ) {
        var selectedOption = elem.val();

        if (selectedOption === 'pf') {
            $('#billing_cpf_field, #billing_rg_field').show();
            $('#billing_cnpj_field, #billing_ie_field, #billing_company_field').hide();
        } else if (selectedOption === 'pj') {
            $('#billing_cpf_field, #billing_rg_field').hide();
            $('#billing_cnpj_field, #billing_ie_field, #billing_company_field').show();
        }
    }

    function applyMask(input, maskFunction, maxLength) {
        input.on('input', function() {
            var value = $(this).val();
            $(this).val(maskFunction(value).substr(0, maxLength));
        });
    }

    //TODO: erro XXX.XXX.XXXX-X
    function maskCPF(cpf) {
        cpf = cpf.replace(/\D/g, '');
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d)/, '$1.$2');
        cpf = cpf.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        return cpf;
    }

    function maskCNPJ(cnpj) {
        cnpj = cnpj.replace(/\D/g, '');
        cnpj = cnpj.replace(/^(\d{2})(\d)/, '$1.$2');
        cnpj = cnpj.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
        cnpj = cnpj.replace(/\.(\d{3})(\d)/, '.$1/$2');
        cnpj = cnpj.replace(/(\d{4})(\d)/, '$1-$2');
        return cnpj;
    }

    function maskRG(rg) {
        rg = rg.replace(/\D/g, '');
        rg = rg.replace(/(\d{2})(\d)/, '$1.$2');
        rg = rg.replace(/(\d{3})(\d)/, '$1.$2');
        rg = rg.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        return rg;
    }

    function maskCellphone(cellphone) {
        cellphone = cellphone.replace(/\D/g, '');
        cellphone = cellphone.replace(/(\d{2})(\d)/, '($1) $2');
        cellphone = cellphone.replace(/(\d{5})(\d)/, '$1-$2');
        return cellphone;
    }

    function maskDate(date) {
        date = date.replace(/\D/g, '');
        date = date.replace(/(\d{2})(\d)/, '$1/$2');
        date = date.replace(/(\d{2})(\d)/, '$1/$2');
        date = date.replace(/(\d{4})$/, '$1');
        return date;
    }

    function maskZipCode(zipcode) {
        zipcode = zipcode.replace(/\D/g, '');
        zipcode = zipcode.replace(/(\d{5})(\d)/, '$1-$2');
        return zipcode;
    }

    if (options.mask) {
        applyMask($('#billing_cpf'), maskCPF, 14); 
        applyMask($('#billing_cnpj'), maskCNPJ, 18); 
        applyMask($('#billing_rg'), maskRG, 12); 
        applyMask($('#billing_cellphone'), maskCellphone, 15);
        applyMask($('#billing_phone'), maskCellphone, 15);
        applyMask($('#billing_birthdate'), maskDate, 10); 
        applyMask($('#billing_postcode'), maskZipCode, 9);
        applyMask($('#shipping_postcode'), maskZipCode, 9);
    }

    function makeFieldRequired(fieldId) {
        var field = $(fieldId);
        var label = field.find('label');

        label.find('span.optional').remove();

        if (!label.find('.required').length) {
            label.append('<abbr class="required" title="obrigatório">*</abbr>');
        }
    }
    // TODO: numero é required mas neighborhood vai depender da config
    makeFieldRequired('#billing_cpf_field');
    makeFieldRequired('#billing_cnpj_field');
    makeFieldRequired('#billing_rg_field');
    makeFieldRequired('#billing_ie_field');
    makeFieldRequired('#billing_neighborhood_field');
    makeFieldRequired('#shipping_neighborhood_field');
    makeFieldRequired('#billing_number_field');
    makeFieldRequired('#shipping_number_field');
    makeFieldRequired('#billing_birthdate_field');
    makeFieldRequired('#billing_gender_field');
    if ( options.phone_required ) {
        makeFieldRequired('#billing_cellphone_field');
    }
});
