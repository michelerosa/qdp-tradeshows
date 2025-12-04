<?php
/**
 * Validazione dei campi
 */

namespace QDP\Tradeshows\Fields;

class FieldValidator {

    /**
     * Valida un valore in base alla configurazione
     *
     * @return bool|string True se valido, stringa di errore altrimenti
     */
    public function validate(mixed $value, array $config): bool|string {
        // Campo obbligatorio vuoto
        if ($config['required'] && $this->is_empty($value)) {
            return sprintf(
                __('Il campo "%s" è obbligatorio.', 'qdp-tradeshows-manager'),
                $config['label']
            );
        }

        // Validazioni specifiche per tipo (solo se il valore non è vuoto)
        if (!$this->is_empty($value)) {
            return match ($config['type']) {
                'url'    => $this->validate_url($value, $config),
                'date'   => $this->validate_date($value, $config),
                'select' => $this->validate_select($value, $config),
                'image'  => $this->validate_image($value, $config),
                default  => true,
            };
        }

        return true;
    }

    /**
     * Verifica se un valore è vuoto
     */
    private function is_empty(mixed $value): bool {
        return $value === '' || $value === null || $value === 0 || $value === '0';
    }

    /**
     * Valida un URL
     */
    private function validate_url(string $value, array $config): bool|string {
        if (!filter_var($value, FILTER_VALIDATE_URL)) {
            return sprintf(
                __('Il campo "%s" deve essere un URL valido.', 'qdp-tradeshows-manager'),
                $config['label']
            );
        }
        return true;
    }

    /**
     * Valida una data
     */
    private function validate_date(string $value, array $config): bool|string {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return sprintf(
                __('Il campo "%s" deve essere una data valida (YYYY-MM-DD).', 'qdp-tradeshows-manager'),
                $config['label']
            );
        }

        // Verifica che la data sia valida
        $parts = explode('-', $value);
        if (!checkdate((int) $parts[1], (int) $parts[2], (int) $parts[0])) {
            return sprintf(
                __('Il campo "%s" contiene una data non valida.', 'qdp-tradeshows-manager'),
                $config['label']
            );
        }

        return true;
    }

    /**
     * Valida un'immagine
     */
    private function validate_image(int $value, array $config): bool|string {
        if ($value && !wp_attachment_is_image($value)) {
            return sprintf(
                __('Il campo "%s" deve essere un\'immagine valida.', 'qdp-tradeshows-manager'),
                $config['label']
            );
        }
        return true;
    }

    /**
     * Valida una select (verifica che il valore sia tra le opzioni)
     */
    private function validate_select(string $value, array $config): bool|string {
        $options = $config['options'] ?? [];

        if (!array_key_exists($value, $options)) {
            return sprintf(
                __('Il campo "%s" contiene un valore non valido.', 'qdp-tradeshows-manager'),
                $config['label']
            );
        }

        return true;
    }
}
