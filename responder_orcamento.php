<?php
session_start();
require_once 'conexao.php';

// Verifica se está logado e se é fabricante
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'fabricante') {
    header("Location: login.php");
    exit;
}

$user_name = explode(' ', trim($_SESSION['user_name']))[0];

if (!isset($_GET['id'])) {
    header("Location: dashboard_fabricante.php");
    exit;
}

$quote_id = (int)$_GET['id'];

// Buscar detalhes do orçamento
$stmt = $pdo->prepare("
    SELECT q.*, p.name as product_name, p.min_quantity, p.dimensions, p.type, 
           u.company_name as buyer_company, u.name as buyer_name, u.email as buyer_email
    FROM quotes q
    JOIN products p ON q.product_id = p.id
    JOIN users u ON q.buyer_id = u.id
    WHERE q.id = ? AND q.manufacturer_id = ?
");
$stmt->execute([$quote_id, $_SESSION['user_id']]);
$quote = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quote) {
    echo "Orçamento não encontrado ou você não tem permissão para acessá-lo.";
    exit;
}

$msg_success = '';
$msg_error = '';

// Processar resposta do fabricante
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['respond_quote'])) {
    $unit_price = str_replace(',', '.', $_POST['unit_price']);
    $real_delivery_date = $_POST['real_delivery_date'];
    $commercial_conditions = trim($_POST['commercial_conditions']);
    $manufacturer_message = trim($_POST['manufacturer_message']);
    
    // Calcula o preço total
    $total_price = $unit_price * $quote['quantity'];

    if (empty($unit_price) || empty($real_delivery_date)) {
        $msg_error = "Preencha o preço unitário e o prazo real de entrega.";
    } else {
        try {
            $stmt = $pdo->prepare("
                UPDATE quotes 
                SET unit_price = ?, real_delivery_date = ?, commercial_conditions = ?, manufacturer_message = ?, total_price = ?, status = 'respondido'
                WHERE id = ? AND manufacturer_id = ?
            ");
            $stmt->execute([
                $unit_price,
                $real_delivery_date,
                $commercial_conditions,
                $manufacturer_message,
                $total_price,
                $quote_id,
                $_SESSION['user_id']
            ]);
            $msg_success = "Orçamento respondido com sucesso! O comprador foi notificado.";
            
            // Atualiza a variável $quote para refletir as mudanças na tela
            $quote['unit_price'] = $unit_price;
            $quote['real_delivery_date'] = $real_delivery_date;
            $quote['commercial_conditions'] = $commercial_conditions;
            $quote['manufacturer_message'] = $manufacturer_message;
            $quote['total_price'] = $total_price;
            $quote['status'] = 'respondido';
            
        } catch (PDOException $e) {
            $msg_error = "Erro ao responder orçamento: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responder Orçamento #ORC-<?php echo str_pad($quote['id'], 4, '0', STR_PAD_LEFT); ?> - PACKGATE</title>
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
                <a href="dashboard_fabricante.php" class="text-2xl font-black text-brand-darkblue tracking-tighter">
                    PACK<span class="text-brand-green">GATE</span>
                </a>
                <span class="bg-brand-darkblue text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Fabricante</span>
            </div>
            
            <div class="flex items-center gap-6">
                <a href="dashboard_fabricante.php" class="text-sm font-semibold text-gray-600 hover:text-brand-green transition">Voltar ao Painel</a>
                <div class="w-10 h-10 rounded-full bg-brand-darkblue text-white flex items-center justify-center font-bold text-lg shadow-inner">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Orçamento #ORC-<?php echo str_pad($quote['id'], 4, '0', STR_PAD_LEFT); ?></h1>
                <p class="text-gray-500">Solicitado em <?php echo date('d/m/Y H:i', strtotime($quote['created_at'])); ?></p>
            </div>
            <div>
                <?php if ($quote['status'] === 'aguardando'): ?>
                    <span class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg font-bold text-sm">Aguardando Resposta</span>
                <?php elseif ($quote['status'] === 'respondido'): ?>
                    <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-bold text-sm">Respondido</span>
                <?php elseif ($quote['status'] === 'pedido_criado'): ?>
                    <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-lg font-bold text-sm">Pedido Criado</span>
                <?php endif; ?>
            </div>
        </div>

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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Detalhes do Comprador -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Dados do Comprador
                </h3>
                <p class="font-bold text-gray-900 text-lg mb-1"><?php echo htmlspecialchars($quote['buyer_company'] ?: $quote['buyer_name']); ?></p>
                <p class="text-sm text-gray-500 mb-4">Atenção: Dados de contato completos são ocultados por questões de intermediação.</p>
                
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold">Localização de Entrega</p>
                        <p class="text-sm text-gray-900"><?php echo htmlspecialchars($quote['location']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Detalhes da Solicitação -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    Detalhes do Pedido
                </h3>
                <p class="font-bold text-gray-900 text-lg mb-1"><?php echo htmlspecialchars($quote['product_name']); ?></p>
                <p class="text-xs text-gray-500 mb-4">Tipo: <?php echo htmlspecialchars($quote['type']); ?> • Dimensões: <?php echo htmlspecialchars($quote['dimensions']); ?></p>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <p class="text-xs text-gray-500 font-semibold">Quantidade</p>
                        <p class="text-lg font-bold text-brand-darkblue"><?php echo number_format($quote['quantity'], 0, ',', '.'); ?> un</p>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <p class="text-xs text-gray-500 font-semibold">Prazo Desejado</p>
                        <p class="text-sm font-bold text-gray-900"><?php echo date('d/m/Y', strtotime($quote['desired_delivery_date'])); ?></p>
                    </div>
                </div>

                <?php if (!empty($quote['buyer_notes'])): ?>
                <div>
                    <p class="text-xs text-gray-500 font-semibold mb-1">Observações do Comprador</p>
                    <p class="text-sm text-gray-700 bg-yellow-50 p-3 rounded-lg border border-yellow-100"><?php echo nl2br(htmlspecialchars($quote['buyer_notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulário de Resposta -->
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-green"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2v5Z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/></svg>
                Sua Proposta
            </h2>

            <?php if ($quote['status'] === 'aguardando'): ?>
            <form method="POST" class="space-y-6">
                <input type="hidden" name="respond_quote" value="1">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Preço Unitário (R$) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="unit_price" required placeholder="Ex: 2.50" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Prazo Real de Entrega <span class="text-red-500">*</span></label>
                        <input type="date" name="real_delivery_date" required class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Condições Comerciais (Pagamento/Frete)</label>
                    <textarea name="commercial_conditions" rows="3" placeholder="Ex: 50% sinal, 50% na entrega. Frete FOB." class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Mensagem Adicional (Opcional)</label>
                    <textarea name="manufacturer_message" rows="3" placeholder="Alguma observação extra para o comprador?" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition resize-none"></textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-brand-green hover:bg-brand-olive text-white font-bold py-3 px-8 rounded-xl transition shadow-md hover:shadow-lg">
                        Enviar Proposta
                    </button>
                </div>
            </form>
            <?php else: ?>
                <!-- Visualização da Proposta Enviada -->
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Preço Unitário</p>
                            <p class="font-bold text-gray-900">R$ <?php echo number_format($quote['unit_price'], 2, ',', '.'); ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Preço Total</p>
                            <p class="font-bold text-brand-darkblue text-lg">R$ <?php echo number_format($quote['total_price'], 2, ',', '.'); ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Prazo Real</p>
                            <p class="font-bold text-gray-900"><?php echo date('d/m/Y', strtotime($quote['real_delivery_date'])); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($quote['commercial_conditions'])): ?>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Condições Comerciais</p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100"><?php echo nl2br(htmlspecialchars($quote['commercial_conditions'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($quote['manufacturer_message'])): ?>
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Sua Mensagem</p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-4 rounded-xl border border-gray-100"><?php echo nl2br(htmlspecialchars($quote['manufacturer_message'])); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
