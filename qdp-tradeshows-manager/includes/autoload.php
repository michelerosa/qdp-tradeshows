<?php
/**
 * Autoloader PSR-4 per il plugin QDP Tradeshows Manager
 */

spl_autoload_register(function (string $class): void {
    $prefix = 'QDP\\Tradeshows\\';
    $base_dir = QDP_TRADESHOWS_PATH . 'includes/';

    // Verifica se la classe usa il nostro namespace
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    // Ottieni il nome relativo della classe
    $relative_class = substr($class, $len);

    // Converti namespace separators in directory separators
    $parts = explode('\\', $relative_class);
    $class_name = array_pop($parts);

    // Converti PascalCase a kebab-case per il nome file
    $file_name = 'class-' . strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $class_name)) . '.php';

    // Costruisci il path
    $path = $base_dir;
    if (!empty($parts)) {
        $path .= implode('/', $parts) . '/';
    }
    $path .= $file_name;

    if (file_exists($path)) {
        require $path;
    }
});
