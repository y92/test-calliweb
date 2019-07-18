$('a[data-action=deleteAddress]').click(function() {
    var id = $(this).attr('data-address');
    var title = $(this).attr('data-name');

    if (confirm("Are you sure you want to delete this address ("+title+") ?")) {
        var formData = new FormData();
        formData.append("id", id);

        $.ajax({
            url: "handleDeleteAddress.php",
            type : "POST",
            data : formData,
            dataType : "json",
            contentType: false,
            processData: false,
            success: function(data) {
                if (data.result == "success") {
                    document.location.reload();
                }
                else {
                    alert(data.msg);
                }
            },
            error: function(data) {
                alert("Error");
                console.error(data);
            }
        })
    }
});

$('a[data-action=createCSV]').click(function() {

    $.ajax({
        url: "handleCreateCSV.php",
        type: "POST",
        data: null,
        dataType: "json",
        contentType: false,
        processData: false,
        success: function(data) {
            console.log(data);
            if (data.result == "success") {
                $('#downloadCSV').attr('href', data.href).show();
                $(this).prop("disabled", true);
            }
            else {
                alert(data.msg);
            }
        },
        error: function(data) {
            alert("Error");
            console.error(data);
        }
    })
});