<?php

    // Inserir o cabeçalho da página
    $page_title = 'Questionário';
    require_once './views/include/header.phtml';

    // Ter certeza de que o usuário está logado antes de prosseguir.
    require_once './views/include/authenticate.phtml';

    // mostrar o menu de navegação
    require_once './views/include/navmenu.phtml';

    $response = new Response();
    $response->user_id = $_SESSION['user_id'];

    $success = false;

    // Se este usuário nunca respondeu o questionário, inserir respostas vazias no banco de dados
    if ($response->getResponses()->rowCount() == 0) {
        // Primeiro obtém a lista de IDs dos tópicos da tabela tópico
        $topic = new Topic();
        $data = $topic->getAll();

        $topicIDs = array();

        foreach ($data as $topic) {
            array_push($topicIDs, $topic->topic_id);
        }

        // Inserir as linhas de respostas vazias na tabela resposta, um por tópico
        foreach ($topicIDs as $topic_id) {
            $response->topic_id = $topic_id;
            $response->insert();
        }
    }

    // Se o formulário do questionário foi submetido, escreva as respostas do formulário no banco de dados
    if (has_post($_POST)) {
        // Escreva as linhas de resposta do questionário na tabela resposta
        foreach ($_POST as $response_id => $response_value) {
            $response->response = $response_value;
            $response->response_id = $response_id;
            $response->update();
        }

        $success = true;
    }

    // Obtém os dados de resposta do banco de dados para gerar o formulário
    $responses = $response->getResponse();

    $category = $responses[0]->category_name;
    // Gera o formulário do questionário percorrendo o array de resposta
    require_once './views/template-questionnaire.phtml';
    
    // Inserir o rodapé da página
    require_once './views/include/footer.phtml';
