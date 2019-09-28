<?php
    // Insere o cabeçalho da página
    $page_title = 'Cadastro';
    require_once ( 'include/header.php' );

    require_once ( 'config/appvars.php' );
    require_once ( 'config/connectvars.php' );

    // Conecta-se ao banco de dados
    $dbc = mysqli_connect ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
    mysqli_set_charset ( $dbc, 'utf8');

    if ( isset ( $_POST['submit'] ) ) {
        // Obtém os dados do perfil do POST
        $username = mysqli_real_escape_string ( $dbc, trim ( $_POST['username'] ) );
        $password1 = mysqli_real_escape_string ( $dbc, trim ( $_POST['password1'] ) );
        $password2 = mysqli_real_escape_string ( $dbc, trim ( $_POST['password2'] ) );

        if ( !empty ( $username ) && !empty ( $password1 ) && !empty ( $password2 ) &&
            ( $password1 == $password2 ) ) {
      
            // Tenha certeza de que ninguém já esteja registrado utilizando este nome de usuário
            $query = "SELECT * FROM mismatch_user WHERE username = '$username'";
            $data = mysqli_query ( $dbc, $query );
            if ( mysqli_num_rows ( $data ) == 0 ) {
                // O nome de usuário é único, portanto, inserir os dados no banco de dados
                $query = "INSERT INTO mismatch_user (username, password, join_date) 
                            VALUES ('$username', SHA('$password1'), NOW())";
                mysqli_query ( $dbc, $query );

                // Confirmar o sucesso com o usuário
                echo '<p>
                        Sua nova conta foi criada com sucesso. Você agora está pronto para 
                        <a href="login.php">entrar</a>.
                    </p>';

                mysqli_close ( $dbc );
                exit ( );
            } else {
                // Uma conta já existe para este nome de usuário, portanto, exibir uma mensagem de erro
                echo '<p class="error">
                        Uma conta já existe para este nome de usuário. Por favor utilize um endereço diferente.
                    </p>';
                $username = "";
            }
        } else {
            echo '<p class="error">
                    Você deve digitar todos os dados do cadastro, incluindo a senha desejada duas vezes.
                </p>';
        }
    }

    mysqli_close ( $dbc );
?>

    <p>Por favor digite seu nome de usuário e senha desejada para cadastrar no Mismatch.</p>
    <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
        <fieldset>
            <legend>Informações de Registro</legend>
            <label for="username">Nome de usuário:</label>
            <input type="text" name="username" value="<?php if ( !empty ( $username ) ) echo $username; ?>"
                required />
            <br />
            <label for="password1">Senha:</label>
            <input type="password" name="password1" required /><br />
            <label for="password2">Senha (redigitar):</label>
            <input type="password" name="password2" required /><br />
        </fieldset>
        <br>
        <button type="submit" name="submit">Cadastrar</button>
    </form>

<?php
    // Inserir o rodapé da página
    require_once ( 'include/footer.php' );
?>