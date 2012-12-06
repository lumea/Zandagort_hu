<?
include('csatlak.php');
include('ujkuki.php');

if ($ismert) if ($uid==1) {

if (isset($_REQUEST['ujakt'])) {
	$user_id=(int)$_REQUEST['user_id'];
	$er=mysql_query("select nev,aktivalo_kulcs,nyelv from userek where id=$user_id") or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);
	$nev=$aux['nev'];
	$aktivalo_kulcs=$aux['aktivalo_kulcs'];
	if (!$aux) {header('Location: aktivalo_level.php');exit;}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort aktiváló levél</title>
</head>
<body>
<? if ($user_id>0) {
if ($aux['nyelv']=='en') {
echo "<pre>
Dear $nev!

Click on the following link to activate your registration:
".$zanda_game_url['en']."?akt=$aktivalo_kulcs


Zandagort and his people
</pre>";
} else {
echo "<pre>
Kedves $nev!

Kattints az alábbi linkre, hogy aktiváld regisztrációdat:
".$zanda_game_url['hu']."?akt=$aktivalo_kulcs


Zandagort és népe
</pre>";
}
} else { ?>
<form action="aktivalo_level.php" method="post">
<input type="text" name="user_id" />
<input type="submit" name="ujakt" />
</form>
<? } ?>
</body>
</html>
<?
}
mysql_close($mysql_csatlakozas);
?>