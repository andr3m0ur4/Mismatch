<?php
    session_start ( );

    // Se as variáveis de sessão não estão configuradas, tentar configurá-las com um cookie
    if ( !isset ( $_SESSION['user_id'] ) ) {
        if ( isset ( $_COOKIE['user_id'] ) && isset ( $_COOKIE['username'] ) ) {
            $_SESSION['user_id'] = $_COOKIE['user_id'];
            $_SESSION['username'] = $_COOKIE['username'];
        }
    }
