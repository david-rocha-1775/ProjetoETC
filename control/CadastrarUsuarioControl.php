<?php
require_once"../model/dao/conexao.php";
try {
    //pegar dados formulario
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    //conectar ao banco
    $conexao = Conexao::getInstance();

    //preparar o comando sql (insert)
    // o id é auto increment, entao nao precisa enviar
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)";

    //Preparar a consulta
    $stmt = $conexao->prepare($sql);

    //vincular os valores
    $stmt->bindParam('nome', $nome,) ;
    $stmt->bindParam('email', $email,) ;
    $stmt->bindParam('senha', $senha, );

    //executar
    $stmt->execute() ;

    echo"<p>usuario cadastrado com sucesso</p>";

} catch (Exception $e) {
    echo"<p>Erro ao conectar:". $e->getMessage() ."</p>";
}
?>

<br>
<a href="../view/CadastrarUsuarioView.php">
    <button>Cadastrar outro</button>
</a>

<a href="../index.php">
    <button>Voltar ao inicio</button>
</a>
