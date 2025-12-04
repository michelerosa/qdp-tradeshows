/**
 * QDP Tradeshows Manager - Admin Scripts
 */

(function ($) {
    'use strict';

    $(document).ready(function () {
        initDatepickers();
        initMediaUploader();
    });

    /**
     * Inizializza i datepicker
     */
    function initDatepickers() {
        $('.qdp-datepicker').datepicker({
            dateFormat: qdpTradeshows.dateFormat,
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            beforeShow: function (input, inst) {
                // Posiziona correttamente il datepicker
                setTimeout(function () {
                    inst.dpDiv.css({
                        zIndex: 999999
                    });
                }, 0);
            }
        });
    }

    /**
     * Inizializza il media uploader per le immagini
     */
    function initMediaUploader() {
        var mediaFrame;

        // Click su "Seleziona Logo"
        $(document).on('click', '.qdp-upload-image', function (e) {
            e.preventDefault();

            var $container = $(this).closest('.qdp-image-field');
            var $input = $container.find('.qdp-image-id');
            var $preview = $container.find('.qdp-image-preview');
            var $removeBtn = $container.find('.qdp-remove-image');

            // Se il frame esiste già, aprilo
            if (mediaFrame) {
                mediaFrame.open();
                return;
            }

            // Crea il media frame
            mediaFrame = wp.media({
                title: qdpTradeshows.selectImage,
                button: {
                    text: qdpTradeshows.useImage
                },
                multiple: false,
                library: {
                    type: 'image'
                }
            });

            // Quando un'immagine viene selezionata
            mediaFrame.on('select', function () {
                var attachment = mediaFrame.state().get('selection').first().toJSON();

                // Aggiorna input nascosto
                $input.val(attachment.id);

                // Aggiorna preview
                var imageUrl = attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url;
                $preview.html('<img src="' + imageUrl + '" alt="">').show();

                // Mostra bottone rimuovi
                $removeBtn.show();
            });

            mediaFrame.open();
        });

        // Click su "Rimuovi"
        $(document).on('click', '.qdp-remove-image', function (e) {
            e.preventDefault();

            var $container = $(this).closest('.qdp-image-field');
            var $input = $container.find('.qdp-image-id');
            var $preview = $container.find('.qdp-image-preview');

            // Pulisci input e preview
            $input.val('');
            $preview.empty().hide();

            // Nascondi bottone rimuovi
            $(this).hide();
        });
    }

})(jQuery);
