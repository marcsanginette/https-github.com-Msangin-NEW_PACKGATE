<?php
session_start();
require_once 'conexao.php';

// Verifica se está logado e se é comprador
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'comprador') {
    header("Location: login.php");
    exit;
}

$user_name = explode(' ', trim($_SESSION['user_name']))[0];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Comprador - PACKGATE</title>
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
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .nav-item.active { background-color: #eef2ff; color: #1A237E; border-right: 4px solid #8DC63F; font-weight: 600; }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 z-20">
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <a href="index.php" class="text-2xl font-black text-brand-darkblue tracking-tighter">
                PACK<span class="text-brand-green">GATE</span>
            </a>
        </div>
        
        <div class="p-6 pb-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu Principal</p>
        </div>

        <nav class="flex-1 overflow-y-auto">
            <ul class="space-y-1">
                <li>
                    <button onclick="switchTab('produtos', this)" class="nav-item active w-full flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        Buscar Produtos
                    </button>
                </li>
                <li>
                    <button onclick="switchTab('orcamentos', this)" class="nav-item w-full flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                        Meus Orçamentos
                        <span class="ml-auto bg-brand-lightgreen text-brand-olive text-xs font-bold px-2 py-0.5 rounded-full">2</span>
                    </button>
                </li>
                <li>
                    <button onclick="switchTab('pedidos', this)" class="nav-item w-full flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Pedidos de Compra
                    </button>
                </li>
                <li>
                    <button onclick="switchTab('notificacoes', this)" class="nav-item w-full flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        Notificações
                        <span class="ml-auto bg-red-100 text-red-600 text-xs font-bold px-2 py-0.5 rounded-full">1</span>
                    </button>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <a href="logout.php" class="flex items-center gap-3 px-4 py-2 text-red-500 hover:bg-red-50 rounded-lg transition font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                Sair da Conta
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 flex-shrink-0 z-10">
            <h2 class="text-lg font-bold text-brand-darkblue" id="page-title">Buscar Produtos</h2>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-xs text-gray-500">Comprador</p>
                    <p class="text-sm font-bold text-gray-900"><?php echo htmlspecialchars($user_name); ?></p>
                </div>
                
                <!-- Ícone do Usuário com funcionalidade de Sair -->
                <a href="logout.php" title="Sair e voltar ao início" class="w-10 h-10 rounded-full bg-brand-lightgreen text-brand-olive flex items-center justify-center font-bold text-lg hover:bg-red-100 hover:text-red-600 transition group relative cursor-pointer">
                    <span class="group-hover:hidden"><?php echo strtoupper(substr($user_name, 0, 1)); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden group-hover:block"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                </a>
            </div>
        </header>

        <!-- Scrollable Area -->
        <div class="flex-1 overflow-y-auto p-8">
            
            <!-- ABA: BUSCAR PRODUTOS -->
            <div id="produtos" class="tab-content active h-full">
                <div class="flex flex-col md:flex-row gap-6 h-full">
                    <!-- Filtros Laterais -->
                    <div class="w-full md:w-64 bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex-shrink-0 h-fit">
                        <form method="GET" action="dashboard_comprador.php" id="searchForm">
                            <input type="hidden" name="tab" value="produtos">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="font-bold text-gray-900">Filtros</h3>
                                <a href="dashboard_comprador.php?tab=produtos" class="text-xs text-brand-green hover:underline">Limpar</a>
                            </div>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Ordenar por</label>
                                    <select name="sort" class="w-full text-sm border-gray-300 rounded-lg focus:ring-brand-green focus:border-brand-green p-2 border outline-none" onchange="document.getElementById('searchForm').submit()">
                                        <option value="recent" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'recent') ? 'selected' : ''; ?>>Mais Recentes</option>
                                        <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Menor Preço</option>
                                        <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Maior Preço</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Categoria</label>
                                    <select name="category" class="w-full text-sm border-gray-300 rounded-lg focus:ring-brand-green focus:border-brand-green p-2 border outline-none" onchange="document.getElementById('searchForm').submit()">
                                        <option value="">Todas as Categorias</option>
                                        <?php
                                        $stmt_cat = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
                                        while ($cat = $stmt_cat->fetch(PDO::FETCH_ASSOC)) {
                                            $selected = (isset($_GET['category']) && $_GET['category'] == $cat['id']) ? 'selected' : '';
                                            echo "<option value=\"{$cat['id']}\" $selected>" . htmlspecialchars($cat['name']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Tipo</label>
                                    <div class="space-y-2">
                                        <?php
                                        $types = ['Papelão', 'Plástico', 'Vidro', 'Sustentável', 'Metal', 'Madeira', 'Outro'];
                                        $selected_types = isset($_GET['type']) ? $_GET['type'] : [];
                                        foreach ($types as $t) {
                                            $checked = in_array($t, $selected_types) ? 'checked' : '';
                                            echo "<label class=\"flex items-center text-sm text-gray-700\"><input type=\"checkbox\" name=\"type[]\" value=\"$t\" $checked class=\"mr-2 text-brand-green focus:ring-brand-green rounded\" onchange=\"document.getElementById('searchForm').submit()\"> $t</label>";
                                        }
                                        ?>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Personalização</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center text-sm text-gray-700">
                                            <input type="radio" name="customizable" value="" <?php echo (!isset($_GET['customizable']) || $_GET['customizable'] === '') ? 'checked' : ''; ?> class="mr-2 text-brand-green focus:ring-brand-green" onchange="document.getElementById('searchForm').submit()"> Todos
                                        </label>
                                        <label class="flex items-center text-sm text-gray-700">
                                            <input type="radio" name="customizable" value="1" <?php echo (isset($_GET['customizable']) && $_GET['customizable'] === '1') ? 'checked' : ''; ?> class="mr-2 text-brand-green focus:ring-brand-green" onchange="document.getElementById('searchForm').submit()"> Personalizável
                                        </label>
                                        <label class="flex items-center text-sm text-gray-700">
                                            <input type="radio" name="customizable" value="0" <?php echo (isset($_GET['customizable']) && $_GET['customizable'] === '0') ? 'checked' : ''; ?> class="mr-2 text-brand-green focus:ring-brand-green" onchange="document.getElementById('searchForm').submit()"> Padrão
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="text-xs font-bold text-gray-500 uppercase mb-2 block">Volume / Capacidade</label>
                                    <input type="text" name="volume" value="<?php echo isset($_GET['volume']) ? htmlspecialchars($_GET['volume']) : ''; ?>" placeholder="Ex: 500ml, 2L" class="w-full text-sm border-gray-300 rounded-lg focus:ring-brand-green focus:border-brand-green p-2 border outline-none">
                                    <button type="submit" class="mt-2 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-2 rounded transition">Aplicar Filtro</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Grid de Produtos -->
                    <div class="flex-1">
                        <form method="GET" action="dashboard_comprador.php" class="relative mb-6">
                            <input type="hidden" name="tab" value="produtos">
                            <?php if (isset($_GET['sort'])): ?>
                                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort']); ?>">
                            <?php endif; ?>
                            <?php if (isset($_GET['type'])): ?>
                                <?php foreach ($_GET['type'] as $t): ?>
                                    <input type="hidden" name="type[]" value="<?php echo htmlspecialchars($t); ?>">
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <input type="text" name="q" value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>" placeholder="Buscar por nome, material ou fabricante..." class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none shadow-sm">
                            <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-brand-green">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                            </button>
                        </form>

                        <?php
                        // Construir a query de busca
                        $query = "SELECT p.*, u.company_name, u.name as manufacturer_name 
                                  FROM products p 
                                  JOIN users u ON p.manufacturer_id = u.id 
                                  WHERE p.status = 'approved'";
                        $params = [];

                        // Filtro de busca por texto
                        if (!empty($_GET['q'])) {
                            $query .= " AND (p.name LIKE ? OR p.type LIKE ? OR u.company_name LIKE ? OR u.name LIKE ?)";
                            $search_term = '%' . $_GET['q'] . '%';
                            $params[] = $search_term;
                            $params[] = $search_term;
                            $params[] = $search_term;
                            $params[] = $search_term;
                        }

                        // Filtro por categoria
                        if (!empty($_GET['category'])) {
                            $query .= " AND p.category_id = ?";
                            $params[] = $_GET['category'];
                        }

                        // Filtro por tipo
                        if (!empty($_GET['type']) && is_array($_GET['type'])) {
                            $in_placeholders = str_repeat('?,', count($_GET['type']) - 1) . '?';
                            $query .= " AND p.type IN ($in_placeholders)";
                            $params = array_merge($params, $_GET['type']);
                        }

                        // Filtro por personalização
                        if (isset($_GET['customizable']) && $_GET['customizable'] !== '') {
                            $query .= " AND p.customizable = ?";
                            $params[] = $_GET['customizable'];
                        }

                        // Filtro por volume
                        if (!empty($_GET['volume'])) {
                            $query .= " AND p.volume LIKE ?";
                            $params[] = '%' . $_GET['volume'] . '%';
                        }

                        // Ordenação
                        $sort = $_GET['sort'] ?? 'recent';
                        if ($sort === 'price_asc') {
                            $query .= " ORDER BY p.price ASC";
                        } elseif ($sort === 'price_desc') {
                            $query .= " ORDER BY p.price DESC";
                        } else {
                            $query .= " ORDER BY p.created_at DESC";
                        }

                        $stmt = $pdo->prepare($query);
                        $stmt->execute($params);
                        $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                            <?php if (empty($produtos)): ?>
                                <div class="col-span-3 text-center py-10 text-gray-500">
                                    Nenhum produto encontrado no momento.
                                </div>
                            <?php else: ?>
                                <?php foreach ($produtos as $prod): ?>
                                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition group flex flex-col">
                                        <div class="h-40 bg-gray-200 relative overflow-hidden">
                                            <?php 
                                            $img_src = !empty($prod['image_url']) ? htmlspecialchars($prod['image_url']) : 'https://via.placeholder.com/500x500?text=Sem+Imagem';
                                            ?>
                                            <img src="<?php echo $img_src; ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="<?php echo htmlspecialchars($prod['name']); ?>">
                                            <span class="absolute top-2 left-2 bg-white/90 text-xs font-bold px-2 py-1 rounded text-gray-700"><?php echo htmlspecialchars($prod['type']); ?></span>
                                        </div>
                                        <div class="p-4 flex flex-col flex-grow">
                                            <h4 class="font-bold text-gray-900 mb-1 line-clamp-1" title="<?php echo htmlspecialchars($prod['name']); ?>"><?php echo htmlspecialchars($prod['name']); ?></h4>
                                            <p class="text-xs text-gray-500 mb-3 line-clamp-1" title="<?php echo htmlspecialchars($prod['company_name'] ?: $prod['manufacturer_name']); ?>">Fabricante: <?php echo htmlspecialchars($prod['company_name'] ?: $prod['manufacturer_name']); ?></p>
                                            <div class="mt-auto flex items-center justify-between">
                                                <span class="text-xs text-gray-500">Mín: <?php echo htmlspecialchars($prod['min_quantity']); ?> un</span>
                                                <a href="produto.php?id=<?php echo $prod['id']; ?>" class="text-sm bg-brand-lightgreen text-brand-olive hover:bg-brand-green hover:text-white px-3 py-1.5 rounded-lg font-medium transition">Orçar</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Paginação -->
                        <div class="flex justify-center gap-2">
                            <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:bg-gray-50">&laquo;</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded bg-brand-green text-white font-bold">1</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-700 hover:bg-gray-50">2</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-700 hover:bg-gray-50">3</button>
                            <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-200 text-gray-500 hover:bg-gray-50">&raquo;</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ABA: MEUS ORÇAMENTOS -->
            <div id="orcamentos" class="tab-content">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-900">Solicitações de Orçamento</h3>
                        <div class="flex gap-2">
                            <select class="text-sm border-gray-300 rounded-lg focus:ring-brand-green focus:border-brand-green p-2 border outline-none">
                                <option>Todos os Status</option>
                                <option>Aguardando</option>
                                <option>Respondido</option>
                            </select>
                        </div>
                    </div>

                    <?php
                    // Buscar orçamentos do comprador
                    $stmt = $pdo->prepare("
                        SELECT q.*, p.name as product_name, u.company_name as manufacturer_company, u.name as manufacturer_name
                        FROM quotes q
                        JOIN products p ON q.product_id = p.id
                        JOIN users u ON q.manufacturer_id = u.id
                        WHERE q.buyer_id = ?
                        ORDER BY q.created_at DESC
                    ");
                    $stmt->execute([$_SESSION['user_id']]);
                    $buyer_quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-medium">ID</th>
                                    <th class="px-6 py-4 font-medium">Produto</th>
                                    <th class="px-6 py-4 font-medium">Fabricante</th>
                                    <th class="px-6 py-4 font-medium">Quantidade</th>
                                    <th class="px-6 py-4 font-medium">Data</th>
                                    <th class="px-6 py-4 font-medium">Status</th>
                                    <th class="px-6 py-4 font-medium text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($buyer_quotes)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            Nenhum orçamento solicitado ainda.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($buyer_quotes as $quote): ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 font-medium text-gray-900">#ORC-<?php echo str_pad($quote['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($quote['product_name']); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($quote['manufacturer_company'] ?: $quote['manufacturer_name']); ?></td>
                                            <td class="px-6 py-4 font-bold"><?php echo number_format($quote['quantity'], 0, ',', '.'); ?> un</td>
                                            <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($quote['created_at'])); ?></td>
                                            <td class="px-6 py-4">
                                                <?php if ($quote['status'] === 'aguardando'): ?>
                                                    <span class="bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full text-xs font-bold">Aguardando</span>
                                                <?php elseif ($quote['status'] === 'respondido'): ?>
                                                    <span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full text-xs font-bold">Respondido</span>
                                                <?php elseif ($quote['status'] === 'recusado'): ?>
                                                    <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold">Recusado</span>
                                                <?php elseif ($quote['status'] === 'aprovado'): ?>
                                                    <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold">Aprovado</span>
                                                <?php elseif ($quote['status'] === 'pedido_criado'): ?>
                                                    <span class="bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full text-xs font-bold">Pedido Criado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <?php if ($quote['status'] === 'respondido'): ?>
                                                    <a href="revisar_proposta.php?id=<?php echo $quote['id']; ?>" class="text-brand-green hover:text-brand-olive font-bold text-sm">Ver Proposta</a>
                                                <?php else: ?>
                                                    <a href="revisar_proposta.php?id=<?php echo $quote['id']; ?>" class="text-gray-500 hover:text-gray-700 font-medium text-sm">Detalhes</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ABA: PEDIDOS DE COMPRA -->
            <div id="pedidos" class="tab-content">
                <!-- Abas de Status -->
                <div class="flex border-b border-gray-200 mb-6 overflow-x-auto hide-scrollbar">
                    <button class="px-6 py-3 text-sm font-bold text-brand-darkblue border-b-2 border-brand-green whitespace-nowrap">Todos</button>
                    <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">Pendentes</button>
                    <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">Em Produção</button>
                    <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap relative">
                        Enviados
                    </button>
                    <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">Concluídos</button>
                </div>

                <?php
                // Buscar pedidos do comprador
                $stmt = $pdo->prepare("
                    SELECT po.*, p.name as product_name, q.quantity, q.real_delivery_date, u.company_name as manufacturer_company, u.name as manufacturer_name
                    FROM purchase_orders po
                    JOIN quotes q ON po.quote_id = q.id
                    JOIN products p ON q.product_id = p.id
                    JOIN users u ON po.manufacturer_id = u.id
                    WHERE po.buyer_id = ?
                    ORDER BY po.created_at DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $buyer_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-medium">Pedido</th>
                                    <th class="px-6 py-4 font-medium">Produto</th>
                                    <th class="px-6 py-4 font-medium">Fabricante</th>
                                    <th class="px-6 py-4 font-medium">Valor Total</th>
                                    <th class="px-6 py-4 font-medium">Previsão</th>
                                    <th class="px-6 py-4 font-medium">Status</th>
                                    <th class="px-6 py-4 font-medium text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($buyer_orders)): ?>
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            Nenhum pedido de compra encontrado.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($buyer_orders as $order): ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 font-medium text-gray-900">#PED-<?php echo str_pad($order['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($order['product_name']); ?> (x<?php echo number_format($order['quantity'], 0, ',', '.'); ?>)</td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($order['manufacturer_company'] ?: $order['manufacturer_name']); ?></td>
                                            <td class="px-6 py-4 font-bold text-brand-darkblue">R$ <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></td>
                                            <td class="px-6 py-4"><?php echo date('d/m/Y', strtotime($order['real_delivery_date'])); ?></td>
                                            <td class="px-6 py-4">
                                                <?php if ($order['status'] === 'pendente_admin'): ?>
                                                    <span class="bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full text-xs font-bold">Pendente Aprovação</span>
                                                <?php elseif ($order['status'] === 'aprovado'): ?>
                                                    <span class="bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full text-xs font-bold">Aprovado</span>
                                                <?php elseif ($order['status'] === 'em_producao'): ?>
                                                    <span class="bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full text-xs font-bold flex items-center gap-1 w-fit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                                        Em Produção
                                                    </span>
                                                <?php elseif ($order['status'] === 'enviado'): ?>
                                                    <span class="bg-indigo-100 text-indigo-700 px-2.5 py-1 rounded-full text-xs font-bold flex items-center gap-1 w-fit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 18H3c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h10c.6 0 1 .4 1 1v11"/><path d="M14 9h4l4 4v4c0 .6-.4 1-1 1h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>
                                                        Enviado
                                                    </span>
                                                <?php elseif ($order['status'] === 'concluido'): ?>
                                                    <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold">Concluído</span>
                                                <?php elseif ($order['status'] === 'cancelado'): ?>
                                                    <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-bold">Cancelado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="detalhes_pedido.php?id=<?php echo $order['id']; ?>" class="text-gray-500 hover:text-gray-700 font-medium text-sm">Detalhes</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ABA: NOTIFICAÇÕES -->
            <div id="notificacoes" class="tab-content">
                <div class="max-w-3xl mx-auto">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-900 text-lg">Suas Notificações</h3>
                        <button class="text-sm text-brand-green hover:underline">Marcar todas como lidas</button>
                    </div>

                    <div class="space-y-4">
                        <!-- Notificação Não Lida -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border-l-4 border-brand-green flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-full bg-green-50 flex items-center justify-center flex-shrink-0 text-brand-green">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">Nova Proposta Recebida</h4>
                                <p class="text-sm text-gray-600 mt-1">A empresa EcoEmbalagens Ltda respondeu ao seu orçamento #ORC-1042.</p>
                                <span class="text-xs text-gray-400 mt-2 block">Há 2 horas</span>
                            </div>
                        </div>

                        <!-- Notificação Lida -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex gap-4 items-start opacity-75">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 18H3c-.6 0-1-.4-1-1V7c0-.6.4-1 1-1h10c.6 0 1 .4 1 1v11"/><path d="M14 9h4l4 4v4c0 .6-.4 1-1 1h-2"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">Pedido Enviado</h4>
                                <p class="text-sm text-gray-600 mt-1">Seu pedido #PO-9921 foi despachado pela transportadora.</p>
                                <span class="text-xs text-gray-400 mt-2 block">Ontem</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAIS -->

    <!-- Modal: Ver Proposta -->
    <div id="modal-proposta" class="fixed inset-0 bg-gray-900/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-900">Proposta de Orçamento #ORC-1042</h3>
                <button onclick="closeModal('modal-proposta')" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Fabricante</p>
                        <p class="font-medium text-gray-900">EcoEmbalagens Ltda</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500 uppercase font-bold">Validade</p>
                        <p class="font-medium text-gray-900">20/03/2026</p>
                    </div>
                </div>
                <hr class="border-gray-100">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-600">Produto:</span>
                        <span class="text-sm font-medium text-gray-900">Caixa de Papelão Ondulado</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-600">Quantidade:</span>
                        <span class="text-sm font-medium text-gray-900">5.000 un</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm text-gray-600">Prazo de Produção:</span>
                        <span class="text-sm font-medium text-gray-900">15 dias úteis</span>
                    </div>
                    <div class="flex justify-between pt-2 mt-2 border-t border-gray-200">
                        <span class="font-bold text-gray-900">Valor Total:</span>
                        <span class="font-bold text-brand-darkblue text-lg">R$ 12.500,00</span>
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium mb-1">Mensagem do Fabricante:</p>
                    <p class="text-sm text-gray-500 italic bg-gray-50 p-3 rounded border border-gray-100">"Podemos conceder 5% de desconto para pagamento à vista. Frete CIF para a região de São Paulo."</p>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50">
                <button onclick="closeModal('modal-proposta')" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-200 rounded-lg transition">Recusar</button>
                <button onclick="closeModal('modal-proposta'); alert('Pedido de Compra (PO) gerado com sucesso!');" class="px-4 py-2 text-sm font-bold text-white bg-brand-green hover:bg-brand-olive rounded-lg transition shadow-sm">Aprovar e Gerar PO</button>
            </div>
        </div>
    </div>

    <!-- Modal: Avaliação -->
    <div id="modal-avaliacao" class="fixed inset-0 bg-gray-900/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-brand-darkblue text-white">
                <h3 class="font-bold">Confirmar Recebimento</h3>
                <button onclick="closeModal('modal-avaliacao')" class="text-white/70 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h4 class="font-bold text-gray-900 mb-2">Pedido Recebido!</h4>
                <p class="text-sm text-gray-500 mb-6">Como você avalia o fornecedor e a qualidade do produto do pedido #PO-9921?</p>
                
                <!-- Estrelas -->
                <div class="flex justify-center gap-2 mb-6 text-gray-300">
                    <svg class="w-8 h-8 cursor-pointer hover:text-yellow-400 text-yellow-400 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg class="w-8 h-8 cursor-pointer hover:text-yellow-400 text-yellow-400 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg class="w-8 h-8 cursor-pointer hover:text-yellow-400 text-yellow-400 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg class="w-8 h-8 cursor-pointer hover:text-yellow-400 text-yellow-400 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <svg class="w-8 h-8 cursor-pointer hover:text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                </div>

                <textarea rows="3" placeholder="Deixe um comentário opcional sobre a entrega e o produto..." class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none text-sm text-gray-700 resize-none"></textarea>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50">
                <button onclick="closeModal('modal-avaliacao'); alert('Avaliação enviada! O pedido foi concluído.');" class="w-full py-3 text-sm font-bold text-white bg-brand-green hover:bg-brand-olive rounded-lg transition shadow-sm">Enviar Avaliação</button>
            </div>
        </div>
    </div>

    <script>
        // Lógica de Tabs
        function switchTab(tabId, element) {
            // Esconde todos os conteúdos
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            // Remove active de todos os botões do menu
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            
            // Mostra o conteúdo selecionado
            document.getElementById(tabId).classList.add('active');
            // Adiciona active no botão clicado
            if (element) {
                element.classList.add('active');
            } else {
                // Find the corresponding nav item if element is not provided
                const navItem = document.querySelector(`.nav-item[onclick*="${tabId}"]`);
                if (navItem) navItem.classList.add('active');
            }

            // Atualiza o título da página
            const titles = {
                'produtos': 'Buscar Produtos',
                'orcamentos': 'Meus Orçamentos',
                'pedidos': 'Pedidos de Compra',
                'notificacoes': 'Notificações'
            };
            document.getElementById('page-title').innerText = titles[tabId];
        }

        // Lógica de Modais
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Switch to correct tab on load if specified in URL
        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab && ['produtos', 'orcamentos', 'pedidos', 'notificacoes'].includes(tab)) {
                switchTab(tab);
            }
        });
    </script>
</body>
</html>
