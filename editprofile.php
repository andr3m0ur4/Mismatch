<?php
    // Iniciar a sessão
    require_once ( 'config/startsession.php' );

    // Insere o cabeçalho da página
    $page_title = 'Editar Perfil';
    require_once ( 'include/header.php' );

    require_once ( 'config/appvars.php' );
    require_once ( 'config/connectvars.php' );

    // Ter certeza de que o usuário está logado antes de prosseguir.
    if ( !isset ( $_SESSION['user_id'] ) ) {
        echo '<p class="login">Por favor <a href="login.php">entre</a> para acessar esta página.</p>';
        exit ( );
    }

    // Mostrar o menu de navegação
    require_once ( 'include/navmenu.php' );

    // Conecta-se ao banco de dados
    $dbc = mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
    mysqli_set_charset ( $dbc, 'utf8');

    if ( isset ( $_POST['submit'] ) ) {
        // Obtém os dados do perfil do POST
        $first_name = mysqli_real_escape_string ( $dbc, trim ( $_POST['firstname'] ) );
        $last_name = mysqli_real_escape_string ( $dbc, trim ( $_POST['lastname'] ) );
        $gender = mysqli_real_escape_string ( $dbc, trim ( $_POST['gender'] ) );
        $birthdate = mysqli_real_escape_string($dbc, trim($_POST['birthdate']));
        $city = mysqli_real_escape_string ( $dbc, trim ( $_POST['city'] ) );
        $state = mysqli_real_escape_string ( $dbc, trim ( $_POST['state'] ) );
        $old_picture = mysqli_real_escape_string ( $dbc, trim ( $_POST['old_picture'] ) );
        if ( !empty ( $_FILES['new_picture']['name'] ) ) {
            $new_picture = mysqli_real_escape_string ( $dbc, trim ( $_FILES['new_picture']['name'] ) );
            $new_picture_type = $_FILES['new_picture']['type'];
            $new_picture_size = $_FILES['new_picture']['size']; 
            list ( $new_picture_width, $new_picture_height) = getimagesize ( $_FILES['new_picture']['tmp_name'] );
        }
        $error = false;

        // Validar e mover o arquivo de foto do upload, se necessário
        if ( !empty ( $new_picture ) ) {
            if ( ( ( $new_picture_type == 'image/gif' ) || ( $new_picture_type == 'image/jpeg' ) ||
                ( $new_picture_type == 'image/pjpeg' ) || ( $new_picture_type == 'image/png' ) ) &&
                ( $new_picture_size > 0 ) && ( $new_picture_size <= MM_MAXFILESIZE ) &&
                ( $new_picture_width <= MM_MAXIMGWIDTH ) && ( $new_picture_height <= MM_MAXIMGHEIGHT ) ) {

                if ( $_FILES['new_picture']['error'] == 0 ) {
                    // Move o arquivo para a pasta de destino do upload
                    $target = MM_UPLOADPATH . basename ( $new_picture );
                    if ( move_uploaded_file ( $_FILES['new_picture']['tmp_name'], $target ) ) {
                        // O novo arquivo de foto foi movido com sucesso, agora tenha certeza de que qualquer
                        // foto antiga seja excluída
                        if ( !empty ( $old_picture ) && ( $old_picture != $new_picture ) ) {
                            @unlink ( MM_UPLOADPATH . $old_picture );
                        }
                    } else {
                        // O novo arquivo de foto falhou ao mover, portanto, excluir o arquivo temporário e
                        // definir o flag de erro
                        @unlink ( $_FILES['new_picture']['tmp_name'] );
                        $error = true;
                        echo '<p class="error">
                                Desculpa, ocorreu um problema realizando o upload de sua foto.
                            </p>';
                    }
                }
            } else {
                // O novo arquivo de foto não é válido, portanto, excluir o arquivo temporário e definir o 
                // flag de erro
                @unlink ( $_FILES['new_picture']['tmp_name'] );
                $error = true;
                echo '<p class="error">
                        Sua foto deve ser um arquivo de imagem GIF, JPEG, ou PNG não maior que ' .
                        ( MM_MAXFILESIZE / 1024 ) . ' KB e ' . MM_MAXIMGWIDTH . 'x' . MM_MAXIMGHEIGHT . 
                        ' pixels de tamanho.
                    </p>';
            }
        }

        // Atualizar os dados do perfil no banco de dados
        if ( !$error ) {
            if ( !empty ( $first_name ) && !empty ( $last_name ) && !empty ( $gender ) && !empty ( $birthdate ) &&
                !empty ( $city ) && !empty ( $state ) ) {
        
                // Somente definir a coluna de foto se houver uma nova foto
                if ( !empty ( $new_picture ) ) {
                    $query = "UPDATE mismatch_user SET 
                                    first_name = '$first_name', 
                                    last_name = '$last_name',
                                    gender = '$gender', 
                                    birthdate = '$birthdate', 
                                    city = '$city', 
                                    state = '$state', 
                                    picture = '$new_picture' 
                                WHERE user_id = '" . $_SESSION['user_id'] . "'";
                } else {
                    $query = "UPDATE mismatch_user SET 
                                    first_name = '$first_name', 
                                    last_name = '$last_name', 
                                    gender = '$gender', 
                                    birthdate = '$birthdate', 
                                    city = '$city', 
                                    state = '$state' 
                                WHERE user_id = '" . $_SESSION['user_id'] . "'";
                }
                mysqli_query ( $dbc, $query );

                // Confirmar o sucesso com o usuário
                echo '<p>
                        Seu perfil foi atualizado com sucesso. Você gostaria de 
                        <a href="viewprofile.php">visualizar seu perfil</a>?
                    </p>';

                mysqli_close ( $dbc );
                exit ( );
            } else {
                echo '<p class="error">Você deve digitar todos os dados do perfil (a foto é opcional).</p>';
            }
        }
    } // Fim da verificação para submissão do formulário
    else {
        // Obtém os dados do perfil do banco de dados
        $query = "SELECT first_name, last_name, gender, birthdate, city, state, picture 
                    FROM mismatch_user WHERE user_id = '" . $_SESSION['user_id'] . "'";
        $data = mysqli_query ( $dbc, $query );
        $row = mysqli_fetch_assoc ( $data );

        if ( $row != NULL ) {
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $gender = $row['gender'];
            $birthdate = $row['birthdate'];
            $city = $row['city'];
            $state = $row['state'];
            $old_picture = $row['picture'];
        } else {
            echo '<p class="error">Ocorreu um problema acessando o seu perfil.</p>';
        }
    }

    mysqli_close ( $dbc );
?>

    <form enctype="multipart/form-data" method="post" action="<?=$_SERVER['PHP_SELF']?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="<?=MM_MAXFILESIZE?>" />
        <fieldset>
            <legend>Informação Pessoal</legend>
            <label for="firstname">Nome:</label>
            <input type="text" id="firstname" name="firstname" required 
                value="<?php if ( !empty ( $first_name ) ) echo $first_name; ?>" />
            <br />
            <label for="lastname">Sobrenome:</label>
            <input type="text" id="lastname" name="lastname" required 
                value="<?php if ( !empty ( $last_name ) ) echo $last_name; ?>" />
            <br />
            <label for="gender">Gênero:</label>
            <select id="gender" name="gender">
                <option value="M" <?php if ( !empty ( $gender ) && $gender == 'M' ) echo 'selected'; ?>>
                    Masculino
                </option>
                <option value="F" <?php if ( !empty ( $gender ) && $gender == 'F' ) echo 'selected'; ?>>
                    Feminino
                </option>
            </select>
            <br />
            <label for="birthdate">Data de nascimento:</label>
            <input type="date" id="birthdate" name="birthdate" required placeholder="YYYY-MM-DD"
                value="<?php if ( !empty ( $birthdate ) ) echo $birthdate; ?>" />
            <br />
            <label for="city">Cidade:</label>
            <input type="text" id="city" name="city" required
                value="<?php if ( !empty ( $city ) ) echo $city; ?>" />
            <br />
            <label for="state">Estado:</label>
            <input type="text" id="state" name="state" required 
                value="<?php if ( !empty ( $state ) ) echo $state; ?>" >
            <br />
            <input type="hidden" name="old_picture" 
                value="<?php if ( !empty ( $old_picture ) ) echo $old_picture; ?>" />
            <label for="new_picture">Foto:</label>
            <input type="file" id="new_picture" name="new_picture" />
            <?php if ( !empty ( $old_picture ) ) {
                echo '<img class="profile" src="' . MM_UPLOADPATH . $old_picture . '" alt="Foto de Perfil" />';
            } ?>
        </fieldset>
        <br>
        <button type="submit" name="submit">Salvar Perfil</button>
    </form>

<?php
    // Inserir o rodapé da página
    require_once ( 'include/footer.php' );
?>