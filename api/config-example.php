<?php
/**
 * ALUMÍNIOS PREMIUM - CONFIGURATION FILE
 * 
 * IMPORTANTE: Renomeia este ficheiro para config.php
 * e atualiza com as tuas credenciais reais
 */

// Configuração da Base de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'aluminios_db');
define('DB_USER', 'aluminios_user');
define('DB_PASS', 'MUDA_ESTA_PASSWORD');

// Configuração de Email
define('COMPANY_EMAIL', 'geral@aluminios.pt');
define('COMPANY_NAME', 'Alumínios Premium');
define('NOREPLY_EMAIL', 'noreply@aluminios.pt');

// Configuração do Site
define('SITE_URL', 'https://www.aluminios.pt');
define('SITE_NAME', 'Alumínios Premium');

// Segurança
define('ENABLE_RATE_LIMITING', true);
define('MAX_SUBMISSIONS_PER_HOUR', 3);

// Modo de Debug (DESATIVAR em produção!)
define('DEBUG_MODE', false);

// Timezone
date_default_timezone_set('Europe/Lisbon');

// Error Reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/error.log');
}

/**
 * Database Connection
 */
function getDbConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;
    } catch (PDOException $e) {
        if (DEBUG_MODE) {
            die('Database connection failed: ' . $e->getMessage());
        } else {
            error_log('Database connection error: ' . $e->getMessage());
            die('Erro de sistema. Por favor, tente mais tarde.');
        }
    }
}

/**
 * Sanitize input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Send Email
 * 
 * Para produção, considera usar PHPMailer ou SendGrid
 */
function sendEmail($to, $subject, $message, $from = null) {
    if ($from === null) {
        $from = NOREPLY_EMAIL;
    }
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: ' . COMPANY_NAME . ' <' . $from . '>',
        'X-Mailer: PHP/' . phpversion()
    ];
    
    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Log Activity
 */
function logActivity($message, $level = 'INFO') {
    $logFile = __DIR__ . '/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>