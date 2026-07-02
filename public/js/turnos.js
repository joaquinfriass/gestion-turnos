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

    function initPacienteSelectFilter() {
        $(document).on('input', '.js-filter-select', function () {
            var input = $(this);
            var query = input.val().toLowerCase();
            var select = $(input.data('target'));

            select.find('option').each(function () {
                var option = $(this);
                if (!option.val()) {
                    option.prop('hidden', false);
                    return;
                }

                var searchable = option.data('search') || option.text();
                option.prop('hidden', String(searchable).toLowerCase().indexOf(query) === -1);
            });
        });
    }

    function initMedicoFilters() {
        function filtrar() {
            var especialidad = String($('#filtro_especialidad').val() || '').toLowerCase();
            var matricula = String($('#filtro_matricula').val() || '').toLowerCase();
            var select = $('#id_medico');

            select.find('option').each(function () {
                var option = $(this);
                if (!option.val()) {
                    option.prop('hidden', false);
                    return;
                }

                var matchEspecialidad = !especialidad || String(option.data('especialidad') || '').indexOf(especialidad) !== -1;
                var matchMatricula = !matricula || String(option.data('matricula') || '').indexOf(matricula) !== -1;
                var visible = matchEspecialidad && matchMatricula;

                option.prop('hidden', !visible);
                if (!visible && option.is(':selected')) {
                    select.val('');
                }
            });
        }

        $(document).on('input', '.js-filter-medicos', filtrar);
    }

    function initFechaPasadaValidation() {
        $(document).on('submit', 'form.js-turno-form', function (event) {
            var fecha = new Date($(this).find('[name="fecha_hora"]').val());

            if (fecha.toString() !== 'Invalid Date' && fecha.getTime() < Date.now()) {
                event.preventDefault();
                alert('No se puede seleccionar una fecha y hora pasada.');
            }
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
        initPacienteSelectFilter();
        initMedicoFilters();
        initFechaPasadaValidation();
    });
})(jQuery);
