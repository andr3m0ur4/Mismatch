<?php
    // Iniciar a sessão
    require_once ( 'config/startsession.php' );

    // Inserir o cabeçalho da página
    $page_title = 'Visualizar Perfil';
    require_once ( 'include/header.php' );

    require_once ( 'config/appvars.php' );
    require_once ( 'config/connectvars.php' );

    // Tenha certeza de que o usuário está logado antes de prosseguir.
    if ( !isset ( $_SESSION['user_id'] ) ) {
        echo '<p class="login">Por favor <a href="login.php">entre</a> para acessar esta página.</p>';
        exit ( );
    }

    // Mostrar o menu de navegação
    require_once ( 'include/navmenu.php' );

    // Conecta-se ao banco de dados
    $dbc = mysqli_connect ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
    mysqli_set_charset ( $dbc, 'utf8');

    // Obtém os dados do perfil do banco de dados
    if ( !isset ( $_GET['user_id'] ) ) {
        $query = "SELECT username, first_name, last_name, gender, birthdate, city, state, picture 
                    FROM mismatch_user WHERE user_id = '" . $_SESSION['user_id'] . "'";
    } else {
        $query = "SELECT username, first_name, last_name, gender, birthdate, city, state, picture 
                    FROM mismatch_user WHERE user_id = '" . $_GET['user_id'] . "'";
    }
    $data = mysqli_query ( $dbc, $query );

    if ( mysqli_num_rows ( $data ) == 1 ) {
        // O registro do usuário foi encontrado, então, exibir os dados do usuário
        $row = mysqli_fetch_assoc ( $data );
        echo '<table>';
        if ( !empty ( $row['username'] ) ) {
            echo '<tr>
                    <td class="label">Nome de usuário:</td>
                    <td>' . $row['username'] . '</td>
                </tr>';
        }
        if ( !empty ( $row['first_name'] ) ) {
            echo '<tr>
                    <td class="label">Nome:</td>
                    <td>' . $row['first_name'] . '</td>
                </tr>';
        }
        if ( !empty ( $row['last_name'] ) ) {
            echo '<tr>
                    <td class="label">Sobrenome:</td>
                    <td>' . $row['last_name'] . '</td>
                </tr>';
        }
        if ( !empty ( $row['gender'] ) ) {
            echo '<tr>
                    <td class="label">Gênero:</td>
                    <td>';
            if ( $row['gender'] == 'M' ) {
                echo 'Masculino';
            } else if ( $row['gender'] == 'F' ) {
                echo 'Feminino';
            } else {
                echo '?';
            }
            echo '</td>
                </tr>';
        }
        if ( !empty ( $row['birthdate'] ) ) {
            if ( !isset ( $_GET['user_id'] ) || ( $_SESSION['user_id'] == $_GET['user_id'] ) ) {
                // Mostrar ao usuário sua própria dada de nasimento
                echo '<tr>
                        <td class="label">Data de nascimento:</td>
                        <td>' . date ( "d-m-Y", strtotime ( $row['birthdate'] ) ) . '</td>
                    </tr>';
            } else {
                // Mostrar somente o ano de nascimento para todos os outros
                list ( $year, $month, $day) = explode ( '-', $row['birthdate'] );
                echo '<tr>
                        <td class="label">Ano de nascimento:</td>
                        <td>' . $year . '</td>
                    </tr>';
            }
        }
        if ( !empty ( $row['city'] ) || !empty ( $row['state'] ) ) {
            echo '<tr>
                    <td class="label">Localização:</td>
                    <td>' . $row['city'] . ', ' . $row['state'] . '</td>
                </tr>';
        }
        if ( !empty ( $row['picture'] ) ) {
            echo '<tr>
                    <td class="label">Foto:</td>
                    <td><img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="Foto de Perfil" /></td>
                </tr>';
        }
        echo '</table>';
        if ( !isset ( $_GET['user_id'] ) || ( $_SESSION['user_id'] == $_GET['user_id'] ) ) {
            echo '<p>Você gostaria de <a href="editprofile.php">editar seu perfil</a>?</p>';
        }
    } // Fim da verificação para um único registro dos resultados do usuário
    else {
        echo '<p class="error">Ocorreu um problema acessando o seu perfil.</p>';
    }

    mysqli_close ( $dbc );

    // Insere o rodapé da página
    require_once ( 'include/footer.php' );
