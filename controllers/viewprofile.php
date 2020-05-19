<?php

    $user = new User();

    // Obtém os dados do perfil do banco de dados
    if (!isset($_GET['user_id'])) {
        $user->__set('user_id', $_SESSION['user_id']);
    } else {
        $user->__set('user_id', $_GET['user_id']);
    }

    // Inserir o cabeçalho da página
    $page_title = 'Visualizar Perfil';
    require_once './views/include/header.phtml';

    // Mostrar o menu de navegação
    require_once './views/include/navmenu.phtml';

    // Insere o conteúdo da página
    require_once './views/template-viewprofile.phtml';

    // Insere o rodapé da página
    require_once './views/include/footer.phtml';
