<?php
/**
 * Gestisce l'attivazione del plugin
 */

namespace QDP\Tradeshows;

use QDP\Tradeshows\PostTypes\Tradeshow;

class Activator {

    /**
     * Eseguito all'attivazione del plugin
     */
    public static function activate(): void {
        // Registra il CPT per flush delle rewrite rules
        $tradeshow = new Tradeshow();
        $tradeshow->register_post_type();

        // Flush rewrite rules
        flush_rewrite_rules();

        // Salva la versione del plugin
        update_option('qdp_tradeshows_version', QDP_TRADESHOWS_VERSION);
    }
}
