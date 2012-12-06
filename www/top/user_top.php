<?
include('../csatlak.php');
$ismert=0;$uid=false;
if (!empty($_COOKIE['uid'])) {
	$suti_uid=(int)(substr($_COOKIE['uid'],32));
	$suti_session_so=substr($_COOKIE['uid'],0,32);
	$r=mysql_query('select * from userek where id='.$suti_uid.' and kitiltva=0 and inaktiv=0');
	$aux=mysql_fetch_array($r);
	if ($aux['session_so']==$suti_session_so) {
		if (time()<strtotime($aux['session_ervenyesseg'])) {
			$adataim=$aux;
			$uid=$adataim['id'];
			$ismert=1;
		}
	}
}

$_REQUEST['u']=(int)$_REQUEST['u'];


$kiszemelt_user=mysql2row('select * from userek where id='.$_REQUEST['u']);
if (!$kiszemelt_user) exit;
if ($kiszemelt_user['karrier']==3 and $kiszemelt_user['speci']==3) if ($kiszemelt_user['id']!=$uid) exit;


function imagettftextbg_right($image,$size,$angle,$x,$y,$color,$fontfile,$text,$bg_col) {
	$b=imagettfbbox($size,$angle,$fontfile,$text);
	$x=$x-$b[2]+$b[6];
	imagefilledrectangle($image,$x+$b[6]+1,$y+$b[7]+1,$x+$b[2]-1,$y+$b[3],$bg_col);
	imagettftext($image,$size,$angle,$x,$y,$color,$fontfile,$text);
}

$maxx=500;$maxy=200;
$margins=array(10,50,30,10);
$kep=imagecreatetruecolor($margins[3]+$maxx+$margins[1],$margins[0]+$maxy+$margins[2]+($uid==1?50:0));
$piros=imagecolorallocate($kep,255,0,0);
$zold=imagecolorallocate($kep,0,200,0);
$kek=imagecolorallocate($kep,0,160,255);
$hatter=imagecolorallocate($kep,42,43,45);
$eros_csik=imagecolorallocate($kep,100,100,100);
for($h=0;$h<24;$h++) $akt_szin[$h]=imagecolorallocate($kep,255,11*$h,0);
for($h=24;$h<168;$h++) $akt_szin[$h]=imagecolorallocate($kep,255-($h-24)/144*255,255,0);
for($h=168;$h<336;$h++) $akt_szin[$h]=imagecolorallocate($kep,0,255-($h-168)/168*255,($h-168)/168*255);
for($h=336;$h<400;$h++) $akt_szin[$h]=imagecolorallocate($kep,0,0,255);
imagefill($kep,0,0,$hatter);


$max_idopont=mysql2num('select max(timestampdiff(day,"'.substr($szerver_indulasa,0,10).'",hi.mikor)+1)
from '.$database_mmog_nemlog.'.hist_idopontok hi, '.$database_mmog_nemlog.'.hist_userek hu
where hu.idopont=hi.id');

imagerectangle($kep
	,$margins[3],$margins[0]
	,$margins[3]+$maxx,$margins[0]+$maxy
	,$eros_csik);
$r=mysql_query('select timestampdiff(day,"'.substr($szerver_indulasa,0,10).'",mikor)+1 as id,mikor,weekday(mikor)+1 from '.$database_mmog_nemlog.'.hist_idopontok order by id');
$n=0;$het=0;
while($aux=mysql_fetch_array($r)) {
	if ($n!=$aux[2] and $aux[2]==5) {
		$het++;
		imageline($kep
			,$margins[3]+($aux[0]-1)/($max_idopont-1)*$maxx,$margins[0]
			,$margins[3]+($aux[0]-1)/($max_idopont-1)*$maxx,$margins[0]+$maxy
			,$eros_csik);
		imagettftext($kep,8,0
			,$margins[3]+($aux[0]-1)/($max_idopont-1)*$maxx,$margins[0]+$maxy+24-12*($het%2)
			,$eros_csik,'../img/arial.ttf',substr($aux[1],5,5));
	}
	$n=$aux[2];
}

for($rank=1;$rank<=10;$rank++) imageline($kep,$margins[3],$margins[0]+log10($rank)/3*$maxy,$margins[3]+$maxx,$margins[0]+log10($rank)/3*$maxy,$eros_csik);
for($rank=10;$rank<=100;$rank+=10) imageline($kep,$margins[3],$margins[0]+log10($rank)/3*$maxy,$margins[3]+$maxx,$margins[0]+log10($rank)/3*$maxy,$eros_csik);
for($rank=100;$rank<=1000;$rank+=100) imageline($kep,$margins[3],$margins[0]+log10($rank)/3*$maxy,$margins[3]+$maxx,$margins[0]+log10($rank)/3*$maxy,$eros_csik);

$rank=1;imagettftext($kep,8,0,$margins[3]+$maxx+5,$margins[0]+log10($rank)/3*$maxy+3,$eros_csik,'../img/arial.ttf',$rank);
$rank=3;imagettftext($kep,8,0,$margins[3]+$maxx+5,$margins[0]+log10($rank)/3*$maxy+3,$eros_csik,'../img/arial.ttf',$rank);
$rank=10;imagettftext($kep,8,0,$margins[3]+$maxx+5,$margins[0]+log10($rank)/3*$maxy+3,$eros_csik,'../img/arial.ttf',$rank);
$rank=30;imagettftext($kep,8,0,$margins[3]+$maxx+5,$margins[0]+log10($rank)/3*$maxy+3,$eros_csik,'../img/arial.ttf',$rank);
$rank=100;imagettftext($kep,8,0,$margins[3]+$maxx+5,$margins[0]+log10($rank)/3*$maxy+3,$eros_csik,'../img/arial.ttf',$rank);
$rank=300;imagettftext($kep,8,0,$margins[3]+$maxx+5,$margins[0]+log10($rank)/3*$maxy+3,$eros_csik,'../img/arial.ttf',$rank);
$rank=1000;imagettftext($kep,8,0,$margins[3]+$maxx+5,$margins[0]+log10($rank)/3*$maxy+3,$eros_csik,'../img/arial.ttf',$rank);

$r=mysql_query('select timestampdiff(day,"'.substr($szerver_indulasa,0,10).'",hi.mikor)+1 as seged_idopont,hu.helyezes,timestampdiff(hour,hu.uccso_akt,hi.mikor)
from '.$database_mmog_nemlog.'.hist_idopontok hi
inner join '.$database_mmog_nemlog.'.hist_userek hu on hi.id=hu.idopont
where hu.id='.$_REQUEST['u']);

unset($elozo_aux);
while($aux=mysql_fetch_array($r)) {
	if (isset($elozo_aux) and $elozo_aux[1]>0 and $aux[1]>0) {
		imageline($kep
			,$margins[3]+($elozo_aux[0]-1)/($max_idopont-1)*$maxx
			,$margins[0]+log10($elozo_aux[1])/3*$maxy
			,$margins[3]+($aux[0]-1)/($max_idopont-1)*$maxx
			,$margins[0]+log10($aux[1])/3*$maxy
			,$piros);
		if ($uid==1)
		imagefilledrectangle($kep
			,$margins[3]+($elozo_aux[0]-1)/($max_idopont-1)*$maxx
			,$margins[0]+$margins[0]+$maxy+$margins[2]
			,$margins[3]+($aux[0]-1)/($max_idopont-1)*$maxx
			,$margins[0]+$maxy+$margins[2]+50-10
			,$akt_szin[$aux[2]]);
	}
	$elozo_aux=$aux;
}

imagettftextbg_right($kep,8,0,$margins[3]+$maxx,$margins[0]+log10($elozo_aux[1])/3*$maxy+13,$piros,'../img/arial.ttf',$elozo_aux[1],$hatter);

mysql_close($mysql_csatlakozas);
header('Content-type: image/png');imagepng($kep);
?>