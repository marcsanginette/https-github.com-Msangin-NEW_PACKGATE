<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cria o banco de dados
    $pdo->exec("CREATE DATABASE IF NOT EXISTS packgate_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE packgate_db");

    // 1. Tabela de Usuários
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role ENUM('comprador', 'fabricante', 'admin') NOT NULL DEFAULT 'comprador',
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        company_name VARCHAR(255) DEFAULT NULL,
        phone VARCHAR(50) DEFAULT NULL,
        cnpj VARCHAR(50) DEFAULT NULL,
        description TEXT DEFAULT NULL,
        employees_count VARCHAR(100) DEFAULT NULL,
        annual_revenue VARCHAR(100) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Tabela de Categorias
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 3. Tabela de Produtos
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        manufacturer_id INT NOT NULL,
        category_id INT,
        type VARCHAR(100),
        name VARCHAR(255) NOT NULL,
        description TEXT,
        weight VARCHAR(100),
        dimensions VARCHAR(100),
        volume VARCHAR(100),
        customizable BOOLEAN DEFAULT FALSE,
        price DECIMAL(10, 2) DEFAULT 0.00,
        min_quantity VARCHAR(100) NOT NULL DEFAULT '1',
        additional_notes TEXT,
        image_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (manufacturer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Tentar adicionar a coluna manufacturer_id caso a tabela seja muito antiga e não a tenha
    try {
        $pdo->exec("ALTER TABLE products ADD COLUMN manufacturer_id INT NOT NULL AFTER id;");
        // Tentar adicionar a chave estrangeira
        $pdo->exec("ALTER TABLE products ADD CONSTRAINT fk_manufacturer FOREIGN KEY (manufacturer_id) REFERENCES users(id) ON DELETE CASCADE;");
    } catch (PDOException $e) {
        // Ignora se a coluna já existir
    }

    // Tentar adicionar as novas colunas caso a tabela já exista (evita erros se o usuário já rodou o script antes)
    try {
        $pdo->exec("ALTER TABLE products 
            ADD COLUMN type VARCHAR(100) AFTER category_id,
            ADD COLUMN weight VARCHAR(100) AFTER description,
            ADD COLUMN dimensions VARCHAR(100) AFTER weight,
            ADD COLUMN volume VARCHAR(100) AFTER dimensions,
            ADD COLUMN customizable BOOLEAN DEFAULT FALSE AFTER volume,
            ADD COLUMN additional_notes TEXT AFTER min_quantity;");
    } catch (PDOException $e) {
        // Se der erro, é porque as colunas já existem, então ignoramos silenciosamente
    }
    
    // Modificar min_quantity para VARCHAR para aceitar textos como "1.000 unidades"
    try {
        $pdo->exec("ALTER TABLE products MODIFY COLUMN min_quantity VARCHAR(100) NOT NULL DEFAULT '1';");
    } catch (PDOException $e) {}

    // 4. Tabela de Orçamentos (Quotes)
    $pdo->exec("CREATE TABLE IF NOT EXISTS quotes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        buyer_id INT NOT NULL,
        manufacturer_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        status ENUM('aguardando', 'respondido', 'recusado', 'aprovado') DEFAULT 'aguardando',
        manufacturer_message TEXT,
        total_price DECIMAL(12, 2),
        valid_until DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (manufacturer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 5. Tabela de Pedidos de Compra (Purchase Orders)
    $pdo->exec("CREATE TABLE IF NOT EXISTS purchase_orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        quote_id INT NOT NULL,
        buyer_id INT NOT NULL,
        manufacturer_id INT NOT NULL,
        total_amount DECIMAL(12, 2) NOT NULL,
        status ENUM('pendente', 'em_producao', 'enviado', 'concluido', 'cancelado') DEFAULT 'pendente',
        delivery_date DATE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
        FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (manufacturer_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 6. Tabela de Avaliações (Reviews)
    $pdo->exec("CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        buyer_id INT NOT NULL,
        manufacturer_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
        FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (manufacturer_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 7. Tabela de Notificações
    $pdo->exec("CREATE TABLE IF NOT EXISTS notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        type VARCHAR(50) DEFAULT 'info',
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Inserir categorias básicas para teste (ignora se já existir)
    $pdo->exec("INSERT IGNORE INTO categories (id, name) VALUES 
        (1, 'Papelão'), 
        (2, 'Plástico'), 
        (3, 'Vidro'), 
        (4, 'Sustentável'), 
        (5, 'Metal')");

    echo "<div style='font-family: sans-serif; padding: 20px;'>";
    echo "<h2 style='color: #8DC63F;'>Banco de Dados Atualizado com Sucesso!</h2>";
    echo "<p>As seguintes tabelas foram criadas/verificadas:</p>";
    echo "<ul>
            <li>users (Usuários)</li>
            <li>categories (Categorias)</li>
            <li>products (Produtos)</li>
            <li>quotes (Orçamentos)</li>
            <li>purchase_orders (Pedidos)</li>
            <li>reviews (Avaliações)</li>
            <li>notifications (Notificações)</li>
          </ul>";
    echo "<br><a href='cadastro.php' style='background: #8DC63F; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Voltar para o Cadastro</a>";
    echo "</div>";

} catch(PDOException $e) {
    echo "Erro ao configurar o banco de dados: " . $e->getMessage();
}
?>
