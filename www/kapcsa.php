<?
include('config.php');
$mysql_csatlakozas=mysql_connect('localhost',$mysql_username,$mysql_password);
$result=mysql_select_db($database_mmog_nemlog);
mysql_query('set names "utf8"');
$er=mysql_query('select * from kapcsak where id='.((int)$_REQUEST['x']));
$aux=mysql_fetch_array($er);
$szo=$aux['kapcsa'];
mysql_close($mysql_csatlakozas);

$maxx=100;
$maxy=30;
$kep=imagecreate($maxx,$maxy);
$hatter=imagecolorallocate($kep,0,0,0);
$szoveg=imagecolorallocate($kep,255,255,255);
imagefill($kep,0,0,$hatter);

for($i=0;$i<8;$i++) {
	imagettftext($kep,10,0,10+10*$i+mt_rand(-1,1),20+mt_rand(-3,3),$szoveg,'img/arial.ttf',$szo[$i]);
}

header('Content-type: image/gif');imagegif($kep);
?>