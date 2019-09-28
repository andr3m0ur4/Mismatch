<?php
    // Inicia a sessão
    require_once ( 'config/startsession.php' );

    // Inserir o cabeçalho da página
    $page_title = 'Onde os opostos se atraem!';
    require_once ( 'include/header.php' );

    require_once ( 'config/appvars.php' );
    require_once ( 'config/connectvars.php' );

    // Exibir o menu de navegação
    require_once ( 'include/navmenu.php' );

    // Conecta-se ao banco de dados
    $dbc = mysqli_connect ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );

    // Recupera os dados do usuário através do MySQL
    $query = "SELECT user_id, first_name, picture FROM mismatch_user WHERE first_name IS NOT NULL ORDER BY
                join_date DESC LIMIT 5";
    $data = mysqli_query ( $dbc, $query );

    // Percorre o array dos dados do usuário,  formatando-os como HTML
    echo '<h4>Membros mais recentes:</h4>
        <table>';
    while ( $row = mysqli_fetch_assoc ( $data ) ) {
        if ( is_file ( MM_UPLOADPATH . $row['picture'] ) && filesize ( MM_UPLOADPATH . $row['picture'] ) > 0 ) {
            echo '<tr>
                    <td>
                        <img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="' . $row['first_name'] . '" />
                    </td>';
        } else {
            echo '<tr>
                    <td>
                        <img src="' . MM_UPLOADPATH . 'nopic.jpg' . '" alt="' . $row['first_name'] . '" />
                    </td>';
        }
        if ( isset ( $_SESSION['user_id'] ) ) {
            echo '<td>
                    <a href="viewprofile.php?user_id=' . $row['user_id'] . '">
                        ' . $row['first_name'] . '
                    </a>
                </td>
                </tr>';
        } else {
            echo '<td>' . $row['first_name'] . '</td>
                </tr>';
        }
    }
    echo '</table>';

    mysqli_close ( $dbc );

    // Insere o rodapé da página
    require_once ( 'include/footer.php' );
