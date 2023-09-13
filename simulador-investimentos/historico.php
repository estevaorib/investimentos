<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico</title>
</head>
<body>
<header>
        <h2>Desenvolvimento Web</h2>
    </header>
    <main>
        <h1>Histórico</h1>
        <form action="" method="get">
            <fieldset>
                <legend>Simulação a se recuperar</legend>
                <label for="id">ID da simulação</label>
                <input type="number" name="id" id="id" required><br>

                <input type="submit" name="recuperar" value="Recuperar">
            </fieldset>
        </form> 
        
        <?php 
        require_once "classes/autoloader.class.php";

        R::setup( 'mysql:host=localhost;dbname=fintech', 'root', '' );

        $ids = isset($_GET['id']) ? $_GET['id'] : 0;

        $dados = R::load('investimentos', $ids);

        if($dados->id){
            echo "<fieldset>
            <legend>Dados</legend>
            <p>Cliente: $dados->nome</p>
            <p>Aporte Inicial: $dados->aporte_inicial</p>
            <p>Aporte Mensal: $dados->valor_mensal</p>
            <p>Rendimento: $dados->rendimento</p>
            <p>Período: $dados->periodo</p>
        </fieldset>";
        }
        else{
            echo "Simulação para o ID {$ids} não encontrado!";
        }
        ?>
        
        <?php
            function calcularRendimento($valor_inicial, $aporte_mensal, $taxaRendimento)
            {
                $rendimentos = ($valor_inicial + $aporte_mensal) * ($taxaRendimento / 100);
                $total = $valor_inicial + $aporte_mensal + $rendimentos;
                return array($rendimentos, $total);
            }
        
            $resultados = array();
            $valorAtual = $dados->aporte_inicial;
        
            for ($mes = 1; $mes <= $dados->periodo; $mes++) {
                if ($mes == 1) {
                    $aporte = 0; //No primeiro mês não há aporte mensal
                } 
                else {
                    $aporte = $dados->valor_mensal;
                }
        
                list($rendimento, $total) = calcularRendimento($dados->aporte_inicial, $aporte, $dados->rendimento);
        
                $resultados[] = array(
                    'mes' => $mes,
                    'valor_inicial' => $valorAtual,
                    'valorMensal' => $aporte,
                    'rendimento' => $rendimento,
                    'total' => $total
                );
        
                $valorAtual = $total;
            }
        
            if($dados->aporte_inicial >= 1 && $dados->periodo >= 1 && $dados->rendimento >= 0.1 && $dados->valor_mensal >= 1)
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
        
        if(isset($_GET["recuperar"])){
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

        <p><a href="index.html">Página Inicial</a></p>
    </main>
    <footer>
        <p>&copy;2023 - Estevão Ribeiro</p>
    </footer>
</body>
</html>