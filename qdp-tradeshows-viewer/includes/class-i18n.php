<?php
/**
 * Gestione internazionalizzazione per date e nazioni
 */

namespace QDP\TradeshowsViewer;

class I18n {

    /**
     * Lingue supportate
     */
    private const SUPPORTED_LANGS = ['it', 'en', 'fr', 'de', 'es'];

    /**
     * Mesi abbreviati per lingua
     */
    private const MONTHS = [
        'it' => ['Gen', 'Feb', 'Mar', 'Apr', 'Mag', 'Giu', 'Lug', 'Ago', 'Set', 'Ott', 'Nov', 'Dic'],
        'en' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        'fr' => ['jan.', 'fév.', 'mar.', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sep.', 'oct.', 'nov.', 'déc.'],
        'de' => ['Jan', 'Feb', 'Mär', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dez'],
        'es' => ['ene.', 'feb.', 'mar.', 'abr.', 'may.', 'jun.', 'jul.', 'ago.', 'sep.', 'oct.', 'nov.', 'dic.'],
    ];

    /**
     * Formatta una data in base alla lingua
     *
     * @param string $date Data in formato Y-m-d
     * @param string $lang Codice lingua (it, en, fr, de, es)
     * @return string Data formattata
     */
    public function format_date(string $date, string $lang): string {
        $lang = $this->normalize_lang($lang);
        $timestamp = strtotime($date);

        if ($timestamp === false) {
            return $date;
        }

        $day = date('j', $timestamp);
        $month_index = (int) date('n', $timestamp) - 1;
        $year = date('Y', $timestamp);
        $month = self::MONTHS[$lang][$month_index] ?? self::MONTHS['en'][$month_index];

        // Formato in base alla lingua
        return match ($lang) {
            'en' => "{$month} {$day}, {$year}",
            'de' => "{$day}. {$month} {$year}",
            default => "{$day} {$month} {$year}",
        };
    }

    /**
     * Formatta un range di date
     *
     * @param string $start_date Data inizio (Y-m-d)
     * @param string $end_date Data fine (Y-m-d)
     * @param string $lang Codice lingua
     * @return string Range formattato
     */
    public function format_date_range(string $start_date, string $end_date, string $lang): string {
        $start = $this->format_date($start_date, $lang);
        $end = $this->format_date($end_date, $lang);

        return "{$start} – {$end}";
    }

    /**
     * Ottiene il nome della nazione tradotto
     *
     * @param string $country_code Codice ISO 3166-1 alpha-2 (es: IT, DE)
     * @param string $lang Codice lingua per la traduzione
     * @return string Nome nazione tradotto
     */
    public function get_country_name(string $country_code, string $lang): string {
        $lang = $this->normalize_lang($lang);
        $country_code = strtoupper($country_code);

        // Usa Intl se disponibile
        if (class_exists('Locale')) {
            $locale = $this->get_full_locale($lang);
            $name = \Locale::getDisplayRegion("-{$country_code}", $locale);

            if ($name && $name !== $country_code) {
                return $name;
            }
        }

        // Fallback: dizionario interno per nazioni comuni
        return $this->get_country_fallback($country_code, $lang);
    }

    /**
     * Normalizza il codice lingua
     */
    private function normalize_lang(string $lang): string {
        $lang = strtolower(substr($lang, 0, 2));
        return in_array($lang, self::SUPPORTED_LANGS) ? $lang : 'en';
    }

    /**
     * Ottiene il locale completo per Intl
     */
    private function get_full_locale(string $lang): string {
        return match ($lang) {
            'it' => 'it_IT',
            'en' => 'en_US',
            'fr' => 'fr_FR',
            'de' => 'de_DE',
            'es' => 'es_ES',
            default => 'en_US',
        };
    }

    /**
     * Fallback per nazioni comuni se Intl non disponibile
     */
    private function get_country_fallback(string $code, string $lang): string {
        $countries = [
            'IT' => ['it' => 'Italia', 'en' => 'Italy', 'fr' => 'Italie', 'de' => 'Italien', 'es' => 'Italia'],
            'DE' => ['it' => 'Germania', 'en' => 'Germany', 'fr' => 'Allemagne', 'de' => 'Deutschland', 'es' => 'Alemania'],
            'FR' => ['it' => 'Francia', 'en' => 'France', 'fr' => 'France', 'de' => 'Frankreich', 'es' => 'Francia'],
            'ES' => ['it' => 'Spagna', 'en' => 'Spain', 'fr' => 'Espagne', 'de' => 'Spanien', 'es' => 'España'],
            'GB' => ['it' => 'Regno Unito', 'en' => 'United Kingdom', 'fr' => 'Royaume-Uni', 'de' => 'Vereinigtes Königreich', 'es' => 'Reino Unido'],
            'US' => ['it' => 'Stati Uniti', 'en' => 'United States', 'fr' => 'États-Unis', 'de' => 'Vereinigte Staaten', 'es' => 'Estados Unidos'],
            'CN' => ['it' => 'Cina', 'en' => 'China', 'fr' => 'Chine', 'de' => 'China', 'es' => 'China'],
            'JP' => ['it' => 'Giappone', 'en' => 'Japan', 'fr' => 'Japon', 'de' => 'Japan', 'es' => 'Japón'],
            'AE' => ['it' => 'Emirati Arabi Uniti', 'en' => 'United Arab Emirates', 'fr' => 'Émirats arabes unis', 'de' => 'Vereinigte Arabische Emirate', 'es' => 'Emiratos Árabes Unidos'],
            'CH' => ['it' => 'Svizzera', 'en' => 'Switzerland', 'fr' => 'Suisse', 'de' => 'Schweiz', 'es' => 'Suiza'],
            'AT' => ['it' => 'Austria', 'en' => 'Austria', 'fr' => 'Autriche', 'de' => 'Österreich', 'es' => 'Austria'],
            'NL' => ['it' => 'Paesi Bassi', 'en' => 'Netherlands', 'fr' => 'Pays-Bas', 'de' => 'Niederlande', 'es' => 'Países Bajos'],
            'BE' => ['it' => 'Belgio', 'en' => 'Belgium', 'fr' => 'Belgique', 'de' => 'Belgien', 'es' => 'Bélgica'],
            'PL' => ['it' => 'Polonia', 'en' => 'Poland', 'fr' => 'Pologne', 'de' => 'Polen', 'es' => 'Polonia'],
            'PT' => ['it' => 'Portogallo', 'en' => 'Portugal', 'fr' => 'Portugal', 'de' => 'Portugal', 'es' => 'Portugal'],
            'BR' => ['it' => 'Brasile', 'en' => 'Brazil', 'fr' => 'Brésil', 'de' => 'Brasilien', 'es' => 'Brasil'],
            'MX' => ['it' => 'Messico', 'en' => 'Mexico', 'fr' => 'Mexique', 'de' => 'Mexiko', 'es' => 'México'],
            'CA' => ['it' => 'Canada', 'en' => 'Canada', 'fr' => 'Canada', 'de' => 'Kanada', 'es' => 'Canadá'],
            'AU' => ['it' => 'Australia', 'en' => 'Australia', 'fr' => 'Australie', 'de' => 'Australien', 'es' => 'Australia'],
            'IN' => ['it' => 'India', 'en' => 'India', 'fr' => 'Inde', 'de' => 'Indien', 'es' => 'India'],
            'KR' => ['it' => 'Corea del Sud', 'en' => 'South Korea', 'fr' => 'Corée du Sud', 'de' => 'Südkorea', 'es' => 'Corea del Sur'],
            'SG' => ['it' => 'Singapore', 'en' => 'Singapore', 'fr' => 'Singapour', 'de' => 'Singapur', 'es' => 'Singapur'],
            'HK' => ['it' => 'Hong Kong', 'en' => 'Hong Kong', 'fr' => 'Hong Kong', 'de' => 'Hongkong', 'es' => 'Hong Kong'],
            'TR' => ['it' => 'Turchia', 'en' => 'Turkey', 'fr' => 'Turquie', 'de' => 'Türkei', 'es' => 'Turquía'],
            'RU' => ['it' => 'Russia', 'en' => 'Russia', 'fr' => 'Russie', 'de' => 'Russland', 'es' => 'Rusia'],
            'SA' => ['it' => 'Arabia Saudita', 'en' => 'Saudi Arabia', 'fr' => 'Arabie saoudite', 'de' => 'Saudi-Arabien', 'es' => 'Arabia Saudita'],
        ];

        return $countries[$code][$lang] ?? $countries[$code]['en'] ?? $code;
    }
}
