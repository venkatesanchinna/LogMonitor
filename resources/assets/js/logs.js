var handleErrorLog = function() {
    handleErrorLogInit = function handleErrorLogInit() {
        var deleteLogModal = $('div#delete-log-modal'),
            deleteLogForm = $('form#delete-log-form'),
            submitBtn = deleteLogForm.find('button[type=submit]');
        $("a[href='#delete-log-modal']").on('click', function(event) {
            event.preventDefault();
            var date = $(this).data('log-date'),
                message = $(this).data('log_confirm_message');
            deleteLogForm.find('input[name=date]').val(date);
            deleteLogModal.find('.modal-body p').html(message.replace(':date', date));
            deleteLogModal.modal('show');
        });
        deleteLogForm.on('submit', function(event) {
            event.preventDefault();
            submitBtn.button('loading');
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                dataType: 'json',
                data: $(this).serialize(),
                success: function(data) {
                    submitBtn.button('reset');
                    if (data.result === 'success') {
                        deleteLogModal.modal('hide');
                        location.reload();
                    } else {
                        alert('AJAX ERROR ! Check the console !');
                        console.error(data);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    alert('AJAX ERROR ! Check the console !');
                    console.error(errorThrown);
                    submitBtn.button('reset');
                }
            });
            return false;
        });
        deleteLogModal.on('hidden.bs.modal', function() {
            deleteLogForm.find('input[name=date]').val('');
            deleteLogModal.find('.modal-body p').html('');
        });
        var deleteLogModalView = $('div#delete-log-modal-view'),
            deleteLogFormView = $('form#delete-log-form-view'),
            submitBtnView = deleteLogForm.find('button[type=submit]');
        deleteLogFormView.on('submit', function(event) {
            var redirect = $(this).attr('redirect')
            event.preventDefault();
            submitBtnView.button('loading');
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                dataType: 'json',
                data: $(this).serialize(),
                success: function(data) {
                    submitBtn.button('reset');
                    deleteLogModalView.modal('hide');
                    location.replace(redirect);
                },
                error: function(xhr, textStatus, errorThrown) {
                    alert('AJAX ERROR ! Check the console !');
                    console.error(errorThrown);
                    submitBtnView.button('reset');
                }
            });
            return false;
        });
        $(".delete_log").on('click', function(event) {
            event.preventDefault();
            $("#is_tenant").val($(this).data('is_tenant'))
        });
    }
    return {
        init: function() {
            handleErrorLogInit()
        }
    };
}();
jQuery(document).ready(function() {
    handleErrorLog.init();
});