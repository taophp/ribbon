$(function () {
    $("a[href^='http://']").click(function () {
        window.open(this.href);
        return false;
    });
    $("a[href^='https://']").click(function () {
        window.open(this.href);
        return false;
    });
    $('#searchbox').val('');
    $('.tag').click(function () {
        search = $('#searchbox').val();
        tag = $(this).text();
        if (search.includes(tag)) {
            $('#searchbox').val(search.replace(tag, ''));
        } else {
            $('#searchbox').val(search + ' ' + tag);
        }
        $('#searchbox').keyup();
    });
    if (getCookie('logged')) {
        $('.tool').show();
        $('.guestTool').hide();
    }
    $('.grid').masonry({
        itemSelector: 'article',
        columnWidth: 220
    });
});

$('#searchbox').keyup(function (e) {
    if (e.which === 13) {
        e.preventDefault();
        return;
    }
    searchWords = $('#searchbox').val().split(' ');
    $('article').each(function () {
        searchableString = $(this).attr('data-searchable').toLowerCase();
        found = true;
        for (i = 0; i < searchWords.length; i++) {
            if (!searchableString.includes(searchWords[i].toLowerCase())) {
                found = false;
                break;
            }
        }
        if (found === true) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    $('.daycontainer').each(function () {
        visible = false;
        $(this).find('article').each(function () {
            if ($(this).css('display') !== 'none') {
                visible = true;
            }
        });
        if (visible === true) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    $('.monthcontainer').each(function () {
        visible = false;
        $(this).find('.daycontainer').each(function () {
            if ($(this).css('display') !== 'none') {
                visible = true;
            }
        });
        if (visible === true) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    $('.yearcontainer').each(function () {
        visible = false;
        $(this).find('.monthcontainer').each(function () {
            if ($(this).css('display') !== 'none') {
                visible = true;
            }
        });
        if (visible === true) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    $('.grid').masonry('layout');
});

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

$(function () {
    var uploader = new plupload.Uploader({
        runtimes: 'html5,flash,silverlight,html4',
        browse_button: 'pickfiles', // you can pass an id...
        container: document.getElementById('container'), // ... or DOM Element itself
        url: 'upload.php',
        flash_swf_url: '../js/Moxie.swf',
        silverlight_xap_url: '../js/Moxie.xap',

        filters: {
            max_file_size: '10mb',
            mime_types: [
                {title: "Image files", extensions: "jpg,gif,png"},
                {title: "Zip files", extensions: "zip"}
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
});
