<?
include('csatlak.php');
include('ujkuki.php');

$felbontas=(int)$_REQUEST['felbontas'];
if ($felbontas<0) $felbontas=0;
if ($felbontas>2) $felbontas=2;


$maxx=640;$kezdo_x=15;$szelesseg=576;$vonal_maxx=587;
$maxy=450;$teteje=40;$alja=350;
$forgalom_alja=430;$forgalom_teteje=390;
$kep=imagecreate($maxx,$maxy);

$idotav=72;
$forg_oszlop_szel=2;
$arf_oszlop_szel=2;

switch($felbontas) {
	case 1:
		$felbontas_cim=$lang[$lang_lang]['tozsde_graf.php']['harmadnap'];$gyertya_felbontas=2;
		$egyseg_mpben=3600*8;$egyseg_neve='hour';$egyseg_szorzo=8;
		if (date('H')>=16) $most=date('Y-m-d').' 16:00:00';
		elseif (date('H')>=8) $most=date('Y-m-d').' 08:00:00';
		else $most=date('Y-m-d').' 00:00:00';
	break;
	case 2:
		$felbontas_cim=$lang[$lang_lang]['tozsde_graf.php']['nap'];$gyertya_felbontas=3;
		$egyseg_mpben=3600*24;$egyseg_neve='day';$egyseg_szorzo=1;$most=date('Y-m-d').' 00:00:00';
	break;
	default:
		$felbontas_cim=$lang[$lang_lang]['tozsde_graf.php']['óra'];$gyertya_felbontas=1;
		$egyseg_mpben=3600;$egyseg_neve='hour';$egyseg_szorzo=1;$most=date('Y-m-d H').':00:00';
	break;
}

$idotav_mpben=$idotav*$egyseg_mpben;

$piros=imagecolorallocate($kep,255,0,0);
$zold=imagecolorallocate($kep,0,255,0);
$kek=imagecolorallocate($kep,0,0,255);

$feher=imagecolorallocate($kep,0,0,0);
$halvany_szurke=imagecolorallocate($kep,42,43,45);
$szurke=imagecolorallocate($kep,80,80,80);
$minmax_tuske=imagecolorallocate($kep,160,160,160);
$sotet_szurke=imagecolorallocate($kep,200,200,200);
$fekete=imagecolorallocate($kep,255,255,255);
$felfele_szin=$fekete;$lefele_szin=$fekete;$felfele_ures=1;
imagefill($kep,0,0,$halvany_szurke);

if (premium_szint()<2) {
	imagettftext($kep,10,0,60,100,$fekete,'img/arial.ttf',$lang[$lang_lang]['tozsde_graf.php']['Ez egy prémium szolgáltatás, elő kell rá fizetned, ha használni szeretnéd.']);
	header('Content-type: image/gif');imagegif($kep);
	kilep();
}

if (isset($_REQUEST['regio'])) {
	$regio=(int)$_REQUEST['regio'];
} else {
	$regio=1;
}

$termek=(int)$_REQUEST['termek'];
$res=mysql_query('select nev'.$lang__lang.',tozsdezheto from eroforrasok where id='.$termek);
$aux=mysql_fetch_array($res);$nev=$aux[0];

if ($aux[1]==0) {
	imagettftext($kep,12,0,160,100,$fekete,'img/arial.ttf',$lang[$lang_lang]['tozsde_graf.php']['Válassz ki egy piacot, hogy láthasd az idősorát.']);
	header('Content-type: image/gif');imagegif($kep);
	kilep();
}



$most_stamp=strtotime($most);
$eleje_stamp=$most_stamp-$idotav_mpben;
$eleje=date('Y-m-d H:i:s',$eleje_stamp);

$er=mysql_query('
select round(timestampdiff('.$egyseg_neve.',mikor,"'.$most.'")/'.$egyseg_szorzo.')-1 as milyen_reg,forgalom,min_ar as min_arf,max_ar as max_arf,nyito_ar as nyito_arf,zaro_ar as zaro_arf,min5_ar as min5_arf,max5_ar as max5_arf
from '.$database_mmog_nemlog.'.tozsdei_gyertyak
where termek_id='.$termek.' and felbontas='.$gyertya_felbontas.' and regio='.$regio.'
and timestampdiff('.$egyseg_neve.',mikor,"'.$most.'")<='.$idotav.'*'.$egyseg_szorzo.' and timestampdiff('.$egyseg_neve.',mikor,"'.$most.'")>='.$egyseg_szorzo.'
order by mikor
');
$max_arfolyam=0;$max_forgalom=0;
$hanyadik=0;$hany_adat_van=mysql_num_rows($er);
while($aux=mysql_fetch_array($er)) {
	$hanyadik++;
	if ($aux['nyito_arf']>$max_arfolyam) $max_arfolyam=$aux['nyito_arf'];
	if ($aux['zaro_arf']>$max_arfolyam) $max_arfolyam=$aux['zaro_arf'];
	//if ($aux['max_arf']>$max_arfolyam) $max_arfolyam=$aux['max_arf'];
	if ($aux['forgalom']>$max_forgalom) $max_forgalom=$aux['forgalom'];
	$adatok[]=$aux;
}
if ($max_forgalom==0) {
	imagettftext($kep,12,0,160,100,$fekete,'img/arial.ttf',$lang[$lang_lang]['tozsde_graf.php']['Nem volt forgalom ezen a piacon.']);
	header('Content-type: image/gif');imagegif($kep);
	kilep();
}


$arfolyam_nagysagrend=ceil(log10($max_arfolyam))-1;
$arfolyam_nagysagrend10=pow(10,$arfolyam_nagysagrend);
//$maxmax_arfolyam=ceil($max_arfolyam/$arfolyam_nagysagrend10)*$arfolyam_nagysagrend10;
$maxmax_arfolyam=ceil($max_arfolyam/$arfolyam_nagysagrend10);
if ($maxmax_arfolyam>1 && $maxmax_arfolyam<=2) $maxmax_arfolyam=2;
if ($maxmax_arfolyam>2 && $maxmax_arfolyam<=5) $maxmax_arfolyam=5;
if ($maxmax_arfolyam>5 && $maxmax_arfolyam<=10) $maxmax_arfolyam=10;
$maxmax_arfolyam*=$arfolyam_nagysagrend10;

if ($max_forgalom>10) $forgalom_nagysagrend=ceil(log10($max_forgalom))-1;else $forgalom_nagysagrend=1;
$forgalom_nagysagrend10=pow(10,$forgalom_nagysagrend);
$maxmax_forgalom=ceil($max_forgalom/$forgalom_nagysagrend10)*$forgalom_nagysagrend10;

$adatszam=count($adatok);

for($i=0;$i<=2;$i++) imageline($kep,$kezdo_x-8,round($forgalom_alja-$i/2*($forgalom_alja-$forgalom_teteje)),$vonal_maxx,round($forgalom_alja-$i/2*($forgalom_alja-$forgalom_teteje)),$szurke);
for($i=0;$i<=10;$i++) imageline($kep,$kezdo_x-8,round($alja-$i/10*($alja-$teteje)),$vonal_maxx,round($alja-$i/10*($alja-$teteje)),$szurke);


for($i=-1;$i<=$idotav;$i++) {
	$x=round(($idotav-$i)/$idotav*$szelesseg)+$kezdo_x;
	if ($i<$idotav) {
		imageline($kep,$x-14,$alja,$x-14,$alja+2,$szurke);
		imageline($kep,$x-14,$forgalom_alja,$x-14,$forgalom_alja+2,$szurke);
	}
	switch($felbontas) {
		case 1:
			$idojelzes=date('H',$most_stamp-$idotav_mpben/$idotav*$i-$egyseg_mpben);
			if ($idojelzes==0) {
				if ($i<$idotav) {
					imageline($kep,$x-14,$teteje,$x-14,$alja-1,$szurke);
					imageline($kep,$x-14,$forgalom_teteje,$x-14,$forgalom_alja-1,$szurke);
				}
				$napjelzes=date('d',$most_stamp-$idotav_mpben/$idotav*$i-$egyseg_mpben);
				if ($i>=1) {
					imagettftext($kep,7,0,$x-18+12,$alja+12,$sotet_szurke,'img/arial.ttf',$napjelzes);
					imagettftext($kep,7,0,$x-18+12,$forgalom_alja+12,$sotet_szurke,'img/arial.ttf',$napjelzes);
				}
			}
		break;
		case 2:
			$idojelzes=date('w',$most_stamp-$idotav_mpben/$idotav*$i-$egyseg_mpben);
			if ($idojelzes==1) {
				if ($i<$idotav) {
					imageline($kep,$x-14,$teteje,$x-14,$alja-1,$szurke);
					imageline($kep,$x-14,$forgalom_teteje,$x-14,$forgalom_alja-1,$szurke);
				}
				if ($i>=0) if ($i<$idotav) {
					$napjelzes=date('d',$most_stamp-$idotav_mpben/$idotav*$i-$egyseg_mpben);
					imagettftext($kep,7,0,$x-18+3,$alja+12,$sotet_szurke,'img/arial.ttf',$napjelzes);
					imagettftext($kep,7,0,$x-18+3,$forgalom_alja+12,$sotet_szurke,'img/arial.ttf',$napjelzes);
				}
			}
		break;
		default:
			$idojelzes=date('H',$most_stamp-$idotav_mpben/$idotav*$i-$egyseg_mpben);
			if ($idojelzes==0) {
				if ($i<$idotav) {
					imageline($kep,$x-14,$teteje,$x-14,$alja-1,$szurke);
					imageline($kep,$x-14,$forgalom_teteje,$x-14,$forgalom_alja-1,$szurke);
				}
			}
			if ($idojelzes%3==0) if ($i<$idotav) {
				imagettftext($kep,7,0,$x-18,$alja+12,$sotet_szurke,'img/arial.ttf',$idojelzes);
				imagettftext($kep,7,0,$x-18,$forgalom_alja+12,$sotet_szurke,'img/arial.ttf',$idojelzes);
			}
		break;
	}
}

for($i=0;$i<count($adatok);$i++) {
	$x=round(($idotav-$adatok[$i]['milyen_reg'])/$idotav*$szelesseg)+$kezdo_x;
	if ($i>0) {
		$elozo_x=round(($idotav-$adatok[$i-1]['milyen_reg'])/$idotav*$szelesseg)+$kezdo_x;
		imageline($kep,
		$elozo_x-10,round($alja-($adatok[$i-1]['nyito_arf']+$adatok[$i-1]['zaro_arf'])/2/$maxmax_arfolyam*($alja-$teteje)),
		$x-10,round($alja-($adatok[$i]['nyito_arf']+$adatok[$i]['zaro_arf'])/2/$maxmax_arfolyam*($alja-$teteje)),
		$fekete);//elozo zaro, mai nyitoval
	}
	//forgalom
	imagefilledrectangle($kep,
	$x-10-$forg_oszlop_szel,round($forgalom_alja-$adatok[$i]['forgalom']/$maxmax_forgalom*($forgalom_alja-$forgalom_teteje)),
	$x-10+$forg_oszlop_szel,$forgalom_alja,
	$fekete);
}


//nyito ar
$i=0;
$ar=number_format(round(($adatok[$i]['nyito_arf']+$adatok[$i]['zaro_arf'])/2),0,',',' ');
$box=imagettfbbox(9,0,'img/arial.ttf',$ar);
$x=round(($idotav-$adatok[$i]['milyen_reg'])/$idotav*$szelesseg)+$kezdo_x-5;
$y=round($alja-($adatok[$i]['nyito_arf']+$adatok[$i]['zaro_arf'])/2/$maxmax_arfolyam*($alja-$teteje))-5;
//imagefilledrectangle($kep,$x+$box[6],$y+$box[7],$x+$box[2],$y+$box[3]+1,$halvany_szurke);
imagettftext($kep,9,0,$x,$y,$sotet_szurke,'img/arial.ttf',$ar);
//zaro ar
$i=count($adatok)-1;
$ar=number_format(round(($adatok[$i]['nyito_arf']+$adatok[$i]['zaro_arf'])/2),0,',',' ');
$box=imagettfbbox(9,0,'img/arial.ttf',$ar);
$x=round(($idotav-$adatok[$i]['milyen_reg'])/$idotav*$szelesseg)+$kezdo_x-5-$box[2]+$box[6];
$y=round($alja-($adatok[$i]['nyito_arf']+$adatok[$i]['zaro_arf'])/2/$maxmax_arfolyam*($alja-$teteje))-5;
//imagefilledrectangle($kep,$x+$box[6],$y+$box[7],$x+$box[2],$y+$box[3]+1,$halvany_szurke);
imagettftext($kep,9,0,$x,$y,$sotet_szurke,'img/arial.ttf',$ar);

//forgalom felirat
imagettftext($kep,9,0,$vonal_maxx+5,round($forgalom_alja+4),$sotet_szurke,'img/arial.ttf',0);
for($i=1;$i<=2;$i++) {
	if ($maxmax_forgalom>10000000000) $szam=number_format($maxmax_forgalom/2*$i/1000000000,0,',',' ').'G';
	else {
		if (($maxmax_forgalom/2%1000000000)==0) $szam=number_format($maxmax_forgalom/2*$i/1000000000,0,',',' ').'G';
		else $szam=(($maxmax_forgalom/2%1000000)==0?(number_format($maxmax_forgalom/2*$i/1000000,0,',',' ').'M'):(($maxmax_forgalom/2%1000)==0?(number_format($maxmax_forgalom/2*$i/1000,0,',',' ').'K'):(number_format($maxmax_forgalom/2*$i,0,',',' '))));
	}
	imagettftext($kep,9,0,$vonal_maxx+5,round($forgalom_alja-$i/2*($forgalom_alja-$forgalom_teteje)+4),$sotet_szurke,'img/arial.ttf',$szam);
}
//arfolyam felirat
for($i=0;$i<=10;$i++) {
	imagettftext($kep,9,0,$vonal_maxx+5,round($alja-$i/10*($alja-$teteje)+4),$sotet_szurke,'img/arial.ttf',($maxmax_arfolyam/10*$i)%10==0?number_format($maxmax_arfolyam/10*$i,0,',',' '):number_format($maxmax_arfolyam/10*$i,1,',',' '));
}




$honaprov[1]='jan';$honaprov_en[1]='Jan';
$honaprov[2]='febr';$honaprov_en[2]='Feb';
$honaprov[3]='márc';$honaprov_en[3]='Mar';
$honaprov[4]='ápr';$honaprov_en[4]='Apr';
$honaprov[5]='máj';$honaprov_en[5]='May';
$honaprov[6]='jún';$honaprov_en[6]='Jun';
$honaprov[7]='júl';$honaprov_en[7]='Jul';
$honaprov[8]='aug';$honaprov_en[8]='Aug';
$honaprov[9]='szept';$honaprov_en[9]='Sep';
$honaprov[10]='okt';$honaprov_en[10]='Oct';
$honaprov[11]='nov';$honaprov_en[11]='Nov';
$honaprov[12]='dec';$honaprov_en[12]='Dec';

if ($lang_lang=='en') {
	if ($felbontas==2) {
		$eleje_str=date('j',$eleje_stamp).' '.$honaprov_en[date('n',$eleje_stamp)];
		$most_str=date('j',$most_stamp-1).' '.$honaprov_en[date('n',$most_stamp-1)];
	} else {
		$eleje_str=date('j',$eleje_stamp).' '.$honaprov_en[date('n',$eleje_stamp)].' '.date('G',$eleje_stamp).' h';
		$most_str=date('j',$most_stamp).' '.$honaprov_en[date('n',$most_stamp)].' '.date('G',$most_stamp).' h';
	}
} else {
	if ($felbontas==2) {
		$eleje_str=$honaprov[date('n',$eleje_stamp)].'. '.date('j',$eleje_stamp).'.';
		$most_str=$honaprov[date('n',$most_stamp-1)].'. '.date('j',$most_stamp-1).'.';
	} else {
		$eleje_str=$honaprov[date('n',$eleje_stamp)].'. '.date('j',$eleje_stamp).'. '.date('G',$eleje_stamp).' óra';
		$most_str=$honaprov[date('n',$most_stamp)].'. '.date('j',$most_stamp).'. '.date('G',$most_stamp).' óra';
	}
}
imagefilledrectangle($kep,0,0,$maxx,20,$halvany_szurke);
imagettftext($kep,10,0,3,14,$fekete,'img/arial.ttf',$nev.' ('.$eleje_str.' - '.$most_str.') '.$felbontas_cim);

insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);
header('Content-type: image/gif');imagegif($kep);
?>