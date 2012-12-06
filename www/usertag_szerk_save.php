<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Ehhez elő kell fizetned.']);

$_REQUEST['id']=(int)$_REQUEST['id'];

//hiába egysoros az input mező, copy-paste-el bizonyos böngészőkben be lehet vinni újsort is
$_REQUEST['cim']=strtr($_REQUEST['cim'],"\r\n\t",'   ');
$_REQUEST['szoveg']=strtr($_REQUEST['szoveg'],"\r\n\t",'   ');

if ($_REQUEST['id']==0) {//uj tulajdonsag
	if (strlen(trim($_REQUEST['cim']))) {
		$q_er=mysql_query('select max(sorszam) from user_tagek where tulaj='.$uid);
		$aux=mysql_fetch_array($q_er);
		mysql_query('insert into user_tagek (tulaj,cim,szoveg,sorszam) values('.$uid.',"'.sanitstr($_REQUEST['cim']).'","'.sanitstr($_REQUEST['szoveg']).'",'.($aux[0]+1).')');
	}
} else {
	$q_er=mysql_query('select * from user_tagek where id='.$_REQUEST['id'].' and tulaj='.$uid);
	$q=mysql_fetch_array($q_er);
	if (!$q) kilep($lang[$lang_lang]['kisphpk']['Nincs ilyen tulajdonság.']);
	if (strlen(trim($_REQUEST['cim']))) {
		mysql_query('update user_tagek set cim="'.sanitstr($_REQUEST['cim']).'",szoveg="'.sanitstr($_REQUEST['szoveg']).'" where id='.$_REQUEST['id']);
	} else {
		mysql_query('delete from user_tagek where id='.$_REQUEST['id']);
	}
}

kilep();
?>