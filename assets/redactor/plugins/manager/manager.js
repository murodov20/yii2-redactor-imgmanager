(function ($) {
    $.Redactor.prototype.manager = function () {
        return {
            init: function () {
                if (!this.opts.managerUrl) return;
                $(document).on('click', '#redactor-manager-box .img-select-and-add',
                    this.manager.insert);
                this.modal.addCallback('image', this.manager.load);
            },
            load: function () {
                var $modal = this.modal.getModal();

                this.modal.createTabber($modal);
                this.modal.addTab(1, 'Загрузить', 'active');
                this.modal.addTab(2, 'Выбрать из сeрвeра');

                $('#redactor-modal-image-droparea').addClass('redactor-tab redactor-tab1');

                var $box = $('<div id="redactor-manager-box" style="overflow: auto; height: 350px; " class="redactor-tab redactor-tab2">').hide();
                $modal.append($box);

                $.ajax({
                    cache: false,
                    url: this.opts.managerUrl,
                    success: $.proxy(function (data) {
                        $('#redactor-manager-box').html(data);
                        $('.venobox-pm').venobox({
                            titleattr: 'data-caption',
                            border: '3px',
                            closeBackground: '#a94442',
                            frameHeight: '500px',
                            spinner: 'cube-grid'
                        });
                    }, this)
                });


            },
            insert: function (e) {
                console.log(e.target);
                this.image.insert('<img class="img-responsive" src="' + $(e.target).data('src') + '" alt="' + $(e.target).data('alt') + '" data-thumb="' + $(e.target).data('thumb') + '" title="' + $(e.target).data('alt') + '" id="image-redactor-' + $(e.target).data('id') + '" />');
            }
        };
    };
})(jQuery);