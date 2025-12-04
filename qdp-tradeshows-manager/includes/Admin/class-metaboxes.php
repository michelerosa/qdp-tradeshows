<?php
/**
 * Gestione Metabox per le Fiere
 */

namespace QDP\Tradeshows\Admin;

use QDP\Tradeshows\PostTypes\Tradeshow;
use QDP\Tradeshows\Fields\FieldRenderer;
use QDP\Tradeshows\Fields\FieldSanitizer;
use QDP\Tradeshows\Fields\FieldValidator;

class Metaboxes {

    private FieldRenderer $renderer;
    private FieldSanitizer $sanitizer;
    private FieldValidator $validator;

    public function __construct() {
        $this->renderer = new FieldRenderer();
        $this->sanitizer = new FieldSanitizer();
        $this->validator = new FieldValidator();
    }

    /**
     * Registra gli hooks
     */
    public function register(): void {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_' . Tradeshow::POST_TYPE, [$this, 'save_meta'], 10, 2);
        add_action('admin_notices', [$this, 'display_validation_errors']);
    }

    /**
     * Aggiunge i metabox
     */
    public function add_meta_boxes(): void {
        add_meta_box(
            'qdp_tradeshow_details',
            __('Dettagli Fiera', 'qdp-tradeshows-manager'),
            [$this, 'render_details_metabox'],
            Tradeshow::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * Renderizza il metabox dei dettagli
     */
    public function render_details_metabox(\WP_Post $post): void {
        wp_nonce_field('qdp_tradeshow_save', 'qdp_tradeshow_nonce');

        $fields = Tradeshow::get_fields_config();

        echo '<div class="qdp-tradeshow-metabox">';

        foreach ($fields as $key => $config) {
            $meta_key = Tradeshow::META_PREFIX . $key;
            $value = get_post_meta($post->ID, $meta_key, true);
            $this->renderer->render($key, $config, $value, $meta_key);
        }

        echo '</div>';
    }

    /**
     * Salva i meta data
     */
    public function save_meta(int $post_id, \WP_Post $post): void {
        // Verifica nonce
        if (!isset($_POST['qdp_tradeshow_nonce']) ||
            !wp_verify_nonce($_POST['qdp_tradeshow_nonce'], 'qdp_tradeshow_save')) {
            return;
        }

        // Verifica autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Verifica permessi
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = Tradeshow::get_fields_config();
        $errors = [];

        foreach ($fields as $key => $config) {
            $meta_key = Tradeshow::META_PREFIX . $key;
            $raw_value = $_POST[$meta_key] ?? '';

            // Sanitizzazione
            $sanitized = $this->sanitizer->sanitize($raw_value, $config['type']);

            // Validazione
            $validation = $this->validator->validate($sanitized, $config);

            if ($validation !== true) {
                $errors[$key] = $validation;
                continue;
            }

            // Salvataggio
            if ($sanitized === '' || $sanitized === 0) {
                delete_post_meta($post_id, $meta_key);
            } else {
                update_post_meta($post_id, $meta_key, $sanitized);
            }
        }

        // Validazione date: data fine >= data inizio
        $start_date = $_POST[Tradeshow::META_START_DATE] ?? '';
        $end_date = $_POST[Tradeshow::META_END_DATE] ?? '';

        if (!empty($start_date) && !empty($end_date) && $end_date < $start_date) {
            $errors['end_date'] = __('La data di fine deve essere uguale o successiva alla data di inizio.', 'qdp-tradeshows-manager');
        }

        // Gestione errori di validazione
        if (!empty($errors)) {
            set_transient('qdp_tradeshow_errors_' . $post_id, $errors, 30);
        }
    }

    /**
     * Mostra gli errori di validazione
     */
    public function display_validation_errors(): void {
        global $post;

        if (!$post || $post->post_type !== Tradeshow::POST_TYPE) {
            return;
        }

        $errors = get_transient('qdp_tradeshow_errors_' . $post->ID);

        if (!$errors) {
            return;
        }

        delete_transient('qdp_tradeshow_errors_' . $post->ID);

        echo '<div class="notice notice-error is-dismissible">';
        echo '<p><strong>' . esc_html__('Errori di validazione:', 'qdp-tradeshows-manager') . '</strong></p>';
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}
