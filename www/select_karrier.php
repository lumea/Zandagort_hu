<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if ($adataim['karrier']!=0) kilep();

$_REQUEST['k']=(int)$_REQUEST['k'];

if ($_REQUEST['i']=='1') {//instant
	if ($_REQUEST['k']==3) {//fantom
		mysql_query('update userek set karrier=3,speci=3 where id='.$uid);
		mysql_query('update bolygok set kezelo=0 where tulaj='.$uid);//tutorokat kivenni
		mysql_query('update bolygok set kulso_nev=concat("B",id) where tulaj='.$uid);//bolygot atnevezni
	} elseif ($_REQUEST['k']==4) {//diplomata
		mysql_query('update userek set karrier=4 where id='.$uid);
	}
} else {
	if ($_REQUEST['k']<1) kilep();
	if ($_REQUEST['k']>4) kilep();
	mysql_query('update userek set leendo_karrier='.$_REQUEST['k'].' where id='.$uid);
}

kilep();
?>