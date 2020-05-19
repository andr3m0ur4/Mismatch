<?php

    $username = '';

    $success = false;
    $error_username = false;
    $error_empty = false;

    if (has_post()) {

        // Obtém os dados do perfil do POST
        $username = htmlentities(trim($_POST['username']));
        $password1 = htmlentities(trim($_POST['password1']));
        $password2 = htmlentities(trim($_POST['password2']));
        
        if (
            !empty($username) && 
            !empty($password1) && 
            !empty($password2) && 
            ($password1 == $password2)
        ) {

            // Garantir que ninguém já esteja registrado utilizando este nome de usuário
            $user = new User();
            $user->__set('username', $username);
            
            if ($user->countUser() == 0) {
                // O nome de usuário é único, portanto, inserir os dados no banco de dados
                $user->__set('password', $password1);
                $user->insert();
                
                // Confirmar o sucesso com o usuário
                $success = true;
                $username = '';
                
            } else {
                // Uma conta já existe para este nome de usuário, portanto, exibir uma mensagem de erro
                $error_username = true;
                $username = '';
            }
        } else {
            $error_empty = true;
        }
    }

    // Insere o cabeçalho da página
    $page_title = 'Cadastro';
    require_once 'views/include/header.phtml';

    // Insere o formulário de cadastro
    require_once './views/template-signup.phtml';

    // Inserir o rodapé da página
    require_once './views/include/footer.phtml';
