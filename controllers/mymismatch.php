<?php

    // Inserir o cabeçalho da página
    $page_title = 'Meu Mismatch';
    require_once './views/include/header.phtml';

    // Tenha certeza de que o usuário está logado antes de prosseguir.
    require_once './views/include/authenticate.phtml';

    // Mostrar o menu de navegação
    require_once './views/include/navmenu.phtml';

    $response = new Response();
    $response->user_id = $_SESSION['user_id'];

    // Somente procura por um mismatch se o usuário armazenou respostas do questionário
    if ($response->getResponseById()->rowCount() != 0) {
        // Primeiro obtém as respostas do usuário da tabela de resposta
        // (JOIN para pegar os nomes dos tópicos e das categorias)
        $user_responses = $response->getResponse();

        // Inicializa os resultados de busca do mismatch
        $mismatch_score = 0;
        $mismatch_user_id = -1;
        $mismatch_topics = array();
        $mismatch_categories = array();

        // Percorre a tabela do usuário comparando as respostas das outras pessoas
        // com as respostas do usuário
        $user = new User();
        $user->user_id = $_SESSION['user_id'];
        $users = $user->getOthers();

        foreach ($users as $user) {
            // Obtém os dados de resposta para o usuário (um potencial mismatch)
            $response = new Response();
            $response->user_id = $user->user_id;
            $mismatch_responses = $response->getResponseById()->fetchAll(PDO::FETCH_CLASS, 'Response');

            // Comparar cada resposta e calcular um total de mismatch
            $score = 0;
            $topics = array();
            $categories = array();

            for ($i = 0; $i < count($user_responses); $i++) {
                if ($user_responses[$i]->response + $mismatch_responses[$i]->response == 3) {
                    $score++;
                    array_push($topics, $user_responses[$i]->topic_name);
                    array_push($categories, $user_responses[$i]->category_name);
                }
            }

            // Verificar para ver se esta pessoa é melhor do que o melhor mismatch até agora
            if ($score > $mismatch_score) {
                // Encontramos um mismatch melhor, portanto, atualizar os resultados de busca do mismatch
                $mismatch_score = $score;
                $mismatch_user_id = $user->user_id;
                $mismatch_topics = array_slice($topics, 0);
                $mismatch_categories = array_slice($categories, 0);
            }
        }

        // Ter certeza de que um mismatch foi encontrado
        if ($mismatch_user_id != -1) {
            $user = new User();
            $user->user_id = $mismatch_user_id;

            if ($user = $user->getUser()) {
                // Os dados do usuário para o mismatch foi encontrado, então, exibir os dados do usuário
                $view = true;

                // Calcular o total de categorias mismatched
                $category_totals = array(
                    array($mismatch_categories[0], 0)
                );

                foreach ($mismatch_categories as $category) {
                    if ($category_totals[count($category_totals) - 1][0] != $category) {
                        array_push($category_totals, array($category, 1));
                    } else {
                        $category_totals[count($category_totals) - 1][1]++;
                    }
                }

                draw_bar_graph(
                    480, 
                    240, 
                    $category_totals, 
                    5, 
                    MM_UPLOADPATH . $_SESSION['user_id'] . '-mymismatchgraph.png'
                );

                
                
            } // Fim da verificação para uma simples linha dos resultados do usuário mismatch
        } // Fim da verificação para um usuário mismatch
    } // Fim da verificação para qualquer resultados de resposta do questionário
    else {
        $view = false;
    }

    require_once './views/template-mymismatch.phtml';

    // Insere o rodapé da página
    require_once './views/include/footer.phtml';
