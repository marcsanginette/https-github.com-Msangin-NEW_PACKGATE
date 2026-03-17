<?php
session_start();
// Aqui futuramente entrará a lógica de validação de login com o banco de dados
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PACKGATE</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col items-center justify-center p-4">

    <div class="w-full max-w-md">
        <!-- Voltar -->
        <a href="index.php" class="flex items-center justify-center gap-2 text-blue-600 hover:text-blue-800 font-medium mb-8 transition">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Voltar ao início
        </a>

        <!-- Card de Login -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Entrar na Plataforma</h1>
                <p class="text-gray-500 text-sm">Acesse sua conta para continuar</p>
            </div>

            <form action="login.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-1.5" for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="seu@email.com" required
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-900 mb-1.5" for="password">Senha</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" placeholder="Sua senha" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 outline-none transition text-gray-700 pr-10">
                        <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-600 cursor-pointer">
                    <label for="remember" class="ml-2 text-sm font-medium text-gray-900 cursor-pointer">Lembrar-me neste dispositivo</label>
                </div>

                <button type="submit" class="w-full bg-[#2563eb] hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition duration-200">
                    Entrar
                </button>
            </form>

            <div class="mt-6 text-center space-y-4">
                <a href="#" class="block text-sm text-blue-600 hover:underline">Esqueci minha senha</a>
                
                <p class="text-sm text-gray-600">
                    Não tem uma conta? <a href="cadastro.php" class="text-blue-600 font-medium hover:underline">Cadastre-se</a>
                </p>

                <div class="flex items-center justify-center gap-1.5 text-xs text-gray-500 pt-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    Conexão segura e protegida
                </div>
            </div>
        </div>
    </div>

</body>
</html>
