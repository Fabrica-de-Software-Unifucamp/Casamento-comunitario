<?php
include 'conexao.php'; //inclui o arquivo de conexão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //pega os dados do formulário
    $usuario = $_POST['username'];
    $senha = $_POST['password'];

    //questoes de segurança contra SQL Injection
    $usuario = $conn->real_escape_string($usuario);
    $senha = $conn->real_escape_string($senha); //

    //consulta direta ao banco de dados para buscar a senha correspondente ao usuário
    $sql = "SELECT senha FROM loginadm WHERE usuario = '$usuario'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        //se houver resultado, pega a senha armazenada
        $row = $result->fetch_assoc();
        $stored_password = $row['senha']; 

        //verifica se a senha fornecida é igual à armazenada
        if ($senha == $stored_password) {
            //senha correta
            header("Location: "); //dentro das "" devera ser colocado qual sera o caminho que o codigo devera levar. (ex: Location: paginicial.html);
            exit(); //encerra o script após redirecionar
        } else {
            //senha incorreta
            echo "Usuário ou senha incorretos. Tente novamente.";
        }
    } else {
        //usuário não encontrado
        echo "Usuário ou senha incorretos. Tente novamente.";
    }
}

//fecha a conexão com o banco
$conn->close();
?>
