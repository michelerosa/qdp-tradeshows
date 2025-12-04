<?php
/**
 * Client per fetch dati dall'API del manager
 */

namespace QDP\TradeshowsViewer;

class ApiClient {

    private const TRANSIENT_KEY = 'qdp_tradeshows_data';
    private const TRANSIENT_LAST_UPDATED_KEY = 'qdp_tradeshows_last_updated';
    private const CACHE_DURATION = 3600; // 1 ora

    /**
     * Fetch dati dall'API con caching
     */
    public function fetch(): ?array {
        // Controlla cache
        $cached = get_transient(self::TRANSIENT_KEY);
        $cached_last_updated = get_transient(self::TRANSIENT_LAST_UPDATED_KEY);

        // Se abbiamo cache valida, verifica se è ancora aggiornata
        if ($cached !== false) {
            // Prova a fare un check leggero per vedere se ci sono aggiornamenti
            $fresh_data = $this->fetch_from_api();

            if ($fresh_data !== null) {
                $new_last_updated = $fresh_data['meta']['last_updated'] ?? null;

                // Se last_updated è cambiato, aggiorna cache
                if ($new_last_updated !== $cached_last_updated) {
                    $this->set_cache($fresh_data);
                    return $fresh_data;
                }
            }

            // Usa cache esistente
            return $cached;
        }

        // Nessuna cache, fetch diretto
        $data = $this->fetch_from_api();

        if ($data !== null) {
            $this->set_cache($data);
            return $data;
        }

        // Fallback: prova a usare cache scaduta se disponibile
        $expired_cache = get_option(self::TRANSIENT_KEY . '_backup');
        if ($expired_cache) {
            return $expired_cache;
        }

        return null;
    }

    /**
     * Fetch dati dall'API remota
     */
    private function fetch_from_api(): ?array {
        $response = wp_remote_get(QDP_TRADESHOWS_API_URL, [
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/json',
            ],
        ]);

        if (is_wp_error($response)) {
            error_log('QDP Tradeshows Viewer: API error - ' . $response->get_error_message());
            return null;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            error_log('QDP Tradeshows Viewer: API returned status ' . $status_code);
            return null;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log('QDP Tradeshows Viewer: Invalid JSON response');
            return null;
        }

        return $data;
    }

    /**
     * Salva dati in cache
     */
    private function set_cache(array $data): void {
        set_transient(self::TRANSIENT_KEY, $data, self::CACHE_DURATION);

        $last_updated = $data['meta']['last_updated'] ?? null;
        if ($last_updated) {
            set_transient(self::TRANSIENT_LAST_UPDATED_KEY, $last_updated, self::CACHE_DURATION);
        }

        // Backup per fallback
        update_option(self::TRANSIENT_KEY . '_backup', $data, false);
    }

    /**
     * Invalida la cache manualmente
     */
    public function invalidate_cache(): void {
        delete_transient(self::TRANSIENT_KEY);
        delete_transient(self::TRANSIENT_LAST_UPDATED_KEY);
    }
}
