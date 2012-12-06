<?
include('csatlak.php');
include('ujkuki.php');
header('Cache-Control: no-cache');
header('Expires: -1');
header('Content-type: text/javascript; charset=utf-8');

$_REQUEST['szov_id']=sanitint($_REQUEST['szov_id']);
if ($_REQUEST['szov_id']==0) $_REQUEST['szov_id']=$adataim['szovetseg'];

$szovetseg=mysql2row('select * from szovetsegek where id='.$_REQUEST['szov_id']);
if (!$szovetseg) kilep();

if ($adataim['szovetseg']==$szovetseg['id']) {//sajat szovi

$vanjoga_mod=$jogaim[9];
$vanjoga_ujtema=$jogaim[8];
$vanjoga_belso=$jogaim[1];
?>
/*{"ujtemajog":<?=$vanjoga_ujtema;?>,"temak":<?
echo mysql2jsonmatrix('
select ttt.t_id,ttt.cim,ttt.kommentek_szama,ttt.u_id,ttt.u_nev,ttt.uccso_datum,ttt.u2_id,ttt.u2_nev,ttt.t_kom
,if(o.uccso_komment=ttt.t_kom,0,1),'.$vanjoga_mod.'
,ttt.belso,ttt.vendeg
from (
select
t.id as t_id,t.cim,t.kommentek_szama,coalesce(u.id,0) as u_id,coalesce(u.nev,"") as u_nev,t.uccso_datum,coalesce(u2.id,0) as u2_id,coalesce(u2.nev,"") as u2_nev,t.uccso_komment as t_kom,t.belso,t.vendeg
from szov_forum_temak t
left join userek u on t.uccso_user=u.id
left join userek u2 on t.nyito_user=u2.id
where t.szov_id='.$szovetseg['id'].' and (t.belso=0 or '.$vanjoga_belso.'=1)
) ttt
left join szov_forum_tema_olv o on o.user_id='.$uid.' and o.tema_id=ttt.t_id
order by ttt.uccso_datum desc
');
?>}*/
<?

} else {//vendegseg

$aux=mysql2row('select * from szovetseg_vendegek where szov_id='.$szovetseg['id'].' and user_id='.$uid);
if (!$aux) kilep();

?>
/*{"ujtemajog":0,"temak":<?
echo mysql2jsonmatrix('
select ttt.t_id,ttt.cim,ttt.kommentek_szama,ttt.u_id,ttt.u_nev,ttt.uccso_datum,ttt.u2_id,ttt.u2_nev,ttt.t_kom
,if(o.uccso_komment=ttt.t_kom,0,1),0
,ttt.belso,ttt.vendeg
from (
select
t.id as t_id,t.cim,t.kommentek_szama,coalesce(u.id,0) as u_id,coalesce(u.nev,"") as u_nev,t.uccso_datum,coalesce(u2.id,0) as u2_id,coalesce(u2.nev,"") as u2_nev,t.uccso_komment as t_kom,t.belso,t.vendeg
from szov_forum_temak t
left join userek u on t.uccso_user=u.id
left join userek u2 on t.nyito_user=u2.id
where t.szov_id='.$szovetseg['id'].' and t.vendeg=1
) ttt
left join szov_forum_tema_olv o on o.user_id='.$uid.' and o.tema_id=ttt.t_id
order by ttt.uccso_datum desc
');
?>}*/
<?

}
?>
<? insert_into_php_debug_log(round(1000*(microtime(true)-$szkript_mikor_indul)));mysql_close($mysql_csatlakozas);?>