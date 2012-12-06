<?
include('csatlak.php');
include('ujkuki.php');

$_REQUEST['id']=(int)$_REQUEST['id'];
$er=mysql_query('select * from diplomacia_szovegek where id='.$_REQUEST['id']);
$szoveg=mysql_fetch_array($er);
if (!$szoveg) kilep();

$jogosult=0;
if ($szoveg['id']) {
	$aux=mysql2row('select * from diplomacia_ajanlatok where szoveg_id='.$szoveg['id']);
	if (!$aux) {
		$aux=mysql2row('select * from diplomacia_statuszok where szoveg_id='.$szoveg['id']);
		if (!$aux) kilep();
	}
	//sajat v nyilvanos
	if ($aux['ki']==$adataim['tulaj_szov'] or $aux['kinek']==$adataim['tulaj_szov'] or $aux['kivel']==$adataim['tulaj_szov']) $jogosult=1;
	//nyilvanos
	if ($aux['nyilvanos']==1) $jogosult=1;
	//vendegstatusz
	$aux=mysql2row('select * from szovetseg_vendegek where szov_id in ('.sanitint($aux['ki']).','.sanitint($aux['kinek']).','.sanitint($aux['kivel']).') and user_id='.$uid);
	if ($aux) $jogosult=1;
}
if ($jogosult==0) kilep();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort - <? if ($lang_lang=='hu') echo 'megjegyzés';else echo 'comment';?></title>
</head>
<body style="background: rgb(42,43,45); color: white; font-family: arial, sans-serif; font-size: 12pt">
<div style="position: absolute; top: 5px; right: 5px;"><a href="" onclick="return window.close()"><img src="img/ikonok/cross.gif" style="border:none" /></a></div>
<?
echo nl2br($szoveg['szoveg']);
?>
</body>
</html>
<? mysql_close($mysql_csatlakozas);?>