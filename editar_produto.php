<?php
session_start();
require_once 'conexao.php';

// Verifica se está logado e se é fabricante
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'fabricante') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = explode(' ', trim($_SESSION['user_name']))[0];
$mensagem_sucesso = '';
$mensagem_erro = '';

// Buscar categorias para o select
$stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

$produto_id = $_GET['id'] ?? null;

if (!$produto_id) {
    header("Location: dashboard_fabricante.php");
    exit;
}

// Buscar dados do produto
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND manufacturer_id = ?");
$stmt->execute([$produto_id, $user_id]);
$produto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$produto) {
    header("Location: dashboard_fabricante.php");
    exit;
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = $_POST['category_id'] ?? null;
    $type = trim($_POST['type'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $weight = trim($_POST['weight'] ?? '');
    $dimensions = trim($_POST['dimensions'] ?? '');
    $volume = trim($_POST['volume'] ?? '');
    $customizable = isset($_POST['customizable']) && $_POST['customizable'] === '1' ? 1 : 0;
    $min_quantity = trim($_POST['min_quantity'] ?? '');
    $additional_notes = trim($_POST['additional_notes'] ?? '');
    
    // Na imagem não tem preço, mas é bom ter no banco de dados. 
    // Vamos definir 0.00 como padrão se não for enviado.
    $price = isset($_POST['price']) ? floatval(str_replace(',', '.', $_POST['price'])) : 0.00;

    // Upload das Imagens
    $image_urls = [
        $produto['image_url'],
        $produto['image_url_2'] ?? null,
        $produto['image_url_3'] ?? null,
        $produto['image_url_4'] ?? null,
        $produto['image_url_5'] ?? null
    ];
    $main_index = isset($_POST['main_image_index']) ? (int)$_POST['main_image_index'] : 0;

    if (isset($_FILES['product_images']) && !empty($_FILES['product_images']['name'][0])) {
        $upload_dir = 'uploads/products/';
        
        // Criar diretório se não existir
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $files = $_FILES['product_images'];
        $uploaded_paths = [];

        $count = min(count($files['name']), 5);
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                // Check file size (1MB = 1048576 bytes)
                if ($files['size'][$i] > 1048576) {
                    $mensagem_erro = "A imagem " . htmlspecialchars($files['name'][$i]) . " excede o limite de 1MB.";
                    break;
                }

                $file_tmp = $files['tmp_name'][$i];
                $file_name = $files['name'][$i];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                if (in_array($file_ext, $allowed_exts)) {
                    $new_file_name = uniqid('prod_') . '_' . $i . '.' . $file_ext;
                    $destination = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($file_tmp, $destination)) {
                        $uploaded_paths[$i] = $destination;
                    } else {
                        $mensagem_erro = "Erro ao salvar a imagem no servidor.";
                        break;
                    }
                } else {
                    $mensagem_erro = "Formato de imagem inválido. Apenas JPG, PNG e WEBP são aceitos.";
                    break;
                }
            }
        }

        if (empty($mensagem_erro)) {
            // Excluir imagens antigas
            foreach ($image_urls as $old_img) {
                if (!empty($old_img) && file_exists($old_img)) {
                    unlink($old_img);
                }
            }
            
            // Resetar array de imagens
            $image_urls = [null, null, null, null, null];
            
            $current_slot = 1;
            foreach ($uploaded_paths as $original_index => $path) {
                if ($original_index === $main_index) {
                    $image_urls[0] = $path;
                } else {
                    if ($current_slot <= 4) {
                        $image_urls[$current_slot] = $path;
                        $current_slot++;
                    }
                }
            }
            
            // Se a imagem principal não foi definida corretamente, usa a primeira disponível
            if ($image_urls[0] === null && count($uploaded_paths) > 0) {
                $first_key = array_key_first($uploaded_paths);
                $image_urls[0] = $uploaded_paths[$first_key];
                unset($uploaded_paths[$first_key]);
                $current_slot = 1;
                foreach ($uploaded_paths as $path) {
                    if ($current_slot <= 4) {
                        $image_urls[$current_slot] = $path;
                        $current_slot++;
                    }
                }
            }
        }
    }

    if (empty($mensagem_erro)) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET 
                category_id = ?, type = ?, name = ?, description = ?, weight = ?, dimensions = ?, volume = ?, customizable = ?, price = ?, min_quantity = ?, additional_notes = ?, image_url = ?, image_url_2 = ?, image_url_3 = ?, image_url_4 = ?, image_url_5 = ?
                WHERE id = ? AND manufacturer_id = ?");
                
            $stmt->execute([
                $category_id, $type, $name, $description, $weight, $dimensions, $volume, $customizable, $price, $min_quantity, $additional_notes, 
                $image_urls[0], $image_urls[1], $image_urls[2], $image_urls[3], $image_urls[4],
                $produto_id, $user_id
            ]);
            
            $mensagem_sucesso = "Produto atualizado com sucesso!";
            
            // Atualizar os dados locais para exibir no formulário
            $produto['name'] = $name;
            $produto['category_id'] = $category_id;
            $produto['type'] = $type;
            $produto['description'] = $description;
            $produto['weight'] = $weight;
            $produto['dimensions'] = $dimensions;
            $produto['volume'] = $volume;
            $produto['customizable'] = $customizable;
            $produto['min_quantity'] = $min_quantity;
            $produto['additional_notes'] = $additional_notes;
            $produto['image_url'] = $image_urls[0];
            $produto['image_url_2'] = $image_urls[1];
            $produto['image_url_3'] = $image_urls[2];
            $produto['image_url_4'] = $image_urls[3];
            $produto['image_url_5'] = $image_urls[4];
            
        } catch (PDOException $e) {
            $mensagem_erro = "Erro ao atualizar no banco de dados: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto - PACKGATE</title>
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
        .file-drop-area.dragover { background-color: #f1f5f9; border-color: #8DC63F; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Header Simples -->
    <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-8 flex-shrink-0 z-10 sticky top-0">
        <h2 class="text-lg font-bold text-brand-darkblue">Painel do Fabricante</h2>
        
        <div class="flex items-center gap-4">
            <button class="text-gray-400 hover:text-gray-600 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
            </button>
            <div class="w-px h-6 bg-gray-200"></div>
            <a href="logout.php" title="Sair" class="w-8 h-8 rounded-full bg-brand-lightgreen text-brand-olive flex items-center justify-center font-bold text-sm hover:bg-red-100 hover:text-red-600 transition group relative cursor-pointer">
                <span class="group-hover:hidden"><?php echo strtoupper(substr($user_name, 0, 1)); ?></span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="hidden group-hover:block"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            
            <!-- Voltar e Título -->
            <div class="mb-8">
                <a href="dashboard_fabricante.php" class="inline-flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-brand-darkblue transition mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
                    Voltar
                </a>
                <h1 class="text-3xl font-black text-brand-darkblue tracking-tight">Editar Produto</h1>
                <p class="text-gray-500 mt-1">Atualize as informações do seu produto</p>
            </div>

            <?php if ($mensagem_sucesso): ?>
                <div class="bg-green-50 border-l-4 border-brand-green p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-brand-green mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <p class="text-green-800 font-medium"><?php echo $mensagem_sucesso; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($mensagem_erro): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <p class="text-red-800 font-medium"><?php echo $mensagem_erro; ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulário -->
            <form action="editar_produto.php?id=<?php echo $produto_id; ?>" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                
                <div class="p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-1">Informações do Produto</h3>
                    <p class="text-sm text-gray-500 mb-8">Preencha todos os campos obrigatórios para atualizar seu produto</p>

                    <div class="space-y-6">
                        <!-- Nome do Produto -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Nome do Produto <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="Ex: Caixa de papelão ondulado" value="<?php echo htmlspecialchars($produto['name'] ?? ''); ?>"
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                        </div>

                        <!-- Categoria e Tipo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Categoria <span class="text-red-500">*</span></label>
                                <select name="category_id" required class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700 bg-white appearance-none">
                                    <option value="" disabled>Selecione uma categoria</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($produto['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Tipo <span class="text-red-500">*</span></label>
                                <input type="text" name="type" required placeholder="Ex: Kraft, Reciclado, etc." value="<?php echo htmlspecialchars($produto['type'] ?? ''); ?>"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Descrição do Produto <span class="text-red-500">*</span></label>
                            <textarea name="description" required rows="4" placeholder="Descreva seu produto em detalhes..." 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700 resize-y"><?php echo htmlspecialchars($produto['description'] ?? ''); ?></textarea>
                        </div>

                        <!-- Medidas (Peso, Dimensões, Volume) -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Peso</label>
                                <input type="text" name="weight" placeholder="Ex: 150g, 2kg" value="<?php echo htmlspecialchars($produto['weight'] ?? ''); ?>"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Dimensões</label>
                                <input type="text" name="dimensions" placeholder="Ex: 30x20x15cm" value="<?php echo htmlspecialchars($produto['dimensions'] ?? ''); ?>"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Volume/Capacidade</label>
                                <input type="text" name="volume" placeholder="Ex: 500ml, 2L" value="<?php echo htmlspecialchars($produto['volume'] ?? ''); ?>"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                            </div>
                        </div>

                        <!-- Personalização e Pedido Mínimo -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-3">Permite Personalização? <span class="text-red-500">*</span></label>
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="customizable" value="1" <?php echo ($produto['customizable'] == 1) ? 'checked' : ''; ?> class="w-4 h-4 text-brand-green focus:ring-brand-green border-gray-300">
                                        <span class="text-gray-700">Sim</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="customizable" value="0" <?php echo ($produto['customizable'] == 0) ? 'checked' : ''; ?> class="w-4 h-4 text-brand-green focus:ring-brand-green border-gray-300">
                                        <span class="text-gray-700">Não</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Pedido Mínimo</label>
                                <input type="text" name="min_quantity" placeholder="Ex: 1.000 unidades, 100 peças" value="<?php echo htmlspecialchars($produto['min_quantity'] ?? ''); ?>"
                                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700">
                            </div>
                        </div>

                        <!-- Observações -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Observações Adicionais</label>
                            <textarea name="additional_notes" rows="3" placeholder="Informações extras sobre o produto..." 
                                class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-brand-green focus:border-brand-green outline-none transition text-gray-700 resize-y"><?php echo htmlspecialchars($produto['additional_notes'] ?? ''); ?></textarea>
                        </div>

                        <!-- Imagens do Produto -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Imagens do Produto (Até 5 imagens)</label>
                            
                            <?php 
                            $existing_images = array_filter([
                                $produto['image_url'],
                                $produto['image_url_2'] ?? null,
                                $produto['image_url_3'] ?? null,
                                $produto['image_url_4'] ?? null,
                                $produto['image_url_5'] ?? null
                            ]);
                            ?>
                            
                            <?php if (!empty($existing_images)): ?>
                                <div class="mb-6">
                                    <p class="text-sm text-gray-500 mb-2">Imagens Atuais:</p>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        <?php foreach ($existing_images as $index => $img): ?>
                                            <div class="relative aspect-square rounded-lg overflow-hidden border-2 border-gray-200">
                                                <img src="<?php echo htmlspecialchars($img); ?>" alt="Imagem do Produto" class="w-full h-full object-cover">
                                                <?php if ($index === 0): ?>
                                                    <div class="absolute top-2 right-2 bg-brand-green text-white text-xs font-bold px-2 py-1 rounded shadow-sm">Principal</div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <p class="text-xs text-orange-500 mt-2 font-medium">Atenção: Ao selecionar novas imagens, todas as imagens atuais serão substituídas.</p>
                                </div>
                            <?php endif; ?>

                            <input type="hidden" name="main_image_index" id="main-image-index" value="0">

                            <div class="file-drop-area relative border-2 border-dashed border-gray-300 rounded-xl p-10 text-center hover:bg-gray-50 transition cursor-pointer" id="drop-area">
                                <input type="file" name="product_images[]" id="file-input" accept="image/png, image/jpeg, image/webp" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                
                                <div class="flex flex-col items-center justify-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400 mb-3"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                                    <p class="text-sm font-medium text-gray-700 mb-1">Clique para alterar as imagens ou arraste e solte aqui</p>
                                    <p class="text-xs text-gray-500 mb-4">Formatos aceitos: JPEG, PNG, WEBP (máx. 1MB cada)</p>
                                    <button type="button" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 shadow-sm">Selecionar Novas Imagens</button>
                                </div>
                            </div>
                            
                            <div class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-4" id="image-preview-container">
                                <!-- Previews will be injected here -->
                            </div>
                            
                            <div class="flex justify-between items-center mt-3">
                                <p class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                                    A primeira imagem selecionada será a principal por padrão. Clique em uma imagem para torná-la principal.
                                </p>
                                <p class="text-xs text-gray-500" id="file-name-display">Nenhuma nova imagem selecionada</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="px-8 py-5 bg-gray-50 border-t border-gray-200 flex items-center justify-end gap-4">
                    <a href="dashboard_fabricante.php" class="px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-200 rounded-lg transition border border-gray-300 bg-white shadow-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 text-sm font-bold text-white bg-brand-darkblue hover:bg-blue-900 rounded-lg transition shadow-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Salvar Alterações
                    </button>
                </div>
            </form>

        </div>
    </main>

    <script>
        const fileInput = document.getElementById('file-input');
        const fileNameDisplay = document.getElementById('file-name-display');
        const dropArea = document.getElementById('drop-area');
        const previewContainer = document.getElementById('image-preview-container');
        const mainImageIndexInput = document.getElementById('main-image-index');

        let selectedFiles = [];
        let mainIndex = 0;

        fileInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                // Limit to 5 files
                const filesToProcess = Array.from(this.files).slice(0, 5);
                selectedFiles = filesToProcess;
                mainIndex = 0; // Reset main index to first image
                mainImageIndexInput.value = 0;
                
                fileNameDisplay.textContent = filesToProcess.length + ' nova(s) imagem(ns) selecionada(s)';
                fileNameDisplay.classList.add('text-brand-green', 'font-medium');
                fileNameDisplay.classList.remove('text-gray-500');
                
                renderPreviews();
            } else {
                selectedFiles = [];
                fileNameDisplay.textContent = 'Nenhuma nova imagem selecionada';
                fileNameDisplay.classList.remove('text-brand-green', 'font-medium');
                fileNameDisplay.classList.add('text-gray-500');
                previewContainer.innerHTML = '';
            }
        });

        function renderPreviews() {
            previewContainer.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const isMain = index === mainIndex;
                    const div = document.createElement('div');
                    div.className = `relative aspect-square rounded-lg overflow-hidden border-2 cursor-pointer transition-all ${isMain ? 'border-brand-green ring-2 ring-brand-green ring-offset-2' : 'border-gray-200 hover:border-brand-lightgreen'}`;
                    div.onclick = () => setMainImage(index);
                    
                    div.innerHTML = `
                        <img src="${e.target.result}" class="w-full h-full object-cover" alt="Preview">
                        ${isMain ? '<div class="absolute top-2 right-2 bg-brand-green text-white text-xs font-bold px-2 py-1 rounded shadow-sm">Principal</div>' : ''}
                        <div class="absolute bottom-0 inset-x-0 bg-black/50 text-white text-[10px] truncate px-2 py-1">${file.name}</div>
                    `;
                    
                    previewContainer.appendChild(div);
                };
                
                reader.readAsDataURL(file);
            });
        }

        function setMainImage(index) {
            mainIndex = index;
            mainImageIndexInput.value = index;
            renderPreviews();
        }

        // Efeitos de Drag and Drop
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropArea.classList.add('dragover');
        }

        function unhighlight(e) {
            dropArea.classList.remove('dragover');
        }
    </script>
</body>
</html>
