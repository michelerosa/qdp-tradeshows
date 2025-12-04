<?php
/**
 * Plugin Name:       QDP Tradeshows Manager
 * Plugin URI:        https://github.com/michelerosa/qdp-tradeshows
 * Description:       Gestione fiere di settore con REST API pubblica per siti satellite
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            QDP
 * License:           GPL v2 or later
 * Text Domain:       qdp-tradeshows-manager
 * Domain Path:       /languages
 */

namespace QDP\Tradeshows;

defined('ABSPATH') || exit;

// Costanti plugin
define('QDP_TRADESHOWS_VERSION', '1.0.0');
define('QDP_TRADESHOWS_PATH', plugin_dir_path(__FILE__));
define('QDP_TRADESHOWS_URL', plugin_dir_url(__FILE__));
define('QDP_TRADESHOWS_BASENAME', plugin_basename(__FILE__));

// Autoloader
require_once QDP_TRADESHOWS_PATH . 'includes/autoload.php';

/**
 * Inizializza il plugin
 */
function qdp_tradeshows_init(): Plugin {
    return Plugin::get_instance();
}
add_action('plugins_loaded', __NAMESPACE__ . '\\qdp_tradeshows_init');

// Activation/Deactivation hooks
register_activation_hook(__FILE__, [Activator::class, 'activate']);
register_deactivation_hook(__FILE__, [Deactivator::class, 'deactivate']);
