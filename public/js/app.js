(function ($) {
    'use strict';

    function ensureDeleteModal() {
        if ($('#deleteConfirmModal').length) {
            return;
        }

        $('body').append(
            '<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">' +
                '<div class="modal-dialog modal-dialog-centered">' +
                    '<div class="modal-content">' +
                        '<div class="modal-header">' +
                            '<h5 class="modal-title">Confirmar eliminacion</h5>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>' +
                        '</div>' +
                        '<div class="modal-body">Esta accion no se puede deshacer.</div>' +
                        '<div class="modal-footer">' +
                            '<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>' +
                            '<button type="button" class="btn btn-danger" id="deleteConfirmButton">Eliminar</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );
    }

    function initDeleteModal() {
        var pendingForm = null;
        ensureDeleteModal();

        $(document).on('submit', 'form.js-delete-form', function (event) {
            event.preventDefault();
            pendingForm = this;
            bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteConfirmModal')).show();
        });

        $(document).on('click', '#deleteConfirmButton', function () {
            if (pendingForm) {
                pendingForm.submit();
            }
        });
    }

    function initLiveSearch() {
        $(document).on('input', '[data-live-search]', function () {
            var query = $(this).val().toLowerCase();
            var target = $(this).data('live-search');

            $(target).find('tbody tr').each(function () {
                var row = $(this);
                if (row.find('.empty-state').length) {
                    return;
                }

                row.toggle(row.text().toLowerCase().indexOf(query) !== -1);
            });
        });
    }

    function initValidation() {
        $(document).on('submit', 'form.js-validate', function (event) {
            var form = this;
            var invalid = false;

            $(form).find('[required]').each(function () {
                var field = $(this);
                var isEmpty = !String(field.val() || '').trim();
                field.toggleClass('is-invalid', isEmpty);
                invalid = invalid || isEmpty;
            });

            if (invalid) {
                event.preventDefault();
                $(form).find('.js-form-error').remove();
                $(form).prepend('<div class="alert alert-danger js-form-error">Completa los campos obligatorios.</div>');
            }
        });

        $(document).on('input change', '.is-invalid', function () {
            if (String($(this).val() || '').trim()) {
                $(this).removeClass('is-invalid');
            }
        });
    }

    $(function () {
        initDeleteModal();
        initLiveSearch();
        initValidation();
    });
})(jQuery);
