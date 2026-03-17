<?php
session_start();
require_once 'config.php';

// Verificar se é comprador
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'comprador') {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    header('Location: dashboard_comprador.php');
    exit;
}

// Buscar detalhes do pedido
$stmt = $pdo->prepare("
    SELECT po.*, 
           q.quantity, q.unit_price, q.real_delivery_date, q.commercial_conditions, q.additional_message,
           p.name as product_name, p.min_order_quantity,
           u.name as manufacturer_name, u.company_name as manufacturer_company, u.email as manufacturer_email, u.phone as manufacturer_phone
    FROM purchase_orders po
    JOIN quotes q ON po.quote_id = q.id
    JOIN products p ON q.product_id = p.id
    JOIN users u ON po.manufacturer_id = u.id
    WHERE po.id = ? AND po.buyer_id = ?
");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: dashboard_comprador.php');
    exit;
}

// Atualizar status do pedido (Comprador pode marcar como concluído se estiver enviado)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm_receipt') {
    if ($order['status'] === 'enviado') {
        $update_stmt = $pdo->prepare("UPDATE purchase_orders SET status = 'concluido' WHERE id = ?");
        $update_stmt->execute([$order_id]);
        
        // Recarregar dados
        header("Location: detalhes_pedido.php?id=" . $order_id . "&success=1");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?> - B2B Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            green: '#4CAF50',
                            olive: '#388E3C',
                            darkblue: '#1A237E',
                            lightblue: '#E8EAF6',
                            accent: '#FFC107'
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col">

    <!-- HEADER -->
    <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="dashboard_comprador.php" class="text-gray-500 hover:text-brand-darkblue transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                </a>
                <h1 class="text-xl font-bold text-brand-darkblue">Detalhes do Pedido #<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Olá, Comprador</span>
            </div>
        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="flex-grow max-w-5xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        
        <?php if (isset($_GET['success'])): ?>
            <div class="mb-6 bg-green-50 border-l-4 border-brand-green p-4 rounded-r-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-brand-green" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">
                            Recebimento confirmado com sucesso! Pedido concluído.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Coluna Esquerda: Detalhes do Pedido -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Status Atual -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Status do Pedido</h2>
                        <?php if ($order['status'] === 'pendente_admin'): ?>
                            <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-bold">Pendente Aprovação (Admin)</span>
                        <?php elseif ($order['status'] === 'aprovado'): ?>
                            <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-bold">Aprovado</span>
                        <?php elseif ($order['status'] === 'em_producao'): ?>
                            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm font-bold flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                Em Produção
                            </span>
                        <?php elseif ($order['status'] === 'enviado'): ?>
                            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-bold flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 18H3c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h10c.6 0 1 .4 1 1v11"/><path d="M14 9h4l4 4v4c0 .6-.4 1-1 1h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>
                                Enviado
                            </span>
                        <?php elseif ($order['status'] === 'concluido'): ?>
                            <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-bold">Concluído</span>
                        <?php elseif ($order['status'] === 'cancelado'): ?>
                            <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-bold">Cancelado</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($order['status'] === 'enviado'): ?>
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <p class="text-sm text-gray-600 mb-4">O fabricante informou que o pedido foi enviado. Por favor, confirme o recebimento assim que a mercadoria chegar.</p>
                            <form method="POST">
                                <input type="hidden" name="action" value="confirm_receipt">
                                <button type="submit" onclick="return confirm('Tem certeza que deseja confirmar o recebimento? Esta ação não pode ser desfeita.')" class="bg-brand-green hover:bg-brand-olive text-white px-6 py-2 rounded-lg font-bold transition w-full sm:w-auto">
                                    Confirmar Recebimento
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Resumo do Pedido -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Resumo do Pedido</h2>
                    <div class="grid grid-cols-2 gap-y-4 gap-x-8">
                        <div>
                            <span class="block text-sm text-gray-500">Produto</span>
                            <span class="block font-medium text-gray-900"><?php echo htmlspecialchars($order['product_name']); ?></span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Quantidade</span>
                            <span class="block font-medium text-gray-900"><?php echo number_format($order['quantity'], 0, ',', '.'); ?> unidades</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Preço Unitário</span>
                            <span class="block font-medium text-gray-900">R$ <?php echo number_format($order['unit_price'], 2, ',', '.'); ?></span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Valor Total</span>
                            <span class="block font-bold text-brand-darkblue text-lg">R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Previsão de Entrega</span>
                            <span class="block font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($order['real_delivery_date'])); ?></span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Data do Pedido</span>
                            <span class="block font-medium text-gray-900"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Condições e Mensagem -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Condições Acordadas</h2>
                    <div class="space-y-4">
                        <div>
                            <span class="block text-sm text-gray-500 mb-1">Condições Comerciais (Frete, Pagamento)</span>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-800">
                                <?php echo nl2br(htmlspecialchars($order['commercial_conditions'])); ?>
                            </div>
                        </div>
                        <?php if (!empty($order['additional_message'])): ?>
                        <div>
                            <span class="block text-sm text-gray-500 mb-1">Mensagem Adicional do Fabricante</span>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-800">
                                <?php echo nl2br(htmlspecialchars($order['additional_message'])); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Nota Fiscal -->
                <?php if ($order['invoice_xml_url']): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Documentos Fiscais</h2>
                    <div class="flex items-center justify-between bg-green-50 p-4 rounded-lg border border-green-100">
                        <div class="flex items-center gap-3 text-green-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-4"/></svg>
                            <div>
                                <span class="block font-bold text-sm">Nota Fiscal (XML) Disponível</span>
                                <span class="block text-xs opacity-80">Enviada pelo fabricante</span>
                            </div>
                        </div>
                        <a href="<?php echo htmlspecialchars($order['invoice_xml_url']); ?>" target="_blank" class="bg-white border border-green-200 hover:bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-bold transition">
                            Baixar XML
                        </a>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Coluna Direita: Dados do Fabricante e Seus Dados -->
            <div class="space-y-6">
                
                <!-- Dados do Fabricante -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Dados do Fabricante</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Empresa</span>
                            <span class="block font-medium text-gray-900"><?php echo htmlspecialchars($order['manufacturer_company'] ?: $order['manufacturer_name']); ?></span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Contato</span>
                            <span class="block text-gray-800"><?php echo htmlspecialchars($order['manufacturer_name']); ?></span>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">E-mail</span>
                            <a href="mailto:<?php echo htmlspecialchars($order['manufacturer_email']); ?>" class="block text-brand-darkblue hover:underline"><?php echo htmlspecialchars($order['manufacturer_email']); ?></a>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider">Telefone</span>
                            <span class="block text-gray-800"><?php echo htmlspecialchars($order['manufacturer_phone'] ?: 'Não informado'); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Seus Dados (Faturamento/Entrega) -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Seus Dados Informados</h2>
                    <div class="space-y-4">
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Dados de Faturamento (CNPJ/Razão)</span>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-800">
                                <?php echo nl2br(htmlspecialchars($order['billing_details'])); ?>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs text-gray-500 uppercase tracking-wider mb-1">Endereço de Entrega</span>
                            <div class="bg-gray-50 p-3 rounded-lg text-sm text-gray-800">
                                <?php echo nl2br(htmlspecialchars($order['delivery_details'])); ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>
</html>
