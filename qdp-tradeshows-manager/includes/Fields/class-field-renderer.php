<?php
/**
 * Rendering dei campi del form
 */

namespace QDP\Tradeshows\Fields;

class FieldRenderer {

    /**
     * Renderizza un campo
     */
    public function render(string $key, array $config, mixed $value, string $meta_key): void {
        $required = $config['required'] ? ' <span class="required">*</span>' : '';

        echo '<div class="qdp-field qdp-field-' . esc_attr($config['type']) . '">';
        echo '<label for="' . esc_attr($meta_key) . '">';
        echo esc_html($config['label']) . $required;
        echo '</label>';

        match ($config['type']) {
            'text'     => $this->render_text($meta_key, $value, $config),
            'url'      => $this->render_url($meta_key, $value, $config),
            'date'     => $this->render_date($meta_key, $value, $config),
            'select'   => $this->render_select($meta_key, $value, $config),
            'image'    => $this->render_image($meta_key, $value, $config),
            'textarea' => $this->render_textarea($meta_key, $value, $config),
            default    => $this->render_text($meta_key, $value, $config),
        };

        echo '</div>';
    }

    /**
     * Campo testo
     */
    private function render_text(string $name, mixed $value, array $config): void {
        printf(
            '<input type="text" id="%s" name="%s" value="%s" class="regular-text"%s>',
            esc_attr($name),
            esc_attr($name),
            esc_attr($value),
            $config['required'] ? ' required' : ''
        );
    }

    /**
     * Campo URL
     */
    private function render_url(string $name, mixed $value, array $config): void {
        printf(
            '<input type="url" id="%s" name="%s" value="%s" class="regular-text" placeholder="https://"%s>',
            esc_attr($name),
            esc_attr($name),
            esc_url($value),
            $config['required'] ? ' required' : ''
        );
    }

    /**
     * Campo data con datepicker
     */
    private function render_date(string $name, mixed $value, array $config): void {
        printf(
            '<input type="text" id="%s" name="%s" value="%s" class="qdp-datepicker" autocomplete="off"%s>',
            esc_attr($name),
            esc_attr($name),
            esc_attr($value),
            $config['required'] ? ' required' : ''
        );
    }

    /**
     * Campo immagine con media uploader
     */
    private function render_image(string $name, mixed $value, array $config): void {
        $image_url = '';
        if ($value) {
            $image_url = wp_get_attachment_image_url($value, 'thumbnail');
        }

        echo '<div class="qdp-image-field" data-name="' . esc_attr($name) . '">';

        // Preview immagine
        echo '<div class="qdp-image-preview"' . ($image_url ? '' : ' style="display:none;"') . '>';
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" alt="">';
        }
        echo '</div>';

        // Input nascosto per l'ID
        printf(
            '<input type="hidden" id="%s" name="%s" value="%s" class="qdp-image-id">',
            esc_attr($name),
            esc_attr($name),
            esc_attr($value)
        );

        // Bottoni
        echo '<div class="qdp-image-buttons">';
        echo '<button type="button" class="button qdp-upload-image">' . esc_html__('Seleziona Logo', 'qdp-tradeshows-manager') . '</button>';
        echo '<button type="button" class="button qdp-remove-image"' . ($image_url ? '' : ' style="display:none;"') . '>' . esc_html__('Rimuovi', 'qdp-tradeshows-manager') . '</button>';
        echo '</div>';

        echo '</div>';
    }

    /**
     * Campo textarea
     */
    private function render_textarea(string $name, mixed $value, array $config): void {
        printf(
            '<textarea id="%s" name="%s" class="large-text" rows="4"%s>%s</textarea>',
            esc_attr($name),
            esc_attr($name),
            $config['required'] ? ' required' : '',
            esc_textarea($value)
        );
    }

    /**
     * Campo select
     */
    private function render_select(string $name, mixed $value, array $config): void {
        $options = $config['options'] ?? [];

        printf(
            '<select id="%s" name="%s" class="regular-text"%s>',
            esc_attr($name),
            esc_attr($name),
            $config['required'] ? ' required' : ''
        );

        foreach ($options as $option_value => $option_label) {
            printf(
                '<option value="%s"%s>%s</option>',
                esc_attr($option_value),
                selected($value, $option_value, false),
                esc_html($option_label)
            );
        }

        echo '</select>';
    }
}
