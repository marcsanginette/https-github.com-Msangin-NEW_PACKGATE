<?php
session_start();
require_once 'conexao.php';

// Função para buscar produtos do banco de dados
function getProducts($pdo, $section) {
    if (!$pdo) return [];
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE section = ? LIMIT 6");
        $stmt->execute([$section]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

$deals = isset($pdo) ? getProducts($pdo, 'deals') : [];
$arrivals = isset($pdo) ? getProducts($pdo, 'arrivals') : [];
$popular = isset($pdo) ? getProducts($pdo, 'popular') : [];

// Fallback de dados caso o banco ainda não esteja configurado
if (empty($deals)) {
    $deals = [
        ['id'=>1, 'category'=>'Papel', 'name'=>'Caixa de Papelão Ondulado 50x50x50', 'rating'=>4.5, 'price'=>4.50, 'old_price'=>6.00, 'image'=>'https://images.unsplash.com/photo-1589939705384-5185137a7f0f?w=500&q=80', 'badge'=>'-25%', 'badge_color'=>'bg-red-500'],
        ['id'=>2, 'category'=>'Vidro', 'name'=>'Pote de Vidro Hermético 1L', 'rating'=>5.0, 'price'=>12.99, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1584346133934-a3afd2a33c4c?w=500&q=80', 'badge'=>'', 'badge_color'=>''],
        ['id'=>3, 'category'=>'Plástico', 'name'=>'Bobina de Plástico Bolha 100m', 'rating'=>4.0, 'price'=>45.00, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1626863905121-3b0c0ed7b94c?w=500&q=80', 'badge'=>'Novo', 'badge_color'=>'bg-brand-green'],
        ['id'=>4, 'category'=>'Metal', 'name'=>'Lata de Alumínio para Mantimentos', 'rating'=>4.5, 'price'=>18.50, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1614735241165-6756e1df61ab?w=500&q=80', 'badge'=>'', 'badge_color'=>''],
        ['id'=>5, 'category'=>'Madeira', 'name'=>'Caixa de Madeira Pinus Decorativa', 'rating'=>5.0, 'price'=>35.00, 'old_price'=>40.00, 'image'=>'https://images.unsplash.com/photo-1611077544811-042813ce8282?w=500&q=80', 'badge'=>'-12%', 'badge_color'=>'bg-red-500'],
        ['id'=>6, 'category'=>'Especiais', 'name'=>'Embalagem para Presente Premium', 'rating'=>4.5, 'price'=>8.99, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=500&q=80', 'badge'=>'', 'badge_color'=>'']
    ];
}

if (empty($arrivals)) {
    $arrivals = [
        ['id'=>7, 'category'=>'Sustentável', 'name'=>'Sacola Kraft Ecológica (100 un)', 'rating'=>4.8, 'price'=>89.90, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1592840062668-9812689c17e6?w=500&q=80', 'badge'=>'Novo', 'badge_color'=>'bg-brand-green'],
        ['id'=>8, 'category'=>'Plástico', 'name'=>'Pote Plástico Descartável 250ml (50 un)', 'rating'=>4.2, 'price'=>15.50, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1606502973842-f64bc2785fe5?w=500&q=80', 'badge'=>'', 'badge_color'=>''],
        ['id'=>9, 'category'=>'Vidro', 'name'=>'Garrafa de Vidro Âmbar 500ml', 'rating'=>4.9, 'price'=>6.50, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1622483767028-3f66f32aef97?w=500&q=80', 'badge'=>'', 'badge_color'=>''],
        ['id'=>10, 'category'=>'Metal', 'name'=>'Lata de Flandres Redonda', 'rating'=>4.5, 'price'=>12.00, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1565586419448-95b774010ee4?w=500&q=80', 'badge'=>'', 'badge_color'=>''],
        ['id'=>11, 'category'=>'Papel', 'name'=>'Tubo de Papelão para Envio', 'rating'=>4.7, 'price'=>3.20, 'old_price'=>4.00, 'image'=>'https://images.unsplash.com/photo-1587582423116-ec07293f0395?w=500&q=80', 'badge'=>'-20%', 'badge_color'=>'bg-red-500'],
        ['id'=>12, 'category'=>'Madeira', 'name'=>'Palete de Madeira Padrão PBR', 'rating'=>4.6, 'price'=>45.00, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1501430654243-c934cec2e1c0?w=500&q=80', 'badge'=>'', 'badge_color'=>'']
    ];
}

if (empty($popular)) {
    $popular = [
        ['id'=>13, 'category'=>'Plástico', 'name'=>'Saco Plástico Transparente (1000 un)', 'rating'=>4.9, 'price'=>25.00, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1530587191325-3db32d826c18?w=500&q=80', 'badge'=>'', 'badge_color'=>''],
        ['id'=>14, 'category'=>'Papel', 'name'=>'Caixa para Pizza Oitavada 35cm', 'rating'=>4.8, 'price'=>2.50, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1566843972142-a7fcb70de55a?w=500&q=80', 'badge'=>'', 'badge_color'=>''],
        ['id'=>15, 'category'=>'Especiais', 'name'=>'Fita Adesiva Personalizada 50m', 'rating'=>5.0, 'price'=>18.90, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1586864387789-628af9feed72?w=500&q=80', 'badge'=>'Top', 'badge_color'=>'bg-yellow-500'],
        ['id'=>16, 'category'=>'Vidro', 'name'=>'Frasco de Vidro para Perfume 50ml', 'rating'=>4.7, 'price'=>8.50, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1594035910387-fea47794261f?w=500&q=80', 'badge'=>'', 'badge_color'=>''],
        ['id'=>17, 'category'=>'Metal', 'name'=>'Tambor Metálico 200L', 'rating'=>4.5, 'price'=>150.00, 'old_price'=>180.00, 'image'=>'https://images.unsplash.com/photo-1605000797499-95a51c5269ae?w=500&q=80', 'badge'=>'-16%', 'badge_color'=>'bg-red-500'],
        ['id'=>18, 'category'=>'Sustentável', 'name'=>'Embalagem Biodegradável para Hambúrguer', 'rating'=>4.9, 'price'=>1.20, 'old_price'=>null, 'image'=>'https://images.unsplash.com/photo-1624372554743-162804798363?w=500&q=80', 'badge'=>'', 'badge_color'=>'']
    ];
}

function renderProductCard($product) {
    $badgeHtml = '';
    if (!empty($product['badge'])) {
        $badgeColor = !empty($product['badge_color']) ? $product['badge_color'] : 'bg-brand-green';
        $badgeHtml = "<span class=\"absolute top-3 left-3 {$badgeColor} text-white text-[10px] font-bold px-2 py-0.5 rounded z-10\">{$product['badge']}</span>";
    }

    $oldPriceHtml = '';
    if (!empty($product['old_price'])) {
        $oldPriceFormatted = number_format($product['old_price'], 2, ',', '.');
        $oldPriceHtml = "<span class=\"text-xs text-gray-400 line-through mt-0.5\">R$ {$oldPriceFormatted}</span>";
    }

    $priceFormatted = number_format($product['price'], 2, ',', '.');
    $rating = floatval($product['rating']);
    $starsHtml = str_repeat('★', floor($rating)) . str_repeat('☆', 5 - floor($rating));
    $ratingFormatted = number_format($rating, 1, '.', '');

    return "
    <div class=\"bg-white border border-gray-100 rounded-xl p-4 relative group hover:shadow-lg hover:border-brand-green transition duration-300 flex flex-col h-full\">
        {$badgeHtml}
        <button class=\"absolute top-3 right-3 text-gray-300 hover:text-red-500 z-10 transition\">
            <svg xmlns=\"http://www.w3.org/2000/svg\" width=\"20\" height=\"20\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\"><path d=\"M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z\"/></svg>
        </button>
        <a class=\"block relative h-36 mb-3 overflow-hidden rounded-lg bg-gray-50 flex items-center justify-center p-2\" href=\"#\">
            <img alt=\"{$product['name']}\" class=\"max-w-full max-h-full object-contain group-hover:scale-110 transition duration-500 rounded\" src=\"{$product['image']}\" />
        </a>
        <div class=\"flex-1 flex flex-col\">
            <span class=\"text-xs text-gray-400 mb-1 block\">{$product['category']}</span>
            <a class=\"font-medium text-gray-800 text-sm leading-tight mb-2 line-clamp-2 hover:text-brand-green\" href=\"#\">{$product['name']}</a>
            <div class=\"flex items-center mb-2\">
                <div class=\"flex text-yellow-400 text-xs\">{$starsHtml}</div>
                <span class=\"text-[10px] text-gray-400 ml-1\">({$ratingFormatted})</span>
            </div>
            <div class=\"mt-auto flex items-center justify-between mb-3\">
                <div class=\"flex flex-col\">
                    <span class=\"font-bold text-lg text-brand-green leading-none\">R$ {$priceFormatted}</span>
                    {$oldPriceHtml}
                </div>
            </div>
            <button class=\"w-full border border-gray-200 text-brand-green font-medium rounded-lg py-1.5 text-sm hover:bg-brand-green hover:text-white hover:border-brand-green transition flex items-center justify-center gap-1\">
                Solicitar Orçamento
            </button>
        </div>
    </div>
    ";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PACKGATE - Embalagens</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-green': '#8DC63F',
                        'brand-darkblue': '#1A237E',
                        'brand-bg': '#f9fafb',
                    }
                }
            }
        }
    </script>
    <style>
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-brand-bg text-gray-800 font-sans antialiased min-h-screen">

    <!-- Top Promo Bar -->
    <div class="bg-brand-darkblue text-white text-xs md:text-sm py-2 px-4">
        <div class="container mx-auto flex justify-between items-center">
            <div class="text-center flex-1">
                Condições especiais para compras no atacado. <a class="font-bold underline ml-1" href="#">Solicite um Orçamento</a>
            </div>
            <div class="flex gap-4">
                <select class="bg-transparent border-none text-white text-xs focus:ring-0 cursor-pointer outline-none">
                    <option class="text-black" value="pt">Português</option>
                    <option class="text-black" value="en">English</option>
                </select>
                <select class="bg-transparent border-none text-white text-xs focus:ring-0 cursor-pointer outline-none">
                    <option class="text-black" value="brl">BRL</option>
                    <option class="text-black" value="usd">USD</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="bg-white py-4 px-4 shadow-sm relative z-20">
        <div class="container mx-auto flex flex-wrap lg:flex-nowrap items-center justify-between gap-4 lg:gap-8">
            <div class="flex-shrink-0">
                <a class="text-2xl font-black text-brand-darkblue tracking-tighter" href="#">PACKGATE</a>
            </div>

            <div class="flex-1 w-full lg:w-auto order-last lg:order-none">
                <form class="flex w-full border border-brand-green rounded-lg overflow-hidden bg-white">
                    <select class="hidden md:block bg-gray-50 border-none text-sm text-gray-600 focus:ring-0 cursor-pointer px-4 border-r border-gray-200 outline-none">
                        <option>Todas as Categorias</option>
                        <option>Plásticas</option>
                        <option>Papel</option>
                        <option>Madeira</option>
                        <option>Metal</option>
                        <option>Vidro</option>
                        <option>Especiais</option>
                    </select>
                    <input class="w-full border-none focus:ring-0 text-sm px-4 outline-none" placeholder="Buscar produtos, categorias..." type="text" />
                    <button class="bg-brand-green text-white px-6 py-2.5 hover:bg-[#7ab036] transition flex items-center justify-center" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-6 flex-shrink-0 text-gray-600">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col text-right leading-tight">
                            <span class="text-[10px] text-gray-400">Olá, <?php echo htmlspecialchars($_SESSION['user_role'] === 'fabricante' ? 'Fabricante' : 'Comprador'); ?></span>
                            <span class="text-sm font-bold text-brand-darkblue"><?php echo htmlspecialchars(explode(' ', trim($_SESSION['user_name']))[0]); ?></span>
                        </div>
                        <a href="logout.php" class="text-xs bg-red-50 text-red-500 hover:bg-red-100 px-2 py-1 rounded font-medium transition">Sair</a>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="hover:text-brand-green transition relative">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </a>
                <?php endif; ?>
                <button class="hover:text-brand-green transition relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                    <span class="absolute -top-1 -right-1 bg-brand-green text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center">0</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200 relative z-10">
        <div class="container mx-auto px-4 flex items-center gap-8 h-14">
            <div class="h-full flex items-center">
                <button class="bg-brand-green text-white px-5 h-full font-medium flex items-center gap-2 hover:bg-[#7ab036] transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    Todas as Categorias
                </button>
            </div>
            
            <div class="hidden lg:flex items-center gap-8 text-sm font-medium text-gray-700">
                <a class="text-brand-green" href="#">Início</a>
                <a class="hover:text-brand-green flex items-center gap-1" href="#">
                    Loja 
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </a>
                <a class="hover:text-brand-green" href="#">Sustentáveis</a>
                <a class="hover:text-brand-green" href="#">Personalizadas</a>
                <a class="hover:text-brand-green" href="#">Blog</a>
                <a class="hover:text-brand-green" href="#">Contato</a>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="container mx-auto px-4 py-6">
            <div class="flex flex-col lg:flex-row gap-6">
                <aside class="hidden lg:block w-64 flex-shrink-0 bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                    <ul class="py-2 text-sm text-gray-700">
                        <li><a class="block px-6 py-3 hover:bg-green-50 hover:text-brand-green flex items-center gap-3 transition" href="#"><span class="w-5 h-5 bg-gray-200 rounded-full inline-block"></span> Embalagens Plásticas</a></li>
                        <li><a class="block px-6 py-3 hover:bg-green-50 hover:text-brand-green flex items-center gap-3 transition" href="#"><span class="w-5 h-5 bg-gray-200 rounded-full inline-block"></span> Embalagens de Papel</a></li>
                        <li><a class="block px-6 py-3 hover:bg-green-50 hover:text-brand-green flex items-center gap-3 transition" href="#"><span class="w-5 h-5 bg-gray-200 rounded-full inline-block"></span> Embalagens de Madeira</a></li>
                        <li><a class="block px-6 py-3 hover:bg-green-50 hover:text-brand-green flex items-center gap-3 transition" href="#"><span class="w-5 h-5 bg-gray-200 rounded-full inline-block"></span> Embalagens de Metal</a></li>
                        <li><a class="block px-6 py-3 bg-brand-green text-white flex items-center gap-3 transition" href="#"><span class="w-5 h-5 bg-white/30 rounded-full inline-block"></span> Embalagens de Vidro</a></li>
                        <li><a class="block px-6 py-3 hover:bg-green-50 hover:text-brand-green flex items-center gap-3 transition" href="#"><span class="w-5 h-5 bg-gray-200 rounded-full inline-block"></span> Embalagens Especiais</a></li>
                        <li><a class="block px-6 py-3 hover:bg-green-50 hover:text-brand-green flex items-center gap-3 transition" href="#"><span class="w-5 h-5 bg-gray-200 rounded-full inline-block"></span> Sustentáveis</a></li>
                        <li><a class="block px-6 py-3 hover:bg-green-50 hover:text-brand-green flex items-center gap-3 transition" href="#"><span class="w-5 h-5 bg-gray-200 rounded-full inline-block"></span> Personalizadas</a></li>
                        <li>
                            <a class="block px-6 py-3 text-center text-brand-green font-medium border-t border-gray-100 mt-2 pt-4" href="#">
                                Ver Todas as Categorias
                            </a>
                        </li>
                    </ul>
                </aside>

                <div class="flex-1 bg-gray-100 rounded-2xl relative overflow-hidden min-h-[400px] flex items-center">
                    <img alt="Hero Background" class="absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-40" src="https://images.unsplash.com/photo-1589939705384-5185137a7f0f?q=80&w=2070&auto=format&fit=crop" />
                    <div class="relative z-10 p-10 lg:p-16 w-full lg:w-2/3">
                        <span class="inline-block px-3 py-1 bg-yellow-400 text-yellow-900 text-xs font-bold rounded-full mb-4">Desconto de Fim de Semana 50%</span>
                        <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-4">Embalagens de Qualidade Entregues</h1>
                        <p class="text-gray-800 font-medium mb-8 text-lg">Faça sua cotação online e garanta as melhores embalagens para sua empresa.</p>
                        <a class="inline-block bg-brand-green text-white px-8 py-3 rounded-full font-medium text-lg hover:bg-[#7ab036] transition shadow-lg shadow-green-200" href="#">Orçar Agora</a>
                    </div>
                    <img alt="Fresh Produce" class="absolute -right-10 bottom-0 w-2/3 max-w-md hidden md:block object-contain h-[90%]" src="https://images.unsplash.com/photo-1605600659873-d808a13e4d2a?q=80&w=1000&auto=format&fit=crop" style="-webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1) 80%, rgba(0,0,0,0)); mask-image: linear-gradient(to left, rgba(0,0,0,1) 80%, rgba(0,0,0,0));" />
                </div>
            </div>
        </section>

        <!-- Ofertas da Semana -->
        <section class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Ofertas da Semana</h2>
                    <p class="text-gray-500 text-sm mt-1">Não perca essas ofertas especiais</p>
                </div>
                <a class="text-brand-green font-medium hover:underline flex items-center gap-1" href="#">
                    Ver tudo <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php foreach($deals as $product) echo renderProductCard($product); ?>
            </div>
        </section>

        <!-- Novidades -->
        <section class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Novidades</h2>
                    <p class="text-gray-500 text-sm mt-1">Adicionados recentemente ao nosso catálogo</p>
                </div>
                <a class="text-brand-green font-medium hover:underline flex items-center gap-1" href="#">
                    Ver tudo <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php foreach($arrivals as $product) echo renderProductCard($product); ?>
            </div>
        </section>

        <!-- Mais Populares -->
        <section class="container mx-auto px-4 py-8">
            <div class="flex justify-between items-end mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Mais Populares</h2>
                    <p class="text-gray-500 text-sm mt-1">Os favoritos dos nossos clientes este mês</p>
                </div>
                <a class="text-brand-green font-medium hover:underline flex items-center gap-1" href="#">
                    Ver tudo <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php foreach($popular as $product) echo renderProductCard($product); ?>
            </div>
        </section>
    </main>
</body>
</html>
