<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$_REQUEST['bolygo_id']=(int)$_REQUEST['bolygo_id'];
$_REQUEST['gyar_id']=(int)$_REQUEST['gyar_id'];
$_REQUEST['db']=(int)$_REQUEST['db'];

$er=mysql_query('select * from bolygo_gyar where bolygo_id='.$_REQUEST['bolygo_id'].' and gyar_id='.$_REQUEST['gyar_id']);
$aux=mysql_fetch_array($er);
if (!$aux) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen gyár.']);

$er2=mysql_query('select * from bolygok where id='.$_REQUEST['bolygo_id']);
$aux2=mysql_fetch_array($er2);
if ($aux2['tulaj']!=$uid) kilep($lang[$lang_lang]['kisphpk']['Nem a tied a bolygó.']);

if ($_REQUEST['db']>$aux['db']) $_REQUEST['db']=$aux['db'];

if ($_REQUEST['db']<0) $_REQUEST['db']=0;

if (!elerheto_ez_a_gyar($aux2['osztaly'],$aux2['hold'],$_REQUEST['gyar_id'],$uid)) if ($_REQUEST['db']>0) kilep($lang[$lang_lang]['kisphpk']['Ez a gyár/üzemmód nem elérhető.']);

mysql_query('update bolygo_gyar set aktiv_db='.$_REQUEST['db'].' where bolygo_id='.$_REQUEST['bolygo_id'].' and gyar_id='.$_REQUEST['gyar_id']);


//szabotalt gyarakat inaktivalni
$r=mysql_query('select bgytsz.bolygo_id,bgytsz.tipus,max(bgytsz.db) as szabotalt,sum(bgy.db) as osszesen,sum(bgy.aktiv_db) as aktiv
,min(gy.id) as min_gyar_id
,max(gy.id) as max_gyar_id
from bolygo_gyar bgy, gyarak gy, bolygo_gyartipus_szabotazs bgytsz
where bgy.bolygo_id='.$_REQUEST['bolygo_id'].'
and bgytsz.bolygo_id='.$_REQUEST['bolygo_id'].'
and bgy.gyar_id=gy.id
and gy.tipus=bgytsz.tipus
group by bgytsz.bolygo_id,bgytsz.tipus
having osszesen<szabotalt+aktiv');
while($bgy=mysql_fetch_array($r)) {
	$inaktivalni_kell=$bgy['szabotalt']+$bgy['aktiv']-$bgy['osszesen'];
	if ($bgy['min_gyar_id']!=$bgy['max_gyar_id']) {
		$r2=mysql_query('select bgy.* from bolygo_gyar bgy, gyarak gy where bgy.bolygo_id='.$bgy['bolygo_id'].' and bgy.gyar_id=gy.id and gy.tipus='.$bgy['tipus'].' order by if(bgy.gyar_id='.$_REQUEST['gyar_id'].',1,0),bgy.gyar_id');
		while(($inaktivalni_kell>0) and ($aux=mysql_fetch_array($r2))) {
			$most_inaktivalni_kell=$inaktivalni_kell;
			if ($most_inaktivalni_kell>$aux['aktiv_db']) $most_inaktivalni_kell=$aux['aktiv_db'];
			mysql_query('update bolygo_gyar set aktiv_db=if(aktiv_db>'.$most_inaktivalni_kell.',aktiv_db-'.$most_inaktivalni_kell.',0) where bolygo_id='.$bgy['bolygo_id'].' and gyar_id='.$aux['gyar_id']);
			$inaktivalni_kell-=$most_inaktivalni_kell;
		}
	} else {
		mysql_query('update bolygo_gyar set aktiv_db=if(aktiv_db>'.$inaktivalni_kell.',aktiv_db-'.$inaktivalni_kell.',0) where bolygo_id='.$bgy['bolygo_id'].' and gyar_id='.$bgy['max_gyar_id']);
	}
}

bgye_frissites($_REQUEST['bolygo_id']);


insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));kilep();
?>