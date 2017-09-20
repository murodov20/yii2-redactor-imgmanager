/**
 * @copyright Mirjalol Murodov, murodov20
 * You can use this widget for both: commercial and open source projects
 */

/**
 * Setting image of widget
 * This function will load image url from server
 * @param inputId Html id of hidden input
 */
phSet = function (inputId) {
    if ($.type(inputId) !== "string")
        return;

    var field = $('#' + inputId);
    var imageId = field.val();
    if ($.type(imageId) === "string" && !isNaN(parseInt(imageId))) {
        imageId = parseInt(imageId);
    }
    var htmlId = inputId;
    var url = field.attr('srcByIdUrl');
    var isThumb = field.attr('hasThumb') === '1';
    var noImageSrc = field.attr('noImageSrc');
    var multi = field.attr('multi') === '1';

    var removee = $('#' + htmlId + 'Remove');
    if ($.isNumeric(imageId) && Math.floor(imageId) === imageId) {
        $.ajax({
            url: url,
            type: "GET",
            cache: true,
            data: {id: imageId},
            success: function (data) {
                var has = isThumb ? data.thumbSrc : data.src;
                $('#' + htmlId + 'Image').attr('src', has);
                $('#' + htmlId + 'Cover').attr('href', data.src);
                removee.attr('data-id', $('#' + htmlId + '').val());
                if (!multi) removee.css('display', 'block')
            },
            error: function () {
                $('#' + htmlId + '').val('');
                phSet(htmlId);
            }
        });
    } else {
        $('#' + htmlId + 'Image').attr('src', noImageSrc);
        $('#' + htmlId + 'Cover').attr('href', noImageSrc);
        removee.attr('data-id', '');
        if (!multi) {
            removee.css('display', 'none');
        } else {
            removee.css('visibility', 'visible')
        }

    }
};
jQuery(document).ready(function () {

    /**
     * Loader function, loads all images for hidden inputs
     */
    $('.hidden-widget-input').each(function () {
        phSet($(this).attr('id'));
    });

    var veno = function () {
        $('.venobox-pm').venobox({
            titleattr: 'data-caption',
            border: '3px',
            closeBackground: '#a94442',
            frameHeight: '500px',
            spinner: 'cube-grid'
        });
    };
    /**
     * Load images in modal
     */
    $(document).on('show.bs.modal', '.image-selector', function () {
        $(this).find('.modal-body').load($(this).attr('loadUrl'), function () {
            veno();
        });
    });


    $(document).on('imgmanagerload', '#redactor-manager-box', function () {
        veno();
    });

    /**
     * Remove or unset image
     */
    $(document).on('click', '.image-remover', function () {
        if ($(this).hasClass('multi-remover')) {
            $(this).parent().parent().remove();
        } else {
            var a = $(this).parent().find('>.hidden-widget-input');
            a.val('');
            phSet(a.attr('id'));
        }
    });

    /**
     * Callback function for setting hidden input value
     * This function will be set input with image id
     */
    $(document).on('click', '.image-selector .img-select-and-add', function () {
        var id = $(this).parents('.image-selector').attr('select-for');
        $('#' + id).val($(this).data('id')).trigger('change');
        $('#' + id + 'Modal').modal('hide');
    });

    /**
     * Changes placeholder image when changed hidden input value
     */
    $(document).on('change', '.hidden-widget-input', function () {
        phSet($(this).attr('id'));
    });

    /**
     * Use venobox to see image
     * Uses extension venobox
     */
    $('.venobox-piw').venobox({
        titleattr: 'data-caption',
        border: '3px',
        closeBackground: '#a94442',
        frameHeight: '500px',
        spinner: 'cube-grid'
    });

    /**
     * Adds image widget to multiple widget
     */
    $(document).on('click', '.multiple-photo .plus-abs', function () {
        var tar = $(this);
        $.ajax({
            url: $(this).attr('generateUrl'),
            type: "POST",
            cache: false,
            data: {
                l: $(this).attr('last'),
                nm: $(this).attr('sampleName'),
                id: $(this).attr('sampleId'),
                w: $(this).attr('imgWidth'),
                h: $(this).attr('imgHeight'),
                itemClass: $(this).attr('itemCssClass'),
                imgLoadUrl: $(this).attr('imageLoadUrl'),
                srcByIdUrl: $(this).attr('srcByIdUrl'),
                thumbOnly: $(this).attr('thumbOnly'),
                noImageSrc: $(this).attr('noImageSrc')
            },
            success: function (data) {
                tar.parent().parent().find('>.multiple-photo-items').prepend(data);
                var ii = parseInt(tar.attr('last'));
                if (!isNaN(ii)) {
                    tar.attr('last', ++ii);
                }
            },
            error: function (e) {
                console.log(e);
            }
        });
    });
});