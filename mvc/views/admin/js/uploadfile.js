
$(function () {
    
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = "/site/admin/subirarchivo";

    $('.fileupload').on('click', function ()
    {
        var progress = parseInt(0 / 1 * 100, 10);
        $('.progress .progress-bar').css(
                'width',
                progress + '%'
                );
    });

    $('.fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                // alert(file.name);

                // alert(file.error);
                // alert(file.lastModifiedDate);
                $("#photoname").val(file.name);
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('.progress .progress-bar').css(
                    'width',
                    progress + '%'
                    );
        }
    }).prop('disabled', !$.support.fileInput)
            .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
