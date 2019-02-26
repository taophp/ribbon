/* global plupload */

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
    var imguploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'pickimgs', // you can pass an id...
        container: document.getElementById('uplimgcontainer'), // ... or DOM Element itself
        url: '../i',
        flash_swf_url: '../../vendor/moxiecode/plupload/js/Moxie.swf',
        silverlight_xap_url: '../../vendor/moxiecode/plupload/js/Moxie.xap',

        filters: {
            mime_types: [
                {title: "Image files", extensions: "jpg,gif,png,svg,jpeg"}
            ]
        },

        init: {
            PostInit: function () {
                document.getElementById('uplimglist').innerHTML = '';

                document.getElementById('uploadimgs').onclick = function () {
                    imguploader.start();
                    return false;
                };
            },

            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    document.getElementById('uplimglist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });
            },

            UploadProgress: function (up, file) {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            },

            Error: function (up, err) {
                document.getElementById('imgconsole').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            },
            FileUploaded: function (up,file,result) {
                response = JSON.parse(result.response)
                if (response.OK === 1) {
                    $('#'+file.id).click(function(){
                        let txt = prompt('Text to linked to the image:');
                        $('#content').val($('#content').val() + '![' + txt + '](' + 'upload/' + file.name + ')');
                        $('#moreActions').hide();
                    });
                }
            }
        }
    });

    imguploader.init();

    var fileuploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'pickfiles', // you can pass an id...
        container: document.getElementById('uplfilecontainer'), // ... or DOM Element itself
        url: '../f',
        flash_swf_url: '../../vendor/moxiecode/plupload/js/Moxie.swf',
        silverlight_xap_url: '../../vendor/moxiecode/plupload/js/Moxie.xap',

        init: {
            PostInit: function () {
                document.getElementById('uplfilelist').innerHTML = '';

                document.getElementById('uploadfiles').onclick = function () {
                    fileuploader.start();
                    return false;
                };
            },

            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    document.getElementById('uplfilelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });
            },

            UploadProgress: function (up, file) {
                document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            },

            Error: function (up, err) {
                document.getElementById('fileconsole').appendChild(document.createTextNode("\nError #" + err.code + ": " + err.message));
            },
            FileUploaded: function (up,file,result) {
                response = JSON.parse(result.response)
                if (response.OK === 1) {
                    $('#'+file.id).click(function(){
                        let txt = prompt('Text to linked to the file:');
                        $('#content').val($('#content').val() + '[' + txt + '](' + 'upload/' + file.name + ')');
                        $('#moreActions').hide();
                    });
                }
            }
        }
    });

    fileuploader.init();
    
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
