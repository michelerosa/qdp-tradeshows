<?php
/**
 * REST API Controller per le Fiere
 */

namespace QDP\Tradeshows\Api;

use QDP\Tradeshows\PostTypes\Tradeshow;
use WP_REST_Server;
use WP_REST_Request;
use WP_REST_Response;
use WP_Query;

class RestController {

    public const NAMESPACE = 'qdp-tradeshows/v1';
    public const ROUTE = '/tradeshows';

    /**
     * Registra gli hooks
     */
    public function register(): void {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Registra le routes REST
     */
    public function register_routes(): void {
        register_rest_route(
            self::NAMESPACE,
            self::ROUTE,
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => [$this, 'get_tradeshows'],
                'permission_callback' => '__return_true', // Endpoint pubblico
            ]
        );
    }

    /**
     * Restituisce le fiere future
     */
    public function get_tradeshows(WP_REST_Request $request): WP_REST_Response {
        $today = current_time('Y-m-d');

        $args = [
            'post_type'      => Tradeshow::POST_TYPE,
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_key'       => Tradeshow::META_START_DATE,
            'orderby'        => 'meta_value',
            'order'          => 'ASC',
            'meta_query'     => [
                [
                    'key'     => Tradeshow::META_END_DATE,
                    'value'   => $today,
                    'compare' => '>=',
                    'type'    => 'DATE',
                ],
            ],
        ];

        $query = new WP_Query($args);
        $tradeshows = [];

        foreach ($query->posts as $post) {
            $tradeshows[] = $this->prepare_item($post);
        }

        // Calcolo last_updated
        $last_updated = $this->get_last_updated();

        $response = new WP_REST_Response([
            'meta' => [
                'total'        => count($tradeshows),
                'last_updated' => $last_updated,
                'generated_at' => current_time('c'),
            ],
            'data' => $tradeshows,
        ]);

        // Header per caching HTTP
        $response->header('Last-Modified', gmdate('D, d M Y H:i:s', strtotime($last_updated)) . ' GMT');
        $response->header('Cache-Control', 'public, max-age=3600'); // 1 ora

        return $response;
    }

    /**
     * Prepara un singolo item per la risposta
     */
    private function prepare_item(\WP_Post $post): array {
        $logo_id = get_post_meta($post->ID, Tradeshow::META_LOGO_ID, true);

        return [
            'id'         => $post->ID,
            'name'       => $post->post_title,
            'slug'       => $post->post_name,
            'start_date' => get_post_meta($post->ID, Tradeshow::META_START_DATE, true),
            'end_date'   => get_post_meta($post->ID, Tradeshow::META_END_DATE, true),
            'website'    => get_post_meta($post->ID, Tradeshow::META_WEBSITE, true),
            'city'       => get_post_meta($post->ID, Tradeshow::META_CITY, true),
            'country'    => get_post_meta($post->ID, Tradeshow::META_COUNTRY, true),
            'location'   => get_post_meta($post->ID, Tradeshow::META_LOCATION, true) ?: null,
            'logo'       => $this->prepare_logo($logo_id),
            'modified'   => get_the_modified_date('c', $post),
        ];
    }

    /**
     * Prepara i dati del logo
     */
    private function prepare_logo(mixed $logo_id): ?array {
        if (!$logo_id) {
            return null;
        }

        $logo_id = (int) $logo_id;

        return [
            'id'        => $logo_id,
            'url'       => wp_get_attachment_url($logo_id) ?: null,
            'thumbnail' => wp_get_attachment_image_url($logo_id, 'thumbnail') ?: null,
            'medium'    => wp_get_attachment_image_url($logo_id, 'medium') ?: null,
        ];
    }

    /**
     * Calcola l'ultimo aggiornamento tra tutte le fiere
     */
    private function get_last_updated(): string {
        global $wpdb;

        $last_modified = $wpdb->get_var($wpdb->prepare(
            "SELECT MAX(post_modified_gmt)
             FROM {$wpdb->posts}
             WHERE post_type = %s
             AND post_status = 'publish'",
            Tradeshow::POST_TYPE
        ));

        if ($last_modified) {
            return gmdate('c', strtotime($last_modified));
        }

        return current_time('c');
    }
}
