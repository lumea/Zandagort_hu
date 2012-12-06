<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$res=mysql_query('select * from bolygok where id='.$_REQUEST['id'].' and letezik=1') or hiba(__FILE__,__LINE__,mysql_error());
$bolygo=mysql_fetch_array($res);


if ($bolygo['id']) {


$szereped='tulaj';if ($bolygo['kezelo']==$uid) $szereped='kezelo';


$bolygo_tulaj=mysql2row('select * from userek where id='.$bolygo['tulaj']);
if ($bolygo['tulaj']!=$uid) {
	if ($bolygo_tulaj['karrier']==3 && $bolygo_tulaj['speci']==3) {//rejtozo
		$bolygo_tulaj['nev']='-';
		$bolygo['tulaj']=0;
		$bolygo['tulaj_szov']=0;
		$bolygo['szovetseg']=0;
		$bolygo['nev']=$bolygo['kulso_nev'];
		$bolygo['alapbol_regisztralhato']=0;
		$bolygo['vedelmi_bonusz']=0;
	}
}


?>
/*{"letezik":1,"id":<?=$bolygo['id'];?>,"te":<?=$uid?>,"tied":<?=($bolygo[$szereped]==$uid)?1:0;?>,"nev":"<?=addslashes($bolygo['nev']);?>","esc_nev":"<?=addslashes(addslashes(htmlspecialchars($bolygo['nev'])));?>","tulaj":"<?
if ($bolygo_tulaj) {
	echo addslashes($bolygo_tulaj['nev']);
} else echo '-';
?>","tulaj_id":<?=$bolygo['tulaj'];?>,"tulaj_szov":<?=$bolygo['tulaj_szov'];

?>,"vegjatek":<?
echo $vegjatek;

?>,"iparag_jelzok":<?
echo $user_beallitasok['iparag_jelzok'];
?>,"gyar_ikonok":<?
echo $user_beallitasok['gyar_ikonok'];

?>,"befagy_utemezett_szallitas":<?
echo $bolygo['befagy_utemezett_szallitas'];
?>,"befagy_utemezett_tozsde":<?
echo $bolygo['befagy_utemezett_tozsde'];

?>,"premium":<?
echo premium_szint();
?>,"x":<?
echo $bolygo['x'];
?>,"y":<?
echo $bolygo['y'];
?>,"hexa_x":<?
echo $bolygo['hexa_x'];
?>,"hexa_y":<?
echo $bolygo['hexa_y'];
?>,"bolygokepmeret":<?
echo round($bolygo['terulet']/2000000);

?>,"regio":"<?
$res2=mysql_query('select nev from regiok where id='.$bolygo['regio']);
$aux2=mysql_fetch_array($res2);
if ($aux2[0]) echo addslashes($aux2[0]);else echo '-';
?>","osztaly":<?
echo $bolygo['osztaly'];
?>,"terulet":<?
echo round($bolygo['terulet']/1000000);
?>,"terulet_foglalt":<?
echo $bolygo['terulet_beepitett'];
?>,"terulet_foglalt_effektiv":<?
echo $bolygo['terulet_beepitett_effektiv'];
?>,"kornyezeti_fejlettseg":<?
if ($bolygo['terraformaltsag']>0) echo round(10000/$bolygo['terraformaltsag']*10000);else echo 10000;
?>,"hold":<?
echo $bolygo['hold'];
?>,"alapbol_regisztralhato":<?
echo (int)$bolygo['alapbol_regisztralhato'];
?>,"random_regisztralhato":<?
echo (int)$bolygo['random_regisztralhato'];



?>,"vedelmi_bonusz":<?
echo $bolygo['vedelmi_bonusz'];
?>,"foszthato":<?
if ($bolygo['tulaj']>0) {
	if ($bolygo['vedelmi_bonusz']<1000) echo '1';
	else echo '0';
} else echo '-1';
?>,"moratorium":<?
$morat=round((strtotime($bolygo['moratorium_mikor_jar_le'])-time())/60);
if ($morat<=0) echo '0';else echo $morat;
?>,"szabot":<?
if ($bolygo_tulaj['uccso_szabotazs_mikor']>date('Y-m-d H:i:s',time()-3600*24*7)) echo '"'.date('Y-m-d H:i:s',strtotime($bolygo_tulaj['uccso_szabotazs_mikor'])+3600*24*7).'"';
else echo '"-"';


?>,"koltozheto":<?
$koltozheto=1;
if ($bolygo['tulaj']!=0) $koltozheto=0;
if ($bolygo['alapbol_regisztralhato']!=1) $koltozheto=0;
if ($adataim['techszint']>3) $koltozheto=0;

if ($koltozheto) {
	$bolygoim_szama=mysql2num('select count(1) from bolygok where tulaj='.$uid);
	if ($bolygoim_szama!=1) $koltozheto=0;
}
if ($koltozheto) {
	$er2=mysql_query('select * from bolygok where tulaj='.$uid);
	$sajat_bolygo=mysql_fetch_array($er2);
	if ($bolygo['osztaly']!=$sajat_bolygo['osztaly']) $koltozheto=0;
	if ($bolygo['terulet']!=$sajat_bolygo['terulet']) $koltozheto=0;
	if ($bolygo['hold']!=$sajat_bolygo['hold']) $koltozheto=0;
}
echo $koltozheto;




if ($bolygo[$szereped]==$uid) {//sajat

?>,"kezelo":"<?
$res2=mysql_query('select nev from userek where id='.$bolygo['kezelo']) or hiba(__FILE__,__LINE__,mysql_error());
$aux2=mysql_fetch_array($res2);
if ($aux2[0]) echo addslashes($aux2[0]);else echo '-';
?>","kezelo_id":<?
echo $bolygo['kezelo'];


$iparag_nevek=array('','Erőművek','Élelmiszeripar','Kitermelés','Feldolgozó ipar','Települések','Speciális épületek','Hadiipar');

$er=mysql_query('select pop from bolygo_ember where bolygo_id='.$bolygo['id']);
$aux=mysql_fetch_array($er);$bolygo_pop=$aux[0];

//$kemkozpontszam=0;
$teleportszam=0;
$kocsmaszam=0;

//prioritas = talan sosem hasznalt valami
$er=mysql_query('
select gy.id as gyarid,gy.tipus,gyt.nev'.$lang__lang.' as nev,bgy.db,bgy.aktiv_db,gyt.iparag,gy.uzemmod,gyt.uzemmod_szam_'.$bolygo['osztaly'].',round(100*efft.effektiv_db) as effektiv_db,1 as prioritas
from
(select bgye.gyar_id,min(if(bgye.io>=0,bgye.aktiv_db,if(bgye.aktiv_db*bgye.io+be.db*bgye.reszarany/1000000000>=0,bgye.aktiv_db,-be.db/1000000000*bgye.reszarany/bgye.io))) as effektiv_db
from bolygo_gyar_eroforras bgye,bolygo_eroforras be
where bgye.bolygo_id='.$bolygo['id'].' and be.bolygo_id='.$bolygo['id'].' and bgye.eroforras_id=be.eroforras_id
group by bgye.gyar_id) efft, gyarak gy, gyartipusok gyt, bolygo_gyar bgy
where gy.id=efft.gyar_id and gy.tipus=gyt.id
and bgy.bolygo_id='.$bolygo['id'].' and bgy.gyar_id=gy.id
order by gyt.iparag_sorszam,gyt.id,gy.uzemmod
') or hiba(__FILE__,__LINE__,mysql_error());
$sorszam=0;
while($aux=mysql_fetch_array($er)) {
	$aux[5]=$lang[$lang_lang]['iparagak'][$iparag_nevek[$aux[5]]];//iparag szam helyett nev
	$gyarak[]=$aux;
	$effektiv_db[$aux['gyarid']]=$aux['effektiv_db'];
	//if ($aux['tipus']==32) $kemkozpontszam+=$aux['effektiv_db'];
	if ($aux['tipus']==33) $kocsmaszam+=$aux['effektiv_db'];
	if ($aux['tipus']==34) $teleportszam+=$aux['effektiv_db'];
	$gyar_sorszamok[$aux['gyarid']]=$sorszam;
	$sorszam++;
}

$er=mysql_query('
select e.id,be.db,0 as delta,0 as hiany,e.raktarozhato,e.szallithato,e.raktarozhato_alapbol
from bolygo_eroforras be, eroforrasok e
where be.bolygo_id='.$bolygo['id'].' and be.eroforras_id=e.id and e.tipus='.EROFORRAS_TIPUS_EROFORRAS.' order by e.id') or hiba(__FILE__,__LINE__,mysql_error());
$sorszam=0;
while($aux=mysql_fetch_array($er)) {
	$aux[8]=$aux[5];$aux[5]=0;
	$aux[12]=$aux[6];$aux[6]=0;
	$eroforrasok[]=$aux;
	$eroforras_sorszamok[$aux['id']]=$sorszam;
	$sorszam++;
}


$er=mysql_query('
select e.id,be.db,round(sum(
if(
(gye.gyar_id=84 and gye.eroforras_id=60 and b.osztaly=3) or
(gye.gyar_id=85 and gye.eroforras_id=61 and b.osztaly=2) or
(gye.gyar_id=90 and gye.eroforras_id=63 and b.osztaly=1) or
(gye.gyar_id=86 and gye.eroforras_id=62 and b.osztaly=5)
,2*gye.io,gye.io
)*bgy_eff.effektiv_db
)) as delta
from (
	select bgye.bolygo_id,bgye.gyar_id,min(if(bgye.io>=0,bgye.aktiv_db,if(bgye.aktiv_db*bgye.io+be.db*bgye.reszarany/1000000000>=0,bgye.aktiv_db,-be.db/1000000000*bgye.reszarany/bgye.io))) as effektiv_db
	from bolygo_gyar_eroforras bgye,bolygo_eroforras be
	where bgye.bolygo_id=be.bolygo_id and bgye.eroforras_id=be.eroforras_id and be.bolygo_id='.$bolygo['id'].'
	group by bgye.gyar_id
) bgy_eff,bolygo_eroforras be,gyar_eroforras gye, eroforrasok e, bolygok b
where be.eroforras_id=gye.eroforras_id and be.bolygo_id=bgy_eff.bolygo_id and bgy_eff.gyar_id=gye.gyar_id
and b.id=be.bolygo_id
and e.id=be.eroforras_id
group by be.eroforras_id
') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	if (isset($eroforrasok[$eroforras_sorszamok[$aux['id']]])) $eroforrasok[$eroforras_sorszamok[$aux['id']]][2]=$aux[2];
	$eroforras_db[$aux['id']]=$aux['db'];
}


$gyarlista=array();$gyarnevek=array();$gyar_aktivdb=array();
$eflista=array();$efnevek=array();
$bgye=array();
$er=mysql_query('select bgye.bolygo_id,bgye.gyar_id,bgye.eroforras_id,bgye.aktiv_db,
if(
(bgye.gyar_id=84 and bgye.eroforras_id=60 and b.osztaly=3) or
(bgye.gyar_id=85 and bgye.eroforras_id=61 and b.osztaly=2) or
(bgye.gyar_id=90 and bgye.eroforras_id=63 and b.osztaly=1) or
(bgye.gyar_id=86 and bgye.eroforras_id=62 and b.osztaly=5)
,2*bgye.io,bgye.io
) as io,
bgye.reszarany
,gyt.nev'.$lang__lang.' as gyarnev,e.nev'.$lang__lang.' as efnev,be.db as efdb
from bolygo_gyar_eroforras bgye, gyarak gy, gyartipusok gyt, eroforrasok e, bolygo_eroforras be, bolygok b
where bgye.gyar_id=gy.id and gy.tipus=gyt.id and bgye.eroforras_id=e.id
and be.bolygo_id='.$bolygo['id'].' and be.eroforras_id=bgye.eroforras_id
and bgye.bolygo_id='.$bolygo['id'].' and b.id='.$bolygo['id'].'
and aktiv_db>0
order by bgye.eroforras_id,bgye.gyar_id
') or hiba(__FILE__,__LINE__,mysql_error());
while($aux=mysql_fetch_array($er)) {
	if (!in_array($aux['gyar_id'],$gyarlista)) {$gyarlista[]=$aux['gyar_id'];$gyarnevek[]=$aux['gyarnev'];$gyar_aktivdb[]=$aux['aktiv_db'];}
	if (!in_array($aux['eroforras_id'],$eflista)) {$eflista[]=$aux['eroforras_id'];$efnevek[]=$aux['efnev'];}
	$bgye[$aux['gyar_id']][$aux['eroforras_id']]=$aux;
}


for($e=0;$e<count($eflista);$e++) {
	$ef_term[$e]=0;
	$ef_fogy[$e]=0;
	$ef_term_t[$e]=0;
	$ef_fogy_t[$e]=0;
}

for($gy=0;$gy<count($gyarlista);$gy++) {
	//$ef_input='"';
	//$ef_output='"';
	for($e=0;$e<count($eflista);$e++) {
		$baj_van=0;
		if ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']<0) {
			$x=round(100*(-$bgye[$gyarlista[$gy]][$eflista[$e]]['efdb']*$bgye[$gyarlista[$gy]][$eflista[$e]]['reszarany']/1000000000/$bgye[$gyarlista[$gy]][$eflista[$e]]['io']));
			if ($x>100*$gyar_aktivdb[$gy]) $x=100*$gyar_aktivdb[$gy];
			if ($effektiv_db[$gyarlista[$gy]]!=$x) $baj_van=1;
		}
		/*echo '<td';
		if ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']>0) echo ' style="background: rgb(200,255,190)"';
		if ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']<0) {
			if ($baj_van) echo ' style="background: rgb(255,0,0)"';
			else echo ' style="background: rgb(255,200,190)"';
		}
		echo '>';*/
		if ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']!=0) {
			if ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']<0) {
				/*echo number_format($bgye[$gyarlista[$gy]][$eflista[$e]]['reszarany']/100,2,',',' ').'% &rarr; ';
				echo $x;
				echo '<br />';*/
			}
			/*echo '<b>'.$bgye[$gyarlista[$gy]][$eflista[$e]]['io']*$effektiv_db[$gyarlista[$gy]].' ('.$effektiv_db[$gyarlista[$gy]].')'.'</b>';
			echo '</td>';*/
			if ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']*$effektiv_db[$gyarlista[$gy]]>0) {
				$ef_term_t[$e]+=round($bgye[$gyarlista[$gy]][$eflista[$e]]['io']*$effektiv_db[$gyarlista[$gy]]/100);
			} else {
				$ef_fogy_t[$e]-=round($bgye[$gyarlista[$gy]][$eflista[$e]]['io']*$effektiv_db[$gyarlista[$gy]]/100);
			}
			if ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']*$bgye[$gyarlista[$gy]][$eflista[$e]]['aktiv_db']>0) {
				$ef_term[$e]+=$bgye[$gyarlista[$gy]][$eflista[$e]]['io']*$bgye[$gyarlista[$gy]][$eflista[$e]]['aktiv_db'];
			} else {
				$ef_fogy[$e]-=$bgye[$gyarlista[$gy]][$eflista[$e]]['io']*$bgye[$gyarlista[$gy]][$eflista[$e]]['aktiv_db'];
			}
		}
		//sima io
		/*$bgye[$gyarlista[$gy]][$eflista[$e]]['io'];
		$ksitott_darab=;
		if ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']>0) {
		} elseif ($bgye[$gyarlista[$gy]][$eflista[$e]]['io']<0) {
			$ef_input.=$bgye[$gyarlista[$gy]][$eflista[$e]]['io'].' ';
		}*/
	}
	//$gyarak[$gyar_sorszamok[$gyarlista[$gy]]][10]=$ef_input.'"';
	//$gyarak[$gyar_sorszamok[$gyarlista[$gy]]][11]=$ef_output.'"';
}

$lakohely_termeles=0;
for($e=0;$e<count($eflista);$e++) {
	$hiany=0;
	//if ($eroforras_db[$eflista[$e]]<$ef_fogy[$e]) {//problema van
		if ($eroforras_db[$eflista[$e]]>0) $hiany=round($ef_fogy[$e]/$eroforras_db[$eflista[$e]]*100);
		else if ($ef_fogy[$e]>0) $hiany=-1;
	//}
	if (isset($eroforrasok[$eroforras_sorszamok[$eflista[$e]]])) {
		$eroforrasok[$eroforras_sorszamok[$eflista[$e]]][3]=$hiany;
		$eroforrasok[$eroforras_sorszamok[$eflista[$e]]][5]=$ef_fogy_t[$e];
		$eroforrasok[$eroforras_sorszamok[$eflista[$e]]][6]=$ef_term_t[$e];
	}
	if ($eflista[$e]==LAKOHELY_ID) $lakohely_termeles=$ef_term_t[$e];
}

if (isset($eroforras_sorszamok[KAJA_ID])) {
	$kaja_termeles=$eroforrasok[$eroforras_sorszamok[KAJA_ID]][2];
	$eroforrasok[$eroforras_sorszamok[KAJA_ID]][2]-=$bolygo_pop;
	$eroforrasok[$eroforras_sorszamok[KAJA_ID]][5]+=$bolygo_pop;
} else $kaja_termeles=0;

//automatikus feltaras
if (isset($eroforras_sorszamok[60])) {
	$sorsz=$eroforras_sorszamok[60];$auto_feltaras=100;if ($bolygo['osztaly']==3) $auto_feltaras=200;
	$eroforrasok[$sorsz][2]+=$auto_feltaras;$eroforrasok[$sorsz][6]+=$auto_feltaras;$eroforrasok[$sorsz][7]+=$auto_feltaras;
}
if (isset($eroforras_sorszamok[61])) {
	$sorsz=$eroforras_sorszamok[61];$auto_feltaras=100;if ($bolygo['osztaly']==2) $auto_feltaras=200;
	$eroforrasok[$sorsz][2]+=$auto_feltaras;$eroforrasok[$sorsz][6]+=$auto_feltaras;$eroforrasok[$sorsz][7]+=$auto_feltaras;
}
if (isset($eroforras_sorszamok[62])) {
	$sorsz=$eroforras_sorszamok[62];$auto_feltaras=500;if ($bolygo['osztaly']==5) $auto_feltaras=1000;
	$eroforrasok[$sorsz][2]+=$auto_feltaras;$eroforrasok[$sorsz][6]+=$auto_feltaras;$eroforrasok[$sorsz][7]+=$auto_feltaras;
}
if (isset($eroforras_sorszamok[63])) {
	$sorsz=$eroforras_sorszamok[63];$auto_feltaras=10;if ($bolygo['osztaly']==1) $auto_feltaras=20;
	$eroforrasok[$sorsz][2]+=$auto_feltaras;$eroforrasok[$sorsz][6]+=$auto_feltaras;$eroforrasok[$sorsz][7]+=$auto_feltaras;
}

//raktarozhato eroforrasok teljes brutto termelese
$er=mysql_query('
select gye.eroforras_id,sum(if(gye.io>0,bgy.db*gye.io,0)) as brutto_termeles
from bolygo_gyar bgy, gyar_eroforras gye, eroforrasok e
where bgy.gyar_id=gye.gyar_id and gye.eroforras_id=e.id and e.raktarozhato=1 and bgy.bolygo_id='.$bolygo['id'].'
group by gye.eroforras_id
');
while($aux=mysql_fetch_array($er)) if (isset($eroforrasok[$eroforras_sorszamok[$aux[0]]])) $eroforrasok[$eroforras_sorszamok[$aux[0]]][7]=$aux[1];


if (premium_szint()==2) {//utemezett szallitas
	$er=mysql_query('select eroforras_id,sum(darab) from cron_tabla_eroforras_transzfer where hova_bolygo_id='.$bolygo['id'].' group by eroforras_id');
	while($aux=mysql_fetch_array($er)) if (isset($eroforrasok[$eroforras_sorszamok[$aux[0]]])) $eroforrasok[$eroforras_sorszamok[$aux[0]]][11]=$aux[1];
	$er=mysql_query('select eroforras_id,sum(darab) from cron_tabla_eroforras_transzfer where honnan_bolygo_id='.$bolygo['id'].' group by eroforras_id');
	while($aux=mysql_fetch_array($er)) if (isset($eroforrasok[$eroforras_sorszamok[$aux[0]]])) $eroforrasok[$eroforras_sorszamok[$aux[0]]][11]-=$aux[1];
}


//premium szamitasok
if (is_array($eroforrasok)) foreach($eroforrasok as $key => $value) {
	if (premium_szint()>0) {
		if ($eroforrasok[$key][2]+$eroforrasok[$key][11]>0) {//betelik
			$eroforrasok[$key][9]=-1;
			if ($eroforrasok[$key][4]) $eroforrasok[$key][10]=ceil(($eroforrasok[$key][12]+500*$eroforrasok[$key][7]-$eroforrasok[$key][1])/($eroforrasok[$key][2]+$eroforrasok[$key][11]));//500 = egykori raktarkapacitas
			else $eroforrasok[$key][10]=-1;
		} elseif ($eroforrasok[$key][2]+$eroforrasok[$key][11]<0) {//kiurul
			if ($eroforrasok[$key][1]-$eroforrasok[$key][5]>=0) {
				$eroforrasok[$key][9]=floor(($eroforrasok[$key][1]-$eroforrasok[$key][5])/(-$eroforrasok[$key][2]-$eroforrasok[$key][11]))+1;
			} else {
				$eroforrasok[$key][9]=0;
			}
			$eroforrasok[$key][10]=-1;
		} else {//valtozatlan
			$eroforrasok[$key][9]=-1;
			$eroforrasok[$key][10]=-1;
		}
	} else {
		$eroforrasok[$key][9]=-2;
		$eroforrasok[$key][10]=-2;
	}
}
if (is_array($gyarak)) foreach($gyarak as $key => $value) {
	$gyarak[$key][10]=premium_szint();
}


$hover_help['hu'][56]='Mielőbb építs még kajagyárakat, különben éhenhalnak az embereid, és nem lesz aki dolgozzon a gyárakban.';//kaja
$hover_help['hu'][59]='Építs erőműveket. De addig is, ha szükséges, ideiglenesen inaktiválj néhány nem alapvető gyárat. Ha már rendbejött az energiamérleg, újra aktiválhatod őket.';//energia
$hover_help['hu'][60]='Ha még nincs városod, ne is foglalkozz ezzel. Ha már van, akkor ez az első és legfontosabb "nyers nyersi", amit fel kell tárnod kutatóintézetekben.';//nyers ko
$hover_help['hu'][61]='Ha még nincs városod, ne is foglalkozz ezzel. Ha már van, és a nyers kő feltárásod is rendben van, feltárhatsz nyers homokot is.';//nyers homok
$hover_help['hu'][62]='Ha még nincs városod, ne is foglalkozz ezzel. Ha már van, és a nyers kő feltárásod is rendben van, feltárhatsz titánércet is.';//titanerc
$hover_help['hu'][63]='Ha még nincs városod, ne is foglalkozz ezzel. Ha már van, és a nyers kő feltárásod is rendben van, feltárhatsz uránércet is.';//uranerc
$hover_help['hu'][64]='Elfeledkeztél róla, hogy nem csak az építkezések viszik a fát, hanem a szénégető is. Építs még fűrészmalmokat.';//fa
$hover_help['hu'][65]='Ha szándékosan szállítasz el ennyi követ innen, akkor semmi gond. Csak aztán ne csodálkozz, ha idővel nem tudsz építkezni ezen a bolygón.';//ko
$hover_help['hu'][66]='Valószínűleg túl sok üveg- és félvezetőgyárad üzemel. Építs még homokbányákat.';//homok
$hover_help['hu'][67]='Ahhoz, hogy fenn bírd tartani ezt a hajógyártási ütemet, további titánműveket kell építened.';//titan
$hover_help['hu'][68]='Építs még urándúsítókat.';//dusitott uran
$hover_help['hu'][69]='A titán- és olajgyártás is fogyasztja a szenet. Tarts fenn elég szénégetőt, hogy legyen utánpótlás.';//szen
$hover_help['hu'][70]='Építs még olajgyárakat.';//olaj
$hover_help['hu'][71]='Építs még műanyaggyárakat.';//muanyag
$hover_help['hu'][72]='Ha szándékosan szállítasz el ennyi üveget innen, akkor semmi gond. Csak aztán ne csodálkozz, ha idővel nem tudsz építkezni ezen a bolygón.';//uveg
$hover_help['hu'][73]='Építs még félvezetőgyárakat.';//felvezeto
$hover_help['hu'][74]='Építs még szeszfőzdéket.';//alkohol

$hover_help['en'][56]='Build food factories as soon as you can. If you run out of food, your people start dying of hunger, and you won\'t have enough workers in your factories.';//kaja
$hover_help['en'][59]='Build power stations. And if necessary, inactivate a few factories until then. You can reactivate them later, when you have enough energy production.';//energia
$hover_help['en'][60]='If you don\'t have cities, forget about it. If you do, it\'s time to start excavating raw stone by building research institutions.';//nyers ko
$hover_help['en'][61]='If you don\'t have cities, forget about it. If you do and you already excavate raw stone, you can start excavating raw sand as well.';//nyers homok
$hover_help['en'][62]='If you don\'t have cities, forget about it. If you do and you already excavate raw stone, you can start excavating titanium ore as well.';//titanerc
$hover_help['en'][63]='If you don\'t have cities, forget about it. If you do and you already excavate raw stone, you can start excavating uranium ore as well.';//uranerc
$hover_help['en'][64]='You have forgotten that besides constructions coal factories consume lumber as well. Build more sawmills.';//fa
$hover_help['en'][65]='If you transfer so much stone to other planets, fine. Just don\'t be surprised when you don\'t have enough stone to build new factories.';//ko
$hover_help['en'][66]='You probably have too many glass and chip factories. Build more sand mines.';//homok
$hover_help['en'][67]='To keep up the current rate of spaceship production, you need more titanium. Build more titanium works.';//titan
$hover_help['en'][68]='Build enrichment plants.';//dusitott uran
$hover_help['en'][69]='Titanium and oil production consumes coal. Build enough coal factories to compensate that.';//szen
$hover_help['en'][70]='Build oil factories.';//olaj
$hover_help['en'][71]='Build plastic factories.';//muanyag
$hover_help['en'][72]='If you transfer so much glass to other planets, fine. Just don\'t be surprised when you don\'t have enough glass to build new factories.';//uveg
$hover_help['en'][73]='Build chip factories.';//felvezeto
$hover_help['en'][74]='Build distilleries.';//alkohol

//kis hover helpek
if (is_array($eroforrasok)) foreach($eroforrasok as $key => $value) {
	$eroforrasok[$key][13]="";
	if ($eroforrasok[$key][2]<0) {//piros
		$eroforrasok[$key][13]=$hover_help[$lang_lang][$eroforrasok[$key][0]];
	}
}


?>,"eroforrasok":<?
echo array2jsonmatrix($eroforrasok,14,'nnnnnnnnnnnnns');//id, keszlet, netto term, hiany, raktarozhato, brutto fogyasztas, brutto termeles, teljes brutto termeles, szallithato, kiurul, betelik, auto_transzfer, alap raktar, help
?>,"auto_transz":<?
if (premium_szint()<2) echo '[]';else {
	echo mysql2jsonmatrix('select c.eroforras_id,c.darab,b.id,b.osztaly,b.nev,1,c.id from cron_tabla_eroforras_transzfer c, bolygok b where c.hova_bolygo_id=b.id and c.honnan_bolygo_id='.$bolygo['id'].' order by c.eroforras_id,b.nev,c.id');
}
?>,"auto_transz_in":<?
if (premium_szint()<2) echo '[]';else {
	echo mysql2jsonmatrix('select c.eroforras_id,c.darab,b.id,b.osztaly,b.nev,1,c.id from cron_tabla_eroforras_transzfer c, bolygok b where c.honnan_bolygo_id=b.id and c.hova_bolygo_id='.$bolygo['id'].' order by c.eroforras_id,b.nev,c.id');
}



?>,"flottak":<?
echo mysql2jsonmatrix('select f.id,f.nev,if(f.tulaj='.$uid.',1,0),u.id,u.nev from flottak f, userek u where f.bolygo='.$bolygo['id'].' and f.statusz=1 and f.tulaj=u.id order by if(f.tulaj='.$uid.',1,2),f.nev');
?>,"teljesflottaertek":<?
echo mysql2num('select coalesce(round(sum(fh.ossz_hp*h.ar)/100),0) from flottak f, flotta_hajo fh, hajok h where f.bolygo='.$bolygo['id'].' and f.statusz=1 and fh.flotta_id=f.id and fh.hajo_id=h.id');
?>,"teljesflottaertek_szondanelkul":<?
echo mysql2num('select coalesce(round(sum(fh.ossz_hp*h.ar)/100),0) from flottak f, flotta_hajo fh, hajok h where f.bolygo='.$bolygo['id'].' and f.statusz=1 and fh.flotta_id=f.id and fh.hajo_id=h.id and h.id!='.HAJO_TIPUS_SZONDA);
?>,"hajok":<?
echo mysql2jsonmatrix('select e.id,ceil(be.db/100),be.db,round(be.db/100*h.ar) from bolygo_eroforras be,eroforrasok e,hajok h where e.tipus='.EROFORRAS_TIPUS_URHAJO.' and be.eroforras_id=e.id and be.bolygo_id='.$bolygo['id'].' and h.id=e.id order by e.id');
?>,"gyarak":<?
echo array2jsonmatrix($gyarak,11,'nnsnnsnnnnn');//leguccso = premium
?>,"fajok":<?
$tercsi=round($bolygo['terulet']/100000);
echo mysql2jsonmatrix('select f.id,bf.db,f.trofikus_szint,bfc.db
from bolygo_eroforras bf,eroforrasok f,bolygo_faj_celszam bfc
where f.tipus=1 and bf.eroforras_id=f.id and bf.bolygo_id='.$bolygo['id'].'
and bfc.eroforras_id=f.id and bfc.osztaly='.$bolygo['osztaly'].' and bfc.terulet='.$tercsi.'
order by f.trofikus_szint,f.nev'.$lang__lang);
if (premium_szint()>0) {
?>,"bio_kitermeles":<?
echo mysql2jsonmatrix('
select t.*,coalesce(bgy.aktiv_db,0) from
(select gy.tipus,gy.uzemmod,gy.id as gyar_id
from gyarak gy, bolygo_eroforras be
where be.bolygo_id='.$bolygo['id'].'
and gy.bio=1
and gy.uzemmod=be.eroforras_id) t
left join bolygo_gyar bgy on bgy.bolygo_id='.$bolygo['id'].' and bgy.gyar_id=t.gyar_id
');
}
?>,"term_mikor":<?
$term_mikor=($bolygo['id']%15)-((mysql2num('select idopont from ido')-1)%15);
if ($term_mikor<=0) $term_mikor+=15;
echo $term_mikor;
?>,"moral":<?
echo $bolygo['moral'];
?>,"lakohely":<?
echo mysql2num('select db from bolygo_eroforras where bolygo_id='.$bolygo['id'].' and eroforras_id='.LAKOHELY_ID);
?>,"lakohelytermeles":<?
echo $lakohely_termeles;
?>,"kaja":<?
echo mysql2num('select db from bolygo_eroforras where bolygo_id='.$bolygo['id'].' and eroforras_id='.KAJA_ID);
?>,"kajatermeles":<?
echo $kaja_termeles;
?>,"pop":<?
echo $bolygo_pop;
?>,"munkaero":<?
echo mysql2num('select db from bolygo_eroforras where bolygo_id='.$bolygo['id'].' and eroforras_id='.MUNKAERO_ID);
?>,"munkaeroigeny":<?
echo mysql2num('select coalesce(-sum(aktiv_db*io),0) from bolygo_gyar_eroforras where bolygo_id='.$bolygo['id'].' and eroforras_id='.MUNKAERO_ID);
?>,"kepzettmunkaero":<?
echo (int)mysql2num('select delta_db from bolygo_eroforras where bolygo_id='.$bolygo['id'].' and eroforras_id='.KEPZETT_MUNKAHELY_ID);
?>,"kepzettmunkaeroigeny":<?
echo (int)mysql2num('select -sum(aktiv_db*io) from bolygo_gyar_eroforras where bolygo_id='.$bolygo['id'].' and io<0 and eroforras_id='.KEPZETT_MUNKAERO_ID);
?>,"teleporttoltes":<?
echo mysql2num('select db from bolygo_eroforras where bolygo_id='.$bolygo['id'].' and eroforras_id=78');
?>,"teleportkapacitas":<?
echo 5*$teleportszam;//a teleportszam, az valojaban a teleportszam 100-szorosa, vagyis a max TT-nek megfeleloen kell beszorozni vagy sem
?>,"kocsmaszam":<?
echo $kocsmaszam;
?>,"techszint":<?
echo $adataim['techszint'];
?>,"magan_bolygok_szama":<?
echo mysql2num('select count(1) from bolygok where tulaj='.$bolygo['tulaj']);
?>,"bolygok":<?
echo mysql2jsonmatrix('select id,nev,osztaly,if(id='.$bolygo['id'].',1,0) from bolygok where tulaj='.$bolygo['tulaj'].' order by nev,id');


?>,"kovetkezo":<?
$res3=mysql_query('select id from bolygok
where (concat(if(kezelo>0,"1","0"),nev)>"'.mysql_real_escape_string(($bolygo['kezelo']>0?'1':'0').$bolygo['nev']).'"
or concat(if(kezelo>0,"1","0"),nev)="'.mysql_real_escape_string(($bolygo['kezelo']>0?'1':'0').$bolygo['nev']).'" and id>'.$bolygo['id'].')
and (tulaj='.$uid.' or kezelo='.$uid.')
order by if(kezelo>0,1,0), nev, id limit 1') or hiba(__FILE__,__LINE__,mysql_error());
$aux3=mysql_fetch_array($res3);
if (!$aux3) {
	$res3=mysql_query('select id from bolygok where (tulaj='.$uid.' or kezelo='.$uid.') order by if(kezelo>0,1,0), nev, id limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux3=mysql_fetch_array($res3);
}
echo $aux3[0];
?>,"elozo":<?
$res3=mysql_query('select id from bolygok
where (concat(if(kezelo>0,"1","0"),nev)<"'.mysql_real_escape_string(($bolygo['kezelo']>0?'1':'0').$bolygo['nev']).'"
or concat(if(kezelo>0,"1","0"),nev)="'.mysql_real_escape_string(($bolygo['kezelo']>0?'1':'0').$bolygo['nev']).'" and id<'.$bolygo['id'].')
and (tulaj='.$uid.' or kezelo='.$uid.')
order by if(kezelo>0,1,0) desc, nev desc, id desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
$aux3=mysql_fetch_array($res3);
if (!$aux3) {
	$res3=mysql_query('select id from bolygok where (tulaj='.$uid.' or kezelo='.$uid.') order by if(kezelo>0,1,0) desc, nev desc, id desc limit 1') or hiba(__FILE__,__LINE__,mysql_error());
	$aux3=mysql_fetch_array($res3);
}
echo $aux3[0];




?>
<? } else {//mase
?>,"kemriportok":<?

if ($jogaim[12]) {//kemjog
echo mysql2jsonmultiassoc('select kr.feladat_domen,kr.feladat_id
,sum(kr.pontos=1) as pontos_van_e
,coalesce(sum(if(kr.pontos=1,kr.darab,null)),0) as pontos_darab
,coalesce(sum(if(kr.pontos=1,kr.aktiv_darab,null)),0) as pontos_aktiv_darab
,coalesce(max(if(kr.pontos=1,kr.mikor,null)),"") as pontos_mikor
,sum(kr.pontos=0) as pontatlan_van_e
,coalesce(sum(if(kr.pontos=0,kr.darab,null)),0) as pontatlan_darab
,0 as pontatlan_aktiv_darab
,coalesce(max(if(kr.pontos=0,kr.mikor,null)),"") as pontatlan_mikor
from '.$database_mmog_nemlog.'.kemriportok kr, (
select feladat_domen,feladat_id,pontos,max(id) as max_id
from '.$database_mmog_nemlog.'.kemriportok
where tulaj_szov='.$adataim['tulaj_szov'].' and bolygo_id='.$bolygo['id'].'
group by feladat_domen,feladat_id,pontos
) t
where kr.id=t.max_id
group by kr.feladat_domen,kr.feladat_id');
} else {
echo mysql2jsonmultiassoc('select kr.feladat_domen,kr.feladat_id
,sum(kr.pontos=1) as pontos_van_e
,coalesce(sum(if(kr.pontos=1,kr.darab,null)),0) as pontos_darab
,coalesce(sum(if(kr.pontos=1,kr.aktiv_darab,null)),0) as pontos_aktiv_darab
,coalesce(max(if(kr.pontos=1,kr.mikor,null)),"") as pontos_mikor
,sum(kr.pontos=0) as pontatlan_van_e
,coalesce(sum(if(kr.pontos=0,kr.darab,null)),0) as pontatlan_darab
,0 as pontatlan_aktiv_darab
,coalesce(max(if(kr.pontos=0,kr.mikor,null)),"") as pontatlan_mikor
from '.$database_mmog_nemlog.'.kemriportok kr, (
select feladat_domen,feladat_id,pontos,max(id) as max_id
from '.$database_mmog_nemlog.'.kemriportok
where tulaj='.$uid.' and bolygo_id='.$bolygo['id'].'
group by feladat_domen,feladat_id,pontos
) t
where kr.id=t.max_id
group by kr.feladat_domen,kr.feladat_id');
}

}
?>}*/
<?
} else {
?>
/*{"letezik":0}*/
<?
}

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>