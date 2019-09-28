<?php
    // Iniciar a sessão
    require_once ( 'config/startsession.php' );

    // Inserir o cabeçalho da página
    $page_title = 'Questionário';
    require_once ( 'include/header.php' );

    require_once ( 'config/appvars.php' );
    require_once ( 'config/connectvars.php' );

    // Ter certeza de que o usuário está logado antes de prosseguir.
    if ( !isset ( $_SESSION['user_id'] ) ) {
        echo '<p class="login">Por favor <a href="login.php">entre</a> para acessar esta página.</p>';
        exit ( );
    }

    // mostrar o menu de navegação
    require_once ( 'include/navmenu.php' );

    // Conecta-se ao banco de dados
    $dbc = mysqli_connect ( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME );
    mysqli_set_charset ( $dbc, 'utf8');

    // Se este usuário nunca respondeu o questionário, inserir respostas vazias no banco de dados
    $query = "SELECT * FROM mismatch_response WHERE user_id = '" . $_SESSION['user_id'] . "'";
    $data = mysqli_query ( $dbc, $query );
    if ( mysqli_num_rows ( $data ) == 0 ) {
        // Primeiro obtém a lista de IDs dos tópicos da tabela tópico
        $query = "SELECT topic_id FROM mismatch_topic ORDER BY category_id, topic_id";
        $data = mysqli_query ( $dbc, $query );
        $topicIDs = array ( );
        while ( $row = mysqli_fetch_assoc ( $data ) ) {
            array_push ( $topicIDs, $row['topic_id'] );
        }

        // Inserir as linhas de respostas vazias na tabela resposta, um por tópico
        foreach ( $topicIDs as $topic_id ) {
            $query = "INSERT INTO mismatch_response (user_id, topic_id) 
                        VALUES ('" . $_SESSION['user_id']. "', '$topic_id')";
            mysqli_query ( $dbc, $query );
        }
    }

    // Se o formulário do questionário foi submetido, escreva as respostas do formulário no banco de dados
    if ( isset ( $_POST['submit'] ) ) {
        // Escreva as linhas de resposta do questionário na tabela resposta
        foreach ( $_POST as $response_id => $response) {
            $query = "UPDATE mismatch_response SET response = '$response' WHERE response_id = '$response_id'";
            mysqli_query ( $dbc, $query );
        }
        echo '<p>Suas respostas foram salvas.</p>';
    }

    // Obtém os dados de resposta do banco de dados para gerar o formulário
    $query = "SELECT mr.response_id, mr.topic_id, mr.response, mt.name AS topic_name, mc.name AS category_name 
                FROM mismatch_response AS mr 
                INNER JOIN mismatch_topic AS mt 
                USING (topic_id) 
                INNER JOIN mismatch_category AS mc 
                USING (category_id) 
                WHERE mr.user_id = '" . $_SESSION['user_id'] . "'";
    $data = mysqli_query ( $dbc, $query );
    $responses = array ( );
    while ( $row = mysqli_fetch_assoc ( $data ) ) {
        array_push ( $responses, $row );
    }

    mysqli_close ( $dbc );

    // Gera o formulário do questionário percorrendo o array de resposta
    echo '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">
            <p>Como você se sente sobre cada tópico?</p>';
    $category = $responses[0]['category_name'];
    echo '<fieldset>
            <legend>' . $responses[0]['category_name'] . '</legend>';
    foreach ( $responses as $response ) {
        // Somente iniciar um novo conjunto de campos se a categoria for alterada
        if ( $category != $response['category_name'] ) {
            $category = $response['category_name'];
            echo '</fieldset>
                <fieldset>
                    <legend>' . $response['category_name'] . '</legend>';
        }

        // Exibir o campo de formulário do tópico
        echo '<label ' . ( $response['response'] == NULL ? 'class="error"' : '' ) . ' 
                for="' . $response['response_id'] . '">' . $response['topic_name'] . ':</label>
            <input type="radio" id="' . $response['response_id'] . '" name="' . $response['response_id'] . '"
                value="1" ' . ( $response['response'] == 1 ? 'checked="checked"' : '' ) . ' />Amo 
            <input type="radio" id="' . $response['response_id'] . '" name="' . $response['response_id'] . '"
                value="2" ' . ( $response['response'] == 2 ? 'checked="checked"' : '' ) . ' />Odeio
            <br />';
    }
    echo '</fieldset>
        <br>
        <button type="submit" name="submit">Salvar Questionário</button>
    </form>';

    // Inserir o rodapé da página
    require_once ( 'include/footer.php' );
