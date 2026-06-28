(function ($) {
    'use strict';

    function normalizarFecha(valor) {
        return valor || '';
    }

    function setEstado(form, tipo, mensaje) {
        var box = form.find('.js-horario-feedback');
        if (!box.length) {
            box = $('<div class="col-12"><div class="alert js-horario-feedback mb-0"></div></div>');
            form.find('.js-horario-anchor').after(box);
            box = box.find('.js-horario-feedback');
        }

        box
            .removeClass('alert-success alert-warning alert-danger')
            .addClass(tipo)
            .text(mensaje)
            .toggle(Boolean(mensaje));
    }

    function verificarHorario(form) {
        var medico = form.find('[name="id_medico"]').val();
        var fechaHora = normalizarFecha(form.find('[name="fecha_hora"]').val());
        var idExcluir = form.find('[name="id"]').val() || '';
        var submit = form.find('button[type="submit"]');

        if (!medico || !fechaHora) {
            submit.prop('disabled', false);
            setEstado(form, '', '');
            return;
        }

        $.getJSON('index.php', {
            action: 'ajax_turno_horario',
            id_medico: medico,
            fecha_hora: fechaHora,
            id_excluir: idExcluir
        }).done(function (response) {
            if (!response.ok) {
                submit.prop('disabled', false);
                setEstado(form, 'alert-warning', response.message || 'No se pudo verificar el horario.');
                return;
            }

            submit.prop('disabled', response.ocupado);
            setEstado(form, response.ocupado ? 'alert-danger' : 'alert-success', response.message);
        }).fail(function () {
            submit.prop('disabled', false);
            setEstado(form, 'alert-warning', 'No se pudo verificar disponibilidad.');
        });
    }

    $(function () {
        var form = $('form.js-turno-form');
        if (!form.length) {
            return;
        }

        form.on('change', '[name="id_medico"], [name="fecha_hora"]', function () {
            verificarHorario(form);
        });

        verificarHorario(form);
    });
})(jQuery);
