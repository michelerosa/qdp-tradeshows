<?php
/**
 * Pulizia dati alla disinstallazione del plugin
 */

// Verifica che sia una disinstallazione legittima
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

global $wpdb;

// Elimina tutti i post del CPT
$wpdb->query(
    $wpdb->prepare(
        "DELETE FROM {$wpdb->posts} WHERE post_type = %s",
        'qdp_tradeshow'
    )
);

// Elimina i meta orfani
$wpdb->query(
    "DELETE meta FROM {$wpdb->postmeta} meta
     LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id
     WHERE posts.ID IS NULL"
);

// Elimina le opzioni del plugin
delete_option('qdp_tradeshows_version');

// Pulisci i transient
$wpdb->query(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_qdp_tradeshow_%'"
);
$wpdb->query(
    "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_qdp_tradeshow_%'"
);

// Flush rewrite rules
flush_rewrite_rules();
