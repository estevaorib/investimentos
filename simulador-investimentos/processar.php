<?php 
require_once 'classes/autoloader.class.php';

$nome = isset($_GET['nome']) ? $_GET['nome'] : null;
$valorInicial = isset($_GET['valorInicial']) ? floatval($_GET['valorInicial']) : 1;
$aporteMensal = isset($_GET['aporteMensal']) ? floatval($_GET['aporteMensal']) : 1;
$rendimentoInicial = isset($_GET['rendimento']) ? floatval($_GET['rendimento']) : 0.1;
$periodo = isset($_GET['periodo']) ? intval($_GET['periodo']) : 1;

R::setup( 'mysql:host=localhost;dbname=fintech', 'root', '' );
R::testConnection();

$p = R::dispense('investimentos');
$p->nome = $nome;
$p->aporte_inicial = $valorInicial;
$p->valor_mensal = $aporteMensal;
$p->rendimento = $rendimentoInicial;
$p->periodo = $periodo;

$id = R::store($p);

$dados = R::load('investimentos', $id);

R::close();

if ($_SERVER["REQUEST_METHOD"] == "GET"){
    function calcularRendimento($valor_inicial, $aporte_mensal, $taxaRendimento)
    {
        $rendimentos = ($valor_inicial + $aporte_mensal) * ($taxaRendimento / 100);
        $total = $valor_inicial + $aporte_mensal + $rendimentos;
        return array($rendimentos, $total);
    }

    $resultados = array();
    $valorAtual = $valorInicial;

    for ($mes = 1; $mes <= $periodo; $mes++) {
        if ($mes == 1) {
            $aporte = 0; //No primeiro mês não há aporte mensal
        } 
        else {
            $aporte = $aporteMensal;
        }

        list($rendimento, $total) = calcularRendimento($valorAtual, $aporte, $rendimentoInicial);

        $resultados[] = array(
            'mes' => $mes,
            'valor_inicial' => $valorAtual,
            'valorMensal' => $aporte,
            'rendimento' => $rendimento,
            'total' => $total
        );

        $valorAtual = $total;
    }

    if($valorInicial >= 1 && $periodo >= 1 && $rendimentoInicial >= 0.1 && $aporteMensal >= 1)
    {
        echo "<h2>Resultados da Simulação</h2> 
        <table>
        <tr>
            <th>Mês</th>
            <th>Valor Inicial</th>
            <th>Aporte Mensal</th>
            <th>Rendimento</th>
            <th>Total</th>
        </tr>";
        
    }
    else{
        echo"";
    }
}

if(isset($_GET["simular"])){
    foreach ($resultados as $resultado){
        echo "<tr>";
        echo "<td>" . $resultado['mes'] . "</td>";
        echo "<td>" . number_format($resultado['valor_inicial'], 2, ',', '.') . "</td>";
        echo "<td>" . number_format($resultado['valorMensal'], 2, ',', '.') . "</td>";
        echo "<td>" . number_format($resultado['rendimento'], 2, ',', '.') . "</td>";
        echo "<td>" . number_format($resultado['total'], 2, ',', '.') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>