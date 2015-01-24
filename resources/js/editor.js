$( document ).ready(function() {
    var id;

    $('button[data-action=duplicate]').click(function(event) {
        id = $(event.target).attr('data-id');
        $.post('/ajax', {id: id, action: 'duplicate-questionnaire'}, function() {
            location.reload();
        });
        $('button').prop('disabled', true);
    });
    $('button[data-action=delete]').click(function(event) {
        if (!confirm('Are you sure you want to delete this questionnaire?')) {
            return false;
        }
        id = $(event.target).attr('data-id');
        $.post('/ajax', {id: id, action: 'delete-questionnaire'}, function() {
            location.reload();
        });
        $('button').prop('disabled', true);
    });

    var hiddenId;
    if (hiddenId = $('#questionnaire-id')) {
        id = hiddenId.val();
    }

    $('#save-intro-text').click(function(event) {
        var text = $('#intro-text-editor').val();
        $(event.target).prop('disabled', true).text('Saving...');
        $.post('/ajax', {id: id, action: 'update-intro-text', text: text}, function() {
            $(event.target).prop('disabled', false).text('Save');
        });
    })

});
