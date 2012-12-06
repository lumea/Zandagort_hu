<?
include('csatlak.php');
include('ujkuki.php');

$_REQUEST['kinek']=(int)$_REQUEST['kinek'];
$er=mysql_query('select * from userek where id='.$_REQUEST['kinek']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) {
$cimzettek=$aux['nev'];
$targy='';
$uzenet='';
} else {

$cimzettek='';
$targy='';
$uzenet='';
$_REQUEST['id']=(int)$_REQUEST['id'];
$er=mysql_query('select * from levelek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$level=mysql_fetch_array($er);
if ($level) if ($level['tulaj']==$uid) {

$cimzettek='';

if ($_REQUEST['to_all']==1) {
	$er2=mysql_query('select u.id,u.nev from cimzettek c,userek u where c.level_id='.$level['id'].' and c.cimzett_tipus='.CIMZETT_TIPUS_USER.' and c.cimzett_id=u.id order by u.nev') or hiba(__FILE__,__LINE__,mysql_error());
	while($aux2=mysql_fetch_array($er2)) {
		if ($aux2[0]!=$uid) $cimzettek.=', '.$aux2[1];
	}
	$er2=mysql_query('select sz.id,sz.nev from cimzettek c,szovetsegek sz where c.level_id='.$level['id'].' and c.cimzett_tipus='.CIMZETT_TIPUS_SZOVETSEG.' and c.cimzett_id=sz.id order by sz.nev') or hiba(__FILE__,__LINE__,mysql_error());
	$szovi_cimzettek=array();
	while($aux2=mysql_fetch_array($er2)) {
		$cimzettek.=', '.$aux2[1];
		$szovi_cimzettek[]=$aux2[0];
	}
	if ($level['felado']!=$uid) {
		$er2=mysql_query('select nev,szovetseg from userek where id='.$level['felado']) or hiba(__FILE__,__LINE__,mysql_error());
		$aux2=mysql_fetch_array($er2);
		if (!in_array($aux2[1],$szovi_cimzettek)) $cimzettek.=', '.$aux2[0];
	}
	$cimzettek=substr($cimzettek,2);
} else {
	if ($level['felado']!=$uid) {
		$er2=mysql_query('select nev from userek where id='.$level['felado']) or hiba(__FILE__,__LINE__,mysql_error());
		$aux2=mysql_fetch_array($er2);
		$cimzettek=$aux2[0];
	} else {//ha az altalad kuldott levelre nyomsz reply-t, akkor a cimzett(eknek) akarsz valaszolni (ugyanugy, mintha reply-to-all-t nyomnal)
		$er2=mysql_query('select u.id,u.nev from cimzettek c,userek u where c.level_id='.$level['id'].' and c.cimzett_tipus='.CIMZETT_TIPUS_USER.' and c.cimzett_id=u.id order by u.nev') or hiba(__FILE__,__LINE__,mysql_error());
		while($aux2=mysql_fetch_array($er2)) {
			if ($aux2[0]!=$uid) $cimzettek.=', '.$aux2[1];
		}
		$er2=mysql_query('select sz.id,sz.nev from cimzettek c,szovetsegek sz where c.level_id='.$level['id'].' and c.cimzett_tipus='.CIMZETT_TIPUS_SZOVETSEG.' and c.cimzett_id=sz.id order by sz.nev') or hiba(__FILE__,__LINE__,mysql_error());
		$szovi_cimzettek=array();
		while($aux2=mysql_fetch_array($er2)) {
			$cimzettek.=', '.$aux2[1];
			$szovi_cimzettek[]=$aux2[0];
		}
		$cimzettek=substr($cimzettek,2);
	}
}

if (substr($level['targy'],0,3)=='Re:') $targy=$level['targy'];else $targy='Re: '.$level['targy'];
$uzenet="\n\n--------------\n".$level['uzenet'];

}

}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort - <?=$lang[$lang_lang]['kisphpk']['levél írása'];?></title>
<link rel="stylesheet" type="text/css" href="stilus.css" />
<script src="actb.js" type="text/javascript"></script>
<script src="actb_common.js" type="text/javascript"></script>
<script type="text/javascript">
function init() {
	new actb(document.getElementById('uj_level_cimzettek'),'ajax_autocomplete_userek_es_szovetsegek',1);
<? if (strlen($cimzettek)>0) { ?>
<? if (strlen($targy)>0) { ?>
	document.getElementById('uj_level_uzenet').focus();
	if (document.getElementById('uj_level_uzenet').setSelectionRange) document.getElementById('uj_level_uzenet').setSelectionRange(0,0);
<? } else { ?>
	document.getElementById('uj_level_targy').focus();
<? }  ?>
<? } else { ?>
	document.getElementById('uj_level_cimzettek').focus();
<? }  ?>
	return false;
};
function levelet_elkuld() {
	sendRequest('levelet_elkuld.php',function(req) {
		if (req.responseText.length==0) {
			window.close();
		} else alert(req.responseText);
	},'cimzettek='+encodeURIComponent(document.getElementById('uj_level_cimzettek').value)+
	'&targy='+encodeURIComponent(document.getElementById('uj_level_targy').value)+
	'&uzenet='+encodeURIComponent(document.getElementById('uj_level_uzenet').value));
	return false;
};
function json2obj(x) {
	return eval('('+x.substring(2,x.length-4)+')');
};
function sendRequest(url,callback,postData) {
	var req = createXMLHTTPObject();
	if (!req) return;
	var method = postData?'POST':'GET';
	req.open(method,url,true);
	req.setRequestHeader('User-Agent','XMLHTTP/1.0');
	if (postData) req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
	req.onreadystatechange = function () {
		if (req.readyState != 4) return;
		if (req.status != 200 && req.status != 304) return;
		callback(req);
	};
	if (req.readyState == 4) return;
	req.send(postData);
};
function XMLHttpFactories() {
	return [
		function () {return new XMLHttpRequest()},
		function () {return new ActiveXObject('Msxml2.XMLHTTP')},
		function () {return new ActiveXObject('Msxml3.XMLHTTP')},
		function () {return new ActiveXObject('Microsoft.XMLHTTP')}
	];
};
function createXMLHTTPObject() {
	var xmlhttp = false;
	var factories = XMLHttpFactories();
	for (var i=0;i<factories.length;i++) {
		try {
			xmlhttp = factories[i]();
		} catch (e) {
			continue;
		}
		break;
	}
	return xmlhttp;
};
</script>
</head>
<body onload="init()" style="background: rgb(42,43,45) !important">
<form action="." method="post" onsubmit="return levelet_elkuld();">
	<table>
	<tr><td><?=$lang[$lang_lang]['kisphpk']['Címzettek'];?>:</td><td><input type="text" id="uj_level_cimzettek" class="cimzett_sor" value="<?=htmlspecialchars($cimzettek,ENT_QUOTES);?>" /> (<?=$lang[$lang_lang]['kisphpk']['vesszővel elválasztva'];?>)</td></tr>
	<tr><td><?=$lang[$lang_lang]['kisphpk']['Tárgy'];?>:</td><td><input type="text" id="uj_level_targy" class="targy_sor" value="<?=htmlspecialchars($targy,ENT_QUOTES,'UTF-8',false);?>" /></td></tr>
	</table>
	<p><textarea id="uj_level_uzenet" class="uzenet_box"><?=$uzenet;?></textarea></p>
	<p><input type="submit" value="<?=$lang[$lang_lang]['kisphpk']['Levél elküldése'];?>" /></p>
</form>
</body>
</html>
<? mysql_close($mysql_csatlakozas);?>