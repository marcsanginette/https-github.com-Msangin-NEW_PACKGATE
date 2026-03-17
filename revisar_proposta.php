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

$quote_id = (int)$_GET['id'];

// Buscar detalhes do orçamento
$stmt = $pdo->prepare("
    SELECT q.*, p.name as product_name, p.type, p.dimensions,
           u.company_name as manufacturer_company, u.name as manufacturer_name
    FROM quotes q
    JOIN products p ON q.product_id = p.id
    JOIN users u ON q.manufacturer_id = u.id
    WHERE q.id = ? AND q.buyer_id = ?
");
$stmt->execute([$quote_id, $_SESSION['user_id']]);
$quote = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quote) {
    echo "Orçamento não encontrado ou você não tem permissão para acessá-lo.";
    exit;
}

$msg_success = '';
$msg_error = '';

// Processar criação do pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_order'])) {
    $billing_details = trim($_POST['billing_details']);
    $delivery_details = trim($_POST['delivery_details']);

    if (empty($billing_details) || empty($delivery_details)) {
        $msg_error = "Preencha os dados de faturamento e entrega.";
    } elseif ($quote['status'] !== 'respondido') {
        $msg_error = "Este orçamento não pode ser convertido em pedido no momento.";
    } else {
        try {
            $pdo->beginTransaction();

            // 1. Inserir na tabela purchase_orders
            $stmt = $pdo->prepare("
                INSERT INTO purchase_orders (quote_id, buyer_id, manufacturer_id, total_amount, billing_details, delivery_details, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'pendente_admin')
            ");
            $stmt->execute([
                $quote_id,
                $_SESSION['user_id'],
                $quote['manufacturer_id'],
                $quote['total_price'],
                $billing_details,
                $delivery_details
            ]);

            // 2. Atualizar status do orçamento
            $stmt_update = $pdo->prepare("UPDATE quotes SET status = 'pedido_criado' WHERE id = ?");
            $stmt_update->execute([$quote_id]);

            $pdo->commit();
            $msg_success = "Pedido de compra criado com sucesso! Aguardando aprovação do administrador.";
            $quote['status'] = 'pedido_criado'; // Atualiza a variável para refletir na tela
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $msg_error = "Erro ao criar pedido: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revisar Proposta #ORC-<?php echo str_pad($quote['id'], 4, '0', STR_PAD_LEFT); ?> - PACKGATE</title>
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
                    <span class="bg-blue-100 text-blue-800 px-4 py-2 rounded-lg font-bold text-sm">Proposta Recebida</span>
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
            <!-- Detalhes da Sua Solicitação -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                    Sua Solicitação
                </h3>
                <p class="font-bold text-gray-900 text-lg mb-1"><?php echo htmlspecialchars($quote['product_name']); ?></p>
                <p class="text-xs text-gray-500 mb-4">Fabricante: <?php echo htmlspecialchars($quote['manufacturer_company'] ?: $quote['manufacturer_name']); ?></p>
                
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
                    <p class="text-xs text-gray-500 font-semibold mb-1">Suas Observações</p>
                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100"><?php echo nl2br(htmlspecialchars($quote['buyer_notes'])); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Proposta do Fabricante -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-brand-green/30 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-brand-green/10 rounded-bl-full -z-10"></div>
                
                <h3 class="text-sm font-bold text-brand-olive uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    Proposta do Fabricante
                </h3>
                
                <?php if ($quote['status'] === 'aguardando'): ?>
                    <div class="flex flex-col items-center justify-center h-40 text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <p class="text-gray-500 font-medium">O fabricante ainda não respondeu.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                            <p class="text-xs text-green-700 font-semibold">Preço Unitário</p>
                            <p class="text-lg font-bold text-brand-darkblue">R$ <?php echo number_format($quote['unit_price'], 2, ',', '.'); ?></p>
                        </div>
                        <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                            <p class="text-xs text-green-700 font-semibold">Prazo Real</p>
                            <p class="text-sm font-bold text-brand-darkblue"><?php echo date('d/m/Y', strtotime($quote['real_delivery_date'])); ?></p>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <div class="flex justify-between items-center mb-1">
                            <p class="text-xs text-gray-500 font-semibold">Valor Total da Proposta</p>
                            <p class="text-xl font-black text-brand-darkblue">R$ <?php echo number_format($quote['total_price'], 2, ',', '.'); ?></p>
                        </div>
                    </div>

                    <?php if (!empty($quote['commercial_conditions'])): ?>
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 font-semibold mb-1">Condições Comerciais</p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100"><?php echo nl2br(htmlspecialchars($quote['commercial_conditions'])); ?></p>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($quote['manufacturer_message'])): ?>
                    <div>
                        <p class="text-xs text-gray-500 font-semibold mb-1">Mensagem do Fabricante</p>
                        <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg border border-gray-100"><?php echo nl2br(htmlspecialchars($quote['manufacturer_message'])); ?></p>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Formulário de Criação de Pedido -->
        <?php if ($quote['status'] === 'respondido'): ?>
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200">
            <h2 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-green"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                Criar Pedido de Compra
            </h2>
            <p class="text-gray-500 text-sm mb-6">Preencha os dados abaixo para confirmar o pedido. O pedido passará por uma aprovação da plataforma antes de ser liberado para produção.</p>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="create_order" value="1">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Dados de Faturamento (CNPJ, Razão Social, Endereço) <span class="text-red-500">*</span></label>
                        <textarea name="billing_details" rows="4" required placeholder="Ex: CNPJ: 00.000.000/0001-00&#10;Razão Social: Minha Empresa LTDA&#10;Endereço: Rua Exemplo, 123..." class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Dados de Entrega (Endereço Completo, Contato) <span class="text-red-500">*</span></label>
                        <textarea name="delivery_details" rows="4" required placeholder="Ex: Endereço: Av. Principal, 456 - Galpão 2&#10;CEP: 01000-000&#10;Contato: João Silva (11) 99999-9999" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition resize-none"></textarea>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end">
                    <button type="submit" class="bg-brand-darkblue hover:bg-blue-900 text-white font-bold py-3 px-8 rounded-xl transition shadow-md hover:shadow-lg flex items-center gap-2">
                        Confirmar e Criar Pedido
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </main>

</body>
</html>
