<?php
    if (isset($_POST['output'])) {
	function BigRandomNumber($min, $max, $limiter = 20) {
	  $range = gmp_sub($max, $min);
	  $random = gmp_random();
	  $random = gmp_mod($random, $range);
	  $random = gmp_add($min, $random);
	  return $random;
	}

	if (strlen($_POST['from']) != 10 && $_POST['from'] < $_POST['to']) {
	    echo "Некорректно задан диапазон"; exit;
	}
	if ($_POST['count'] < 1) {
	    echo "Некорректно задано количество"; exit;
	}


	$result = "";
	$digits = [];
	while ($_POST['count'] > 0) {
	    $d = BigRandomNumber($_POST['from'], $_POST['to']);
	    if (isset($digits[$d]))
		continue;
	    $result .= ";".$d.";<br/>";
	    $_POST['count']--;
	}

	echo $result;
    }
    else {
?>

<form method="post">
    Диапазон от <input type="text" name="from" placeholder="9260000000" /> до <input type="text" name="to" placeholder="9269999999" /><br/>
    Количество номеров на выходе <input type="text" name="count" placeholder="2000" />
    <input type="submit" name="output" value="Сформировать"/>
</form>

<?php
    }
?>