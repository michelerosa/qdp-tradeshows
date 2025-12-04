<?php
/**
 * Plugin Name:       QDP Tradeshows Viewer
 * Plugin URI:        https://github.com/michelerosa/qdp-tradeshows
 * Description:       Visualizza le fiere di settore tramite shortcode, leggendo i dati dal sito master
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            QDP
 * License:           GPL v2 or later
 * Text Domain:       qdp-tradeshows-viewer
 * Domain Path:       /languages
 */

namespace QDP\TradeshowsViewer;

defined('ABSPATH') || exit;

// Costanti plugin
define('QDP_TRADESHOWS_VIEWER_VERSION', '1.0.0');
define('QDP_TRADESHOWS_VIEWER_PATH', plugin_dir_path(__FILE__));
define('QDP_TRADESHOWS_VIEWER_URL', plugin_dir_url(__FILE__));

// URL endpoint del sito master (MODIFICA QUESTO URL)
define('QDP_TRADESHOWS_API_URL', 'https://example.com/wp-json/qdp-tradeshows/v1/tradeshows');

// Carica le classi
require_once QDP_TRADESHOWS_VIEWER_PATH . 'includes/class-i18n.php';
require_once QDP_TRADESHOWS_VIEWER_PATH . 'includes/class-api-client.php';
require_once QDP_TRADESHOWS_VIEWER_PATH . 'includes/class-renderer.php';

/**
 * Registra lo shortcode
 */
function register_shortcode(): void {
    add_shortcode('qdp_tradeshows', __NAMESPACE__ . '\\render_shortcode');
}
add_action('init', __NAMESPACE__ . '\\register_shortcode');

/**
 * Renderizza lo shortcode
 */
function render_shortcode(array $atts = []): string {
    // Ottieni la lingua corrente
    $locale = determine_locale();
    $lang = substr($locale, 0, 2); // es: 'it_IT' -> 'it'

    // Fetch dati dall'API
    $api_client = new ApiClient();
    $data = $api_client->fetch();

    if (empty($data) || empty($data['data'])) {
        return '<!-- QDP Tradeshows: Nessuna fiera disponibile -->';
    }

    // Renderizza HTML
    $renderer = new Renderer();
    return $renderer->render($data['data'], $lang);
}
