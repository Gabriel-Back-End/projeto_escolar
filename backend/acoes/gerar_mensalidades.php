<?php
require_once '../conexao.php';

$mes = date('m');
$ano = date('Y');

// 1. Busca todos os alunos ativos
$alunos = $conn->query("SELECT id, valor_mensalidade FROM alunos WHERE status = 'Ativo'");

$gerados = 0;
$ja_existiam = 0;

while ($aluno = $alunos->fetch_assoc()) {
    $id_aluno = $aluno['id'];
    $valor = $aluno['valor_mensalidade'];

    // 2. Verifica se já existe mensalidade para este aluno neste mês (evita duplicar)
    $check = $conn->query("SELECT id FROM mensalidades WHERE id_aluno = $id_aluno AND mes = $mes AND ano = $ano");

    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO mensalidades (id_aluno, mes, ano, valor_devido, status) 
                      VALUES ($id_aluno, $mes, $ano, $valor, 'Pendente')");
        $gerados++;
    } else {
        $ja_existiam++;
    }
}

echo "Sucesso! $gerados novas mensalidades geradas. $ja_existiam já estavam no sistema.";