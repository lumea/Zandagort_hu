<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['id']=(int)$_REQUEST['id'];
$res=mysql_query('select *, coalesce(timestampdiff(minute,uccso_akt,now()),coalesce('.$suti_hossz_percben.'-timestampdiff(minute,now(),session_ervenyesseg),coalesce(timestampdiff(minute,uccso_login,now()),timestampdiff(minute,mikortol,now())))) as utoljara from userek where id='.$_REQUEST['id']) or hiba(__FILE__,__LINE__,mysql_error());
$jatekos=mysql_fetch_array($res);

if ($jatekos['id']==0) {
?>
/*{"letezik":0}*/
<?
kilep();
}

?>
/*{"letezik":1,"te_vagy":<?
if ($uid==$jatekos['id']) echo 1;else echo 0;
?>,"id":<?=$jatekos['id'];?>,"nev":"<?=addslashes($jatekos['nev']);?>","reg":<?
$mikortol=explode('-',$jatekos['mikortol']);$mainap=getdate();
echo (int)((mktime(0,0,0,$mainap['mon'],$mainap['mday'],$mainap['year'],0)-mktime(0,0,0,$mikortol[1],$mikortol[2],$mikortol[0],0))/3600/24);
?>,"karrier":<?
echo $jatekos['karrier'];
?>,"speci":<?
echo $jatekos['speci'];
?>,"sztazis":0,"avatar_fajlnev":"<?
if (strlen($jatekos['avatar_ext'])) {
	echo 'p'.$jatekos['id'].$jatekos['avatar_crc'].'.'.$jatekos['avatar_ext'];
} else echo '';
?>","nagy_avatar_fajlnev":"<?
if (user_premium_szint($jatekos)>0 && strlen($jatekos['nagy_avatar_ext'])) {
	echo 'p'.$jatekos['id'].$jatekos['avatar_crc'].'.'.$jatekos['nagy_avatar_ext'];
} else echo '';
?>","admin":<?
if ($adataim['admin']) echo 1;else echo 0;//specko admin felulet csak akkor jelenik meg, ha en nezem
?>,"premium":<?
echo user_premium_szint($jatekos);

?>,"viewer_premium":<?
echo premium_szint();

if ($adataim['admin']) {//csak az admin latja!!!
?>,"uccso_akt":<?
if ($jatekos['id']==1) echo 1440;else echo (int)$jatekos['utoljara'];
?>,"premium_meddig":<?
echo '"';
if ($jatekos['premium']>0) echo 'korlátlan ideig';
elseif (time()<strtotime($jatekos['premium_emelt'])) echo $jatekos['premium_emelt'];
elseif (time()<strtotime($jatekos['premium_alap'])) echo $jatekos['premium_alap'];
else '-';
echo '"';
?>,"zanda_ref":<?=json_encode($jatekos['zanda_ref']);
?>,"email":<?=json_encode($jatekos['email']);
?>,"szamla_nev":<?=json_encode($jatekos['szamlazasi_nev']);
?>,"szamla_cim":<?=json_encode($jatekos['szamlazasi_cim']);
?>,"szamla_nev_input":<?=json_encode(htmlspecialchars($jatekos['szamlazasi_nev']));
?>,"szamla_cim_input":<?=json_encode(htmlspecialchars($jatekos['szamlazasi_cim']));
?>,"twitter_nev":<?=json_encode($jatekos['twitter_nev']);
?>,"multik":<?
//echo mysql2jsonmatrix('select u.id,u.nev,m.magyarazat from '.$database_mmog_nemlog.'.multi_matrix m, userek u where m.pont>'.MULTI_LIMIT.' and m.pont>2*m.minusz_pont and m.ki='.$jatekos['id'].' and m.kivel=u.id order by u.nev,u.id');
echo mysql2jsonmatrix('select u.id,u.nev,m.magyarazat,m.pont,m.minusz_pont from '.$database_mmog_nemlog.'.multi_matrix m, userek u where m.ki='.$jatekos['id'].' and m.kivel=u.id order by u.nev,u.id');
}


?>,"nyelv":<?
echo '"'.$jatekos['nyelv'].'"';

?>,"torles_alatt":<?
$r=mysql_query('select * from torlendo_userek where user_id='.$jatekos['id']);
$aux=mysql_fetch_array($r);
if ($aux['mikor']>date('Y-m-d H:i:s')) echo 1;
else echo 0;
?>,"kepfajl_random":<?
echo '"'.addslashes($jatekos['kepfajl_random']).'"';


?>,"badgek":<?
echo mysql2jsonmatrix('select b.id,b.cim,b.alcim,ub.szin,if(ub.mikor="0000-00-00 00:00:00","",left(ub.mikor,16)),b.leiras_'.(($lang_lang=='hu')?'hu':'en').',ub.publikus from user_badge ub, badgek b where ub.user_id='.$jatekos['id'].' and ub.badge_id=b.id and ub.bejelentett=1 and (ub.publikus=2 or ub.publikus=1 and '.($adataim['tulaj_szov']==$jatekos['tulaj_szov']?1:0).' or ub.publikus=0 and '.($uid==$jatekos['id']?1:0).') order by ub.mikor,b.id');



?>,"tagek":<?
echo '[';
if (user_premium_szint($jatekos)>0) {
$er2=mysql_query('select id,cim,szoveg,sorszam from user_tagek where tulaj='.$jatekos['id'].' order by sorszam') or hiba(__FILE__,__LINE__,mysql_error());
$tagek_szama=mysql_num_rows($er2);
$i=0;while($aux2=mysql_fetch_array($er2)) {
	$i++;if ($i>1) echo ',';
	echo '[';
	echo $aux2[0].',';
	echo json_encode($aux2[1]).',';
	echo json_encode($aux2[2]).',';
	echo $aux2[3].',';
	echo $tagek_szama.',';
	echo '"'.addslashes(addslashes(htmlspecialchars($aux2[1]))).'",';
	echo '"'.addslashes(addslashes(htmlspecialchars($aux2[2]))).'"';
	echo ']';
}
}
echo ']';



?>,"meghivo_id":<?
echo (int)$jatekos['kin_keresztul_id'];
?>,"meghivo_nev":"<?
if ($jatekos['kin_keresztul_id']>0) echo addslashes(mysql2num('select nev from userek where id='.$jatekos['kin_keresztul_id']));
?>","pontszam":<?
if ($jatekos['tulaj_szov']==$adataim['tulaj_szov']) echo $jatekos['pontszam_exp_atlag'];else echo '"?"';
?>,"helyezes":<?
if ($jatekos['id']!=$uid && $jatekos['karrier']==3 && $jatekos['speci']==3) {//rejtozo
	echo '"?"';
} else {
	$er=mysql_query('select count(1) from userek where szovetseg not in ('.implode(',',$specko_szovetsegek_listaja).') and id not in ('.implode(',',$specko_userek_listaja).') and (karrier!=3 or speci!=3) and pontszam_exp_atlag>'.$jatekos['pontszam_exp_atlag']);
	$aux=mysql_fetch_array($er);
	echo $aux[0]+1;
}
?>,"bolygok":<?
if ($jatekos['id']!=$uid && $jatekos['karrier']==3 && $jatekos['speci']==3) {//rejtozo
echo '[]';
} else {
echo mysql2jsonmatrix('select b.id,b.nev,b.osztaly,b.x,b.y,b.kezelo,uk.nev,sum(ucs.darab),round(b.terulet/1000000)
from bolygok b left join userek uk on uk.id=b.kezelo
left join ugynokcsoportok ucs on ucs.bolygo_id=b.id and ucs.tulaj='.$uid.'
where b.tulaj='.$jatekos['id'].'
group by b.id
order by b.nev,b.id');
}
?>,"szovetseg":{"id":<?
$er=mysql_query('select * from szovetsegek where id='.$jatekos['szovetseg']) or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_array($er);
if ($aux) echo $aux['id'];else echo 0;
?>,"nev":"<?
if ($aux) echo addslashes($aux['nev']);
?>",tisztseg:"<?
if ($aux) {
	$res2=mysql_query('select * from szovetseg_tisztsegek where szov_id='.$aux['id'].' and id='.$jatekos['tisztseg']) or hiba(__FILE__,__LINE__,mysql_error());
	$aux2=mysql_fetch_array($res2);
	if ($aux2) {
		echo addslashes($aux2['nev']);
	} else {
		if ($jatekos['tisztseg']==-1) echo $aux['alapito_elnevezese'];
		else echo ($lang_lang=='hu')?'Tag':'Ordinary member';
	}
}
?>"}}*/
<?

?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>