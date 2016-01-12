<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<?php
	date_default_timezone_set('Europe/Rome');
	require_once('library/phpGlue.php');
	require_once('library/utils.php');

    define('PAY_DAY', 4);
    
	function cmpCategories($a, $b) {
		if ($a['average'] > $b['average']) {
			return -1;
		} elseif ($a['average'] < $b['average']) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * Restituisce una sringa di tipo numerico interpretando i vari formati
	 * Se il valore non è valido restituisce null
	 *
	 * @param str $name nome della variabile di post
	 * @return mixed stringa numerica se il valore è valido altrimenti null
	 */
	function getPostAmount($name) {
		if (isset($_POST[$name])) {
			$value = trim($_POST[$name]);
			if (preg_match('/^[0-9]+(,[0-9]+)?$/', $value)) { //3575,5
				return str_replace(',', '.', $value);
			} elseif (preg_match('/^[0-9]{1,3}(\.[0-9]{3})*(,[0-9]*)?$/', $value)) {
				return str_replace(',', '.', str_replace('.', '', $value));
			} elseif (preg_match('/^[0-9]{1,3}(,[0-9]{3})*(\.[0-9]*)?$/', $value)) {
				return str_replace(',', '', $value);
			} elseif (preg_match('/^[0-9]+(\.[0-9]+)?$/', $value)) {
				return $value;
			}
		}
		return null;
	}

	function getBudgetTable($db) {

		$tr = new TableRow();
		$tr->add(new TableHeader(new Text('Dettaglio')));
		$tr->add(new TableHeader(new Text('Segno')));
		$th = new TableHeader(new Text('Importo'));
		$atrs = array(
			'alt' => 'modifica',
			'id' => 'imgEdit',
			'onClick' => "editVariables()",
			'style' => 'cursor: pointer;vertical-align:baseline;',
			'src' => 'images/edit.png',
		);
		$th->add(new Img($atrs));

		$atrs = array(
			'alt' => 'salva',
			'id' => 'imgSave',
			'onClick' => "document.getElementById('accantonaForm').submit();",
			'style' => 'cursor: pointer; display: none;vertical-align:baseline;',
			'src' => 'images/save.png'
		);
		$th->add(new Img($atrs));
		$tr->add($th);
		$table = new Table($tr, array('class' => 'budget'));

		//reperisco i valori delle variabili
		$sommatoreVariabili = 0;
		$rs = $db->query('SELECT * FROM variabili');
		while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
			$tr = new TableRow();
			$tr->add(new TableData(new Text(ucfirst(str_replace('_', ' ', $row['nome'])))));

			$atrs = $row['segno'] > 0 ? array('alt' => '+', 'src' => 'images/list-add.png') :
				array('alt' => '-', 'src' => 'images/list-remove.png');
			$atrs['align'] = 'right';
			$tr->add(new TableData(new Img($atrs)));

			$tr->add(new TableData(new Text(number_format($row['valore'], 2)), array('align' => 'right', 'class' => 'variabile', 'id' => $row['nome'])));

			$table->add($tr);

			$sommatoreVariabili += $row['segno'] * $row['valore'];
		}

		//totali
		$a = $db->query('SELECT sum(importo) AS somma FROM spese')->fetchArray(SQLITE3_ASSOC);
		$totaleSpese = $a['somma'];
		$a = $db->query('SELECT sum(importo) AS somma FROM accantonati')->fetchArray(SQLITE3_ASSOC);
		$totaleAccantonati = $a['somma'];

		//accantonati
		$tr = new TableRow(new TableData(new Text('Accantonati')));
		$tr->add(new TableData(new Img(array('alt' => '-', 'src' => 'images/list-remove.png')), array('align' => 'right')));
		$accantonati = round($totaleAccantonati - $totaleSpese, 2);
		$tr->add(new TableData(new Text($accantonati), array('align' => 'right')));
		$table->add($tr);

		//budget mensile residuo
		$tr = new TableRow(new TableData(new Text('Budget mensile residuo'), array('style' => 'border-top:1px solid;')));
		$tr->add(new TableData(new Text('&nbsp;'), array('style' => 'border-top:1px solid;')));
		$budget = $sommatoreVariabili - $accantonati;
		$tr->add(new TableData(new Text(number_format($budget, 2, '.', '')), array('align' => 'right', 'style' => 'border-top:1px solid;')));
		$table->add($tr);

		//giorni residui
		$tr = new TableRow(new TableData(new Text('Giorni residui')));
		$tr->add(new TableData(new Text('&nbsp;')));
		$currentDay = date('j');
		$giorni = $currentDay < PAY_DAY ? PAY_DAY - $currentDay : date('t') - $currentDay + PAY_DAY;
		$tr->add(new TableData(new Text($giorni), array('align' => 'right')));
		$table->add($tr);

		//budget giornaliero di oggi
		$tr = new TableRow(new TableData(new Text('Budget giornaliero medio ad oggi')));
		$tr->add(new TableData(new Text('&nbsp;')));
		$tr->add(new TableData(new Text(number_format($budget / $giorni, 2)), array('align' => 'right')));
		$table->add($tr);

		//budget giornaliero di domani
		$trTommorrow = new TableRow(new TableData(new Text('Budget giornaliero medio a domani')));
		$trTommorrow->add(new TableData(new Text('&nbsp;')));
		$str = $giorni > 1 ? number_format(round($budget / ($giorni - 1), 2), 2) : 'n.d.';
		$trTommorrow->add(new TableData($text = new Text($str), array('align' => 'right')));
		$table->add($trTommorrow);

		return $table;
	}

	function getCategoriesTable($db) {
		//preparo la tabella con le categorie
		$currentDay = date('j');
		$trh = new TableRow();
		$trh->add(new TableHeader(new Text('Categoria')));
		$trh->add(new TableHeader(new Text('Spesa media')));
		$table = new Table($trh);

		//calcolo le spese medie per ogni categoria
		$sqlSum = <<< eoc
SELECT sum(importo) AS somma, min(valuta) AS prima_valuta, id_categoria FROM spese WHERE date('now', '-30 months') <= valuta
GROUP BY id_categoria
eoc;
		$rs = $db->query("SELECT somma, prima_valuta, descrizione FROM categorie, ($sqlSum) t WHERE id_categoria=id");
		$categories = array();
		$currentDate = mktime(0, 0, 0, date('n'), $currentDay, date('Y'));
		$sommaMedie = 0;

		while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
			$monthDiff = ($currentDate - mktime(0, 0, 0, substr($row['prima_valuta'], 5, 2), substr($row['prima_valuta'], 8), substr($row['prima_valuta'], 0, 4))) / 2628000;
			if ($monthDiff) {
				$row['average'] = $row['somma'] / $monthDiff;
				$categories[$row['descrizione']] = $row;
				$sommaMedie += $row['average'];
			}
		}
		uasort($categories, 'cmpCategories');
		foreach ($categories as $category => $info) {
			$tr = new TableRow(new TableData(new Text($category)));
			$tr->add(new TableData(new Text(number_format($info['average'], 2)), array('align' => 'right')));
			$table->add($tr);
		}
		$tr = new TableRow(new TableData(new Text('Somma medie')));
		$tr->add(new TableData(new Text(number_format(round($sommaMedie, 2), 2)), array('align' => 'right')));
		$table->add($tr);

		return $table;
	}//getCategoriesTable

	function getSpendTable($db) {

		if (isset ($_POST['valuta'])) {
			$valutaFilter = $_POST['valuta'];
		} else {
			$valutaFilter = '3 month';
		}

		if (isset ($_POST['categoria'])) {
			$categoriaFilter = (int) $_POST['categoria'];
		} else {
			$categoriaFilter = '';
		}

		//dettaglio delle spese
		$tr = new TableRow();
		$tr->add(new TableHeader(new Text('Data')));
		$tr->add(new TableHeader(new Text('Categoria')));
		$tr->add(new TableHeader(new Text('Importo')));
		$tr->add(new TableHeader(new Text('Descrizione')));
		$tSpese = new Table($tr, array('style' => 'margin-top: 10px;'));

		//creo la riga con i filtri
		$tr = new TableRow();
		$atrs = array('name' => 'valuta', 'onchange' => "document.getElementById('accantonaForm').submit()");
		$options = array('' => 'Tutte', '1 week' => 'Ultima settimana', '1 month' => 'Ultimo mese', '3 month' => 'Ultimi 3 mesi', '6 month' => 'Ultimi 6 mesi', '1 year' => 'Ultimo anno');
		$select = new Select(null, $atrs, $options, $valutaFilter);
		$tr->add(new TableData($select));

		$categories = array();
		$rs = $db->query('SELECT * FROM categorie ORDER BY descrizione');
		while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
			$categories[$row['id']] = $row['descrizione'];
		}
		$atrs = array('name' => 'categoria', 'onchange' => "document.getElementById('accantonaForm').submit()");
		$tr->add(new TableData(new Select(new Option('Tutte'), $atrs, $categories, $categoriaFilter)));

		$tr->add(new TableData(new Text('&nbsp;')));
		$tr->add(new TableData(new Text('&nbsp;')));
		$tSpese->add($tr);

		$conditions = '';
		if ($valutaFilter) {
			$conditions .= " AND valuta >= DATE('now', '-$valutaFilter')";
		}
		if ($categoriaFilter) {
			$conditions .= " AND id_categoria=$categoriaFilter";
		}
		$sql = 'SELECT strftime(\'%d/%m/%Y\', valuta) AS f_valuta, c.descrizione AS descrizione_categoria, importo, s.descrizione '.
			"FROM spese s, categorie c WHERE id_categoria=c.id $conditions ORDER BY valuta";
		$rs = $db->query($sql);
		while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
			$tr = new TableRow();

			$tr->add(new TableData(new Text($row['f_valuta'])));
			$tr->add(new TableData(new Text($row['descrizione_categoria'])));
			$tr->add(new TableData(new Text(number_format($row['importo'], 2)), array('align' => 'right')));
			$tr->add(new TableData(new Text($row['descrizione'])));

			$tSpese->add($tr);
		}

		//riga per l'aggiunta di un nuovo dettaglio
		$tr = new TableRow();

		//data valuta
		$atrs = array(
			'class' => 'data',
			'id' => 'add_valuta', 'name' => 'add_valuta', 'type' => 'text', 'value' => date('d/m/Y'));
		$td = new TableData(new Input($atrs));
		$atrs = array('type' => "text/javascript");
		$script = new Script("$(function() {\$('#add_valuta').datepicker();});", $atrs);
		$td->add($script);
		$tr->add($td);

		//categoria
		$sql = <<< eoc
SELECT COUNT(spese.id) AS count, categorie.id FROM spese, categorie WHERE categorie.id=id_categoria group BY categorie.descrizione
ORDER BY count DESC
eoc;
		$sel = $db->query($sql)->fetchArray(SQLITE3_ASSOC);
		$tr->add(new TableData(new Select(null, array('name' => 'add_categoria'), $categories, (int) $sel['id'])));

		//importo
		$input = new Input(array('name' => 'add_importo', 'type' => 'text', 'class' => 'importo'));
		$tr->add(new TableData($input, array('align' => 'right')));

		//descrizione
		$td = new TableData(new Input(array('class' => 'descrizione', 'name' => 'add_descrizione', 'type' => 'text')));
		$td->add(new Input(array('name' => 'add', 'src' => 'images/add.png', 'alt' => 'aggiungi', 'type' => 'image')));
		$tr->add($td);

		//aggiungo la riga per l'aggiunta del dettaglio alla tabella
		$tSpese->add($tr);

		return $tSpese;
	}

	function getStoredTable($db) {

		if (isset ($_POST['valuta'])) {
			$valutaFilter = $_POST['valuta'];
		} else {
			$valutaFilter = '6 month';
		}

		//dettaglio delle spese
		$tr = new TableRow();
		$tr->add(new TableHeader(new Text('Data')));
		$tr->add(new TableHeader(new Text('Importo')));
		$tr->add(new TableHeader(new Text('Descrizione')));
		$t = new Table($tr, array('style' => 'margin-top: 10px;'));

		//creo la riga con i filtri
		$tr = new TableRow();
		$atrs = array('name' => 'valuta', 'onchange' => "document.getElementById('accantonaForm').submit()");
		$options = array(
			'' => 'Tutte', '1 week' => 'Ultima settimana',
			'1 month' => 'Ultimo mese', '6 month' => 'Ultimi 6 mesi', '1 year' => 'Ultimo anno'
		);
		$select = new Select(null, $atrs, $options, $valutaFilter);
		$tr->add(new TableData($select));

		$tr->add(new TableData(new Text('&nbsp;')));
		$tr->add(new TableData(new Text('&nbsp;')));
		$t->add($tr);

		$conditions = $valutaFilter ? " DATE('now', '-$valutaFilter') <= valuta" : 1;
		$sql = <<< eoc
SELECT strftime('%d/%m/%Y', valuta) AS f_valuta, importo, descrizione FROM accantonati WHERE $conditions ORDER BY valuta
eoc;
		$rs = $db->query($sql);
		while ($row = $rs->fetchArray(SQLITE3_ASSOC)) {
			$tr = new TableRow();
			$tr->add(new TableData(new Text($row['f_valuta'])));
			$tr->add(new TableData(new Text(number_format($row['importo'], 2)), array('align' => 'right')));
			$tr->add(new TableData(new Text($row['descrizione'])));

			$t->add($tr);
		}

		//riga per l'aggiunta di un nuovo dettaglio
		$tr = new TableRow();

		//data valuta
		$atrs = array(
			'class' => 'data',
			'id' => 'stored_add_valuta', 'name' => 'add_valuta', 'type' => 'text', 'value' => date('d/m/Y')
		);
		$td = new TableData(new Input($atrs));
		$atrs = array('type' => "text/javascript");
		$script = new Script("$(function() {\$('#stored_add_valuta').datepicker();});", $atrs);
		$td->add($script);
		$tr->add($td);

		//categoria
		$sql = <<< eoc
SELECT COUNT(spese.id) AS count, categorie.id FROM spese, categorie WHERE categorie.id=id_categoria group BY categorie.descrizione
ORDER BY count DESC
eoc;
		$sel = $db->query($sql)->fetchArray(SQLITE3_ASSOC);

		//importo
		$input = new Input(array('name' => 'add_importo', 'type' => 'text', 'class' => 'importo'));
		$tr->add(new TableData($input, array('align' => 'right')));

		//descrizione
		$td = new TableData(new Input(array('class' => 'descrizione', 'name' => 'add_descrizione', 'type' => 'text')));
		$td->add(new Input(array('name' => 'add', 'src' => 'images/add.png', 'alt' => 'aggiungi', 'type' => 'image')));
		$tr->add($td);

		//aggiungo la riga per l'aggiunta del dettaglio alla tabella
		$t->add($tr);

		return $t;
	}//getStoredTable

	/********
	 * MAIN *
	 ********/

	//imposto la connessione al db
	$db = new SQLite3('accantona.sqlite');

	$currentTab = getGetData('tab');
	$tabs = array('spese', 'stored');
	if (!in_array($currentTab, $tabs)) {
		$currentTab = 'spese';
	}

	if ($_POST) {

		$saldoBanca = getPostAmount('saldo_banca');
		$risparmio  = getPostAmount('risparmio');
		$contanti   = getPostAmount('contanti');

		if (isset($saldoBanca) && isset($risparmio) && isset($contanti)) {
			$db->exec("UPDATE variabili SET valore='$saldoBanca' WHERE nome='saldo_banca'");
			$db->exec("UPDATE variabili SET valore='$risparmio' WHERE nome='risparmio'");
			$db->exec("UPDATE variabili SET valore='$contanti' WHERE nome='contanti'");
		}

		//controllo se devo salvare un nuovo record nelle spese
		if (isset($_POST['add_x']) && isset($_POST['add_y'])) {

			//cotrollo che la valuta sia una data valida
			$dayValuta   = str_pad((int) substr($_POST['add_valuta'], 0, 2), 2, 0, STR_PAD_LEFT);
			$monthValuta = str_pad((int) substr($_POST['add_valuta'], 3, 2), 2, 0, STR_PAD_LEFT);
			$yearValuta  = (int) substr($_POST['add_valuta'], -4);
			$addValuta   = checkdate($monthValuta, $dayValuta, $yearValuta) ? "$yearValuta-$monthValuta-$dayValuta" : '';

			//controllo la categoria
			$addCategoria = (int) getPostData('add_categoria');
			$rs = $db->query("SELECT * FROM categorie WHERE id=$addCategoria");
			if (!$rs->fetchArray(SQLITE3_ASSOC)) {
				$addCategoria = 0;
			}

			//controllo l'importo
			$addImporto = getPostAmount('add_importo');
			$addDescrizione = getPostData('add_descrizione');

			if ($currentTab == 'spese' && $addValuta && $addCategoria && $addImporto && $addDescrizione) {
				$sql = "INSERT INTO spese (valuta, id_categoria, importo, descrizione) ".
					"VALUES ('$addValuta', '$addCategoria', '$addImporto', '$addDescrizione')";
				$db->exec($sql);
			} elseif ($currentTab == 'stored' && $addValuta && $addImporto && $addDescrizione) {
				$sql = "INSERT INTO accantonati (valuta, importo, descrizione) VALUES ('$addValuta', '$addImporto', '$addDescrizione')";
				$db->exec($sql);
			}
		}
	}
?>
<html>
	<head>
		<title>Accantona</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="css/style.css" type="text/css" rel="stylesheet">
		<link rel="icon" type="image/png" href="images/favicon.png">
		<link href="css/ui-lightness/jquery-ui-1.8.14.custom.css" type="text/css" rel="stylesheet">
		<script type="text/javascript" src="js/utils.js"></script>
		<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
		<script type="text/javascript" src="js/jquery-ui-1.8.14.custom.min.js"></script>
		<script type="text/javascript" src="js/ui.datepicker-it.js"></script>
		<style type="text/css">
			body{font-family:sans-serif;font-size:14px;}
			a{text-decoration:none;}
			th{background:#eee;text-align:left;}
			img{border:0;}
			input{background:#ffffb0;border:0;font-family:sans-serif;font-size:14px;margin:0;padding:0;}
			.budget{float:left;margin:0 15px 0 0;}
			input.data{width:90px;}
			input[type=image]{vertical-align: middle;}
			input.descrizione{width:510px;}
			input.importo{text-align:right;width:60px;}
			#header ul{list-style:none;padding:0;margin:0;}
			#header li{float:left;border:1px solid #bbb;border-bottom-width:0;margin:0;}
			#header a{text-decoration:none;display:block;background:#eee;padding:0.24em 1em;color:#00c;width:8em;text-align:center;}
			#header a:hover{background:#ddf;}
			#header #selected{border-color:#000;}
			#header #selected a{position:relative;top:1px;background:white;color:#000;font-weight:bold;}
			#tabs-container{clear:both;display:block;margin:40px 0 0 0;}
			#content{border:1px solid black;border-bottom:none;border-left:none;border-right:none;clear:both;padding: 0 1em;}
			.variabili{width:100px;text-align:right;}
		</style>
	</head>
	<body>
<?php
	$div = new Div();
	$div->add(getBudgetTable($db));
	$div->add(getCategoriesTable($db));

	$dHeader = new Div(new H1(), array('id' => 'header'));

	$ul = new UnorderList();
	if ($currentTab == 'stored') {
		$ul->add(new ListItem(new Anchor(new Text('Spese'), array('href' => 'index.php?tab=spese'))));
		$ul->add(new ListItem(new Anchor(new Text('Accantonati'), array()), array('id' => 'selected')));
	} else {
		$ul->add(new ListItem(new Anchor(new Text('Spese')), array('id' => 'selected')));
		$ul->add(new ListItem(new Anchor(new Text('Accantonati'), array('href' => 'index.php?tab=stored'))));
	}
	$dHeader->add($ul);

	//creo la form e la visualizzo
	$form = new Form($div, array('action' => "index.php?tab=$currentTab", 'id' => 'accantonaForm', 'method' => 'post'));

	$divTabs = new Div(null, array('id' => 'tabs-container'));
	$divTabs->add($dHeader);
	$divTabs->add(new Div($currentTab == 'stored' ? getStoredTable($db) : getSpendTable($db), array('id' => 'content')));
	$form->add($divTabs);
	$form->flush();

	$db->close();
?>
	</body>
</html>
