<?php
/**
 * Classe principale del plugin - Orchestratore
 */

namespace QDP\Tradeshows;

use QDP\Tradeshows\PostTypes\Tradeshow;
use QDP\Tradeshows\Admin\Admin;
use QDP\Tradeshows\Api\RestController;

class Plugin {

    private static ?Plugin $instance = null;

    /**
     * Singleton pattern
     */
    public static function get_instance(): Plugin {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Costruttore privato
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Inizializza i componenti del plugin
     */
    private function init(): void {
        // Carica traduzioni
        add_action('init', [$this, 'load_textdomain']);

        // Registra Custom Post Type
        $tradeshow = new Tradeshow();
        $tradeshow->register();

        // Inizializza Admin
        if (is_admin()) {
            $admin = new Admin();
            $admin->register();
        }

        // Inizializza REST API
        $rest_controller = new RestController();
        $rest_controller->register();
    }

    /**
     * Carica il text domain per le traduzioni
     */
    public function load_textdomain(): void {
        load_plugin_textdomain(
            'qdp-tradeshows-manager',
            false,
            dirname(QDP_TRADESHOWS_BASENAME) . '/languages'
        );
    }
}
