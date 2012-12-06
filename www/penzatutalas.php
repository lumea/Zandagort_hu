<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

$er3=mysql_query('select * from userek where nev="'.sanitstr($_REQUEST['kinek']).'"') or hiba(__FILE__,__LINE__,mysql_error());
$kinek=mysql_fetch_array($er3);
if (!$kinek) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen játékos.']);
if ($kinek['id']==$uid) kilep($lang[$lang_lang]['kisphpk']['Saját magadnak nem küldhetsz pénzt.']);

$mennyit=sanitint($_REQUEST['mennyit']);

$hiba='';
//LOCK KEZDETE
mysql_query('lock tables userek write, userek u read');
	$er3=mysql_query('select * from userek u where u.id='.$kinek['id']) or hiba(__FILE__,__LINE__,mysql_error());
	$kinek=mysql_fetch_array($er3);
	$er3=mysql_query('select * from userek u where u.id='.$uid) or hiba(__FILE__,__LINE__,mysql_error());
	$kitol=mysql_fetch_array($er3);
	//
	if ($mennyit>$kitol['vagyon']) $mennyit=$kitol['vagyon'];
	if ($mennyit>0) {
		if ($mennyit+$kitol['penz_adott']>$kitol['penz_adhato_max']) $mennyit=$kitol['penz_adhato_max']-$kitol['penz_adott'];
		if ($mennyit>0) {
			mysql_query('update userek set vagyon=vagyon+'.$mennyit.' where id='.$kinek['id']);
			mysql_query('update userek set vagyon=if(vagyon>'.$mennyit.',vagyon-'.$mennyit.',0),penz_adott=penz_adott+'.$mennyit.' where id='.$uid);
			/*if ($mennyit+$kinek['penz_kapott']>$kinek['penz_kaphato_max']) $mennyit=$kinek['penz_kaphato_max']-$kinek['penz_kapott'];
			if ($mennyit>0) {
				mysql_query('update userek set vagyon=vagyon+'.$mennyit.',penz_kapott=penz_kapott+'.$mennyit.' where id='.$kinek['id']);
				mysql_query('update userek set vagyon=if(vagyon>'.$mennyit.',vagyon-'.$mennyit.',0),penz_adott=penz_adott+'.$mennyit.' where id='.$uid);
			} else {
				$hiba=$lang[$lang_lang]['kisphpk']['A címzetted már felhasználta a napi fogadási limitjét.'];
			}*/
		} else {
			$hiba=$lang[$lang_lang]['kisphpk']['Már felhasználtad a napi küldési limitedet.'];
		}
	} else {
		$hiba=$lang[$lang_lang]['kisphpk']['Nincs is pénzed.'];
	}
mysql_query('unlock tables');
//LOCK VEGE


if ($hiba=='') insert_into_penz_transzfer_log($uid,$adataim['tulaj_szov'],$kinek['id'],$kinek['tulaj_szov'],$mennyit);


kilep($hiba);
?>