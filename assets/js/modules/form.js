$(function() {

    // Get the form
    var form = $('#contact');

    $(form).submit(function(event){

        // Serialize the form data and store the ID of the submitted form
        var formData = $(form).serialize(),
            id = $(form).attr('id');

        // Stop the browser from submitting the form.
        event.preventDefault();

        $.ajax({
            type: "POST",
            // Get the URL we're submitting to from the current form
            url: $(form).attr('action'),
            data: formData
        }).done(function(data) {
            // Find the form in perch's response (based on the id specified above)
            var newForm = $(data).find('#' + id);
            // Replace the existing form with the response from the server
            $(form).replaceWith(newForm);
        });
    });

});