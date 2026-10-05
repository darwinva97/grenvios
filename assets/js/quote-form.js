/*
 * Envío por AJAX de los formularios de cotización.
 *
 * Atiende a los dos: el largo de /cotizar/ (#ajax_quote_form, con su caja de
 * mensajes fuera del <form>) y el cotizador rápido del hero de la portada
 * (#gr-hero-quote, que lleva la suya dentro, en .js-form-messages).
 */
$(function () {

    $('#ajax_quote_form, #gr-hero-quote').each(function () {
        var form = $(this);
        // La caja de mensajes propia del formulario; si no la tiene, la de /cotizar/.
        var formMessages = form.find('.js-form-messages');
        if (!formMessages.length) formMessages = $('#q-form-messages');
        formMessages.hide();

        var aviso = function (texto, ok) {
            formMessages
                .removeClass(ok ? 'alert-danger' : 'alert-success')
                .addClass(ok ? 'alert-success' : 'alert-danger')
                .text(texto)
                .show();
            setTimeout(function () { formMessages.hide(); }, 8000);
        };

        form.on('submit', function (event) {
            event.preventDefault();

            var boton = form.find('button[type="submit"]');
            boton.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize()
            })
            .done(function (response) {
                aviso(response, true);
                form[0].reset();
            })
            .fail(function (data) {
                aviso(
                    data.responseText ? data.responseText
                        : 'Ocurrió un error y no pudimos enviar tu solicitud.',
                    false
                );
            })
            .always(function () {
                boton.prop('disabled', false);
            });
        });
    });

});
