<?
include('../csatlak.php');
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;
set_time_limit(0);


$npc_egyenertekek[1][4]=1540;
$npc_egyenertekek[1][6]=11548;
$npc_egyenertekek[1][8]=76990;
$npc_egyenertekek[1][10]=481187;
$npc_egyenertekek[2][4]=1654;
$npc_egyenertekek[2][6]=12403;
$npc_egyenertekek[2][8]=82688;
$npc_egyenertekek[2][10]=516801;
$npc_egyenertekek[3][4]=2567;
$npc_egyenertekek[3][6]=19254;
$npc_egyenertekek[3][8]=128360;
$npc_egyenertekek[3][10]=802251;
$npc_egyenertekek[4][4]=1502;
$npc_egyenertekek[4][6]=11261;
$npc_egyenertekek[4][8]=75076;
$npc_egyenertekek[4][10]=469226;
$npc_egyenertekek[5][4]=1776;
$npc_egyenertekek[5][6]=13317;
$npc_egyenertekek[5][8]=88778;
$npc_egyenertekek[5][10]=554861;

$npc_megoszlas[4][201]=150;
$npc_megoszlas[4][202]=200;
$npc_megoszlas[4][203]=150;
$npc_megoszlas[4][204]=100;
$npc_megoszlas[4][205]=200;
$npc_megoszlas[4][206]=0;
$npc_megoszlas[4][1]=200;
$npc_megoszlas[4][207]=0;
$npc_megoszlas[4][208]=0;
$npc_megoszlas[4][209]=0;
$npc_megoszlas[4][210]=0;
$npc_megoszlas[4][211]=0;
$npc_megoszlas[4][212]=0;
$npc_megoszlas[4][2]=0;
$npc_megoszlas[4][213]=0;
$npc_megoszlas[4][214]=0;
$npc_megoszlas[4][215]=0;
$npc_megoszlas[4][216]=0;
$npc_megoszlas[4][217]=0;
$npc_megoszlas[4][218]=0;
$npc_megoszlas[4][3]=0;

$npc_megoszlas[6][201]=175;
$npc_megoszlas[6][202]=90;
$npc_megoszlas[6][203]=140;
$npc_megoszlas[6][204]=70;
$npc_megoszlas[6][205]=90;
$npc_megoszlas[6][206]=0;
$npc_megoszlas[6][1]=60;
$npc_megoszlas[6][207]=75;
$npc_megoszlas[6][208]=60;
$npc_megoszlas[6][209]=60;
$npc_megoszlas[6][210]=30;
$npc_megoszlas[6][211]=60;
$npc_megoszlas[6][212]=50;
$npc_megoszlas[6][2]=40;
$npc_megoszlas[6][213]=0;
$npc_megoszlas[6][214]=0;
$npc_megoszlas[6][215]=0;
$npc_megoszlas[6][216]=0;
$npc_megoszlas[6][217]=0;
$npc_megoszlas[6][218]=0;
$npc_megoszlas[6][3]=0;

$npc_megoszlas[8][201]=0;
$npc_megoszlas[8][202]=0;
$npc_megoszlas[8][203]=0;
$npc_megoszlas[8][204]=0;
$npc_megoszlas[8][205]=0;
$npc_megoszlas[8][206]=0;
$npc_megoszlas[8][1]=0;
$npc_megoszlas[8][207]=70;
$npc_megoszlas[8][208]=40;
$npc_megoszlas[8][209]=135;
$npc_megoszlas[8][210]=90;
$npc_megoszlas[8][211]=80;
$npc_megoszlas[8][212]=100;
$npc_megoszlas[8][2]=140;
$npc_megoszlas[8][213]=30;
$npc_megoszlas[8][214]=60;
$npc_megoszlas[8][215]=15;
$npc_megoszlas[8][216]=60;
$npc_megoszlas[8][217]=20;
$npc_megoszlas[8][218]=100;
$npc_megoszlas[8][3]=60;

$npc_megoszlas[10][201]=0;
$npc_megoszlas[10][202]=0;
$npc_megoszlas[10][203]=0;
$npc_megoszlas[10][204]=0;
$npc_megoszlas[10][205]=0;
$npc_megoszlas[10][206]=0;
$npc_megoszlas[10][1]=0;
$npc_megoszlas[10][207]=15;
$npc_megoszlas[10][208]=20;
$npc_megoszlas[10][209]=15;
$npc_megoszlas[10][210]=30;
$npc_megoszlas[10][211]=20;
$npc_megoszlas[10][212]=150;
$npc_megoszlas[10][2]=20;
$npc_megoszlas[10][213]=135;
$npc_megoszlas[10][214]=80;
$npc_megoszlas[10][215]=135;
$npc_megoszlas[10][216]=70;
$npc_megoszlas[10][217]=80;
$npc_megoszlas[10][218]=150;
$npc_megoszlas[10][3]=80;

function uj_npc_flottat_felrak($bolygo,$hajok) {
	mysql_query('insert into flottak (nev,tulaj,tulaj_szov,kezelo,bolygo,bazis_bolygo,statusz,sebesseg,x,y) values("NPC'.$bolygo['id'].'",0,0,0,'.$bolygo['id'].','.$bolygo['id'].','.STATUSZ_ALLOMAS.',0,'.$bolygo['x'].','.$bolygo['y'].')') or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select last_insert_id() from flottak') or hiba(__FILE__,__LINE__,mysql_error());
	$aux=mysql_fetch_array($er);$flotta_id=$aux[0];
	mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp) values('.$flotta_id.',0,100)') or hiba(__FILE__,__LINE__,mysql_error());
	$er=mysql_query('select id from eroforrasok where tipus='.EROFORRAS_TIPUS_URHAJO) or hiba(__FILE__,__LINE__,mysql_error());
	while($aux=mysql_fetch_array($er)) mysql_query('insert into flotta_hajo (flotta_id,hajo_id,ossz_hp) values('.$flotta_id.','.$aux[0].','.(((int)($hajok[$aux[0]]))*100).')') or hiba(__FILE__,__LINE__,mysql_error());
	flotta_minden_frissites($flotta_id);
}


$er=mysql_query('select * from hajok order by id');
while($aux=mysql_fetch_array($er)) $hajo_arak[$aux['id']]=$aux['ar']/100;



$er=mysql_query('select * from bolygok where terulet>2000000 and tulaj=0 order by id');//normal esetben ezt kell futtatni!!!
//$er=mysql_query('select * from bolygok where terulet>2000000 and tulaj=0 and id=7873');
//$er=mysql_query('select * from bolygok where terulet>2000000 and tulaj=0 and osztaly=5 order by id');
//$er=mysql_query('select * from bolygok where terulet>2000000 and tulaj=0 order by id limit 10');
while($aux=mysql_fetch_array($er)) {
	//regi flotta leszedese
	//$er2=mysql_query('select id from flottak where tulaj=0 and x='.$aux['x'].' and y='.$aux['y']);while($aux2=mysql_fetch_array($er2)) flotta_torles($aux2[0]);
	//
	$osztaly=$aux['osztaly'];
	$terulet=round($aux['terulet']/1000000);
	$egyenertek=$npc_egyenertekek[$osztaly][$terulet]/100*mt_rand(70,130);
	//
	for($i=1;$i<=6;$i++) $random[$i]=mt_rand(1,100);
	$Srandom=0;for($i=1;$i<=6;$i++) $Srandom+=$random[$i];
	for($i=1;$i<=6;$i++) $random[$i]/=$Srandom;
	for($i=1;$i<=5;$i++) $random5[$i]=mt_rand(1,100);
	$Srandom5=0;for($i=1;$i<=5;$i++) $Srandom5+=$random5[$i];
	for($i=1;$i<=5;$i++) $random5[$i]/=$Srandom5;
	//
	unset($megoszlas);
	foreach($npc_megoszlas[$terulet] as $hajo_id => $ezrelek) {
		if ($hajo_id==1) {
			for($i=1;$i<=5;$i++) $megoszlas[200+$i]+=$ezrelek*$random5[$i]*2;
		}
		elseif ($hajo_id==2) {
			for($i=1;$i<=6;$i++) $megoszlas[206+$i]+=$ezrelek*$random[$i]*4;
		} elseif ($hajo_id==3) {
			for($i=1;$i<=6;$i++) $megoszlas[212+$i]+=$ezrelek*$random[$i]*3;
		} else $megoszlas[$hajo_id]+=$ezrelek;
	}
	$S=0;foreach($megoszlas as $x) $S+=$x;
	//
	foreach($megoszlas as $hajo_id => $ezrelek) {
		$hajo_darabok[$hajo_id]=round($egyenertek/$S*$ezrelek/$hajo_arak[$hajo_id]);
	}
	//
	uj_npc_flottat_felrak($aux,$hajo_darabok);
	//
	echo $osztaly;
	echo ','.$terulet;
	echo ','.$egyenertek;
	echo '<br />';
}


?>