<?php
    // Gera o menu de navegação
    echo '<hr />';
    if ( isset ( $_SESSION['username'] ) ) {
        echo '<a href="index.php">Home</a> &#10084; 
            <a href="viewprofile.php">Visualizar Perfil</a> &#10084; 
            <a href="editprofile.php">Editar Perfil</a> &#10084; 
            <a href="questionnaire.php">Questionário</a> &#10084; 
            <a href="mymismatch.php">Meu Mismatch</a> &#10084; 
            <a href="logout.php">Sair (' . $_SESSION['username'] . ')</a>';
    } else {
        echo '<a href="login.php">Entrar</a> &#10084; 
            <a href="signup.php">Cadastrar</a>';
    }
    echo '<hr />';
