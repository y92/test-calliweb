var message = $('#message').attr('class', 'formResult').text('').hide();


$('#loginForm').submit(function(evt) {
    evt.preventDefault();

    var formData = new FormData(this);

    message.attr('class', 'formResult').text('').hide();
    $.ajax({
        url: 'handleLogin.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            message.addClass(data.result).text(data.msg).show();

            if (data.result == "success") {
                setTimeout(function() {
                    window.location = ".";
                }, 1500);
            }
        },
        error: function(data) {
            message.addClass('error').text("Error").show();
            console.error(data);
        }
    })
})