function reloadPage() {
    location.reload();
}

$( document ).ready(function() {
    $('button[data-action=change-password]').click(function(event) {
        var username = $(event.target).parents('tr').attr('data-username');
        var name = $(event.target).parents('tr').children('td')[1].innerText;
        var password = prompt('Enter a new password for '+name+' (leave blank to use the username as the password)');
        if (password === null) {
            return false;
        }
        $.post('/ajax', {username: username, action: 'change-password', password: password}, function(data) {
            alert('Password successfully changed.');
        });
    });

    $('button[data-action=delete-user]').click(function(event) {
        var username = $(event.target).parents('tr').attr('data-username');
        var name = $(event.target).parents('tr').children('td')[1].innerText;
        if (!confirm('Are you sure you want to delete '+name+'?')) {
            return false;
        }
        $('button').prop('disabled', true);
        $.post('/ajax', {username: username, action: 'delete-user'}, reloadPage);
    });
});
