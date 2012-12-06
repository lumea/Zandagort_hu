<?
include('../csatlak.php');
if (!isset($argv[1]) or $argv[1]!=$zanda_private_key) exit;

$uid=1;
$jelszo='password';
$jelszo_so=randomgen(32);
$jelszo_hash=hash('whirlpool',$jelszo.$jelszo_so.$rendszer_so);
$kozos_jelszo_hash=hash('whirlpool',$jelszo.$rendszer_so);
mysql_query('update userek set kozos_jelszo_hash="'.$kozos_jelszo_hash.'", jelszo_so="'.$jelszo_so.'", jelszo_hash="'.$jelszo_hash.'" where id='.$uid);
mysql_query('update zanda_nemlog.userek_ossz set jelszo_so="'.$jelszo_so.'", jelszo_hash="'.$jelszo_hash.'" where eredeti_id='.$uid);

echo 'kesz';
mysql_close($mysql_csatlakozas);
?>