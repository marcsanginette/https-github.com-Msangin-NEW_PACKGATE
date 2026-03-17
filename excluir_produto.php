<?php
session_start();
require_once 'conexao.php';

// Verifica se está logado e se é fabricante
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'fabricante') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $produto_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Verifica se o produto pertence ao fabricante logado
        $stmt = $pdo->prepare("SELECT id, image_url, image_url_2, image_url_3, image_url_4, image_url_5 FROM products WHERE id = ? AND manufacturer_id = ?");
        $stmt->execute([$produto_id, $user_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            // Se tiver imagem, tenta excluir o arquivo
            $images = [
                $produto['image_url'],
                $produto['image_url_2'],
                $produto['image_url_3'],
                $produto['image_url_4'],
                $produto['image_url_5']
            ];
            
            foreach ($images as $img) {
                if (!empty($img) && file_exists($img)) {
                    unlink($img);
                }
            }

            // Exclui o produto do banco de dados
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$produto_id]);
        }
    } catch (PDOException $e) {
        // Em caso de erro, apenas redireciona
    }
}

header("Location: dashboard_fabricante.php");
exit;
?>
