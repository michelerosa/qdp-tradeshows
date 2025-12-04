<?php
/**
 * Setup Admin
 */

namespace QDP\Tradeshows\Admin;

use QDP\Tradeshows\PostTypes\Tradeshow;

class Admin {

    private Metaboxes $metaboxes;
    private Columns $columns;

    public function __construct() {
        $this->metaboxes = new Metaboxes();
        $this->columns = new Columns();
    }

    /**
     * Registra gli hooks admin
     */
    public function register(): void {
        $this->metaboxes->register();
        $this->columns->register();

        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Carica CSS e JS per l'admin
     */
    public function enqueue_assets(string $hook): void {
        global $post_type;

        // Carica solo nelle pagine del nostro CPT
        if ($post_type !== Tradeshow::POST_TYPE) {
            return;
        }

        // WordPress datepicker
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style(
            'jquery-ui-datepicker-style',
            'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css',
            [],
            '1.13.2'
        );

        // Media uploader
        wp_enqueue_media();

        // CSS Admin
        wp_enqueue_style(
            'qdp-tradeshows-admin',
            QDP_TRADESHOWS_URL . 'assets/css/admin.css',
            [],
            QDP_TRADESHOWS_VERSION
        );

        // JS Admin
        wp_enqueue_script(
            'qdp-tradeshows-admin',
            QDP_TRADESHOWS_URL . 'assets/js/admin.js',
            ['jquery', 'jquery-ui-datepicker'],
            QDP_TRADESHOWS_VERSION,
            true
        );

        // Localizzazione per JS
        wp_localize_script('qdp-tradeshows-admin', 'qdpTradeshows', [
            'selectImage' => __('Seleziona Logo', 'qdp-tradeshows-manager'),
            'useImage'    => __('Usa questo logo', 'qdp-tradeshows-manager'),
            'removeImage' => __('Rimuovi', 'qdp-tradeshows-manager'),
            'dateFormat'  => 'yy-mm-dd',
        ]);
    }
}
