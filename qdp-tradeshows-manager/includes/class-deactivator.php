<?php
/**
 * Gestisce la disattivazione del plugin
 */

namespace QDP\Tradeshows;

class Deactivator {

    /**
     * Eseguito alla disattivazione del plugin
     */
    public static function deactivate(): void {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
