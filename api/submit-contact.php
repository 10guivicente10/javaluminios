<?php
/**
 * ALUMÍNIOS PREMIUM - CONTACT FORM API
 * Handles secure form submissions with database storage
 */

// Security headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CORS settings (adjust for your domain)
header('Access-Control-Allow-Origin: *'); // Change to your domain in production
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Rate limiting check (simple implementation)
session_start();
$maxSubmissions = 3;
$timeWindow = 3600; // 1 hour

if (!isset($_SESSION['form_submissions'])) {
    $_SESSION['form_submissions'] = [];
}

// Clean old submissions
$_SESSION['form_submissions'] = array_filter(
    $_SESSION['form_submissions'],
    function($timestamp) use ($timeWindow) {
        return (time() - $timestamp) < $timeWindow;
    }
);

if (count($_SESSION['form_submissions']) >= $maxSubmissions) {
    http_response_code(429);
    echo json_encode([
        'success' => false,
        'message' => 'Demasiados pedidos. Por favor, tente novamente mais tarde.'
    ]);
    exit;
}

// Get and validate input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON']);
    exit;
}

// Validation
$errors = [];

// Nome
if (empty($data['nome']) || strlen($data['nome']) < 3) {
    $errors[] = 'Nome inválido';
}

// Email
if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email inválido';
}

// Telefone
if (empty($data['telefone']) || !preg_match('/^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{3,6}$/', str_replace(' ', '', $data['telefone']))) {
    $errors[] = 'Telefone inválido';
}

// Serviço
$servicosValidos = ['janelas', 'portas', 'fachadas', 'estores', 'varandas', 'medida', 'outro'];
if (empty($data['servico']) || !in_array($data['servico'], $servicosValidos)) {
    $errors[] = 'Serviço inválido';
}

// Localidade
if (empty($data['localidade']) || strlen($data['localidade']) < 3) {
    $errors[] = 'Localidade inválida';
}

// Mensagem
if (empty($data['mensagem']) || strlen($data['mensagem']) < 10) {
    $errors[] = 'Mensagem muito curta';
}

// If there are validation errors
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Dados inválidos',
        'errors' => $errors
    ]);
    exit;
}

// Sanitize data
$nome = htmlspecialchars(trim($data['nome']), ENT_QUOTES, 'UTF-8');
$email = filter_var($data['email'], FILTER_SANITIZE_EMAIL);
$telefone = htmlspecialchars(trim($data['telefone']), ENT_QUOTES, 'UTF-8');
$servico = htmlspecialchars($data['servico'], ENT_QUOTES, 'UTF-8');
$localidade = htmlspecialchars(trim($data['localidade']), ENT_QUOTES, 'UTF-8');
$mensagem = htmlspecialchars(trim($data['mensagem']), ENT_QUOTES, 'UTF-8');
$marketing = isset($data['marketing']) && $data['marketing'] ? 1 : 0;
$timestamp = date('Y-m-d H:i:s');

// Database configuration
$dbConfig = [
    'host' => 'localhost',
    'dbname' => 'aluminios_db',
    'username' => 'aluminios_user',
    'password' => 'CHANGE_THIS_PASSWORD', // Change in production!
];

try {
    // Connect to database
    $dsn = "mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Prepare SQL statement
    $sql = "INSERT INTO contactos (nome, email, telefone, servico, localidade, mensagem, marketing, data_submissao, ip_address, user_agent) 
            VALUES (:nome, :email, :telefone, :servico, :localidade, :mensagem, :marketing, :timestamp, :ip, :user_agent)";
    
    $stmt = $pdo->prepare($sql);
    
    // Execute with parameters
    $stmt->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':telefone' => $telefone,
        ':servico' => $servico,
        ':localidade' => $localidade,
        ':mensagem' => $mensagem,
        ':marketing' => $marketing,
        ':timestamp' => $timestamp,
        ':ip' => $_SERVER['REMOTE_ADDR'],
        ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown'
    ]);

    $contactId = $pdo->lastInsertId();

    // Send email notification to company
    $emailSent = sendNotificationEmail([
        'nome' => $nome,
        'email' => $email,
        'telefone' => $telefone,
        'servico' => $servico,
        'localidade' => $localidade,
        'mensagem' => $mensagem,
        'id' => $contactId
    ]);

    // Send confirmation email to client
    sendConfirmationEmail($email, $nome);

    // Add to rate limiting
    $_SESSION['form_submissions'][] = time();

    // Success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Pedido enviado com sucesso!',
        'id' => $contactId
    ]);

} catch (PDOException $e) {
    // Log error (in production, log to file instead of exposing)
    error_log('Database error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao processar pedido. Por favor, contacte-nos diretamente.'
    ]);
}

/**
 * Send notification email to company
 */
function sendNotificationEmail($data) {
    $to = 'geral@aluminios.pt'; // Your company email
    $subject = 'Novo Pedido de Orçamento #' . $data['id'] . ' - ' . ucfirst($data['servico']);
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2a2d34; color: #fff; padding: 20px; text-align: center; }
            .content { background: #f5f5f5; padding: 20px; }
            .field { margin-bottom: 15px; }
            .label { font-weight: bold; color: #2a2d34; }
            .value { margin-top: 5px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Novo Pedido de Orçamento</h2>
            </div>
            <div class='content'>
                <div class='field'>
                    <div class='label'>Pedido #:</div>
                    <div class='value'>{$data['id']}</div>
                </div>
                <div class='field'>
                    <div class='label'>Nome:</div>
                    <div class='value'>{$data['nome']}</div>
                </div>
                <div class='field'>
                    <div class='label'>Email:</div>
                    <div class='value'><a href='mailto:{$data['email']}'>{$data['email']}</a></div>
                </div>
                <div class='field'>
                    <div class='label'>Telefone:</div>
                    <div class='value'><a href='tel:{$data['telefone']}'>{$data['telefone']}</a></div>
                </div>
                <div class='field'>
                    <div class='label'>Serviço:</div>
                    <div class='value'>" . ucfirst($data['servico']) . "</div>
                </div>
                <div class='field'>
                    <div class='label'>Localidade:</div>
                    <div class='value'>{$data['localidade']}</div>
                </div>
                <div class='field'>
                    <div class='label'>Mensagem:</div>
                    <div class='value'>{$data['mensagem']}</div>
                </div>
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: Website <noreply@aluminios.pt>',
        'Reply-To: ' . $data['email'],
        'X-Mailer: PHP/' . phpversion()
    ];

    return mail($to, $subject, $message, implode("\r\n", $headers));
}

/**
 * Send confirmation email to client
 */
function sendConfirmationEmail($email, $nome) {
    $subject = 'Confirmação do seu pedido - Alumínios Premium';
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #d4af37; color: #1a1c20; padding: 20px; text-align: center; }
            .content { padding: 20px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Obrigado pelo seu contacto!</h2>
            </div>
            <div class='content'>
                <p>Olá {$nome},</p>
                <p>Recebemos o seu pedido de orçamento e entraremos em contacto consigo nas próximas 24 horas.</p>
                <p>Se tiver alguma dúvida urgente, não hesite em contactar-nos:</p>
                <ul>
                    <li>Telefone: +351 XXX XXX XXX</li>
                    <li>Email: geral@aluminios.pt</li>
                </ul>
                <p>Com os melhores cumprimentos,<br>Equipa Alumínios Premium</p>
            </div>
        </div>
    </body>
    </html>
    ";

    $headers = [
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: Alumínios Premium <noreply@aluminios.pt>',
        'X-Mailer: PHP/' . phpversion()
    ];

    return mail($email, $subject, $message, implode("\r\n", $headers));
}
?>