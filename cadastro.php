<?php
session_start();
// Aqui futuramente entrará a lógica de inserção no banco de dados
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - PACKGATE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-4 py-10">

    <div class="w-full max-w-2xl">
        <!-- Voltar -->
        <a href="index.php" class="flex items-center justify-center gap-2 text-blue-600 hover:text-blue-800 font-medium mb-8 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Voltar ao início
        </a>

        <!-- Card de Cadastro -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-10">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Criar Nova Conta</h1>
                <p class="text-gray-500 text-sm">Junte-se à maior plataforma B2B de embalagens do Brasil</p>
            </div>

            <form action="cadastro.php" method="POST">
                <input type="hidden" id="role_input" name="role" value="comprador">

                <!-- Toggle Personas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <!-- Comprador Btn -->
                    <button type="button" id="btn_comprador" onclick="setRole('comprador')" 
                        class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-blue-600 bg-blue-50 text-blue-700 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        <span class="font-bold">Comprador</span>
                        <span class="text-xs opacity-80 mt-1">Solicite orçamentos de embalagens</span>
                    </button>

                    <!-- Fabricante Btn -->
                    <button type="button" id="btn_fabricante" onclick="setRole('fabricante')" 
                        class="flex flex-col items-center justify-center p-4 rounded-xl border-2 border-gray-200 text-gray-500 hover:border-blue-300 transition cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-2"><path d="M2 20a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8l-7 5V8l-7 5V4a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M17 18h1"/><path d="M12 18h1"/><path d="M7 18h1"/></svg>
                        <span class="font-bold">Fabricante</span>
                        <span class="text-xs opacity-80 mt-1">Venda seus produtos de embalagem</span>
                    </button>
                </div>

                <!-- Campos Comuns -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Nome Completo*</label>
                        <input type="text" name="name" placeholder="Seu nome completo" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Email*</label>
                        <input type="email" name="email" placeholder="seu@email.com" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-bold text-gray-900 mb-1.5">Senha</label>
                    <div class="relative">
                        <input type="password" name="password" placeholder="Sua senha segura" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700 pr-10">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label id="label_empresa" class="block text-sm font-bold text-gray-900 mb-1.5">Empresa (Opcional)</label>
                        <input type="text" name="company_name" placeholder="Nome da empresa"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Telefone</label>
                        <input type="text" name="phone" placeholder="(11) 99999-9999"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700">
                    </div>
                </div>

                <!-- Campos Específicos do Fabricante (Ocultos por padrão) -->
                <div id="fields_fabricante" class="hidden space-y-5 mb-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">CNPJ*</label>
                        <input type="text" name="cnpj" placeholder="00.000.000/0000-00"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-1.5">Descrição da Empresa</label>
                        <textarea name="description" rows="3" placeholder="Conte sobre sua empresa e produtos"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1.5">Quantidade de Funcionários*</label>
                            <input type="text" name="employees_count" placeholder="Ex: 50"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-1.5">Faturamento Anual*</label>
                            <input type="text" name="annual_revenue" placeholder="Ex: R$ 1.000.000"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700">
                        </div>
                    </div>
                </div>

                <!-- Termos de Uso -->
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-700"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" x2="8" y1="13" y2="13"/><line x1="16" x2="8" y1="17" y2="17"/><line x1="10" x2="8" y1="9" y2="9"/></svg>
                        <h3 id="termos_title" class="font-bold text-gray-900 text-sm">Termos e Condições de Uso - Compradores</h3>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">Leia atentamente antes de aceitar • Versão 1.1</p>
                    
                    <div class="bg-white border border-slate-200 rounded-lg p-4 h-40 overflow-y-auto custom-scrollbar text-xs text-gray-600 space-y-3" id="termos_text">
                        <p class="font-bold">#Termos de Uso Gerais do Marketplace de Embalagens</p>
                        <p>Bem-vindo ao Packgate, o marketplace pioneiro no Brasil voltado ao comércio de embalagens.</p>
                        <p>Estes Termos de Uso regulam a utilização da plataforma pelos Usuários Finais (Clientes), ou seja, empresas ou consumidores que utilizam o portal para solicitar orçamentos e adquirir embalagens.</p>
                        <p>Ao acessar ou utilizar o Packgate, o Usuário Final concorda integralmente com os presentes Termos.</p>
                        <p>1. O Packgate atua apenas como intermediador entre compradores e fabricantes, não se responsabilizando por defeitos de fabricação.</p>
                    </div>
                </div>

                <div class="flex items-center mb-6">
                    <input type="checkbox" id="accept_terms" name="accept_terms" required class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-600 cursor-pointer">
                    <label for="accept_terms" class="ml-2 text-sm text-gray-700 cursor-pointer">Declaro que li e aceito os Termos e Condições acima</label>
                </div>

                <button type="submit" id="btn_submit" class="w-full bg-[#8ba5e3] hover:bg-blue-600 text-white font-bold py-3.5 rounded-lg transition duration-200">
                    Criar Conta
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Já tem uma conta? <a href="login.php" class="text-blue-600 font-medium hover:underline">Faça login</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Lógica para alternar entre Comprador e Fabricante
        function setRole(role) {
            document.getElementById('role_input').value = role;
            
            const btnComprador = document.getElementById('btn_comprador');
            const btnFabricante = document.getElementById('btn_fabricante');
            const fieldsFabricante = document.getElementById('fields_fabricante');
            const labelEmpresa = document.getElementById('label_empresa');
            const termosTitle = document.getElementById('termos_title');
            const termosText = document.getElementById('termos_text');

            if (role === 'comprador') {
                // Estilo Comprador Ativo
                btnComprador.className = "flex flex-col items-center justify-center p-4 rounded-xl border-2 border-blue-600 bg-blue-50 text-blue-700 transition cursor-pointer";
                btnFabricante.className = "flex flex-col items-center justify-center p-4 rounded-xl border-2 border-gray-200 text-gray-500 hover:border-blue-300 transition cursor-pointer";
                
                // Ocultar campos de fabricante
                fieldsFabricante.classList.add('hidden');
                
                // Ajustar label empresa
                labelEmpresa.innerText = 'Empresa (Opcional)';
                
                // Ajustar Termos
                termosTitle.innerText = 'Termos e Condições de Uso - Compradores';
                termosText.innerHTML = `
                    <p class="font-bold">#Termos de Uso Gerais do Marketplace de Embalagens</p>
                    <p>Bem-vindo ao Packgate, o marketplace pioneiro no Brasil voltado ao comércio de embalagens.</p>
                    <p>Estes Termos de Uso regulam a utilização da plataforma pelos Usuários Finais (Clientes), ou seja, empresas ou consumidores que utilizam o portal para solicitar orçamentos e adquirir embalagens.</p>
                    <p>Ao acessar ou utilizar o Packgate, o Usuário Final concorda integralmente com os presentes Termos.</p>
                    <p>1. O Packgate atua apenas como intermediador entre compradores e fabricantes, não se responsabilizando por defeitos de fabricação.</p>
                `;
            } else {
                // Estilo Fabricante Ativo
                btnFabricante.className = "flex flex-col items-center justify-center p-4 rounded-xl border-2 border-blue-600 bg-blue-50 text-blue-700 transition cursor-pointer";
                btnComprador.className = "flex flex-col items-center justify-center p-4 rounded-xl border-2 border-gray-200 text-gray-500 hover:border-blue-300 transition cursor-pointer";
                
                // Mostrar campos de fabricante
                fieldsFabricante.classList.remove('hidden');
                
                // Ajustar label empresa
                labelEmpresa.innerText = 'Nome da Empresa*';
                
                // Ajustar Termos
                termosTitle.innerText = 'Termos e Condições de Uso - Fabricantes';
                termosText.innerHTML = `
                    <p class="font-bold">#Termos de Uso do Fornecedor – Packgate</p>
                    <p class="font-bold">##Preâmbulo</p>
                    <p>Bem-vindo ao Packgate, o marketplace pioneiro no Brasil voltado exclusivamente ao comércio de embalagens. Este documento estabelece os Termos de Uso aplicáveis aos Fornecedores, ou seja, indústrias e distribuidores que disponibilizam seus produtos na plataforma.</p>
                    <p>Ao cadastrar-se e utilizar o Packgate, o Fornecedor declara ter lido, compreendido e aceito integralmente os presentes Termos.</p>
                    <p>1. O Fornecedor é inteiramente responsável pela veracidade das informações dos produtos e cumprimento dos prazos de entrega.</p>
                `;
            }
        }

        // Lógica simples para mudar a cor do botão quando o checkbox é marcado
        const checkbox = document.getElementById('accept_terms');
        const btnSubmit = document.getElementById('btn_submit');
        
        checkbox.addEventListener('change', function() {
            if(this.checked) {
                btnSubmit.classList.remove('bg-[#8ba5e3]');
                btnSubmit.classList.add('bg-[#2563eb]');
            } else {
                btnSubmit.classList.remove('bg-[#2563eb]');
                btnSubmit.classList.add('bg-[#8ba5e3]');
            }
        });
    </script>
</body>
</html>
