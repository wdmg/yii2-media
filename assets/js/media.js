$(function() {

    var $uploads = $(".upload-wrapper");

    if ($uploads.length > 0) {
        $uploads.each(function (index, element) {

            var $dropZone = $(element);
            var $form = $dropZone.parent('form');
            var $input = $dropZone.find('input[type="file"]');

            var $list = $('<div class="upload-filelist" />');
            $dropZone.after($list);

            $dropZone.on("dragenter dragover", function(e) {
                e.preventDefault();
                e.stopPropagation();
                var target = $(e.currentTarget);
                $(target).addClass("has-drag");
            });

            $dropZone.on("dragleave", function(e) {
                e.preventDefault();
                e.stopPropagation();
                var target = $(e.currentTarget);
                $(target).removeClass("has-drag");
            });

            $dropZone.on("drop", function(e) {
                e.preventDefault();
                e.stopPropagation();

                var target = $(e.currentTarget);
                $(target).removeClass("has-drag");

                var files = e.originalEvent.dataTransfer.files;
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    addProgress($list, file);
                    uploadData($form, $input.attr('name'), file);
                }
            });

            $input.change(function() {
                var files = $(this)[0].files;
                for (var i = 0; i < files.length; i++) {
                    var file = files[i];
                    addProgress($list, file);
                    uploadData($form, $input.attr('name'), file);
                }
            });
        });
    }

    function addProgress(list, file) {
        var fileName = file.name;
        var mimeType = (file.type) ? file.type + ", " : "";
        var fileSize = convertSize(file.size);
        var className = getIconClass(file.type);
        $(list).append('<div data-upload="' + fileName + '">' +
            '<span class="icon icon-filetype icon-sm ' + className + '"></span> ' + fileName + ' <span class="text-muted">(' + mimeType + fileSize + ')</span>' +
            '<div class="progress" style="height:3px;">' +
                '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
            '</div>' +
        '</div>');
    }

    function updateProgress(index, value, status) {
        var $progressBar = $('[data-upload="' + index + '"]').find('.progress > .progress-bar');

        if (status == 'process')
            $progressBar.addClass('progress-bar-primary').addClass('progress-bar-striped');
        else if (status == 'success')
            $progressBar.addClass('progress-bar-success').removeClass('progress-bar-striped');
        else if (status == 'error')
            $progressBar.addClass('progress-bar-danger').removeClass('progress-bar-striped');
        else
            $progressBar.removeClass('progress-bar-striped');

        $progressBar.attr('aria-valuenow', parseInt(value));
        $progressBar.css('width', parseInt(value) + '%');
    }

    function createRequestObject() {
        var http;
        if (navigator.appName == "Microsoft Internet Explorer") {
            http = new ActiveXObject("Microsoft.XMLHTTP");
        }
        else {
            http = new XMLHttpRequest();
        }
        return http;
    }

    function uploadData(form, name, file, index) {

        var url = window.location.href;
        if ($(form).attr('action'))
            url = $(form).attr('action');

        var method = 'POST';
        if ($(form).attr('method'))
            method = $(form).attr('method');

        var formData = new FormData();
        if ($(form).get(0))
            formData = new FormData(form.get(0));

        if (url && method && form && formData && name && file) {


            var xhr = new createRequestObject();
            xhr.open(method, url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');

            xhr.upload.addEventListener("progress", function(e) {
                if (e.lengthComputable) {
                    if (e.loaded < e.total) {
                        updateProgress(file.name, (((e.loaded / e.total) * 100).toFixed(1)) || 100, 'process');
                    } else {
                        updateProgress(file.name, 100, 'success');
                    }
                }
            } , false);

            xhr.addEventListener('readystatechange', function(e) {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (typeof response[file.name] == "object") {
                        if (response[file.name].status == true)
                            updateProgress(file.name, 100, 'success');
                    } else {
                        updateProgress(file.name, 100, 'error');
                    }
                } else if (xhr.readyState == 4 && xhr.status != 200) {
                    updateProgress(file.name, 100, 'error');
                }
            });

            var exist = false;
            for (var pair of formData.entries()) {
                if (pair[0] === name)
                    exist = true;
            }

            if (exist) {
                formData.set(name, file);
            } else {
                formData.append(name, file);
            }

            if (typeof checkProgress !== "undefined") {
                clearInterval(checkProgress);
            }

            return xhr.send(formData);
        } else {
            return false;
        }
    }

    function getIconClass(mime) {
        switch(mime) {
            case 'image/bmp':
                return "icon-image-bmp";

            case 'image/gif':
                return "icon-image-gif";

            case 'image/jpeg':
                return "icon-image-jpeg";

            case 'image/png':
                return "icon-image-png";

            case 'image/svg':
                return "icon-image-svg";

            case 'image/tiff':
                return "icon-image-tiff";

            case 'image/webp':
                return "icon-image-webp";


            case 'video/avi':
                return "icon-video-avi";

            case 'video/mkv':
                return "icon-video-mkv";

            case 'video/mov':
                return "icon-video-mov";

            case 'video/mp4':
                return "icon-video-mp4";

            case 'video/mpeg':
                return "icon-video-mpeg";

            case 'video/ogv':
                return "icon-video-ogv";

            case 'video/webm':
                return "icon-video-webm";

            case 'video/wmv':
                return "icon-video-wmv";


            case 'audio/aac':
                return "icon-audio-aac";

            case 'audio/mka':
                return "icon-audio-mka";

            case 'audio/mp3':
                return "icon-audio-mp3";

            case 'audio/mp4':
                return "icon-audio-mp4";

            case 'audio/ogg':
                return "icon-audio-ogg";

            case 'audio/rm':
                return "icon-audio-rm";

            case 'audio/wav':
                return "icon-audio-wav";

            case 'audio/weba':
                return "icon-audio-weba";

            case 'audio/wma':
                return "icon-audio-wma";


            case 'document/csv':
                return "icon-document-csv";

            case 'document/doc':
                return "icon-document-doc";

            case 'document/json':
                return "icon-document-json";

            case 'document/odf':
                return "icon-document-odf";

            case 'document/odp':
                return "icon-document-odp";

            case 'document/ods':
                return "icon-document-ods";

            case 'document/pdf':
                return "icon-document-pdf";

            case 'document/ppt':
                return "icon-document-ppt";

            case 'document/rtf':
                return "icon-document-rtf";

            case 'document/txt':
                return "icon-document-txt";

            case 'document/xls':
                return "icon-document-xls";

            default:
                return "icon-unknown";
        }
    }

    function convertSize(size) {
        var sizes = ['Bytes', 'Kb', 'Mb', 'Gb', 'Tb'];
        if (size == 0) return '0 Byte';
        var i = parseInt(Math.floor(Math.log(size) / Math.log(1024)));
        return Math.round(size / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }
});
