<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['hova']=(int)$_REQUEST['hova'];

$er=mysql_query('select * from szovetsegek where id='.$_REQUEST['hova']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen szövetség.']);

$er=mysql_query('select * from szovetseg_meghivas_kerelmek where ki='.$uid.' and hova='.$_REQUEST['hova']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) kilep();//van mar kerelem, minek uj

szovi_belepo_kilepo_uzenet($_REQUEST['hova']
,'Belépési kérelem: '.$adataim['nev']
,$adataim['nev'].' szeretne belépni a szövetségbe. A SZÖVETSÉG menüpontban a Tagok alatt tudod elfogadni vagy elutasítani a kérelmet. Elfogadás esetén '.$adataim['nev'].' a szövetség tagja lesz.'
,'Request for entry: '.$adataim['nev']
,$adataim['nev'].' would like to enter your alliance. You can accept or reject his/her application in the ALLIANCE menu under Members. If you accept it '.$adataim['nev'].' will become a member of the alliance.');

mysql_query('insert ignore into szovetseg_meghivas_kerelmek (ki,hova) values('.$uid.','.$_REQUEST['hova'].')');

kilep();
?>