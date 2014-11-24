$(function () {

	function submitHandler(event) {
		event.preventDefault();
				
		var form = $(this);
		
		// Clear the class of all the inputs
		$.each(form.find(".clearfix"), function(value){$(this).removeClass("success error warning")});
		$.each(form.find(".help-inline"), function(Value){$(this).remove()});

        var msg = $("#processing");
        msg.show();

        $("#save-config").attr("disabled", "disabled");

		$.post("verify.php", $(this).serialize(), function (data) {
			data = jQuery.parseJSON(data);
			
			var results = data.results;
			$.each(results, function(key, value) {
			
				var input = form.find("#"+key);
				var parent = input.parent();
				var clearfix = parent.parent();
				clearfix.addClass(value);
				
				input.after("<span class='help-inline'>" + data.details[key] + "</span>");
			});

			if (data.success) {
                $("#before-msg").hide();
                $("#save-config").hide();

                $("#after-msg").show();
                $("#refresh-page").show();

                msg.html("<strong>Complete!</strong>");
                msg.removeClass("info");
                msg.addClass("success");
			} else {
                msg.hide();
                $("#save-config").removeAttr("disabled");
			}
			
		});
	}

	$("form").submit(submitHandler);

	$('#refresh-page').click( function(e) {
		location.reload();
	});
	
});