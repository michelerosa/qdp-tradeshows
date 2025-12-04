<?php
/**
 * Colonne custom nella lista delle Fiere
 */

namespace QDP\Tradeshows\Admin;

use QDP\Tradeshows\PostTypes\Tradeshow;

class Columns {

    /**
     * Registra gli hooks
     */
    public function register(): void {
        add_filter('manage_' . Tradeshow::POST_TYPE . '_posts_columns', [$this, 'set_columns']);
        add_action('manage_' . Tradeshow::POST_TYPE . '_posts_custom_column', [$this, 'render_column'], 10, 2);
        add_filter('manage_edit-' . Tradeshow::POST_TYPE . '_sortable_columns', [$this, 'set_sortable_columns']);
        add_action('pre_get_posts', [$this, 'sort_by_start_date']);
    }

    /**
     * Definisce le colonne
     */
    public function set_columns(array $columns): array {
        $new_columns = [];

        // Checkbox
        if (isset($columns['cb'])) {
            $new_columns['cb'] = $columns['cb'];
        }

        // Logo
        $new_columns['logo'] = __('Logo', 'qdp-tradeshows-manager');

        // Titolo
        $new_columns['title'] = __('Nome', 'qdp-tradeshows-manager');

        // Date
        $new_columns['dates'] = __('Date', 'qdp-tradeshows-manager');

        // Sito web
        $new_columns['website'] = __('Sito Web', 'qdp-tradeshows-manager');

        // Città / Nazione
        $new_columns['city_country'] = __('Città / Nazione', 'qdp-tradeshows-manager');

        return $new_columns;
    }

    /**
     * Renderizza i valori delle colonne
     */
    public function render_column(string $column, int $post_id): void {
        switch ($column) {
            case 'logo':
                $logo_id = get_post_meta($post_id, Tradeshow::META_LOGO_ID, true);
                if ($logo_id) {
                    echo wp_get_attachment_image($logo_id, [50, 50], false, ['style' => 'border-radius: 4px;']);
                } else {
                    echo '<span class="dashicons dashicons-format-image" style="color: #ccc; font-size: 30px;"></span>';
                }
                break;

            case 'dates':
                $start = get_post_meta($post_id, Tradeshow::META_START_DATE, true);
                $end = get_post_meta($post_id, Tradeshow::META_END_DATE, true);
                if ($start && $end) {
                    $start_formatted = date_i18n(get_option('date_format'), strtotime($start));
                    $end_formatted = date_i18n(get_option('date_format'), strtotime($end));
                    echo esc_html($start_formatted . ' - ' . $end_formatted);
                } else {
                    echo '—';
                }
                break;

            case 'website':
                $website = get_post_meta($post_id, Tradeshow::META_WEBSITE, true);
                if ($website) {
                    $domain = wp_parse_url($website, PHP_URL_HOST);
                    echo '<a href="' . esc_url($website) . '" target="_blank" rel="noopener">' . esc_html($domain) . '</a>';
                } else {
                    echo '—';
                }
                break;

            case 'city_country':
                $city = get_post_meta($post_id, Tradeshow::META_CITY, true);
                $country_code = get_post_meta($post_id, Tradeshow::META_COUNTRY, true);
                if ($city && $country_code) {
                    $countries = Tradeshow::get_countries();
                    $country_name = $countries[$country_code] ?? $country_code;
                    echo esc_html($city . ', ' . $country_name);
                } else {
                    echo '—';
                }
                break;
        }
    }

    /**
     * Definisce le colonne ordinabili
     */
    public function set_sortable_columns(array $columns): array {
        $columns['dates'] = 'start_date';
        return $columns;
    }

    /**
     * Gestisce l'ordinamento per data inizio
     */
    public function sort_by_start_date(\WP_Query $query): void {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        if ($query->get('post_type') !== Tradeshow::POST_TYPE) {
            return;
        }

        // Ordinamento di default per data inizio
        if (!$query->get('orderby')) {
            $query->set('meta_key', Tradeshow::META_START_DATE);
            $query->set('orderby', 'meta_value');
            $query->set('order', 'ASC');
        }

        // Ordinamento custom quando cliccato sulla colonna
        if ($query->get('orderby') === 'start_date') {
            $query->set('meta_key', Tradeshow::META_START_DATE);
            $query->set('orderby', 'meta_value');
        }
    }
}
