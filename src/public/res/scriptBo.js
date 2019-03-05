/* global plupload */

/** @see https://stackoverflow.com/questions/7467840/nl2br-equivalent-in-javascript */
function nl2br (str, is_xhtml) {
    if (typeof str === 'undefined' || str === null) {
        return '';
    }
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    //return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag);
}

/** @see https://stackoverflow.com/questions/8062399/how-replace-html-br-with-newline-character-n */
function br2nl(str) {
    return str.replace(/<\s*\/?br>/ig, "\n");
}

function removeDiv(str) {
    return str.replace(/<\s*\/?div>/ig, "");
}

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

/** @see https://davidwalsh.name/convert-image-data-uri-javascript */
function getDataUri(url, callback) {
    var image = new Image();

    image.onload = function () {
        var canvas = document.createElement('canvas');
        canvas.width = this.naturalWidth; // or 'width' if you want a special/scaled size
        canvas.height = this.naturalHeight; // or 'height' if you want a special/scaled size

        canvas.getContext('2d').drawImage(this, 0, 0);

        // Get raw image data
        callback(canvas.toDataURL('image/png').replace(/^data:image\/(png|jpg);base64,/, ''));

        // ... or get as Data URI
        callback(canvas.toDataURL('image/png'));
    };

    image.src = url;
}

/** @see https://jsfiddle.net/Xeoncross/4tUDk/ */
function pasteHtmlAtCaret(html) {
    var sel, range;
    if (window.getSelection) {
        // IE9 and non-IE
        sel = window.getSelection();
        if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();

            // Range.createContextualFragment() would be useful here but is
            // non-standard and not supported in all browsers (IE9, for one)
            var el = document.createElement("div");
            el.innerHTML = html;
            var frag = document.createDocumentFragment(), node, lastNode;
            while ( (node = el.firstChild) ) {
                lastNode = frag.appendChild(node);
            }
            range.insertNode(frag);
            
            // Preserve the selection
            if (lastNode) {
                range = range.cloneRange();
                range.setStartAfter(lastNode);
                range.collapse(true);
                sel.removeAllRanges();
                sel.addRange(range);
            }
        }
    } else if (document.selection && document.selection.type != "Control") {
        // IE < 9
        document.selection.createRange().pasteHTML(html);
    }
}

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
    
    $('#contentPlus').height($('#content').height());
    $('#contentPlus').width($('#content').width());
    $('#content').hide();
    $('#contentPlus').focus();
    $('#contentPlus').html(nl2br($('#content').val()));
    $('#contentPlus').keypress(function (event) {
        if (event.keyCode === 10 || event.keyCode === 13) {
            event.preventDefault();
            pasteHtmlAtCaret('<br>');
        }
    });
    $('#contentPlus').on('focusout',function(){
        $('#content').val(removeDiv(br2nl($('#contentPlus').html())));
    });
    $('#contentPlus').pastableContenteditable();

   $('#contentPlus').on('pasteImage', function(ev, data){
        var blobUrl = URL.createObjectURL(data.blob);
        getDataUri(blobUrl,function(dataUri) {
            $('#contentPlus').focus();
            pasteHtmlAtCaret('->brut<br><img src="' + data.dataURL +'" ><br><-<br>');
            event.stopPropagation();
        });
      }).on('pasteImageError', function(ev, data){
        console.log('Oops: ' + data.message);
        if(data.url){
          console.log('But we got its url anyway:' + data.url)
        }
      }); 
});
