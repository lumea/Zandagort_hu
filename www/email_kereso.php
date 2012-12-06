<?
include('csatlak.php');
include('ujkuki.php');

if ($ismert) if ($uid==1) {

if (isset($_REQUEST['email'])) {
	$er=mysql_query('select * from userek where email="'.sanitstr($_REQUEST['email']).'"') or hiba(__FILE__,__LINE__,mysql_error());
	$keresett_user=mysql_fetch_array($er);
	if (!$keresett_user) {header('Location: email_kereso.php');exit;}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort email kereső</title>
</head>
<body>
<form action="email_kereso.php" method="get">
<input type="text" name="email" /> <input type="submit" />
</form>
<?
if ($keresett_user) {
	echo '['.$keresett_user['id'].'] '.$keresett_user['nev'].' &lt;'.$keresett_user['email'].'&gt; '.$keresett_user['uccso_login'];
}
?>
</body>
</html>
<?
}
mysql_close($mysql_csatlakozas);
?>