<?php
session_start();
require_once 'conexao.php';

// Verifica se está logado e se é comprador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'comprador') {
    header("Location: login.php");
    exit;
}

$user_name = explode(' ', trim($_SESSION['user_name']))[0];

if (!isset($_GET['id'])) {
    header("Location: dashboard_comprador.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Buscar detalhes do produto e fabricante
$stmt = $pdo->prepare("
    SELECT p.*, u.company_name, u.name as manufacturer_name 
    FROM products p 
    JOIN users u ON p.manufacturer_id = u.id 
    WHERE p.id = ? AND p.status = 'approved'
");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Produto não encontrado ou não aprovado.";
    exit;
}

// Processar solicitação de orçamento
$msg_success = '';
$msg_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_quote'])) {
    $quantity = (int)$_POST['quantity'];
    $desired_delivery_date = $_POST['desired_delivery_date'];
    $location = trim($_POST['location']);
    $buyer_notes = trim($_POST['buyer_notes']);

    if ($quantity < $product['min_quantity']) {
        $msg_error = "A quantidade deve ser maior ou igual ao pedido mínimo (" . $product['min_quantity'] . ").";
    } elseif (empty($desired_delivery_date) || empty($location)) {
        $msg_error = "Preencha o prazo de entrega desejado e a localização.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO quotes (buyer_id, manufacturer_id, product_id, quantity, desired_delivery_date, location, buyer_notes, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'aguardando')
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $product['manufacturer_id'],
                $product_id,
                $quantity,
                $desired_delivery_date,
                $location,
                $buyer_notes
            ]);
            $msg_success = "Orçamento solicitado com sucesso! O fabricante foi notificado.";
        } catch (PDOException $e) {
            $msg_error = "Erro ao solicitar orçamento: " . $e->getMessage();
        }
    }
}

// Coletar imagens
$images = [];
if (!empty($product['image_url'])) $images[] = $product['image_url'];
if (!empty($product['image_url_2'])) $images[] = $product['image_url_2'];
if (!empty($product['image_url_3'])) $images[] = $product['image_url_3'];
if (!empty($product['image_url_4'])) $images[] = $product['image_url_4'];
if (!empty($product['image_url_5'])) $images[] = $product['image_url_5'];

if (empty($images)) {
    $images[] = 'https://via.placeholder.com/500x500?text=Sem+Imagem';
}

$main_image = $images[0]; // A primeira imagem é a principal por padrão (ou a que foi salva no image_url)
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - PACKGATE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-green': '#8DC63F',
                        'brand-darkblue': '#1A237E',
                        'brand-lightgreen': '#C5E1A5',
                        'brand-olive': '#558B2F'
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; }
    </style>
</head>
<body class="flex flex-col min-h-screen">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="dashboard_comprador.php" class="text-2xl font-black text-brand-darkblue tracking-tighter">
                    PACK<span class="text-brand-green">GATE</span>
                </a>
                <span class="bg-brand-lightgreen text-brand-olive text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Comprador</span>
            </div>
            
            <div class="flex items-center gap-6">
                <a href="dashboard_comprador.php" class="text-sm font-semibold text-gray-600 hover:text-brand-green transition">Voltar ao Painel</a>
                <div class="w-10 h-10 rounded-full bg-brand-green text-white flex items-center justify-center font-bold text-lg shadow-inner">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        
        <?php if ($msg_success): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <?php echo htmlspecialchars($msg_success); ?>
            </div>
        <?php endif; ?>

        <?php if ($msg_error): ?>
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <?php echo htmlspecialchars($msg_error); ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                
                <!-- Galeria de Imagens -->
                <div class="p-8 border-b lg:border-b-0 lg:border-r border-gray-100 bg-gray-50/50">
                    <div class="aspect-square bg-white rounded-xl border border-gray-200 overflow-hidden mb-4 shadow-sm">
                        <img id="mainImage" src="<?php echo htmlspecialchars($main_image); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-contain">
                    </div>
                    
                    <?php if (count($images) > 1): ?>
                    <div class="grid grid-cols-5 gap-2">
                        <?php foreach ($images as $index => $img): ?>
                            <button onclick="document.getElementById('mainImage').src='<?php echo htmlspecialchars($img); ?>'" class="aspect-square bg-white rounded-lg border border-gray-200 overflow-hidden hover:border-brand-green focus:border-brand-green focus:ring-2 focus:ring-brand-green outline-none transition">
                                <img src="<?php echo htmlspecialchars($img); ?>" class="w-full h-full object-cover">
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Detalhes e Formulário de Orçamento -->
                <div class="p-8 flex flex-col">
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="bg-gray-100 text-gray-600 text-xs font-bold px-2.5 py-1 rounded-md uppercase tracking-wide"><?php echo htmlspecialchars($product['type']); ?></span>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>
                        <p class="text-gray-500 font-medium">Fabricante: <span class="text-brand-darkblue"><?php echo htmlspecialchars($product['company_name'] ?: $product['manufacturer_name']); ?></span></p>
                    </div>

                    <div class="prose prose-sm text-gray-600 mb-8">
                        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dimensões</p>
                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($product['dimensions']); ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pedido Mínimo</p>
                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($product['min_quantity']); ?> unidades</p>
                        </div>
                    </div>

                    <hr class="border-gray-100 mb-8">

                    <!-- Formulário de Solicitação -->
                    <div class="mt-auto">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-green"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                            Solicitar Orçamento
                        </h3>
                        
                        <?php if (!$msg_success): ?>
                        <form method="POST" class="space-y-4">
                            <input type="hidden" name="request_quote" value="1">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Quantidade Desejada <span class="text-red-500">*</span></label>
                                    <input type="number" name="quantity" min="<?php echo $product['min_quantity']; ?>" value="<?php echo $product['min_quantity']; ?>" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Prazo de Entrega Desejado <span class="text-red-500">*</span></label>
                                    <input type="date" name="desired_delivery_date" required class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Localização (CEP ou Cidade/UF) <span class="text-red-500">*</span></label>
                                <input type="text" name="location" required placeholder="Ex: São Paulo, SP ou 01000-000" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Observações / Personalização</label>
                                <textarea name="buyer_notes" rows="3" placeholder="Detalhes sobre impressão, cores, acabamentos..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition resize-none"></textarea>
                            </div>

                            <button type="submit" class="w-full bg-brand-darkblue hover:bg-blue-900 text-white font-bold py-3.5 px-4 rounded-xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                                Enviar Solicitação ao Fabricante
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            </button>
                        </form>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <a href="dashboard_comprador.php" class="inline-block bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-3 px-6 rounded-xl transition">Voltar para Produtos</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>
