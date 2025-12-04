<?php
/**
 * Sanitizzazione dei campi
 */

namespace QDP\Tradeshows\Fields;

class FieldSanitizer {

    /**
     * Sanitizza un valore in base al tipo
     */
    public function sanitize(mixed $value, string $type): mixed {
        return match ($type) {
            'text'     => sanitize_text_field($value),
            'textarea' => sanitize_textarea_field($value),
            'url'      => esc_url_raw($value),
            'email'    => sanitize_email($value),
            'date'     => $this->sanitize_date($value),
            'select'   => sanitize_text_field($value),
            'image'    => absint($value),
            'number'   => absint($value),
            default    => sanitize_text_field($value),
        };
    }

    /**
     * Sanitizza una data
     */
    private function sanitize_date(string $value): string {
        // Formato atteso: YYYY-MM-DD
        if (empty($value)) {
            return '';
        }

        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d', $timestamp) : '';
    }
}
