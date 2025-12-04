<?php
/**
 * Registrazione Custom Post Type per le Fiere
 */

namespace QDP\Tradeshows\PostTypes;

class Tradeshow {

    public const POST_TYPE = 'qdp_tradeshow';
    public const META_PREFIX = '_qdp_tradeshow_';

    // Meta keys
    public const META_START_DATE = self::META_PREFIX . 'start_date';
    public const META_END_DATE = self::META_PREFIX . 'end_date';
    public const META_WEBSITE = self::META_PREFIX . 'website';
    public const META_CITY = self::META_PREFIX . 'city';
    public const META_COUNTRY = self::META_PREFIX . 'country';
    public const META_LOCATION = self::META_PREFIX . 'location';
    public const META_LOGO_ID = self::META_PREFIX . 'logo_id';

    /**
     * Registra gli hooks
     */
    public function register(): void {
        add_action('init', [$this, 'register_post_type']);
    }

    /**
     * Registra il Custom Post Type
     */
    public function register_post_type(): void {
        $labels = [
            'name'                  => __('Fiere', 'qdp-tradeshows-manager'),
            'singular_name'         => __('Fiera', 'qdp-tradeshows-manager'),
            'add_new'               => __('Aggiungi Fiera', 'qdp-tradeshows-manager'),
            'add_new_item'          => __('Aggiungi Nuova Fiera', 'qdp-tradeshows-manager'),
            'edit_item'             => __('Modifica Fiera', 'qdp-tradeshows-manager'),
            'new_item'              => __('Nuova Fiera', 'qdp-tradeshows-manager'),
            'view_item'             => __('Visualizza Fiera', 'qdp-tradeshows-manager'),
            'search_items'          => __('Cerca Fiere', 'qdp-tradeshows-manager'),
            'not_found'             => __('Nessuna fiera trovata', 'qdp-tradeshows-manager'),
            'not_found_in_trash'    => __('Nessuna fiera nel cestino', 'qdp-tradeshows-manager'),
            'all_items'             => __('Tutte le Fiere', 'qdp-tradeshows-manager'),
            'menu_name'             => __('Fiere', 'qdp-tradeshows-manager'),
        ];

        $args = [
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_rest'        => true,
            'query_var'           => false,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-calendar-alt',
            'supports'            => ['title'],
        ];

        register_post_type(self::POST_TYPE, $args);
    }

    /**
     * Restituisce la configurazione dei campi
     */
    public static function get_fields_config(): array {
        return [
            'start_date' => [
                'type'     => 'date',
                'label'    => __('Data Inizio', 'qdp-tradeshows-manager'),
                'required' => true,
            ],
            'end_date' => [
                'type'     => 'date',
                'label'    => __('Data Fine', 'qdp-tradeshows-manager'),
                'required' => true,
            ],
            'website' => [
                'type'     => 'url',
                'label'    => __('Sito Web', 'qdp-tradeshows-manager'),
                'required' => true,
            ],
            'city' => [
                'type'     => 'text',
                'label'    => __('Città', 'qdp-tradeshows-manager'),
                'required' => true,
            ],
            'country' => [
                'type'     => 'select',
                'label'    => __('Nazione', 'qdp-tradeshows-manager'),
                'required' => true,
                'options'  => self::get_countries(),
            ],
            'location' => [
                'type'     => 'text',
                'label'    => __('Posizione', 'qdp-tradeshows-manager'),
                'required' => false,
            ],
            'logo_id' => [
                'type'     => 'image',
                'label'    => __('Logo Fiera', 'qdp-tradeshows-manager'),
                'required' => true,
            ],
        ];
    }

    /**
     * Restituisce la lista dei paesi con codice ISO 3166-1 alpha-2
     */
    public static function get_countries(): array {
        return [
            ''   => __('— Seleziona —', 'qdp-tradeshows-manager'),
            'AF' => 'Afghanistan',
            'AL' => 'Albania',
            'DZ' => 'Algeria',
            'AD' => 'Andorra',
            'AO' => 'Angola',
            'AG' => 'Antigua e Barbuda',
            'AR' => 'Argentina',
            'AM' => 'Armenia',
            'AU' => 'Australia',
            'AT' => 'Austria',
            'AZ' => 'Azerbaigian',
            'BS' => 'Bahamas',
            'BH' => 'Bahrein',
            'BD' => 'Bangladesh',
            'BB' => 'Barbados',
            'BY' => 'Bielorussia',
            'BE' => 'Belgio',
            'BZ' => 'Belize',
            'BJ' => 'Benin',
            'BT' => 'Bhutan',
            'BO' => 'Bolivia',
            'BA' => 'Bosnia ed Erzegovina',
            'BW' => 'Botswana',
            'BR' => 'Brasile',
            'BN' => 'Brunei',
            'BG' => 'Bulgaria',
            'BF' => 'Burkina Faso',
            'BI' => 'Burundi',
            'KH' => 'Cambogia',
            'CM' => 'Camerun',
            'CA' => 'Canada',
            'CV' => 'Capo Verde',
            'CF' => 'Repubblica Centrafricana',
            'TD' => 'Ciad',
            'CL' => 'Cile',
            'CN' => 'Cina',
            'CO' => 'Colombia',
            'KM' => 'Comore',
            'CG' => 'Congo',
            'CD' => 'RD Congo',
            'CR' => 'Costa Rica',
            'CI' => 'Costa d\'Avorio',
            'HR' => 'Croazia',
            'CU' => 'Cuba',
            'CY' => 'Cipro',
            'CZ' => 'Repubblica Ceca',
            'DK' => 'Danimarca',
            'DJ' => 'Gibuti',
            'DM' => 'Dominica',
            'DO' => 'Repubblica Dominicana',
            'EC' => 'Ecuador',
            'EG' => 'Egitto',
            'SV' => 'El Salvador',
            'GQ' => 'Guinea Equatoriale',
            'ER' => 'Eritrea',
            'EE' => 'Estonia',
            'SZ' => 'Eswatini',
            'ET' => 'Etiopia',
            'FJ' => 'Figi',
            'FI' => 'Finlandia',
            'FR' => 'Francia',
            'GA' => 'Gabon',
            'GM' => 'Gambia',
            'GE' => 'Georgia',
            'DE' => 'Germania',
            'GH' => 'Ghana',
            'GR' => 'Grecia',
            'GD' => 'Grenada',
            'GT' => 'Guatemala',
            'GN' => 'Guinea',
            'GW' => 'Guinea-Bissau',
            'GY' => 'Guyana',
            'HT' => 'Haiti',
            'HN' => 'Honduras',
            'HU' => 'Ungheria',
            'IS' => 'Islanda',
            'IN' => 'India',
            'ID' => 'Indonesia',
            'IR' => 'Iran',
            'IQ' => 'Iraq',
            'IE' => 'Irlanda',
            'IL' => 'Israele',
            'IT' => 'Italia',
            'JM' => 'Giamaica',
            'JP' => 'Giappone',
            'JO' => 'Giordania',
            'KZ' => 'Kazakistan',
            'KE' => 'Kenya',
            'KI' => 'Kiribati',
            'KP' => 'Corea del Nord',
            'KR' => 'Corea del Sud',
            'KW' => 'Kuwait',
            'KG' => 'Kirghizistan',
            'LA' => 'Laos',
            'LV' => 'Lettonia',
            'LB' => 'Libano',
            'LS' => 'Lesotho',
            'LR' => 'Liberia',
            'LY' => 'Libia',
            'LI' => 'Liechtenstein',
            'LT' => 'Lituania',
            'LU' => 'Lussemburgo',
            'MG' => 'Madagascar',
            'MW' => 'Malawi',
            'MY' => 'Malesia',
            'MV' => 'Maldive',
            'ML' => 'Mali',
            'MT' => 'Malta',
            'MH' => 'Isole Marshall',
            'MR' => 'Mauritania',
            'MU' => 'Mauritius',
            'MX' => 'Messico',
            'FM' => 'Micronesia',
            'MD' => 'Moldavia',
            'MC' => 'Monaco',
            'MN' => 'Mongolia',
            'ME' => 'Montenegro',
            'MA' => 'Marocco',
            'MZ' => 'Mozambico',
            'MM' => 'Myanmar',
            'NA' => 'Namibia',
            'NR' => 'Nauru',
            'NP' => 'Nepal',
            'NL' => 'Paesi Bassi',
            'NZ' => 'Nuova Zelanda',
            'NI' => 'Nicaragua',
            'NE' => 'Niger',
            'NG' => 'Nigeria',
            'MK' => 'Macedonia del Nord',
            'NO' => 'Norvegia',
            'OM' => 'Oman',
            'PK' => 'Pakistan',
            'PW' => 'Palau',
            'PS' => 'Palestina',
            'PA' => 'Panama',
            'PG' => 'Papua Nuova Guinea',
            'PY' => 'Paraguay',
            'PE' => 'Perù',
            'PH' => 'Filippine',
            'PL' => 'Polonia',
            'PT' => 'Portogallo',
            'QA' => 'Qatar',
            'RO' => 'Romania',
            'RU' => 'Russia',
            'RW' => 'Ruanda',
            'KN' => 'Saint Kitts e Nevis',
            'LC' => 'Santa Lucia',
            'VC' => 'Saint Vincent e Grenadine',
            'WS' => 'Samoa',
            'SM' => 'San Marino',
            'ST' => 'São Tomé e Príncipe',
            'SA' => 'Arabia Saudita',
            'SN' => 'Senegal',
            'RS' => 'Serbia',
            'SC' => 'Seychelles',
            'SL' => 'Sierra Leone',
            'SG' => 'Singapore',
            'SK' => 'Slovacchia',
            'SI' => 'Slovenia',
            'SB' => 'Isole Salomone',
            'SO' => 'Somalia',
            'ZA' => 'Sudafrica',
            'SS' => 'Sudan del Sud',
            'ES' => 'Spagna',
            'LK' => 'Sri Lanka',
            'SD' => 'Sudan',
            'SR' => 'Suriname',
            'SE' => 'Svezia',
            'CH' => 'Svizzera',
            'SY' => 'Siria',
            'TW' => 'Taiwan',
            'TJ' => 'Tagikistan',
            'TZ' => 'Tanzania',
            'TH' => 'Thailandia',
            'TL' => 'Timor Est',
            'TG' => 'Togo',
            'TO' => 'Tonga',
            'TT' => 'Trinidad e Tobago',
            'TN' => 'Tunisia',
            'TR' => 'Turchia',
            'TM' => 'Turkmenistan',
            'TV' => 'Tuvalu',
            'UG' => 'Uganda',
            'UA' => 'Ucraina',
            'AE' => 'Emirati Arabi Uniti',
            'GB' => 'Regno Unito',
            'US' => 'Stati Uniti',
            'UY' => 'Uruguay',
            'UZ' => 'Uzbekistan',
            'VU' => 'Vanuatu',
            'VA' => 'Città del Vaticano',
            'VE' => 'Venezuela',
            'VN' => 'Vietnam',
            'YE' => 'Yemen',
            'ZM' => 'Zambia',
            'ZW' => 'Zimbabwe',
        ];
    }
}
