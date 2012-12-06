<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/plain; charset=utf-8');

if (premium_szint()==0) kilep($lang[$lang_lang]['kisphpk']['Prémium szolgáltatás.']);

$_REQUEST['bolygo']=(int)$_REQUEST['bolygo'];

$_REQUEST['osztaly']=(int)$_REQUEST['osztaly'];
if ($_REQUEST['osztaly']<1) $_REQUEST['osztaly']=1;
if ($_REQUEST['osztaly']>5) $_REQUEST['osztaly']=5;

$_REQUEST['terulet']=round(sanitint($_REQUEST['terulet'])/100000);
if ($_REQUEST['terulet']<20) $_REQUEST['terulet']=20;
if ($_REQUEST['terulet']>100) $_REQUEST['terulet']=100;

/*
$_REQUEST['beepitettseg']=(int)$_REQUEST['beepitettseg'];
if ($_REQUEST['beepitettseg']<0) $_REQUEST['beepitettseg']=0;
if ($_REQUEST['beepitettseg']>99) $_REQUEST['beepitettseg']=99;//100-nal div by zero lenne
$_REQUEST['terulet_szabad']=$_REQUEST['terulet']*(100-$_REQUEST['beepitettseg'])/100;
*/
$_REQUEST['terulet_szabad']=$_REQUEST['terulet'];

$_REQUEST['napok_szama']=(int)$_REQUEST['napok_szama'];
if ($_REQUEST['napok_szama']<1) $_REQUEST['napok_szama']=1;
if ($_REQUEST['napok_szama']>10) $_REQUEST['napok_szama']=10;
$_REQUEST['korok_szama']=96*$_REQUEST['napok_szama'];

$input_fajok=explode(',',$_REQUEST['input_fajok']);
for($i=0;$i<count($input_fajok)-1;$i++) {
	$faj=explode(':',$input_fajok[$i]);
	$faj_idk[]=$faj[0];
	$rev_faj_idx[$faj[0]]=$i;
	$darabszamok[]=sanitint($faj[1]);
	for($j=0;$j<count($input_fajok)-1;$j++) $beta_matrix[$i][$j]=0;
	$beta_null[$i]=0;
}
$er=mysql_query('select faj_id,masik_faj_id,coef from faj_faj where faj_id in ('.implode(',',$faj_idk).') and masik_faj_id in (0,'.implode(',',$faj_idk).') order by faj_id,masik_faj_id') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	if ($aux[1]==0) $beta_null[$rev_faj_idx[$aux[0]]]=$aux[2];
	else $beta_matrix[$rev_faj_idx[$aux[0]]][$rev_faj_idx[$aux[1]]]=$aux[2];
}
$er=mysql_query('select eroforras_id,db from bolygo_faj_celszam where osztaly='.$_REQUEST['osztaly'].' and terulet='.$_REQUEST['terulet']) or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) $celszamok[$rev_faj_idx[$aux[0]]]=$aux[1];

$input_gyarak=explode(',',$_REQUEST['input_gyarak']);
for($i=0;$i<count($input_gyarak)-1;$i++) {
	$gyar=explode(':',$input_gyarak[$i]);
	$gyar_idk[]=$gyar[0];
	$rev_gyar_idx[$gyar[0]]=$i;
	$gyarszamok[]=$gyar[1];
}
$er=mysql_query('select gyar_id,eroforras_id,io from gyar_eroforras where gyar_id in ('.implode(',',$gyar_idk).') and eroforras_id<55 and io<0') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	$gyar_inputok[$rev_gyar_idx[$aux[0]]]=$aux[1];
	$gyar_input_mennyisegek[$rev_gyar_idx[$aux[0]]]=-$aux[2];//abszolut ertek
}
$er=mysql_query('select gyar_id,eroforras_id,io from gyar_eroforras where gyar_id in ('.implode(',',$gyar_idk).') and io>0') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	$gyar_outputok[$rev_gyar_idx[$aux[0]]]=$aux[1];
	$gyar_output_mennyisegek[$rev_gyar_idx[$aux[0]]]=$aux[2];
}



$faj_magassag=38;

$maxx=400;$maxy=$faj_magassag*count($darabszamok);
$kep=imagecreate($maxx+1,$maxy+1);
$feher=imagecolorallocate($kep,255,255,255);
$halvany_szurke=imagecolorallocate($kep,42,43,45);
$szurke=imagecolorallocate($kep,80,80,80);
$szurke2=imagecolorallocate($kep,60,60,60);
imagefill($kep,0,0,$halvany_szurke);
$szin[1]=imagecolorallocate($kep,180,42,4);
$szin[2]=imagecolorallocate($kep,252,130,4);
$szin[3]=imagecolorallocate($kep,252,254,4);
$szin[4]=imagecolorallocate($kep,156,218,4);
$szin[5]=imagecolorallocate($kep,52,182,4);

for($N=0;$N<=$_REQUEST['korok_szama'];$N+=24) {
	imageline($kep,
	round($N/$_REQUEST['korok_szama']*$maxx),0,
	round($N/$_REQUEST['korok_szama']*$maxx),$maxy,
	($N%96==0)?$szurke:$szurke2);
}
for($i=0;$i<=count($darabszamok);$i++) {
	imageline($kep,
	0,round($maxy-$faj_magassag*$i),
	$maxx,round($maxy-$faj_magassag*$i),
	$szurke);
	imageline($kep,
	1,round($maxy-$faj_magassag*$i-1*20),
	$maxx-1,round($maxy-$faj_magassag*$i-1*20),
	$szurke2);
	//imagettftext($kep,7,0,5,round($maxy-$faj_magassag*$i-1*20),$szurke,'img/arial.ttf',$i);
}
/*for($i=0;$i<count($gyarszamok);$i++) {
	$input_faj_idx=$rev_faj_idx[$gyar_inputok[$i]];
	imagettftext($kep,7,0,5,round($faj_magassag*$input_faj_idx+10),$feher,'img/arial.ttf',$gyarszamok[$i].' ['.$gyar_input_mennyisegek[$i].' -&gt; '.$gyar_output_mennyisegek[$i].']');
}*/

//szim
$ef_kitermelesek[56]=0;
$ef_kitermelesek[74]=0;
$ef_kitermelesek[64]=0;
$ef_kitermelesek[59]=0;
for($i=0;$i<count($darabszamok);$i++) $elozo_darabszamok[$i]=$darabszamok[$i];
for($N=0;$N<$_REQUEST['korok_szama'];$N++) {
	//ipar
	for($i=0;$i<count($gyarszamok);$i++) {
		$input_faj_idx=$rev_faj_idx[$gyar_inputok[$i]];
		$input_mennyiseg=$darabszamok[$input_faj_idx];
		$effektiv_darabszam=$gyarszamok[$i];
		if ($input_mennyiseg<$gyarszamok[$i]*$gyar_input_mennyisegek[$i]) $effektiv_darabszam=$input_mennyiseg/$gyar_input_mennyisegek[$i];
		$ef_kitermelesek[$gyar_outputok[$i]]+=round($effektiv_darabszam*$gyar_output_mennyisegek[$i]);
		$darabszamok[$input_faj_idx]-=$effektiv_darabszam*$gyar_input_mennyisegek[$i];
	}
	//szaporodas
	for($i=0;$i<count($darabszamok);$i++) {
		$fitness[$i]=$beta_null[$i];
		for($j=0;$j<count($darabszamok);$j++) $fitness[$i]+=$beta_matrix[$i][$j]/$_REQUEST['terulet_szabad']*$darabszamok[$j];
		$fitness[$i]/=1000000;
	}
	for($i=0;$i<count($darabszamok);$i++) {
		$delta=$fitness[$i]*$darabszamok[$i]-($darabszamok[$i]<1000?(1000-$darabszamok[$i]):0);
		$darabszamok[$i]=round($darabszamok[$i]+$delta);
		if ($darabszamok[$i]<0) $darabszamok[$i]=0;
	}
	//grafikon
	for($i=0;$i<count($darabszamok);$i++) {
		$y1=$elozo_darabszamok[$i]/$celszamok[$i]*20;
		$y2=$darabszamok[$i]/$celszamok[$i]*20;
		//
		$szinszam1=5;
		if ($elozo_darabszamok[$i]<0.9*$celszamok[$i]) $szinszam1=4;
		if ($elozo_darabszamok[$i]<0.67*$celszamok[$i]) $szinszam1=3;
		if ($elozo_darabszamok[$i]<0.50*$celszamok[$i]) $szinszam1=2;
		if ($elozo_darabszamok[$i]<0.33*$celszamok[$i]) $szinszam1=1;
		$szinszam2=5;
		if ($darabszamok[$i]<0.9*$celszamok[$i]) $szinszam2=4;
		if ($darabszamok[$i]<0.67*$celszamok[$i]) $szinszam2=3;
		if ($darabszamok[$i]<0.50*$celszamok[$i]) $szinszam2=2;
		if ($darabszamok[$i]<0.33*$celszamok[$i]) $szinszam2=1;
		//
		if ($y1>$faj_magassag-1) $y1=$faj_magassag-1;
		if ($y2>$faj_magassag-1) $y2=$faj_magassag-1;
		$yk=($y1+$y2)/2;
		imageline($kep,
		round($N/$_REQUEST['korok_szama']*$maxx),round($faj_magassag*($i+1)-$y1),
		round(($N+0.5)/$_REQUEST['korok_szama']*$maxx),round($faj_magassag*($i+1)-$yk),
		$szin[$szinszam1]);
		imageline($kep,
		round(($N+0.5)/$_REQUEST['korok_szama']*$maxx),round($faj_magassag*($i+1)-$yk),
		round(($N+1)/$_REQUEST['korok_szama']*$maxx),round($faj_magassag*($i+1)-$y2),
		$szin[$szinszam2]);
	}
	//
	for($i=0;$i<count($darabszamok);$i++) $elozo_darabszamok[$i]=$darabszamok[$i];
}

$crc=randomgen(32);
imagegif($kep,'img/okoszim/g'.$_REQUEST['bolygo'].$crc.'.gif',9);

?>
/*{"kep":"<?
echo $crc;
?>","bolygo":<?
echo $_REQUEST['bolygo'];
?>,"fajok":[<?
for($i=0;$i<count($darabszamok);$i++) {
	if ($i>0) echo ',';
	echo '['.$faj_idk[$i].','.round($darabszamok[$i]).']';
}
?>],"kiterm":[<?
echo $ef_kitermelesek[56].','.$ef_kitermelesek[74].','.$ef_kitermelesek[64].','.$ef_kitermelesek[59];
?>]}*/
<?

kilep();
?>