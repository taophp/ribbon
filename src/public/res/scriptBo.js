/** @see https://stackoverflow.com/questions/2830542/prevent-double-submission-of-forms-in-jquery */
jQuery.fn.preventDoubleSubmission = function () {
    $(this).on('submit', function (e) {
        var $form = $(this);

        if ($form.data('submitted') === true) {
            e.preventDefault();
        } else {
            $form.data('submitted', true);
        }
    });
    return this;
};
$(function () {
    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'pickfiles', // you can pass an id...
        container: document.getElementById('uplcontainer'), // ... or DOM Element itself
        url: '../f',
        flash_swf_url: '../../vendor/moxiecode/plupload/js/Moxie.swf',
        silverlight_xap_url: '../../vendor/moxiecode/plupload/js/Moxie.xap',

        filters: {
            max_file_size: '10mb',
            mime_types: [
                {title: "Image files", extensions: "jpg,gif,png"},
            ]
        },

        init: {
            PostInit: function () {
                document.getElementById('filelist').innerHTML = '';

                document.getElementById('uploadfiles').onclick = function () {
                    uploader.start();
                    return false;
                };
            },

            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });
            },

            UploadProgress: function (up, file) {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            },

            Error: function (up, err) {
                document.getElementById('console').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            }
        }
    });

    uploader.init();
    
    $('form').preventDoubleSubmission();
    $('#more').click(function () {
        $('#moreActions').fadeToggle();
    });
    $('.close').click(function () {
        $('#moreActions').fadeToggle();
    });
    $('#moreActionsTabs > div').click(function () {
        $('#moreActionsTabs > div').removeClass('activ');
        $(this).addClass('activ');
        $('#moreActionsMain > div').hide();
        $('#' + $(this).attr('id').replace('Tab', 'Main')).fadeToggle();
    });
    $('.flashmsg.Success').fadeOut(2000);
    var content = $('#content').val();
    $('#content').val('');
    $('#content').focus();
    $('#content').val(content);
    $('#fileList li').click(function () {
        let txt = prompt('Text to linked to the file:');
        $('#content').val($('#content').val() + '[' + txt + '](' + $(this).text() + ')');
        $('#moreActions').hide();
    });
    $('#imgList li').click(function () {
        let txt = prompt('Text replacement for the image:');
        $('#content').val($('#content').val() + '![' + txt + '](' + $(this).text() + ')');
        $('#moreActions').hide();
    });
});
