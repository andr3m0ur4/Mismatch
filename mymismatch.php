<?php
    require_once ( 'function/lib_function.php' );

    // Iniciar a sessão
    require_once ( 'config/startsession.php' );

    // Inserir o cabeçalho da página
    $page_title = 'Meu Mismatch';
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

    // Somente procura por um mismatch se o usuário armazenou respostas do questionário
    $query = "SELECT * FROM mismatch_response WHERE user_id = '" . $_SESSION['user_id'] . "'";
    $data = mysqli_query ( $dbc, $query );
    if ( mysqli_num_rows ( $data ) != 0 ) {
        // Primeiro obtém as respostas do usuário da tabela de resposta
        // (JOIN para pegar os nomes dos tópicos e das categorias)
        $query = "SELECT mr.response_id, mr.topic_id, mr.response, mt.name AS topic_name, mc.name 
                        AS category_name 
                    FROM mismatch_response AS mr 
                    INNER JOIN mismatch_topic AS mt 
                    USING (topic_id) 
                    INNER JOIN mismatch_category AS mc 
                    USING (category_id) 
                    WHERE mr.user_id = '" . $_SESSION['user_id'] . "'";
        $data = mysqli_query ( $dbc, $query );
        $user_responses = array ( );
        while ( $row = mysqli_fetch_assoc ( $data ) ) {
            array_push ( $user_responses, $row );
        }

        // Inicializa os resultados de busca do mismatch
        $mismatch_score = 0;
        $mismatch_user_id = -1;
        $mismatch_topics = array ( );
        $mismatch_categories = array ( );

        // Percorre a tabela do usuário comparando as respostas das outras pessoas com as respostas do usuário
        $query = "SELECT user_id FROM mismatch_user WHERE user_id != '" . $_SESSION['user_id'] . "'";
        $data = mysqli_query ( $dbc, $query );
        while ( $row = mysqli_fetch_assoc ( $data ) ) {
            // Obtém os dados de resposta para o usuário (um potencial mismatch)
            $query2 = "SELECT response_id, topic_id, response FROM mismatch_response 
                        WHERE user_id = '" . $row['user_id'] . "'";
            $data2 = mysqli_query ( $dbc, $query2 );
            $mismatch_responses = array ( );
            while ( $row2 = mysqli_fetch_array ( $data2 ) ) {
                array_push ( $mismatch_responses, $row2 );
            } // Fim loop while interno

            // Comparar cada resposta e calcular um total de mismatch
            $score = 0;
            $topics = array ( );
            $categories = array ( );
            for ( $i = 0; $i < count ( $user_responses ); $i++ ) {
                if ( $user_responses[$i]['response'] + $mismatch_responses[$i]['response'] == 3 ) {
                    $score += 1;
                    array_push ( $topics, $user_responses[$i]['topic_name'] );
                    array_push ( $categories, $user_responses[$i]['category_name'] );
                }
            }

            // Verificar para ver se esta pessoa é melhor do que o melhor mismatch até agora
            if ( $score > $mismatch_score ) {
                // Encontramos um mismatch melhor, portanto, atualizar os resultados de busca do mismatch
                $mismatch_score = $score;
                $mismatch_user_id = $row['user_id'];
                $mismatch_topics = array_slice ( $topics, 0 );
                $mismatch_categories = array_slice ( $categories, 0 );
            }
        } // Fim do loop while externo

        // Ter certeza de que um mismatch foi encontrado
        if ( $mismatch_user_id != -1 ) {
            $query = "SELECT username, first_name, last_name, city, state, picture 
                        FROM mismatch_user WHERE user_id = '$mismatch_user_id'";
            $data = mysqli_query ( $dbc, $query );
            if ( mysqli_num_rows ( $data ) == 1 ) {
                // A linha do usuário para o mismatch foi encontrado, portanto, exibir os dados do usuário
                $row = mysqli_fetch_assoc ( $data );
                echo '<table>
                        <tr>
                            <td class="label">';
                if ( !empty ( $row['first_name'] ) && !empty ( $row['last_name'] ) ) {
                    echo $row['first_name'] . ' ' . $row['last_name'] . '<br />';
                }
                if ( !empty ( $row['city'] ) && !empty ( $row['state'] ) ) {
                    echo $row['city'] . ', ' . $row['state'] . '<br />';
                }
                echo '</td>
                    <td>';
                if ( !empty ( $row['picture'] ) ) {
                    echo '<img src="' . MM_UPLOADPATH . $row['picture'] . '" alt="Foto de Perfil" /><br />';
                }
                echo '</td>
                    </tr>
                </table>';

                // Exibir os tópicos mismatched em uma tabela com quatro colunas
                echo '<h4>Você é mismatched nos seguintes ' . count ( $mismatch_topics ) . ' tópicos:</h4>
                    <table>
                        <tr>';
                $i = 0;
                foreach ( $mismatch_topics as $topic ) {
                    echo '<td>' . $topic . '</td>';
                    if ( ++$i > 3 ) {
                        echo '</tr>
                            <tr>';
                        $i = 0;
                    }
                }
                echo '</tr>
                    </table>';

                // Calcular o total de categorias mismatched
                $category_totals = array ( array ( $mismatch_categories[0], 0 ) );
                foreach ( $mismatch_categories as $category ) {
                    if ( $category_totals[count ( $category_totals ) - 1][0] != $category ) {
                        array_push ( $category_totals, array ( $category, 1 ) );
                    } else {
                        $category_totals[count ( $category_totals ) - 1][1]++;
                    }
                }

                // Gera e exibe a barra de imagem gráfica da categoria mismatched
                echo '<h4>Divisão de categoria mismatched:</h4>';
                draw_bar_graph (
                    480, 
                    240, 
                    $category_totals, 
                    5, 
                    MM_UPLOADPATH . $_SESSION['user_id'] . '-mymismatchgraph.png'
                );
                echo '<img src="' . MM_UPLOADPATH . $_SESSION['user_id'] . '-mymismatchgraph.png" 
                        alt="Gráfico de categoria do Mismatch" />
                    <br />';

                // Exibe um link para o perfil do usuário mismatch
                echo '<h4>
                        Visualizar <a href=viewprofile.php?user_id=' . $mismatch_user_id . '>Perfil de ' .
                        $row['first_name'] . '</a>.
                    </h4>';
            } // Fim da verificação para uma simples linha dos resultados do usuário mismatch
        } // Fim da verificação para um usuário mismatch
    } // Fim da verificação para qualquer resultados de resposta do questionário
    else {
        echo '<p>
                Você deve primeiro <a href="questionnaire.php">responder o questionário</a> antes de você poder
                ser mismatched.
            </p>';
    }

    mysqli_close ( $dbc );

    // Insere o rodapé da página
    require_once ( 'include/footer.php' );
