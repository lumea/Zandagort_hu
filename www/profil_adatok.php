<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

if (!$ismert) kilep();

?>
/*{"id":<?=$uid;
?>,"nev":<?=json_encode($adataim['nev']);
?>,"mail":<? if ($adataim['epp_most_helyettes_id']==0) echo json_encode($adataim['email']);else echo '""';
?>,"leendo_karrier":<?
echo $adataim['leendo_karrier'];
?>,"nepesseg":<?
echo mysql2num('select coalesce(sum(pop),0) from bolygo_ember be,bolygok b where b.tulaj='.$uid.' and b.id=be.bolygo_id and b.letezik=1');
?>,"karrier":<?
echo $adataim['karrier'];
?>,"speci":<?
echo $adataim['speci'];
?>,"elerheto_specik":<?
switch($adataim['karrier']) {
	case 1:echo '['.$adataim['speci_1_1'].','.$adataim['speci_1_2'].','.$adataim['speci_1_3'].']';break;
	case 2:echo '['.$adataim['speci_2_1'].','.$adataim['speci_2_2'].','.$adataim['speci_2_3'].','.$adataim['speci_2_4'].']';break;
	case 3:echo '['.$adataim['speci_3_1'].','.$adataim['speci_3_2'].','.$adataim['speci_3_3'].']';break;
	case 4:echo '['.$adataim['speci_4_1'].','.$adataim['speci_4_2'].']';break;
	default:echo '[]';break;
}
?>,"sztazis":0,"admin":<?
if ($uid==1) echo 1;else echo 0;//specko admin felulet csak akkor jelenik meg, ha en nezem
?>,"epp_most_helyettes_id":<?
echo $adataim['epp_most_helyettes_id'];
?>,"premium_szint":<?
echo $adataim['premium_szint'];
?>,"premium":<?
echo premium_szint();
?>,"premium_meddig":"<?
if ($adataim['premium']>0) echo ($lang_lang=='hu')?'korlátlan ideig':'indefinitely';
elseif (time()<strtotime($adataim['premium_emelt'])) echo $adataim['premium_emelt'];
elseif (time()<strtotime($adataim['premium_alap'])) echo $adataim['premium_alap'];
else '-';
?>","szamla_nev":<? if ($adataim['epp_most_helyettes_id']==0) echo json_encode($adataim['szamlazasi_nev']);else echo '""';
?>,"szamla_cim":<? if ($adataim['epp_most_helyettes_id']==0) echo json_encode($adataim['szamlazasi_cim']);else echo '""';
?>,"szamla_nev_input":<? if ($adataim['epp_most_helyettes_id']==0) echo json_encode(htmlspecialchars($adataim['szamlazasi_nev']));else echo '""';
?>,"szamla_cim_input":<? if ($adataim['epp_most_helyettes_id']==0) echo json_encode(htmlspecialchars($adataim['szamlazasi_cim']));else echo '""';
?>,"meghivo_id":<?
if ($adataim['epp_most_helyettes_id']==0) echo (int)$adataim['kin_keresztul_id'];else echo 0;
?>,"meghivo_nev":"<?
if ($adataim['kin_keresztul_id']>0) if ($adataim['epp_most_helyettes_id']==0) echo addslashes(mysql2num('select nev from userek where id='.$adataim['kin_keresztul_id']));
?>","helyettes_id":<?=((int)$adataim['helyettes_id']);
?>,"helyettes_id_nev":<?
$helyettes_aux=mysql2row('select nev from userek where id='.$adataim['helyettes_id']);
if ($helyettes_aux) echo json_encode($helyettes_aux['nev']);else echo '""';
?>,"helyettesitett_ido":<?
$helyettesitett_ido=((int)$adataim['helyettesitett_ido']);
if ($adataim['epp_most_helyettes_id']>0) {
	$helyettesitett_ido+=time()-strtotime($adataim['uccso_login']);
}
echo $helyettesitett_ido;
?>,"reg":<?
$mikortol=explode('-',$adataim['mikortol']);$mainap=getdate();
echo (int)((mktime(0,0,0,$mainap['mon'],$mainap['mday'],$mainap['year'],0)-mktime(0,0,0,$mikortol[1],$mikortol[2],$mikortol[0],0))/3600/24);



if ($adataim['epp_most_helyettes_id']==0) {
?>,"beallitasok":<?
	echo '{';
	echo '"iparag_jelzok":'.$user_beallitasok['iparag_jelzok'];
	echo ',"gyar_ikonok":'.$user_beallitasok['gyar_ikonok'];
	echo ',"kozos_flottak_listaban":'.$user_beallitasok['kozos_flottak_listaban'];
	echo ',"email_noti_eplista":'.$user_beallitasok['email_noti_eplista'];
	echo ',"email_noti_epites_alatt":'.$user_beallitasok['email_noti_epites_alatt'];
	echo ',"css_munkahelyi":'.$user_beallitasok['css_munkahelyi'];
	echo ',"chat_hu":'.$user_beallitasok['chat_hu'];
	echo ',"chat_en":'.$user_beallitasok['chat_en'];
	echo ',"badge_pub":'.$user_beallitasok['badge_pub'];
	echo '}';
}


?>,"avatar_fajlnev":"<?
if (strlen($adataim['avatar_ext'])) {
	echo 'p'.$uid.$adataim['avatar_crc'].'.'.$adataim['avatar_ext'];
} else echo '';
?>"<?
if (premium_szint()>0) {
?>,"nagy_avatar_fajlnev":"<?
if (strlen($adataim['nagy_avatar_ext'])) {
	echo 'p'.$uid.$adataim['avatar_crc'].'.'.$adataim['nagy_avatar_ext'];
} else echo '';
?>","jegyzetek":<?
$er2=mysql_query('select id,mikor,szoveg,sorszam from jegyzetek where tulaj='.$uid.' order by sorszam') or hiba(__FILE__,__LINE__,mysql_error());
$jegyzetek_szama=mysql_num_rows($er2);
echo '[';
$i=0;while($aux2=mysql_fetch_array($er2)) {
	$i++;if ($i>1) echo ',';
	echo '[';
	echo $aux2[0].',';
	echo json_encode($aux2[1]).',';
	echo json_encode(nl2br(htmlspecialchars($aux2[2]))).',';
	echo $aux2[3].',';
	echo $jegyzetek_szama.',';
	echo json_encode(htmlspecialchars($aux2[2]));//br nelkuli a textareanak
	echo ']';
}
echo ']';
}



?>,"akik_megosztottak_veled":<?
echo mysql2jsonmatrix('select u.id,u.nev,coalesce(timestampdiff(minute,u.uccso_akt,now()),coalesce('.$suti_hossz_percben.'-timestampdiff(minute,now(),u.session_ervenyesseg),coalesce(timestampdiff(minute,u.uccso_login,now()),timestampdiff(minute,u.mikortol,now())))),coalesce(sz.id,0),coalesce(sz.nev,""),u.pontszam_exp_atlag,count(b.id),if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),"")
from userek u
inner join aktivitas_megosztas am on am.ki=u.id and am.kivel='.$uid.'
left join szovetsegek sz on u.szovetseg=sz.id
left join bolygok b on b.tulaj=u.id
group by u.id
order by u.nev');
?>,"akikkel_megosztottad":<?
echo mysql2jsonmatrix('
select u.id,u.nev,if(length(u.avatar_ext)>0,concat("p",u.id,u.avatar_crc,".",u.avatar_ext),"") from userek u, aktivitas_megosztas am where am.kivel=u.id and am.ki='.$uid.'
union all
select 0,"",""
');



if ($adataim['admin']==1) {
?>,"egyeb_info":<?
echo '[';
$res=mysql_query('select * from ido') or hiba(__FILE__,__LINE__,mysql_error());
$aux=mysql_fetch_row($res);
for($k=0;$k<mysql_num_fields($res);$k++) echo '["'.mysql_field_name($res,$k).'","'.$aux[$k].'"],';
echo '["szim_log",'.json_encode('...'.nl2br(file_get_contents('../szim/szim_log',0,null,filesize('../szim/szim_log')-100))).']';
echo ']';
}


?>}*/
<?

?>
<? mysql_close($mysql_csatlakozas);?>