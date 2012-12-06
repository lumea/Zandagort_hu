<?
include('csatlak.php');
include('ujkuki.php');

if ($ismert) if ($uid==1) {

if (isset($_REQUEST['ujpw'])) {
	$user_id=(int)$_REQUEST['user_id'];
	$er=mysql_query("select nev,nyelv from userek where id=$user_id") or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);$nev=$aux['nev'];
	if (!$aux) {header('Location: pwgen.php');exit;}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu-HU" lang="hu-HU">
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<title>Zandagort pwgen</title>
</head>
<body>
<? if ($user_id>0) { ?>
<?
$jelszo=randomgen(8);
$jelszo_so=randomgen(32);
$jelszo_hash=hash('whirlpool',$jelszo.$jelszo_so.$rendszer_so);
mysql_query("update userek set jelszo_so='$jelszo_so', jelszo_hash='$jelszo_hash' where id=$user_id");

if ($aux['nyelv']=='hu')
echo "<pre>
Szia!

Itt egy új jelszó:
Név: $nev
Jelszó: $jelszo
Belépés után a PROFIL menüben tudod megváltoztatni.


Zandagort és népe
</pre>";
else
echo "<pre>
Hi!

Here's a new password:
Name: $nev
Password: $jelszo
After you login, you can change it in the PROFILE menu.


Zandagort and his people
</pre>";
?>
<? } else { ?>
<form action="pwgen.php" method="post">
<input type="text" name="user_id" />
<input type="submit" name="ujpw" />
</form>
<? } ?>
</body>
</html>
<?
}
mysql_close($mysql_csatlakozas);
?>