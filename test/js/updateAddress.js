var message = $('#message').attr('class', "formResult").text('').hide();

$('#updateAddressForm').submit(function(e) {

    e.preventDefault();
    message.attr('class', "formResult").text('').hide();

    var formData = new FormData(this);

    $.ajax({
        type: 'POST',
        url: 'handleUpdateAddress.php',
        data: formData,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function(data) {
            message.addClass(data.result).text(data.msg).show();

            if (data.result == "success") {
                setTimeout(function() {
                    window.location = "myAddresses.php";
                }, 1500);
            }
        },
        error: function(data) {
            console.error(data);
            message.addClass("error").text("Error").show();
        }
    })
});