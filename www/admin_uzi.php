<?
set_time_limit(0);//levelek miatt
include('csatlak.php');
include('ujkuki.php');
if ($ismert) if ($uid==1) {




/*
$targy='';
$szoveg="
";

$szoveg.="\n\n\nZandagort és népe";
$szoveg.="\n\n".$zanda_ingame_msg_ps['hu'];

$er=mysql_query('select id,nev from userek where id=1');
//$er=mysql_query('select id,nev from userek where nyelv="hu"');
while($aux=mysql_fetch_array($er)) {
	$uzi="Kedves ".strip_tags($aux[1])."!\n\n".$szoveg;rendszeruzenet_a_kozponti_szolgaltatohaztol($aux[0],$targy,$uzi);
}
echo 'kesz';exit;
*/



/*
$targy='';
$szoveg="
";

$szoveg.="\n\n\nZandagort and his people";
$szoveg.="\n\n".$zanda_ingame_msg_ps['en'];

$er=mysql_query('select id,nev from userek where id=1');
//$er=mysql_query('select id,nev from userek where nyelv="en"');
while($aux=mysql_fetch_array($er)) {
	$uzi="Dear ".strip_tags($aux[1])."!\n\n".$szoveg;rendszeruzenet_a_kozponti_szolgaltatohaztol($aux[0],$targy,$uzi,'en');
}
echo 'kesz';exit;
*/








/*
$er=mysql_query('select id,nev,email from userek where id=1') or hiba(__FILE__,__LINE__,mysql_error());
//$er=mysql_query('select * from userek where timestampdiff(hour,uccso_akt,now())>2*24 and nyelv="hu" order by id') or hiba(__FILE__,__LINE__,mysql_error());
//$er=mysql_query('select id,nev,email from userek where nyelv="hu" order by id') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
zandamail('hu',array(
	'email'	=>	$aux['email'],
	'name'	=>	$aux[1],
	'subject'	=>	'',
	'html'	=>	"<p>Kedves ".$aux[1]."!</p>
<p></p>",
	'plain'	=>	"Kedves ".$aux[1]."!

"
));
echo $aux['id'].' '.$aux['nev'].' '.$aux['email'].'<br />';
usleep(500000);//0,5 sec
}
echo 'kesz';exit;
*/



/*
$er=mysql_query('select id,nev,email from userek where id=1') or hiba(__FILE__,__LINE__,mysql_error());
//$er=mysql_query('select * from userek where timestampdiff(hour,uccso_akt,now())>2*24 and nyelv="en" order by id') or hiba(__FILE__,__LINE__,mysql_error());
//$er=mysql_query('select id,nev,email from userek where nyelv="en" order by id') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
zandamail('en',array(
	'email'	=>	$aux['email'],
	'name'	=>	$aux[1],
	'subject'	=>	'',
	'html'	=>	"<p>Dear ".$aux[1]."!</p>
<p></p>",
	'plain'	=>	"Dear ".$aux[1]."!

"
));
echo $aux['id'].' '.$aux['nev'].' '.$aux['email'].'<br />';
usleep(500000);//0,5 sec
}
echo 'kesz';exit;
*/




echo 'hello admin';

}
mysql_close($mysql_csatlakozas);
?>