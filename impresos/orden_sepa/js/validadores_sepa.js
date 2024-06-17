 
$("#sepa").validate({
    rules: {
        bic: {
            //required: true,
            bic:true
        },
        iban: {
            required: true,
            iban: true
        },
        firma:{
        required: true
        },
        titular_cuenta: {
        required: true
        }
    },
    messages: {
        bic: {
            //required: "Complete el campo",
            bic:"El código BIC no es válido.<br>Si no lo sabe déjelo en blanco."
        },
        iban: {
            required: "Complete el campo",
            iban:"El IBAN no es válido"
        },
        firma:{
        required: "Falta firma para la orden SEPA"
        },
        titular_cuenta: {
        required: "Se necesita el titular de la cuenta asociada al IBAN."
        }
    },
    errorPlacement: function(error, element) {
        $(element).prev($('.errorTxt')).html(error);
    }
});

 









