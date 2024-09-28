<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clonar Repositório Git</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Clonar Repositório Git</h1>
        <div class="instructions">
            <h2>Instruções:</h2>
            <p>Para clonar um repositório Git, basta inserir a URL do repositório abaixo e clicar no botão "Clonar".</p>
        </div>

        <div class="input-group">
            <label for="repoUrl">URL do Repositório:</label>
            <input type="text" id="repoUrl" placeholder="Exemplo: https://github.com/user/repo.git">
        </div>

        <button onclick="cloneRepository()">Clonar</button>

        <div id="output" class="output"></div>
    </div>

    <footer>
        <p>&copy; 2024 Ferramenta de Clonagem Git. Todos os direitos reservados.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
