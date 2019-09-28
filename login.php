<?php
    require_once ( 'config/connectvars.php' );

    // Iniciar a sessão
    session_start();

    // Limpar a mensagem de erro
    $error_msg = "";

    // Se o usuário não está logado, tentar logá-lo
    if ( !isset ( $_SESSION['user_id'] ) ) {
        if ( isset ( $_POST['submit'] ) ) {
            // Conecta-se ao banco de dados
            $dbc = mysqli_connect ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
            mysqli_set_charset ( $dbc, 'utf8');

            // Obtém os dados de login digitados pelo usuário
            $user_username = mysqli_real_escape_string ( $dbc, trim ( $_POST['username'] ) );
            $user_password = mysqli_real_escape_string ( $dbc, trim ( $_POST['password'] ) );

            if ( !empty ( $user_username ) && !empty ( $user_password ) ) {
                // Procura o nome de usuário e senha no banco de dados
                $query = "SELECT user_id, username FROM mismatch_user 
                            WHERE username = '$user_username' AND password = SHA('$user_password')";
                $data = mysqli_query ( $dbc, $query );

                if ( mysqli_num_rows ( $data ) == 1 ) {
                    // O login está OK, então definir as variáveis de sessão do ID do usuário e nome de usuário 
                    // (e cookies), e redirecionar para a página principal
                    $row = mysqli_fetch_assoc ( $data );
                    $_SESSION['user_id'] = $row['user_id'];
                    $_SESSION['username'] = $row['username'];
                    setcookie ( 'user_id', $row['user_id'], time ( ) + ( 60 * 60 * 24 * 30 ) ); 
                    // expira em 30 dias
                    setcookie ( 'username', $row['username'], time ( ) + ( 60 * 60 * 24 * 30 ) );
                    // expira em 30 dias
                    $home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname ( $_SERVER['PHP_SELF'] ) .
                        '/index.php';
                    header ( 'Location: ' . $home_url );
                } else {
                    // O nome de usuário/senha estão incorretos, portanto, definir uma mensagem de erro
                    $error_msg = 'Desculpa, você deve digitar um nome de usuário e senha válidos para entrar.';
                }
            } else {
                // O nome de usuário/senha não foram digitados, portanto, definir uma mensagem de erro
                $error_msg = 'Desculpa, você deve digitar seu nome de usuário e senha para entrar.';
            }
        }
    }

    // Inserir o cabeçalho da página
    $page_title = 'Entrar';
    require_once ( 'include/header.php' );

    // Se a variável de sessão está vazia, mostrar alguma mensagem de erro e o formulário de login; 
    // caso contrário, confirme o login
    if ( empty ( $_SESSION['user_id'] ) ) {
        echo '<p class="error">' . $error_msg . '</p>';
?>

        <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
            <fieldset>
                <legend>Entrar</legend>
                <label for="username">Nome de usuário:</label>
                <input type="text" name="username" required 
                    value="<?php if ( !empty ( $user_username ) ) echo $user_username; ?>" />
                <br />
                <label for="password">Senha:</label>
                <input type="password" name="password" required />
            </fieldset>
            <button type="submit" name="submit">Entrar</button>
        </form>

<?php
    } else {
        // Confirmar o login com sucesso
        echo ( '<p class="login">Você está logado como ' . $_SESSION['username'] . '.</p>' );
    }

    // Inserir o rodapé da página
    require_once ( 'include/footer.php' );
?>