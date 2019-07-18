var message = $('#message').attr('class', 'formResult').text('').hide();


$('#registerForm').submit(function(evt) {
    evt.preventDefault();

    var formData = new FormData(this);

    message.attr('class', 'formResult').text('').hide();
    $.ajax({
        url: 'handleRegister.php',
        type: 'POST',
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            message.addClass(data.result).text(data.msg).show();

            if (data.result == "success") {
                setTimeout(function() {
                    window.location = "login.php";
                }, 1500);
            }
        },
        error: function(data) {
            message.addClass('error').text("Error").show();
        }
    })
})