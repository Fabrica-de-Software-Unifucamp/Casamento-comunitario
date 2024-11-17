<?php
include("conexao.php");

function enviarArquivo($error, $size, $name, $tmp_name, $cpf) {
    global $mysqli; // Access the database connection
    // Verifica se houve erro no upload
    if ($error) {
        echo "Falha ao enviar o arquivo: " . $error;
        return false;
    }

    // Verifica o tamanho do arquivo
    if ($size > 20000000) { 
        echo "Arquivo muito grande! Max: 2MB";
        return false;
    }

    // Diretório onde os arquivos serão salvos
    $pasta = "arquivos/$cpf/"; // Cria uma pasta com o CPF do cônjuge
    if (!is_dir($pasta)) { // Verifica se a pasta existe
        mkdir($pasta, 0777, true); // Cria a pasta se não existir
    }

    $novoNomeDoArquivo = uniqid();
    $extensao = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $path = $pasta . $novoNomeDoArquivo . "." . $extensao;

    // Verifica se a extensão do arquivo é permitida
    if (!in_array($extensao, ['jpg', 'png', 'pdf'])) {
        echo "Tipo de arquivo não aceito!";
        return false;
    }

    // Move o arquivo para o diretório especificado
    if (move_uploaded_file($tmp_name, $path)) {
        return $path; // Retorna o caminho do arquivo
    } else {
        echo "<p>Falha ao mover o arquivo! Caminho: $path</p>";
        return false; // Failure
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cadastro do cônjuge
    if (isset($_POST['cadastrar_conjuge'])) {
        $nome = $_POST['nome'];
        $cpf = $_POST['cpf'];
        $rg = $_POST['rg'];
        $dataNascimento = $_POST['dataNascimento'];
        $estadoCivil = $_POST['estadoCivil'];
        $numero = $_POST['numero'];
        $rua = $_POST['rua'];
        $bairro = $_POST['bairro'];
        $cidade = $_POST['cidade'];
        $uf = $_POST['uf'];
        $cep = $_POST['cep'];
        $telefone1 = $_POST['telefone1'];
        $telefone2 = $_POST['telefone2'];

        // Insere o cônjuge na tabela Conjuge
        $stmt = $mysqli->prepare("INSERT INTO Conjuge (Nome, CPF, RG, Data_Nascimento, Estado_Civil, Numero, Rua, Bairro, Cidade, UF, CEP, Telefone1, Telefone2) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssssss", $nome, $cpf, $rg, $dataNascimento, $estadoCivil, $numero, $rua, $bairro, $cidade, $uf, $cep, $telefone1, $telefone2);
        $stmt->execute();
        $id_conjuge = $stmt->insert_id; // ID do cônjuge inserido
        $stmt->close();

        // Processar upload dos documentos
        $documentos = [
            'copia_identidade' => $_FILES['copia_identidade'],
            'copia_residencia' => $_FILES['copia_residencia'],
            'certidao_nascimento' => $_FILES['certidao_nascimento'],
            'certidao_casamento' => $_FILES['certidao_casamento'],
            'certidao_obito' => $_FILES['certidao_obito']
        ];

        // Inicializa as variáveis para os caminhos
        $caminhoCertidaoNascimento = null;
        $caminhoCopiaIdentidade = null;
        $caminhoCopiaResidencia = null;
        $caminhoCertidaoCasamento = null;
        //$caminhoCertidaoObito = null;

        foreach ($documentos as $key => $arquivo) {
            $caminhoArquivo = enviarArquivo($arquivo['error'], $arquivo['size'], $arquivo['name'], $arquivo['tmp_name'], $cpf);
            if ($caminhoArquivo) {
                switch ($key) {
                    case 'certidao_nascimento':
                        $caminhoCertidaoNascimento = $caminhoArquivo;
                        break;
                    case 'copia_identidade':
                        $caminhoCopiaIdentidade = $caminhoArquivo;
                        break;
                    case 'copia_residencia':
                        $caminhoCopiaResidencia = $caminhoArquivo;
                        break;
                    case 'certidao_casamento':
                        $caminhoCertidaoCasamento = $caminhoArquivo;
                        break;
                    case 'certidao_obito':
                        $caminhoCertidaoObito = $caminhoArquivo;
                        break;
                }
            }
        }

        // Insere os caminhos dos documentos na tabela Documentos
        $stmt = $mysqli->prepare("INSERT INTO Documentos (ID_Conjuge, Certidao_Nascimento, Copia_Identidade, Copia_Residencia, Certidao_Casamento, Certidao_Obito) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssss", $id_conjuge, $caminhoCertidaoNascimento, $caminhoCopiaIdentidade, $caminhoCopiaResidencia, $caminhoCertidaoCasamento, $caminhoCertidaoObito);
        $stmt->execute();
        $stmt->close();

        echo "<p>Cônjuge cadastrado com sucesso!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Cônjuge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2>Cadastro de Cônjuge</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="rg" class="form-label">RG</label>
                <input type="text" class="form-control" id="rg" name="rg" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="dataNascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control" id="dataNascimento" name="dataNascimento" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="estadoCivil" class="form-label">Estado Civil</label>
                <select class="form-select" id="estadoCivil" name="estadoCivil" required>
                    <option value="">Selecione</option>
                    <option value="solteiro">Solteiro</option>
                    <option value="casado">Casado</option>
                    <option value="divorciado">Divorciado</option>
                    <option value="viuvo">Viúvo</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="numero" class="form-label">Número</label>
                <input type="text" class="form-control" id="numero" name="numero" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="rua" class="form-label">Rua</label>
                <input type="text ```php
                " class="form-control" id="rua" name="rua" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="bairro" class="form-label">Bairro</label>
                <input type="text" class="form-control" id="bairro" name="bairro" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cidade" class="form-label">Cidade</label>
                <input type="text" class="form-control" id="cidade" name="cidade" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="uf" class="form-label">UF</label>
                <select class="form-select" id="uf" name="uf" required>
                    <option value="">Selecione</option>
                    <option value="AC">AC</option>
                    <option value="AL">AL</option>
                    <option value="AP">AP</option>
                    <option value="AM">AM</option>
                    <option value="BA">BA</option>
                    <option value="CE">CE</option>
                    <option value="DF">DF</option>
                    <option value="ES">ES</option>
                    <option value="GO">GO</option>
                    <option value="MA">MA</option>
                    <option value="MT">MT</option>
                    <option value="MS">MS</option>
                    <option value="MG">MG</option>
                    <option value="PA">PA</option>
                    <option value="PB">PB</option>
                    <option value="PR">PR</option>
                    <option value="PE">PE</option>
                    <option value="PI">PI</option>
                    <option value="RJ">RJ</option>
                    <option value="RN">RN</option>
                    <option value="RS">RS</option>
                    <option value="RO">RO</option>
                    <option value="RR">RR</option>
                    <option value="SC">SC</option>
                    <option value="SP">SP</option>
                    <option value="SE">SE</option>
                    <option value="TO">TO</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="cep" class="form-label">CEP</label>
                <input type="text" class="form-control" id="cep" name="cep" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="telefone1" class="form-label">Telefone 1</label>
                <input type="tel" class="form-control" id="telefone1" name="telefone1" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="telefone2" class="form-label">Telefone 2 (opcional)</label>
                <input type="tel" class="form-control" id="telefone2" name="telefone2">
            </div>
        </div>

        <h3>Upload de Documentos</h3>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="copia_identidade" class="form-label">Cópia de Identidade</label>
                <input type="file" class="form-control" id="copia_identidade" name="copia_identidade" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="copia_residencia" class="form-label">Cópia de Residência</label>
                <input type="file" class="form-control" id="copia_residencia" name="copia_residencia" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="certidao_nascimento" class="form-label">Certidão de Nascimento</label>
                <input type="file" class="form-control" id="certidao_nascimento" name="certidao_nascimento" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="certidao_casamento" class="form-label">Certidão de Casamento</label>
                <input type="file" ```php
                class="form-control" id="certidao_casamento" name="certidao_casamento">
            </div>
        </div>
        <button type="submit" name="cadastrar_conjuge" class="btn btn-primary">Cadastrar Cônjuge</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>