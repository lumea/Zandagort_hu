<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['karrier']==0) kilep();

$_REQUEST['k']=(int)$_REQUEST['k'];
if ($_REQUEST['k']<1) kilep();
if ($_REQUEST['k']>4) kilep();

if (($adataim['karrier']==2)&&($_REQUEST['k']==4)) {//zelota
	if ($adataim['speci_2_4']==1) mysql_query('update userek set speci=4 where id='.$uid);
} else {
	if ($adataim['speci']!=0) kilep();
	if ($adataim['speci_'.$adataim['karrier'].'_'.$_REQUEST['k']]==1) {
		mysql_query('update userek set speci='.$_REQUEST['k'].' where id='.$uid);
		//fantom eseten tutorokat kivenni, bolygot atnevezni
		if ($adataim['karrier']==3) if ($_REQUEST['k']==3) {
			mysql_query('update bolygok set kezelo=0 where tulaj='.$uid);
			mysql_query('update bolygok set kulso_nev=concat("B",id) where tulaj='.$uid);
		}
		//fejvadasz eseten flottakat frissiteni
		if ($adataim['karrier']==2) if ($_REQUEST['k']==3) {
			$r=mysql_query('select id from flottak where tulaj='.$uid);
			while($aux=mysql_fetch_array($r)) flotta_minden_frissites($aux[0]);
		}
		//bekebiro eseten megnezni, h legalabb 5 fos szovi tagja-e
		if ($adataim['karrier']==4) if ($_REQUEST['k']==1) {
			$aux=(int)mysql2num('select tagletszam from szovetsegek where id='.$adataim['szovetseg']);
			if ($aux<5) mysql_query('update userek set speci=3 where id='.$uid);//potencialis bekebiro
		}
	}
}


kilep();
?>