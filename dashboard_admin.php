<?php
session_start();
require_once 'conexao.php';

// Verifica se está logado e é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$user_name = $_SESSION['user_name'];

// Lógica para aprovar/rejeitar fabricantes
if (isset($_POST['action']) && isset($_POST['user_id'])) {
    $action = $_POST['action'];
    $target_user_id = (int)$_POST['user_id'];
    
    if ($action === 'approve_user') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'approved' WHERE id = ? AND role = 'fabricante'");
        $stmt->execute([$target_user_id]);
        $msg_user = "Fabricante aprovado com sucesso.";
    } elseif ($action === 'reject_user') {
        $stmt = $pdo->prepare("UPDATE users SET status = 'rejected' WHERE id = ? AND role = 'fabricante'");
        $stmt->execute([$target_user_id]);
        $msg_user = "Fabricante rejeitado.";
    }
}

// Lógica para aprovar/rejeitar produtos
if (isset($_POST['action']) && isset($_POST['product_id'])) {
    $action = $_POST['action'];
    $target_product_id = (int)$_POST['product_id'];
    
    if ($action === 'approve_product') {
        $stmt = $pdo->prepare("UPDATE products SET status = 'approved' WHERE id = ?");
        $stmt->execute([$target_product_id]);
        $msg_prod = "Produto aprovado com sucesso.";
    } elseif ($action === 'reject_product') {
        $stmt = $pdo->prepare("UPDATE products SET status = 'rejected' WHERE id = ?");
        $stmt->execute([$target_product_id]);
        $msg_prod = "Produto rejeitado.";
    }
}

// Lógica para aprovar/rejeitar pedidos de compra
if (isset($_POST['action']) && isset($_POST['order_id'])) {
    $action = $_POST['action'];
    $target_order_id = (int)$_POST['order_id'];
    
    if ($action === 'approve_order') {
        $stmt = $pdo->prepare("UPDATE purchase_orders SET status = 'aprovado' WHERE id = ?");
        $stmt->execute([$target_order_id]);
        $msg_order = "Pedido aprovado com sucesso. O fabricante foi notificado para iniciar a produção.";
    } elseif ($action === 'reject_order') {
        $stmt = $pdo->prepare("UPDATE purchase_orders SET status = 'cancelado' WHERE id = ?");
        $stmt->execute([$target_order_id]);
        $msg_order = "Pedido cancelado.";
    }
}

// Buscar fabricantes pendentes
$stmt = $pdo->query("SELECT id, name, email, company_name, cnpj, created_at FROM users WHERE role = 'fabricante' AND status = 'pending' ORDER BY created_at DESC");
$pending_manufacturers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar produtos pendentes
$stmt = $pdo->query("SELECT p.id, p.name, p.type, p.created_at, u.company_name FROM products p JOIN users u ON p.manufacturer_id = u.id WHERE p.status = 'pending' ORDER BY p.created_at DESC");
$pending_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar pedidos pendentes
$stmt = $pdo->query("
    SELECT po.*, p.name as product_name, u_buyer.company_name as buyer_company, u_buyer.name as buyer_name, u_manuf.company_name as manuf_company
    FROM purchase_orders po
    JOIN quotes q ON po.quote_id = q.id
    JOIN products p ON q.product_id = p.id
    JOIN users u_buyer ON po.buyer_id = u_buyer.id
    JOIN users u_manuf ON po.manufacturer_id = u_manuf.id
    WHERE po.status = 'pendente_admin'
    ORDER BY po.created_at DESC
");
$pending_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador - PACKGATE</title>
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
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-8">
                <a href="dashboard_admin.php" class="text-2xl font-black text-brand-darkblue tracking-tighter">
                    PACK<span class="text-brand-green">GATE</span>
                </a>
                <span class="bg-brand-darkblue text-white text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">Admin</span>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($user_name); ?></p>
                    <p class="text-xs text-gray-500">Administrador</p>
                </div>
                <div class="w-10 h-10 rounded-full bg-brand-darkblue text-white flex items-center justify-center font-bold text-lg shadow-inner">
                    <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                </div>
                <a href="logout.php" class="text-gray-400 hover:text-red-500 transition" title="Sair">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full">
        
        <div class="mb-10">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight mb-2">Painel de Controle</h1>
            <p class="text-gray-600">Gerencie aprovações de fabricantes e produtos.</p>
        </div>

        <?php if (isset($msg_user)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($msg_user); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($msg_prod)): ?>
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                <?php echo htmlspecialchars($msg_prod); ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- Fabricantes Pendentes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-darkblue"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Fabricantes Pendentes
                        <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full ml-2"><?php echo count($pending_manufacturers); ?></span>
                    </h2>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <?php if (empty($pending_manufacturers)): ?>
                        <div class="p-8 text-center text-gray-500">
                            Nenhum fabricante pendente de aprovação.
                        </div>
                    <?php else: ?>
                        <?php foreach ($pending_manufacturers as $manuf): ?>
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($manuf['company_name'] ?: $manuf['name']); ?></h3>
                                        <p class="text-sm text-gray-500"><?php echo htmlspecialchars($manuf['email']); ?> • CNPJ: <?php echo htmlspecialchars($manuf['cnpj'] ?: 'Não informado'); ?></p>
                                        <p class="text-xs text-gray-400 mt-1">Cadastrado em: <?php echo date('d/m/Y H:i', strtotime($manuf['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="action" value="approve_user">
                                        <input type="hidden" name="user_id" value="<?php echo $manuf['id']; ?>">
                                        <button type="submit" class="w-full py-2 bg-brand-green hover:bg-brand-olive text-white text-sm font-semibold rounded-lg transition">Aprovar</button>
                                    </form>
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="action" value="reject_user">
                                        <input type="hidden" name="user_id" value="<?php echo $manuf['id']; ?>">
                                        <button type="submit" class="w-full py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-lg transition" onclick="return confirm('Tem certeza que deseja rejeitar este fabricante?');">Rejeitar</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Produtos Pendentes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-darkblue"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        Produtos Pendentes
                        <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full ml-2"><?php echo count($pending_products); ?></span>
                    </h2>
                </div>
                
                <div class="divide-y divide-gray-100">
                    <?php if (empty($pending_products)): ?>
                        <div class="p-8 text-center text-gray-500">
                            Nenhum produto pendente de aprovação.
                        </div>
                    <?php else: ?>
                        <?php foreach ($pending_products as $prod): ?>
                            <div class="p-6 hover:bg-gray-50 transition">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="font-bold text-gray-900 text-lg"><?php echo htmlspecialchars($prod['name']); ?></h3>
                                        <p class="text-sm text-gray-500">Fabricante: <?php echo htmlspecialchars($prod['company_name']); ?></p>
                                        <p class="text-xs text-gray-400 mt-1">Tipo: <?php echo htmlspecialchars($prod['type']); ?> • Cadastrado em: <?php echo date('d/m/Y H:i', strtotime($prod['created_at'])); ?></p>
                                    </div>
                                </div>
                                <div class="flex gap-3">
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="action" value="approve_product">
                                        <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                        <button type="submit" class="w-full py-2 bg-brand-green hover:bg-brand-olive text-white text-sm font-semibold rounded-lg transition">Aprovar</button>
                                    </form>
                                    <form method="POST" class="flex-1">
                                        <input type="hidden" name="action" value="reject_product">
                                        <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                        <button type="submit" class="w-full py-2 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-sm font-semibold rounded-lg transition" onclick="return confirm('Tem certeza que deseja rejeitar este produto?');">Rejeitar</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Pedidos Pendentes -->
        <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-brand-darkblue"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    Pedidos de Compra Pendentes de Aprovação
                    <span class="bg-orange-100 text-orange-700 text-xs font-bold px-2 py-0.5 rounded-full ml-2"><?php echo count($pending_orders); ?></span>
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-4 font-medium">ID Pedido</th>
                            <th class="px-6 py-4 font-medium">Produto</th>
                            <th class="px-6 py-4 font-medium">Comprador</th>
                            <th class="px-6 py-4 font-medium">Fabricante</th>
                            <th class="px-6 py-4 font-medium">Valor Total</th>
                            <th class="px-6 py-4 font-medium">Data</th>
                            <th class="px-6 py-4 font-medium text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($pending_orders)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Nenhum pedido pendente de aprovação.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pending_orders as $order): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-bold text-gray-900">#PED-<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($order['product_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($order['buyer_company'] ?: $order['buyer_name']); ?></td>
                                    <td class="px-6 py-4"><?php echo htmlspecialchars($order['manuf_company']); ?></td>
                                    <td class="px-6 py-4 font-bold text-brand-darkblue">R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></td>
                                    <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="approve_order">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <button type="submit" class="px-3 py-1.5 bg-brand-green hover:bg-brand-olive text-white text-xs font-bold rounded-lg transition">Aprovar</button>
                                            </form>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="reject_order">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <button type="submit" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold rounded-lg transition" onclick="return confirm('Tem certeza que deseja cancelar este pedido?');">Cancelar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>
