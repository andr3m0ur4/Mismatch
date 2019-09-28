<?php
    // Se o usuário está logado, excluir as variáveis de sessão para desconectá-lo
    session_start ( );
    if ( isset ( $_SESSION['user_id'] ) ) {
        // Excluir as variáveis de sessão limpando o array do $_SESSION
        $_SESSION = array ( );

        // Excluir o cookie de sessão configurando sua expiração para uma hora atrás (3600)
        if ( isset ( $_COOKIE[session_name ( )] ) ) {
            setcookie ( session_name ( ), '', time ( ) - 3600 );
        }

        // Destruir a sessão
        session_destroy ( );
    }

    // Excluir os cookies de ID do usuário e nome de usuário configurando sua expiração para uma hora atrás
    setcookie ( 'user_id', '', time ( ) - 3600 );
    setcookie ( 'username', '', time ( ) - 3600 );

    // Redirecionar para a página principal
    $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname ( $_SERVER['PHP_SELF'] ) . '/index.php';
    header ( 'Location: ' . $home_url );
