<?php
/**
 * Rendering HTML delle card fiere
 */

namespace QDP\TradeshowsViewer;

class Renderer {

    private I18n $i18n;

    public function __construct() {
        $this->i18n = new I18n();
    }

    /**
     * Renderizza tutte le fiere
     *
     * @param array $tradeshows Array di fiere dall'API
     * @param string $lang Codice lingua
     * @return string HTML completo
     */
    public function render(array $tradeshows, string $lang): string {
        if (empty($tradeshows)) {
            return '';
        }

        $cards = '';
        foreach ($tradeshows as $tradeshow) {
            $cards .= $this->render_card($tradeshow, $lang);
        }

        return sprintf(
            '<div class="wp-block-greenshift-blocks-row gspb_row tradeshowContainer">
                <div class="gspb_row__content">
                    %s
                </div>
            </div>',
            $cards
        );
    }

    /**
     * Renderizza una singola card
     */
    private function render_card(array $tradeshow, string $lang): string {
        // Estrai dati
        $name = esc_html($tradeshow['name'] ?? '');
        $website = esc_url($tradeshow['website'] ?? '');
        $logo_url = esc_url($tradeshow['logo']['url'] ?? '');
        $start_date = $tradeshow['start_date'] ?? '';
        $end_date = $tradeshow['end_date'] ?? '';
        $location = esc_html($tradeshow['location'] ?? '');
        $city = esc_html($tradeshow['city'] ?? '');
        $country_code = $tradeshow['country'] ?? '';

        // Formatta date
        $formatted_dates = $this->i18n->format_date_range($start_date, $end_date, $lang);

        // Traduci nazione
        $country_name = $this->i18n->get_country_name($country_code, $lang);

        // Estrai dominio
        $domain = $this->extract_domain($tradeshow['website'] ?? '');

        // Genera HTML
        return sprintf(
            '<div class="wp-block-greenshift-blocks-row-column gspb_row__col--4 gspb_row__col--md-6 tradeshowCard">
                %s
                %s
                %s
            </div>',
            $this->render_image($logo_url, $website, $name),
            $this->render_date_row($formatted_dates),
            $this->render_info($name, $location, $city, $country_name, $website, $domain)
        );
    }

    /**
     * Renderizza sezione immagine
     */
    private function render_image(string $logo_url, string $website, string $alt): string {
        if (empty($logo_url)) {
            return '';
        }

        return sprintf(
            '<div class="wp-block-greenshift-blocks-image gspb_image tradeshowImage">
                <a href="%s" target="_blank" rel="noopener nofollow">
                    <img decoding="async" src="%s" alt="%s" loading="lazy">
                </a>
            </div>',
            $website,
            $logo_url,
            esc_attr($alt)
        );
    }

    /**
     * Renderizza riga data
     */
    private function render_date_row(string $formatted_dates): string {
        return sprintf(
            '<div class="wp-block-greenshift-blocks-container gspb_container tradeshowDateRow">
                <div class="wp-block-greenshift-blocks-iconbox gspb_iconBox tradeshowDateIcon">
                    <div class="gspb_iconBox__wrapper" style="display:inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="512" height="512">
                            <path d="M19,2H18V1a1,1,0,0,0-2,0V2H8V1A1,1,0,0,0,6,1V2H5A5.006,5.006,0,0,0,0,7V19a5.006,5.006,0,0,0,5,5H19a5.006,5.006,0,0,0,5-5V7A5.006,5.006,0,0,0,19,2ZM2,7A3,3,0,0,1,5,4H19a3,3,0,0,1,3,3V8H2ZM19,22H5a3,3,0,0,1-3-3V10H22v9A3,3,0,0,1,19,22Z"></path>
                            <circle cx="12" cy="15" r="1.5"></circle>
                            <circle cx="7" cy="15" r="1.5"></circle>
                            <circle cx="17" cy="15" r="1.5"></circle>
                        </svg>
                    </div>
                </div>
                <h2 class="gspb_heading tradeshowDate">%s</h2>
            </div>',
            esc_html($formatted_dates)
        );
    }

    /**
     * Renderizza sezione info
     */
    private function render_info(
        string $name,
        string $location,
        string $city,
        string $country_name,
        string $website,
        string $domain
    ): string {
        // Subtitle (location) - mostra solo se presente
        $subtitle_html = '';
        if (!empty($location)) {
            $subtitle_html = sprintf(
                '<div class="gspb_text tradeshowSubtitle"><strong>%s</strong></div>',
                esc_html($location)
            );
        }

        // Location (city + country)
        $location_html = '';
        if (!empty($city) && !empty($country_name)) {
            $location_html = sprintf(
                '<div class="gspb_text tradeshowLocation">%s (%s)</div>',
                esc_html($city),
                esc_html($country_name)
            );
        }

        return sprintf(
            '<div class="wp-block-greenshift-blocks-container gspb_container tradeshowInfo">
                <h1 class="gspb_heading tradeshowTitle" data-no-auto-translation>%s</h1>
                %s
                %s
                <div class="gspb_text tradeshowLink"><a href="%s" target="_blank" rel="noopener">%s</a></div>
            </div>',
            $name,
            $subtitle_html,
            $location_html,
            $website,
            esc_html($domain)
        );
    }

    /**
     * Estrae il dominio da un URL (senza www)
     */
    private function extract_domain(string $url): string {
        if (empty($url)) {
            return '';
        }

        $host = wp_parse_url($url, PHP_URL_HOST);

        if (!$host) {
            return $url;
        }

        // Rimuovi www.
        if (str_starts_with($host, 'www.')) {
            $host = substr($host, 4);
        }

        return $host;
    }
}
