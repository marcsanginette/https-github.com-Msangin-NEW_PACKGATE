<?php
// Configurações do Banco de Dados (XAMPP Local)
$host = 'localhost';
$dbname = 'packgate_db'; // Nome do banco de dados criado no XAMPP
$username = 'root';      // Usuário padrão do XAMPP
$password = '';          // Senha padrão do XAMPP (vazia)

$pdo = null;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Em desenvolvimento (XAMPP), é útil ver o erro
    // echo "Erro de conexão: " . $e->getMessage();
}
?>
