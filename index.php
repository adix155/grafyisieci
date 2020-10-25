<html>
<head>
    <meta charset="UTF-8">
    <title>Grafy i sieci - projekt</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <script>

        function Wypelnij(i, j) {
            document.getElementById(j + "_" + i).value = document.getElementById(i + "_" + j).value;
        }

    </script>
    <style>
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button{
            -webkit-appearance: none;
            margin:0;
        }

        input[type=number]{
            -moz-appearance: : textfield;
        }
    </style>
</head>
<body>
<div style="text-align: center;"><h2>Projekt z przedmiotu "Grafy i Sieci"</h2></div>
<div style="text-align: center;"><h3>Implementacja algorytmu optymalnego usytuowania straży pożarnej w miasteczku, minimalizującego odległość od najdalszego zabudowania</h3></div>
<center><h3>Autorzy: Michał Szustowicz i Adrian Szczerba</h3></center>
<br>
<form id="tabela1" action='index.php' method='post'>
    <table align="center">
        <tr>
            <td>Podaj ilość wierzchołków</td>
            <td><input type="number" name="ilosc"></td>
        </tr>
        <tr>
            <td width="40" height="35" colspan="2" align="center"><input class="btn btn-sm btn-primary" type="submit" name="new_matrix"
                                                  value="Zatwierdź"></td>
        </tr>
    </table>
</form>
</body>
</html>

<?php

require_once("kolejkapriorytetowa.php");

if (isset($_POST['new_matrix'])) {
    if ($_POST['ilosc'] < 2) {
        $errcode = 4;
        Error1($errcode);
        exit();
    }
    else if ($_POST['ilosc'] > 50)
    {
        $errcode = 5;
        Error1($errcode);
        exit();
    }
    echo "<script type=\"text/javascript\">document.getElementById('tabela1').style.display = 'none';</script>";
    $n = $_POST['ilosc'];
    echo '<div style="text-align: center;"><h4>Uzupełnij macierz sąsiedztwa</h4></div><br>';
    echo '<div style="text-align: center;">Ilość wierzchołków: ' . $n . '</div><br>';
    echo '<form action="index.php" method="post"><table align="center"><tr><td width="35" height="35"><b><center>W</center></b></td>';
    for ($i = 1; $i <= $n; $i++) {
        echo '<td width="35" height="35 "><center><b>' . $i . '</b></center></td>';
    }
    echo '</tr>';
    for ($i = 1; $i <= $n; $i++) {
        echo '<tr><td width="35" height="35"><center><b>' . $i . '</b></center></td>';
        for ($j = 1; $j <= $n; $j++) {
            if ($i == $j) {
                echo '<td width="35" height="35"><center><input style="width: 30px; height: 30px; text-align: center;" value="0" readonly="readonly" type="text" name="' . $i . '_' . $j . '"></center></td>';
            } else {
                echo '<td width="35" height="35"><center><input type="number" style=" width: 30px; height: 30px; text-align: center;" onchange="Wypelnij(' . $i . ',' . $j . ')" id="' . $i . '_' . $j . '" name="' . $i . '_' . $j . '"></center></td>';
            }

        }
        echo '</tr>';
    }
    echo '<tr><td colspan="' . $n . '"><input type="hidden" name="n" value="' . $n . '"></td></tr>';
    echo '<tr><td height="40" align="center" colspan="' . ($n + 1) . '"><input class="btn btn-success btn-sm" type="submit" name="dijkstra" value="Wykonaj algorytm"></td></tr>';
    echo '<tr><td height="40" align="center" colspan="' . ($n + 1) . '"><input class="btn btn-success btn-sm" type="submit" onclick="PokazPonownie()" value="Zmień ilość wierzchołków"></td></tr>';
    echo '</table></form>';
}

if (isset($_POST['dijkstra']))
{
    echo "<script type=\"text/javascript\">document.getElementById('tabela1').style.display = 'none';</script>";
    $n = $_POST['n'];
    for ($i = 1; $i <= $n; $i++) {
        for ($j = 1; $j <= $n; $j++) {
            if ($_POST[$i . '_' . $j] != "" && $_POST[$i . '_' . $j] >=0)
            {
                $graf[$i][$j] = (int)$_POST[$i . '_' . $j];
            }
            elseif ($_POST[$i . '_' . $j] <0)
            {
                $errcode = 3;
                Error2($n, $errcode);
                exit();
            }
            else {
                $errcode = 1;
                Error2($n, $errcode);
                exit();
            }
        }
    }

    if (!DFScheck($graf,$n))
    {
        $errcode = 2;
        Error2($n, $errcode);
        exit();
    }

    echo'<center><h4><b>Macierz sąsiedztwa</b></h4></center>';
    echo '<table border="2" align="center"><tr><td width="35" height="35"><b><center>W</center></b></td>';
    for ($i = 1; $i <= $n; $i++) {
        echo '<td width="35" height="35 "><center><b>' . $i . '</b></center></td>';
    }
    echo '</tr>';
    for ($i = 1; $i <= $n; $i++) {
        echo '<tr><td width="35" height="35"><center><b>' . $i . '</b></center></td>';
        for ($j = 1; $j <= $n; $j++) {
            echo '<td><center>'.$graf[$i][$j].'</center></td>';
        }
        echo '</tr>';
    }
    echo '</table><br>';

    $stop = 0;
    $droga = PHP_INT_MAX;

    for ($i = 1; $i <= $n; $i++) {
        $dist=Dijkstra($graf, $i, $n);
        //echo $dist[0]." do ".$dist[1]." z ".$i."<br>";
        if($dist[0]<$droga&&$dist[0]!=0)
        {
            unset($start);
            unset($stop);
            $start[0] = $i;
            $stop[0] = $dist[1];
            $droga = $dist[0];
        }
        elseif ($dist[0]==$droga&&$dist[0]!=0)
        {
            $start[sizeof($start)] = $i;
            $stop[sizeof($stop)] = $dist[1];
        }

    }

    echo '<center>Najbardziej optymalne miejsce do usytuowania straży pożarnej to ';
    if (sizeof($start) == 1)
    {
        echo 'wierzchołek nr '.$start[0].".";
    }
    else
    {
        echo 'wierzchołki nr ';
        for($i=0;$i<sizeof($start)-1;$i++)
        {
            echo $start[$i].", ";
        }
        echo $start[sizeof($start)-1].".";
    }

    echo "<br><br>Odległość do najdalszego zabudowania wynosi ".$droga.".<br><br>";
    if (sizeof($start) == 1)
    {
        if (sizeof($stop[0]) == 1)
        {
            echo "Najdalsze zabudowanie znajduje się w wierzchołku nr ".$stop[0][0].".<br>";
        }
        else
        {
            echo "Najdalsze zabudowania znajdują się w wierzchołkach nr ";
            for($i=0;$i<sizeof($stop[0])-1;$i++)
            {
                echo $stop[0][$i].", ";
            }
            echo $stop[0][sizeof($stop[0])-1].".<br>";
        }
    }
    else
    {
        for ($j=0;$j<sizeof($start);$j++)
        {
            echo "- dla wierzchołka nr ".$start[$j];
            if (sizeof($stop[$j]) == 1)
            {
                echo " najdalsze zabudowanie znajduje się w wierzchołku nr ".$stop[$j][0].".<br>";
            }
            else
            {
                echo " najdalsze zabudowania znajdują się w wierzchołkach nr ";
                for($i=0;$i<sizeof($stop[$j])-1;$i++)
                {
                    echo $stop[$j][$i].", ";
                }
                echo $stop[$j][sizeof($stop[$j])-1].".<br>";
            }
        }
    }


    echo"</center>";
    echo '<br><form><center><input class="btn btn-success btn-sm" type="submit" onclick="PokazPonownie()" value="Zakończ i przejdź do strony głównej"></center></form>';
}

function DFScheck($graf, $n)
{
    for($i=1;$i<=$n;$i++)
    {
        $visited[$i]=false;
    }
    $visited=explore($graf,1,$visited,$n);
    for($i=1;$i<=$n;$i++)
    {

        if(!$visited[$i]){
            return false;
        }
    }
    return true;
}

function explore($graf,$v,$visited,$n)
{
    $visited[$v] = true;
    for($i=1;$i<=$n;$i++){
        if($graf[$v][$i]!=0&&!$visited[$i]){
            $visited=explore($graf,$i,$visited,$n);
        }
    }
    return $visited;
}

function PokazPonownie()
{
    echo "<script type=\"text/javascript\">document.getElementById('tabela1').style.display = 'yes';</script>";
}

function Error1($code)
{
    if ($code == 4)
        {
            echo '<div style="text-align: center; color: red;">Błędna liczba wierzchołków - musi być większa od jedynki</div>';
        }
    elseif($code == 5)
        {
            echo '<div style="text-align: center; color: red;">Maksymalna liczba wierzchołków to 50</div>';
        }

}

function Error2($n, $code)
{
    if ($code == 1)
    {
        echo '<div style="text-align: center; color: red;">Błędna macierz sąsiedztwa - nie wszystkie wartości zostały uzupełnione</div>';
    }
    elseif ($code == 2)
    {
        echo '<div style="text-align: center; color: red;">Błędna macierz sąsiedztwa - przedstawiony graf nie jest spójny</div>';
    }
    elseif ($code == 3)
    {
        echo '<div style="text-align: center; color: red;">Błędna macierz sąsiedztwa - przynajmniej jeden element macierzy sąsiedztwa jest ujemny</div>';
    }
    echo '<form id="tabela2" action=\'index.php\' method=\'post\'><tr><td><input type="hidden" name="ilosc" value="' . $n . '"></td><td><div style="text-align: center;"><input class="btn btn-sm btn-primary" type="submit" name="new_matrix" value="Wprowadź ponownie"></td></tr></form></div>';
}

function Dijkstra($graf, $s, $n)
{
    for ($i = 1; $i <= $n; $i++) {
        $dist[$i] = PHP_INT_MAX;
    }
    $dist[$s] = 0;
    $priorQueue = new PriorityQueue("compareWeights");

    for ($i = 1; $i <= $n; $i++)
    {
        $priorQueue->add($dist[$i], $i);
    }

    while ($priorQueue->size() > 0) {

        $u = $priorQueue->remove();
        for ($i = 1; $i <= $n; $i++) {
            if (($dist[$i] > $u[0] + $graf[$u[1]][$i])&&$graf[$u[1]][$i]!=0) {
                $dist[$i] = $u[0] + $graf[$u[1]][$i];
                $priorQueue->modifyElement($i, $dist[$i]);
            }
        }
    }

    $maxDist=-1;

    for ($i = 1; $i <= $n; $i++) {
        if($dist[$i]>$maxDist){
            unset($maxDistVertex);
            $maxDist=$dist[$i];
            $maxDistVertex[0]=$i;
        }
        elseif ($dist[$i]==$maxDist)
        {
            $maxDistVertex[sizeof($maxDistVertex)]=$i;
        }
    }

    return array(0=>$maxDist,$maxDistVertex);
}

function compareWeights($a, $b)
{
    return $a->data - $b->data;
}

?>
