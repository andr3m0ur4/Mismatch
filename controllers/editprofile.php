<?php

    // Insere o cabeçalho da página
    $page_title = 'Editar Perfil';
    require_once './views/include/header.phtml';

    // Ter certeza de que o usuário está logado antes de prosseguir.
    require_once './views/include/authenticate.phtml';

    // Mostrar o menu de navegação
    require_once './views/include/navmenu.phtml';

    $user = new User();

    $first_name = '';
    $last_name = '';
    $gender = '';
    $birthdate = '';
    $city = '';
    $state = '';
    $old_picture = '';
    $error_profile = false;
    $error = false;
    $error_move = false;
    $success = false;
    $error_data = false;

    if (has_post()) {
        // Obtém os dados do perfil do POST
        $user->first_name = htmlentities(trim($_POST['firstname']));
        $user->last_name = htmlentities(trim($_POST['lastname']));
        $user->gender = htmlentities(trim($_POST['gender']));
        $user->birthdate = htmlentities(trim($_POST['birthdate']));
        $user->city = htmlentities(trim($_POST['city']));
        $user->state = htmlentities(trim($_POST['state']));
        $old_picture = htmlentities(trim($_POST['old_picture']));

        if (!empty($_FILES['new_picture']['name'])) {
            $new_picture = htmlentities(trim($_FILES['new_picture']['name']));
            $new_picture_type = $_FILES['new_picture']['type'];
            $new_picture_size = $_FILES['new_picture']['size'];
            $new_picture_tmp_name = $_FILES['new_picture']['tmp_name'];
            list($new_picture_width, $new_picture_height) = getimagesize($new_picture_tmp_name);
        }

        // Validar e mover o arquivo de foto do upload, se necessário
        if (!empty($new_picture)) {
            if (
                (
                    ($new_picture_type == 'image/gif') ||
                    ($new_picture_type == 'image/jpeg') ||
                    ($new_picture_type == 'image/pjpeg') ||
                    ($new_picture_type == 'image/png')
                ) &&
                ($new_picture_size > 0) &&
                ($new_picture_size <= MM_MAXFILESIZE) &&
                ($new_picture_width <= MM_MAXIMGWIDTH) &&
                ($new_picture_height <= MM_MAXIMGHEIGHT)
            ) {

                if ($_FILES['new_picture']['error'] == 0) {
                    // Move o arquivo para a pasta de destino do upload
                    $target = MM_UPLOADPATH . basename($new_picture);

                    if (move_uploaded_file($new_picture_tmp_name, $target)) {
                        // O novo arquivo de foto foi movido com sucesso,
                        // agora tenha certeza de que qualquer foto antiga seja excluída
                        if (!empty($old_picture) && ($old_picture != $new_picture)) {
                            @unlink(MM_UPLOADPATH . $old_picture);
                        }
                    } else {
                        // O novo arquivo de foto falhou ao mover, portanto,
                        // excluir o arquivo temporário e definir o flag de erro
                        @unlink($new_picture_tmp_name);
                        $error_move = true;
                    }
                }
            } else {
                // O novo arquivo de foto não é válido, portanto, excluir o arquivo temporário
                // e definir o flag de erro
                @unlink($new_picture_tmp_name);
                $error = true;
            }
        }

        // Atualizar os dados do perfil no banco de dados
        if (!$error && !$error_move) {
            if (
                !empty($user->__get('first_name')) &&
                !empty($user->__get('last_name')) &&
                !empty($user->__get('gender')) &&
                !empty($user->__get('birthdate')) &&
                !empty($user->__get('city')) &&
                !empty($user->__get('state'))
            ) {
                $user->user_id = $_SESSION['user_id'];

                // Somente definir a coluna de foto se houver uma nova foto
                if (!empty($new_picture)) {
                    $user->picture = $new_picture;
                } else {
                    $user->picture = $old_picture;
                }

                $user->update();

                // Confirmar o sucesso com o usuário
                $success = true;
            } else {
                $error_data = true;
            }
        }

        // Fim da verificação para submissão do formulário
    } else {
        // Obtém os dados do perfil do banco de dados
        $user->__set('user_id', $_SESSION['user_id']);
        $user = $user->getUser();

        if ($user) {
            $first_name = $user->first_name;
            $last_name = $user->last_name;
            $gender = $user->gender;
            $birthdate = $user->birthdate;
            $city = $user->city;
            $state = $user->state;
            $old_picture = $user->picture;
        } else {
            $error_profile = true;
        }
    }

    // Insere o conteúdo da página
    require_once './views/template-editprofile.phtml';

    // Inserir o rodapé da página
    require_once './views/include/footer.phtml';
