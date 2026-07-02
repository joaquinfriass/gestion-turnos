(function ($) {
    'use strict';

    $(function () {
        $(document).on('click', '.js-marcar-atendido', function () {
            var button = $(this);
            var row = button.closest('tr');
            var idTurno = button.data('turno-id');

            button.prop('disabled', true);

            $.post('index.php?action=medico_marcar_atendido', { id_turno: idTurno, csrf_token: button.data('csrf-token') }, null, 'json')
                .done(function (response) {
                    if (!response.ok) {
                        button.prop('disabled', false);
                        alert(response.message || 'No se pudo actualizar el turno.');
                        return;
                    }

                    row.find('.js-estado-turno')
                        .removeClass('text-bg-warning text-bg-success text-bg-danger text-bg-secondary')
                        .addClass('text-bg-secondary')
                        .text('atendido');

                    button.replaceWith('<span class="text-secondary small">Atendido</span>');
                })
                .fail(function () {
                    button.prop('disabled', false);
                    alert('No se pudo actualizar el turno.');
                });
        });
    });
})(jQuery);
