<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['szovetseg']==0) kilep($lang[$lang_lang]['kisphpk']['Nem vagy tagja szövetségnek.']);

$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$adataim['szovetseg'].' and id='.$adataim['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2) $tiszt_jog=$aux2;else $tiszt_jog=0;

if ($adataim['tisztseg']!=-1 && !$tiszt_jog['jog_2']) kilep($lang[$lang_lang]['kisphpk']['Nincs meghívási jogod.']);


$_REQUEST['kit']=sanitstr($_REQUEST['kit']);
if ($_REQUEST['csatol']!=='1') $_REQUEST['csatol']=0;


$er=mysql_query('select * from userek where nev="'.$_REQUEST['kit'].'"') or hiba(__FILE__,__LINE__,mysql_error());
$meghivott=mysql_fetch_array($er);
if (!$meghivott) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);
if ($meghivott['szovetseg']==$adataim['szovetseg']) kilep($lang[$lang_lang]['kisphpk']['Ő már tagja a szövetségednek.']);

$meghivo_neve=$adataim['nev'];
$megjegyzes=trim($_REQUEST['megjegyzes']);

$er=mysql_query('select * from szovetsegek where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen szövetség.']);
$szovetseg_neve=$aux['nev'];
$er=mysql_query('select * from szovetseg_szabalyzatok where id='.$adataim['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
$szabalyzat=$aux['szabalyzat'];

$meghivo_megjegyzes='';if (strlen($megjegyzes)) $meghivo_megjegyzes="\n\n$meghivo_neve a következőt üzeni:\n$megjegyzes";
$meghivo_szabalyzat='';if ($_REQUEST['csatol']) $meghivo_szabalyzat="\n\n----------\nA szövetség belső szabályzata:\n$szabalyzat";
$meghivo_megjegyzes_en='';if (strlen($megjegyzes)) $meghivo_megjegyzes_en="\n\n$meghivo_neve adds the following comment:\n$megjegyzes";
$meghivo_szabalyzat_en='';if ($_REQUEST['csatol']) $meghivo_szabalyzat_en="\n\n----------\nThe regulations of the alliance:\n$szabalyzat";

rendszeruzenet($meghivott['id'],"Meghívás szövetségbe: $szovetseg_neve","$meghivo_neve meghívott a(z) $szovetseg_neve szövetségbe. Ha szeretnél csatlakozni, kattints a SZÖVETSÉG menüre, ahol láthatod a meghívóid között ezt is, és fogadd el.$meghivo_megjegyzes$meghivo_szabalyzat","Invitation into alliance: $szovetseg_neve","$meghivo_neve invited you to the alliance $szovetseg_neve. If you would like to join them, click on the ALLIANCE menu, where you can see this invitation, and accept it.$meghivo_megjegyzes_en$meghivo_szabalyzat_en");

$datum=date('Y-m-d H:i:s');

mysql_query('insert ignore into szovetseg_meghivok (ki,kit,hova,mikor) values('.$uid.','.$meghivott['id'].','.$adataim['szovetseg'].',"'.$datum.'")') or hiba(__FILE__,__LINE__,mysql_error());


kilep();
?>