<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $sql = "INSERT INTO Conjuge (Nome, CPF, RG, Data_Nascimento, Estado_Civil, Numero, Rua, Bairro, Cidade, UF, CEP, Telefone1, Telefone2) 
        VALUES ('$nome', '$cpf', '$rg', '$dataNascimento', '$estadoCivil', '$numero', '$rua', '$bairro', '$cidade', '$uf', '$cep', '$telefone1', '$telefone2')";
        $result = $mysqli->query($sql);

    }
}
?>