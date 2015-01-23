$( document ).ready(function() {
    $('button[data-action=duplicate]').click(function(event) {
        var id = $(event.target).attr('data-id');
        $.post('/ajax', {id: id, action: 'duplicate-questionnaire'}, function() {
            location.reload();
        });
        $('button').prop('disabled', true);
    });
    $('button[data-action=delete]').click(function() {
        if (!confirm('Are you sure you want to delete this questionnaire?')) {
            return false;
        }
        var id = $(event.target).attr('data-id');
        $.post('/ajax', {id: id, action: 'delete-questionnaire'}, function() {
            location.reload();
        });
        $('button').prop('disabled', true);
    });
});
