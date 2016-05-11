var ED = ED || {};

ED.CanvasEditor = (function($) {

    var defaultOptions = {
        debug: true,
        storeUrlStub: '/OphCiExamination/default/storeCanvasForEditing',
        downloadUrlRootStub: '/OphCiExamination/api/downloadCanvasForEditing/',
        uploadUrlRootStub: '/OphCiExamination/api/uploadEditedCanvas/',
        loadEditedUrlRootStub: '/OphCiExamination/default/downloadEditedCanvas/',
        baseUrl: 'http://openeyes',
        csrfToken: null,
        poll_interval: 5
    };

    function CanvasEditor(element, options)
    {
        this.element = element;
        this.options = $.extend(true, {}, defaultOptions, options);
        this.init();
    }

    CanvasEditor.prototype.init = function() {
        var self = this;

        var targetId = self.element.data('target-canvas');
        self.target = $('#'+targetId);
        if (!self.target.length) {
            self.error("target canvas not found.");
        }
        self.edBody = self.target.parents('.ed-body').find('.ed-editor-container');
        self.edToolbar = self.edBody.find('.ed-toolbar');

        self.storeUrl = self.options.baseUrl + self.options.storeUrlStub;
        self.downloadUrlRoot = self.options.baseUrl + self.options.downloadUrlRootStub;
        self.uploadUrlRoot = self.options.baseUrl + self.options.uploadUrlRootStub;
        self.loadEditedUrlRoot = self.options.baseUrl + self.options.loadEditedUrlRootStub;

        self.element.on('click', function(e) {
            e.preventDefault();
            self.captureImage();
        })
    };

    CanvasEditor.prototype.debug = function(msg)
    {
        if (this.options.debug) {
            console.log('CanvasEditor DEBUG:' + msg);
        }
    };

    CanvasEditor.prototype.error = function(msg)
    {
        console.log('CanvasEditor ERROR:' + msg);
        if (this.options.debug)
            debugger;
    };

    CanvasEditor.prototype.captureImage = function() {
        var self = this;
        self.element.prop('disabled', true);

        var image = new Image();
        image.src = self.target[0].toDataURL('image/png');

        var height = self.edToolbar.css('height');
        var width = self.edToolbar.css('width');

        self.qrContainer = self.edBody.append('<div class="qr-header" style="height:'+height+'; width: '+width+';">Please wait ...</div>').find('.qr-header');
        self.edToolbar.hide();
        self.edBody.find('.ed-editor').hide();

        $.ajax({
            cache: false,
            type: 'POST',
            data: {
                image: image.src,
                YII_CSRF_TOKEN: self.options.csrfToken
            },
            dataType: 'json',
            url: self.storeUrl,
            success: function (data) {
                qrData = {
                    download: self.downloadUrlRoot + '?uuid=' + data['uuid'],
                    upload: self.uploadUrlRoot + '?uuid=' + data['uuid']
                };

                self.debug(JSON.stringify(qrData));

                self.qrContainer.html("Please scan the QR Code below with your app.");
                self.qrContainer.qrcode({
                    size: 300,
                    color: "#3a3",
                    text: JSON.stringify(qrData)
                });

                self.startPolling(data['uuid']);
            },
            error: function (req, status, err) {
                var alert = new OpenEyes.UI.Dialog.Alert({
                    title: 'Service Unavailable',
                    content: "eyePad Draw is not available at the moment."});
                alert.on('close', function() {
                    self.cleanUp();
                });
                alert.open();

            }
        });
    };

    CanvasEditor.prototype.cleanUp = function()
    {
        var self = this;
        self.edToolbar.show();
        self.edBody.find('.ed-editor').show();
        self.qrContainer.remove();
        self.element.prop('disabled', false);
    };

    CanvasEditor.prototype.startPolling = function(uuid)
    {
        var self = this;
        self.debug('starting polling');
        self.interval = setInterval(function() {
            self.loadEditedCanvas(uuid);
        }, self.options.poll_interval * 1000)
    };

    /**
     * polls for the edited canvas image and replaces eyedraw canvas with the result
     *
     * @TODO: interface with eyedraw to manage this correctly
     * @param uuid
     */
    CanvasEditor.prototype.loadEditedCanvas = function(uuid)
    {
        var self = this;
        self.debug('loading from: ' + self.loadEditedUrlRoot);
        $.ajax({
            type: 'GET',
            data: {
                uuid: uuid
            },
            url: self.loadEditedUrlRoot,
            success: function(data, textStatus, xhr) {
                if (xhr.status == 204) {
                    // no content indicates that the request was fine, but the edited image is not ready yet
                    return;
                }
                else {
                    clearInterval(self.interval);
                    self.cleanUp();
                    var img = new Image(300,300);
                    img.src = 'data:image/png;base64,' + data;

                    var drawing = ED.getInstance(self.target.data('drawing-name'));

                    drawing.addDoodle('EyePadDrawing', {'image': img.src });

                }
            },
            error: function(xhr, ajaxOptions, thrownError) {

                var alert = new OpenEyes.UI.Dialog.Alert({
                    title: 'Service Unavailable',
                    content: "eyePad Draw is not available at the moment."});
                alert.on('close', function() {
                    self.cleanUp();
                });
                alert.open();
                clearInterval(self.interval);
                self.cleanUp();
            }
        });
    };

    $.fn.canvaseditor = function(options) {
        this.each(function() {
            var el = $(this);
            if (el.data('canvaseditor')) {
                return el.data('canvaseditor');
            }
            el.data('canvaseditor', new CanvasEditor(el, options));
        });
    };

    return CanvasEditor;
}(jQuery));
