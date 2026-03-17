<?php
session_start();
require_once 'conexao.php';

// Verifica se está logado e se é fabricante
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'fabricante') {
    header("Location: login.php");
    exit;
}

$user_name = explode(' ', trim($_SESSION['user_name']))[0];
$user_id = $_SESSION['user_id'];

// Buscar produtos do fabricante
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.id 
    WHERE p.manufacturer_id = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$user_id]);
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Fabricante - PACKGATE</title>
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
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu do Fornecedor</p>
        </div>

        <nav class="flex-1 overflow-y-auto">
            <ul class="space-y-1">
                <li>
                    <button onclick="switchTab('produtos', this)" class="nav-item active w-full flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                        Meus Produtos
                    </button>
                </li>
                <li>
                    <button onclick="switchTab('orcamentos', this)" class="nav-item w-full flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                        Orçamentos Recebidos
                        <span class="ml-auto bg-brand-lightgreen text-brand-olive text-xs font-bold px-2 py-0.5 rounded-full">3</span>
                    </button>
                </li>
                <li>
                    <button onclick="switchTab('pedidos', this)" class="nav-item w-full flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Pedidos de Venda
                    </button>
                </li>
                <li>
                    <button onclick="switchTab('notificacoes', this)" class="nav-item w-full flex items-center gap-3 px-6 py-3 text-gray-600 hover:bg-gray-50 transition text-left">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        Notificações
                    </button>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col h-screen overflow-hidden relative">
        <!-- Header -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 flex-shrink-0 z-10">
            <h2 class="text-lg font-bold text-brand-darkblue" id="page-title">Meus Produtos</h2>
            
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-xs text-gray-500">Fabricante</p>
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
            
            <!-- ABA: MEUS PRODUTOS -->
            <div id="produtos" class="tab-content active h-full">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-900">Catálogo de Produtos</h3>
                        <a href="cadastrar_produto.php" class="bg-brand-green hover:bg-brand-olive text-white px-4 py-2 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="5" y2="19"/><line x1="5" x2="19" y1="12" y2="12"/></svg>
                            Adicionar Produto
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-medium">Ref</th>
                                    <th class="px-6 py-4 font-medium">Produto</th>
                                    <th class="px-6 py-4 font-medium">Categoria</th>
                                    <th class="px-6 py-4 font-medium">Preço Base</th>
                                    <th class="px-6 py-4 font-medium">Qtd Mínima</th>
                                    <th class="px-6 py-4 font-medium text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (count($produtos) > 0): ?>
                                    <?php foreach ($produtos as $produto): ?>
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 font-medium text-gray-900">#PRD-<?php echo str_pad($produto['id'], 3, '0', STR_PAD_LEFT); ?></td>
                                            <td class="px-6 py-4 font-medium text-gray-900">
                                                <div class="flex items-center gap-3">
                                                    <?php if ($produto['image_url']): ?>
                                                        <img src="<?php echo htmlspecialchars($produto['image_url']); ?>" alt="Produto" class="w-10 h-10 rounded object-cover border border-gray-200">
                                                    <?php else: ?>
                                                        <div class="w-10 h-10 rounded bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-400">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php echo htmlspecialchars($produto['name']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($produto['category_name'] ?? 'Sem Categoria'); ?></td>
                                            <td class="px-6 py-4 text-brand-darkblue font-bold">R$ <?php echo number_format($produto['price'], 2, ',', '.'); ?></td>
                                            <td class="px-6 py-4"><?php echo htmlspecialchars($produto['min_quantity']); ?></td>
                                            <td class="px-6 py-4 text-right">
                                                <button class="text-blue-500 hover:text-blue-700 font-medium text-sm mr-3">Editar</button>
                                                <button class="text-red-500 hover:text-red-700 font-medium text-sm">Excluir</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                            Nenhum produto cadastrado ainda. <br>
                                            <a href="cadastrar_produto.php" class="text-brand-green font-bold hover:underline mt-2 inline-block">Cadastrar meu primeiro produto</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ABA: ORÇAMENTOS RECEBIDOS -->
            <div id="orcamentos" class="tab-content">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-gray-900">Solicitações de Compradores</h3>
                        <div class="flex gap-2">
                            <select class="text-sm border-gray-300 rounded-lg focus:ring-brand-green focus:border-brand-green p-2 border outline-none">
                                <option>Todos os Status</option>
                                <option>Aguardando Resposta</option>
                                <option>Respondidos</option>
                            </select>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-medium">ID</th>
                                    <th class="px-6 py-4 font-medium">Comprador</th>
                                    <th class="px-6 py-4 font-medium">Produto Solicitado</th>
                                    <th class="px-6 py-4 font-medium">Quantidade</th>
                                    <th class="px-6 py-4 font-medium">Data</th>
                                    <th class="px-6 py-4 font-medium">Status</th>
                                    <th class="px-6 py-4 font-medium text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">#ORC-1042</td>
                                    <td class="px-6 py-4">Logística Express S/A</td>
                                    <td class="px-6 py-4">Caixa de Papelão Ondulado</td>
                                    <td class="px-6 py-4 font-bold">5.000 un</td>
                                    <td class="px-6 py-4">15/03/2026</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-yellow-100 text-yellow-700 px-2.5 py-1 rounded-full text-xs font-bold">Aguardando</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="openModal('modal-responder-orcamento')" class="text-brand-green hover:text-brand-olive font-bold text-sm">Responder</button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">#ORC-1038</td>
                                    <td class="px-6 py-4">Mercado Central</td>
                                    <td class="px-6 py-4">Bobina de Plástico Bolha</td>
                                    <td class="px-6 py-4 font-bold">50 rolos</td>
                                    <td class="px-6 py-4">12/03/2026</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-bold">Respondido</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="text-gray-500 hover:text-gray-700 font-medium text-sm">Ver Proposta</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ABA: PEDIDOS DE VENDA -->
            <div id="pedidos" class="tab-content">
                <!-- Abas de Status -->
                <div class="flex border-b border-gray-200 mb-6 overflow-x-auto hide-scrollbar">
                    <button class="px-6 py-3 text-sm font-bold text-brand-darkblue border-b-2 border-brand-green whitespace-nowrap">Todos</button>
                    <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap relative">
                        Novos <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">Em Produção</button>
                    <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">Enviados</button>
                    <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap">Concluídos</button>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-600">
                            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                                <tr>
                                    <th class="px-6 py-4 font-medium">Pedido</th>
                                    <th class="px-6 py-4 font-medium">Comprador</th>
                                    <th class="px-6 py-4 font-medium">Produto</th>
                                    <th class="px-6 py-4 font-medium">Valor Total</th>
                                    <th class="px-6 py-4 font-medium">Previsão</th>
                                    <th class="px-6 py-4 font-medium">Status</th>
                                    <th class="px-6 py-4 font-medium text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <!-- Pedido Novo -->
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">#PO-9925</td>
                                    <td class="px-6 py-4">Logística Express S/A</td>
                                    <td class="px-6 py-4">Caixa de Papelão (x5000)</td>
                                    <td class="px-6 py-4 font-medium text-brand-darkblue">R$ 12.500,00</td>
                                    <td class="px-6 py-4">30/03/2026</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full text-xs font-bold">Pendente</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="openModal('modal-atualizar-pedido')" class="bg-brand-green hover:bg-brand-olive text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                            Atualizar Status
                                        </button>
                                    </td>
                                </tr>
                                <!-- Pedido Em Produção -->
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 font-medium text-gray-900">#PO-9910</td>
                                    <td class="px-6 py-4">Mercado Central</td>
                                    <td class="px-6 py-4">Bobina Plástico (x50)</td>
                                    <td class="px-6 py-4 font-medium text-brand-darkblue">R$ 2.250,00</td>
                                    <td class="px-6 py-4">20/03/2026</td>
                                    <td class="px-6 py-4">
                                        <span class="bg-purple-100 text-purple-700 px-2.5 py-1 rounded-full text-xs font-bold">Em Produção</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button onclick="openModal('modal-atualizar-pedido')" class="bg-brand-green hover:bg-brand-olive text-white px-3 py-1.5 rounded-lg text-xs font-bold transition shadow-sm">
                                            Atualizar Status
                                        </button>
                                    </td>
                                </tr>
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">Novo Orçamento Solicitado</h4>
                                <p class="text-sm text-gray-600 mt-1">A empresa Logística Express solicitou orçamento para Caixa de Papelão.</p>
                                <span class="text-xs text-gray-400 mt-2 block">Há 10 minutos</span>
                            </div>
                        </div>

                        <!-- Notificação Lida -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex gap-4 items-start opacity-75">
                            <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0 text-blue-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-sm">Novo Pedido Aprovado!</h4>
                                <p class="text-sm text-gray-600 mt-1">O orçamento #ORC-1038 foi aprovado e gerou o pedido #PO-9910.</p>
                                <span class="text-xs text-gray-400 mt-2 block">Ontem</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAIS -->

    <!-- Modal: Adicionar Produto -->
    <div id="modal-novo-produto" class="fixed inset-0 bg-gray-900/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-900">Adicionar Novo Produto</h3>
                <button onclick="closeModal('modal-novo-produto')" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-1.5">Nome do Produto</label>
                    <input type="text" placeholder="Ex: Caixa de Papelão Duplo" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Categoria</label>
                        <select class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700 bg-white">
                            <option>Papelão</option>
                            <option>Plástico</option>
                            <option>Vidro</option>
                            <option>Sustentável</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Material</label>
                        <input type="text" placeholder="Ex: Kraft" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Preço Base (R$)</label>
                        <input type="number" step="0.01" placeholder="0,00" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Qtd Mínima</label>
                        <input type="number" placeholder="Ex: 100" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-1.5">Descrição</label>
                    <textarea rows="3" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700 resize-none"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50">
                <button onclick="closeModal('modal-novo-produto')" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-200 rounded-lg transition">Cancelar</button>
                <button onclick="closeModal('modal-novo-produto'); alert('Produto salvo com sucesso!');" class="px-4 py-2 text-sm font-bold text-white bg-brand-green hover:bg-brand-olive rounded-lg transition shadow-sm">Salvar Produto</button>
            </div>
        </div>
    </div>

    <!-- Modal: Responder Orçamento -->
    <div id="modal-responder-orcamento" class="fixed inset-0 bg-gray-900/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-900">Responder Orçamento #ORC-1042</h3>
                <button onclick="closeModal('modal-responder-orcamento')" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg mb-4">
                    <p class="text-sm text-blue-800"><strong>Comprador:</strong> Logística Express S/A</p>
                    <p class="text-sm text-blue-800"><strong>Produto:</strong> Caixa de Papelão Ondulado</p>
                    <p class="text-sm text-blue-800"><strong>Quantidade Solicitada:</strong> 5.000 un</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Valor Total Proposto (R$)</label>
                        <input type="number" step="0.01" placeholder="Ex: 12500,00" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700 font-bold text-brand-darkblue">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Validade da Proposta</label>
                        <input type="date" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-1.5">Mensagem para o Comprador</label>
                    <textarea rows="3" placeholder="Detalhes sobre frete, prazo de produção, descontos..." class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700 resize-none"></textarea>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50">
                <button onclick="closeModal('modal-responder-orcamento')" class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition">Recusar Orçamento</button>
                <button onclick="closeModal('modal-responder-orcamento'); alert('Proposta enviada ao comprador!');" class="px-4 py-2 text-sm font-bold text-white bg-brand-green hover:bg-brand-olive rounded-lg transition shadow-sm">Enviar Proposta</button>
            </div>
        </div>
    </div>

    <!-- Modal: Atualizar Pedido -->
    <div id="modal-atualizar-pedido" class="fixed inset-0 bg-gray-900/50 z-50 hidden items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="font-bold text-gray-900">Atualizar Status</h3>
                <button onclick="closeModal('modal-atualizar-pedido')" class="text-gray-400 hover:text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <p class="text-sm text-gray-600 mb-2">Atualizando o pedido <strong>#PO-9925</strong></p>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-1.5">Novo Status</label>
                    <select class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700 bg-white">
                        <option>Pendente</option>
                        <option>Em Produção</option>
                        <option>Enviado (Em trânsito)</option>
                        <option>Cancelado</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-1.5">Previsão de Entrega</label>
                    <input type="date" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 flex gap-3 justify-end bg-gray-50">
                <button onclick="closeModal('modal-atualizar-pedido');" class="w-full py-2.5 text-sm font-bold text-white bg-brand-green hover:bg-brand-olive rounded-lg transition shadow-sm">Salvar Alterações</button>
            </div>
        </div>
    </div>

    <script>
        // Lógica de Tabs
        function switchTab(tabId, element) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
            
            document.getElementById(tabId).classList.add('active');
            element.classList.add('active');

            const titles = {
                'produtos': 'Meus Produtos',
                'orcamentos': 'Orçamentos Recebidos',
                'pedidos': 'Pedidos de Venda',
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
    </script>
</body>
</html>
