<?php
$host = 'localhost'; //endereço do servidor MySQL (localhost para servidor local)
$username = '';  //nome de usuario do workbench ou mysql server
$password = '';  //senha (se nao tiver, deixar vazia)
$database = ''; //nome do banco de dados

//criando a conexao
$conn = new mysqli($host, $username, $password, $database);

//verificando se deu tudo certo com a conexao. pra testar é só tirar o /* */.
/*if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} else {
    echo "Conexão estabelecida com sucesso!";
}*/
?>
