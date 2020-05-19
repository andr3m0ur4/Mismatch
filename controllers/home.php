<?php

    // Criar um objeto da classe User
    $user = new User();

    // Recupera os dados do usuário através do MySQL
    $users = $user->getAll();
    
    // Inserir o cabeçalho da página
    $page_title = 'Onde os opostos se atraem!';
    require_once './views/include/header.phtml';

    // Exibir o menu de navegação
    require_once './views/include/navmenu.phtml';

    // Insere o conteúdo da página
    require_once './views/template.phtml';

    // Insere o rodapé da página
    require_once './views/include/footer.phtml';
