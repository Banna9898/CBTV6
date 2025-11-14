<?php
// Database connection (supports DATABASE_URL or DB_* env)
$databaseUrl = getenv('DATABASE_URL');

if ($databaseUrl) {
    $parts = parse_url($databaseUrl);
    $DB_HOST = $parts['host'] ?? 'dpg-d4ar9ubipnbc73agindg-a';
    $DB_PORT = $parts['port'] ?? 5432;
    $DB_USER = $parts['user'] ?? 'jadeja';
    $DB_PASS = $parts['pass'] ?? 'XjJZMwDxBFiZyjnpHwGErWZKUAK4sA1X';
    $DB_NAME = ltrim($parts['path'] ?? '/cbt_s2tr', '/');
} else {
    $DB_HOST = getenv('DB_HOST') ?: 'dpg-d4ar9ubipnbc73agindg-a';
    $DB_PORT = getenv('DB_PORT') ?: 5432;
    $DB_USER = getenv('DB_USER') ?: 'jadeja';
    $DB_PASS = getenv('DB_PASS') ?: 'XjJZMwDxBFiZyjnpHwGErWZKUAK4sA1X';
    $DB_NAME = getenv('DB_NAME') ?: 'cbt_s2tr';
}
try {
    $dsn = "pgsql:host={$DB_HOST};port={$DB_PORT};dbname={$DB_NAME};";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    die('DB connection failed: ' . $e->getMessage());
}
// run migrations if present
$migrations = __DIR__ . '/../migrations/schema.sql';
if(file_exists($migrations)){
    try{ $pdo->exec(file_get_contents($migrations)); } catch(Exception $e){ /* ignore */ }
}
// seed admin via PHP if not exists
try{
    $stmt = $pdo->prepare('SELECT id, email FROM users WHERE email=?');
    $stmt->execute(['admin@example.com']);
    if(!$stmt->fetch()){
        $hash = password_hash('Admin@123', PASSWORD_DEFAULT);
        $pdo->prepare('INSERT INTO users (name,email,phone,password,role,status) VALUES (?,?,?,?,?,?)')->execute(['Admin User','admin@example.com','9999999999',$hash,'admin','active']);
    }
} catch(Exception $e){}
session_start();
function require_login(){ if(empty($_SESSION['user'])){ header('Location: /login.php'); exit; } }
function require_admin(){ if(empty($_SESSION['user'])||($_SESSION['user']['role']??'')!=='admin'){ header('Location: /login.php'); exit; } }
function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
