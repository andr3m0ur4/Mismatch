<?php  
    // Personalizar função para desenhar uma barra gráfica, dado um conjunto de dados, valor máximo, e nome do
    // arquivo da imagem
    function draw_bar_graph ( $width, $height, $data, $max_value, $filename ) {
        // Criar a imagem gráfica vazia
        $img = imagecreatetruecolor ( $width, $height );

        // Definir um fundo branco com texto preto e gráficos em cinza
        $bg_color = imagecolorallocate ( $img, 255, 255, 255 );       // branco
        $text_color = imagecolorallocate ( $img, 255, 255, 255 );     // branco
        $bar_color = imagecolorallocate ( $img, 0, 0, 0 );            // preto
        $border_color = imagecolorallocate ( $img, 192, 192, 192 );   // cinza claro

        // Preencher o fundo
        imagefilledrectangle ( $img, 0, 0, $width, $height, $bg_color );
        // Desenhar as barras
        $bar_width = $width / ( ( count ( $data ) * 2 ) + 1 );
        for ( $i = 0; $i < count ( $data ); $i++ ) {
            imagefilledrectangle ( 
                $img, 
                ( $i * $bar_width * 2 ) + $bar_width, 
                $height,
                ( $i * $bar_width * 2 ) + ( $bar_width * 2 ),
                $height - ( ( $height / $max_value ) * $data[$i][1] ), 
                $bar_color 
            );
            imagestringup ( 
                $img, 
                5, 
                ( $i * $bar_width * 2 ) + ( $bar_width ), 
                $height - 5, 
                utf8_decode ( $data[$i][0] ), 
                $text_color
            );
        }

        // Desenhar um retângulo ao redor da coisa toda
        imagerectangle ( $img, 0, 0, $width - 1, $height - 1, $border_color );

        // Desenhar o intervalo do lado esquerdo do gráfico
        for ( $i = 1; $i <= $max_value; $i++ ) {
            imagestring ( $img, 5, 0, $height - ( $i * ( $height / $max_value ) ), $i, $bar_color );
        }

        // Escrever a imagem gráfica para um arquivo
        imagepng ( $img, $filename, 5 );
        imagedestroy ( $img );
    } // Fim da função draw_bar_graph()
