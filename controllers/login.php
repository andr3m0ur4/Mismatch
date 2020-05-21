<?php

    // Limpar a mensagem de erro
    $error_msg = '';

    $user_username = '';

    // Se o usuário não está logado, tentar logá-lo
    if (!isset($_SESSION['user_id'])) {
        if (has_post()) {

            // Obtém os dados de login digitados pelo usuário
            $user_username = htmlentities(trim($_POST['username']));
            $user_password = htmlentities(trim($_POST['password']));

            if (!empty($user_username) && !empty($user_password)) {
                // Procura o nome de usuário e senha no banco de dados
                $user = new User();
                $user->__set('username', $user_username);
                $user->__set('password', $user_password);

                if ($user->verifyUser()->rowCount() == 1) {
                    // O login está OK, então definir as variáveis de sessão do ID do usuário
                    // e nome de usuário (e cookies),
                    // por fim, redirecionar para a página principal
                    $user = $user->verifyUser()->fetchObject('User');

                    $_SESSION['user_id'] = $user->user_id;
                    $_SESSION['username'] = $user->username;

                    setcookie('user_id', $user->user_id, time() + (60 * 60 * 24 * 30)); 
                    // expira em 30 dias
                    setcookie ('username', $user->username, time() + (60 * 60 * 24 * 30));
                    // expira em 30 dias

                    $home_url = './index.php';
                    header('Location: ' . $home_url);
                } else {
                    // O nome de usuário/senha estão incorretos, portanto, definir uma mensagem de erro
                    $error_msg = '
                        Desculpa, você deve digitar um nome de usuário e senha válidos para entrar.
                    ';
                }
            } else {
                // O nome de usuário/senha não foram digitados, portanto, definir uma mensagem de erro
                $error_msg = 'Desculpa, você deve digitar seu nome de usuário e senha para entrar.';
            }
        }
    }

    // Inserir o cabeçalho da página
    $page_title = 'Entrar';
    require_once './views/include/header.phtml';

    // Insere o conteúdo da página
    require_once './views/template-login.phtml';

    // Inserir o rodapé da página
    require_once './views/include/footer.phtml';
