<?php

    // Inicia a sessão
    require_once './config/startsession.php';

    require_once './config/appvars.php';
    require_once './config/connectvars.php';
    require_once './helpers/Db.php';
    require_once './helpers/functions.php';
    require_once './models/Service.php';
    require_once './models/User.php';

    // Verificar qual arquivo (rota) deve ser usado para tratar a requisição

    $route = 'home';  // Rota padrão

    if (array_key_exists('route', $_GET)) {
        $route = (string) $_GET['route'];
    }

    // Incluir o arquivo que vai tratar a requisição

    if (is_file("controllers/{$route}.php")) {
        require_once "controllers/{$route}.php";
    } else {
        echo 'Rota não encontrada';
    }
