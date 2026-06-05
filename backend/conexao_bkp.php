<?php
    $host = 'projetoescolar-ailabs.l.aivencloud.com';
    $user = 'avnadmin';
    $pass = 'AVNS_dEP78CHvSDyyEWUPt-l';
    $dbname = 'projeto_escolar';

    $portas = [3306, 16947];
    $conn = null;

    // Desativa temporariamente os relatórios de erro do mysqli para não sujar a tela
    mysqli_report(MYSQLI_REPORT_OFF);

    foreach ($portas as $porta) {
        // O '@' antes do 'new' ajuda a suprimir o alerta caso a porta atual esteja fechada
        $conn = @new mysqli($host, $user, $pass, $dbname, $porta);

        // Se conectou com sucesso (connect_error for nulo), nós paramos o loop
        if ($conn->connect_error === null) {
            // Você pode descomentar a linha abaixo para testar se funcionou:
            // echo "Conectado com sucesso na porta: " . $porta;
            break; 
        }
    }

    // Reativa os relatórios de erro para te ajudar a debugar o resto do seu código
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // Verifica se, mesmo após testar todas as portas, a conexão falhou
    if ($conn->connect_error) {
        die("Erro crítico: Não foi possível conectar ao banco de dados em nenhuma das portas.");
    }
    $conn->set_charset( "utf8mb4" );

?>