<script src="jskod.php" type="text/javascript"></script>
<script src="actb.js" type="text/javascript"></script>
<script src="actb_common.js" type="text/javascript"></script>
<script src="jskod_userek.php" type="text/javascript"></script>
<script src="jskod_eroforrasok.php" type="text/javascript"></script>
</head>
<? flush(); ?>
<body onload="init()" id="jatek_body"><div id="nagy_keret">
<div id="fejlec"></div><div id="belso_keret">

<? if (premium_szint()==2) { ?>
<div id="fullscreen_terkep">
	<div id="fullscreen_terkep_kontroll_div">
	<table style="margin: 0 auto"><tr><td>
		<table><tr>
			<td></td>
			<td><a href="" onclick="document.getElementById('fullscreen_terkep_y').value=Math.round(parseInt(document.getElementById('fullscreen_terkep_y').value)-parseInt(document.getElementById('fullscreen_terkep_zoom').value)/4);return refresh_fullscreen_terkep()"><img src="img/ikonok/arrow_up.gif" /></a></td>
			<td></td>
		</tr><tr>
			<td><a href="" onclick="document.getElementById('fullscreen_terkep_x').value=Math.round(parseInt(document.getElementById('fullscreen_terkep_x').value)-parseInt(document.getElementById('fullscreen_terkep_zoom').value)/4);return refresh_fullscreen_terkep()"><img src="img/ikonok/arrow_left.gif" /></a></td>
			<td><a href="" onclick="$('fullscreen_terkep_x').value=0;$('fullscreen_terkep_y').value=0;return refresh_fullscreen_terkep()"><img src="img/ikonok/center.gif" /></a></td>
			<td><a href="" onclick="document.getElementById('fullscreen_terkep_x').value=Math.round(parseInt(document.getElementById('fullscreen_terkep_x').value)+parseInt(document.getElementById('fullscreen_terkep_zoom').value)/4);return refresh_fullscreen_terkep()"><img src="img/ikonok/arrow_right.gif" /></a></td>
		</tr><tr>
			<td></td>
			<td><a href="" onclick="document.getElementById('fullscreen_terkep_y').value=Math.round(parseInt(document.getElementById('fullscreen_terkep_y').value)+parseInt(document.getElementById('fullscreen_terkep_zoom').value)/4);return refresh_fullscreen_terkep()"><img src="img/ikonok/arrow_down.gif" /></a></td>
			<td></td>
		</tr></table>
	</td><td>
		<table><tr><td>
			<a href="" onclick="if (parseInt(document.getElementById('fullscreen_terkep_zoom').value)>1000) document.getElementById('fullscreen_terkep_zoom').value=Math.round(parseInt(document.getElementById('fullscreen_terkep_zoom').value)/2);return refresh_fullscreen_terkep()"><img src="img/ikonok/add.gif" id="fullscreen_terkep_zoom_in_ikon" /></a>
		</td></tr><tr><td>
			<a href="" onclick="if (parseInt(document.getElementById('fullscreen_terkep_zoom').value)<128000) document.getElementById('fullscreen_terkep_zoom').value=Math.round(parseInt(document.getElementById('fullscreen_terkep_zoom').value)*2);return refresh_fullscreen_terkep()"><img src="img/ikonok/delete-ff.gif" id="fullscreen_terkep_zoom_out_ikon" /></a>
		</td></tr></table>
	</td></tr></table>
	<form onsubmit="return refresh_fullscreen_terkep()">
	<p>
		<?=$lang[$lang_lang]['index_belso.php']['Alapszínezés'];?>:<br /><select id="fullscreen_terkep_par_asz">
			<option value="0"><?=$lang[$lang_lang]['index_belso.php']['semmilyen'];?></option>
			<option value="1" selected="selected"><?=$lang[$lang_lang]['index_belso.php']['osztály'];?></option>
			<option value="2"><?=$lang[$lang_lang]['index_belso.php']['diplomácia'];?></option>
		</select>
	</p>
	<p>
		<?=$lang[$lang_lang]['index_belso.php']['Bolygóméretek'];?>:<br /><select id="fullscreen_terkep_par_kbm">
			<? for($i=1;$i<=10;$i++) { ?><option value="<?=$i;?>"<? if($i==1)echo' selected="selected"';?>><?=$i;?></option><? } ?>
		</select> / <select id="fullscreen_terkep_par_nbm">
			<? for($i=1;$i<=10;$i++) { ?><option value="<?=$i;?>"<? if($i==2)echo' selected="selected"';?>><?=$i;?></option><? } ?>
		</select> / <select id="fullscreen_terkep_par_pbm">
			<? for($i=1;$i<=10;$i++) { ?><option value="<?=$i;?>"<? if($i==4)echo' selected="selected"';?>><?=$i;?></option><? } ?>
		</select><br />
		<?=$lang[$lang_lang]['index_belso.php']['NPC'];?> / <?=$lang[$lang_lang]['index_belso.php']['lakott'];?> / <?=$lang[$lang_lang]['index_belso.php']['kijelölt'];?>
	</p>
	<p>
		<?=$lang[$lang_lang]['index_belso.php']['Bolygónevek'];?>:<br /><select id="fullscreen_terkep_par_bn">
			<option value="0"><?=$lang[$lang_lang]['index_belso.php']['nincsenek'];?></option>
			<option value="1" selected="selected"><?=$lang[$lang_lang]['index_belso.php']['csak lakottak'];?></option>
			<option value="2"><?=$lang[$lang_lang]['index_belso.php']['minden'];?></option>
		</select><br />
		(<?=$lang[$lang_lang]['index_belso.php']['nagyobb zoomnál jelenik meg'];?>)
	</p>
	<p>
		<?=$lang[$lang_lang]['index_belso.php']['Hexák'];?>:<br /><select id="fullscreen_terkep_par_ter">
			<option value="0"><?=$lang[$lang_lang]['index_belso.php']['nincsenek'];?></option>
			<option value="1" selected="selected"><?=$lang[$lang_lang]['index_belso.php']['vannak'];?></option>
		</select><br />
	</p>
	<p>
		<?=$lang[$lang_lang]['index_belso.php']['Flották'];?>:<br /><select id="fullscreen_terkep_par_flottak">
			<option value="0"><?=$lang[$lang_lang]['index_belso.php']['nincsenek'];?></option>
			<option value="1" selected="selected"><?=$lang[$lang_lang]['index_belso.php']['vannak'];?></option>
		</select><br />
	</p>
	<p><?=$lang[$lang_lang]['index_belso.php']['Játékosok/szövetségek'];?>:</p>
	<?
	$rgb=array('FF4343','FFFF43','43FF43','43FFFF','4343FF');
	for($i=1;$i<=5;$i++) { ?>
	<p>
		<input type="text" class="terkep_usernev" style="background:#<?=$rgb[$i-1];?>" id="fullscreen_terkep_par_psz<?=$i;?>n" value="" /> <img src="img/kis_szinkocka.png" onclick="return szinkocka_nyit(<?=$i;?>)" />
		<div id="fullscreen_terkep_par_psz<?=$i;?>kocka" style="display:none" onclick="return szinkocka_klikk(event,<?=$i;?>)"><img src="img/szinkocka.png" /></div>
		<input type="hidden" id="fullscreen_terkep_par_psz<?=$i;?>sz" value="<?=$rgb[$i-1];?>" />
	</p>
	<? } ?>
	<p style="text-align: center">
		<input type="submit" class="gomb" value="<?=$lang[$lang_lang]['index_belso.php']['FRISSÍT'];?>" />
	</p>
<?/*?>
	<br />
	<p style="text-align: center">
		<a href="#" onclick="return false">print verzió</a>
	</p>
<?*/?>
	<br /><br />
	<p style="text-align: center">
		<a href="#" onclick="return toggle('fullscreen_terkep')"><?=$lang[$lang_lang]['index_belso.php']['bezár'];?></a>
	</p>
	</form>
	</div>
	<div id="fullscreen_terkep_div" onclick="return fullscreen_terkep_klikk(event)"><img id="fullscreen_terkep_img" src="minimap_v2.php" /></div>
	<input type="hidden" id="fullscreen_terkep_x" value="0" />
	<input type="hidden" id="fullscreen_terkep_y" value="0" />
	<input type="hidden" id="fullscreen_terkep_zoom" value="128000" />
</div>
<? } ?>

<div id="menu">
	<div id="bolygo_lista_kont">
		<div id="bolygo_lista"></div>
	</div>
	<div id="flotta_lista_kont">
		<div id="flotta_lista"></div>
	</div>
	<div id="menu_egyeb">
		<a href="" id="menu_egyeb_birod" onclick="return oldal_nyit('birod');" style="display: block; position: absolute; top: 19px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="menu_egyeb_szovetseg" onclick="aktiv_szovetseg=0;return oldal_nyit('szovetseg');" style="display: block; position: absolute; top: 57px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="olvasatlan_kommentek_szama" onclick="aktiv_szovetseg=0;return oldal_nyit('szovetseg');" style="display: block; position: absolute; top: 63px; left: 38px; width: 30px; color:rgb(255,0,0); font-weight: bold"></a>
		<a href="" id="menu_egyeb_terkep" class="aktiv_menu" onclick="return oldal_nyit('terkep');" style="display: block; position: absolute; top: 95px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="menu_egyeb_felder" onclick="return oldal_nyit('felder');" style="display: block; position: absolute; top: 133px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="menu_egyeb_komm" onclick="return oldal_nyit('komm');" style="display: block; position: absolute; top: 170px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="olvasatlan_levelek_szama" onclick="return oldal_nyit('komm');" style="display: block; position: absolute; top: 175px; right: 35px; font-weight:bold"></a>
		<a href="" id="menu_egyeb_komm_2" onclick="return oldal_nyit('komm2');" style="display: block; position: absolute; top: 207px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="olvasatlan_levelek_szama2" onclick="return oldal_nyit('komm2');" style="display: block; position: absolute; top: 212px; right: 35px; font-weight:bold"></a>
		<a href="" id="menu_egyeb_komm_3" onclick="return oldal_nyit('komm3');" style="display: block; position: absolute; top: 244px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="olvasatlan_levelek_szama3" onclick="return oldal_nyit('komm3');" style="display: block; position: absolute; top: 249px; right: 35px; font-weight:bold"></a>
		<a href="" id="menu_egyeb_cset" onclick="return oldal_nyit('cset');" style="display: block; position: absolute; top: 281px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="olvasatlan_csetek_jelzo" onclick="return oldal_nyit('cset');" style="display: block; position: absolute; top: 294px; left: 178px; width: 30px; color:rgb(255,0,0); font-weight: bold"></a>
		<a href="" id="menu_egyeb_profil" onclick="return oldal_nyit('profil');" style="display: block; position: absolute; top: 317px; left: 28px; width: 160px; height: 32px;"></a>
		<a href="" id="menu_egyeb_help" onclick="return oldal_nyit('help');" style="display: block; position: absolute; top: 355px; left: 28px; width: 160px; height: 32px;"></a>
		<a href=".?logout" style="display: block; position: absolute; top: 392px; left: 28px; width: 130px; height: 23px; padding: 10px 0 0 30px"></a>
	</div>
</div>


<div id="cont">

<img src="img/ikonok/ajax-loader.gif" id="tolto_ikon" style="position: absolute; top: 0px; left: 300px; display:none" />

<div id="oldal_bolygo_hatter"><div id="oldal_bolygo"><div style="margin-left: 15px">
	<div style="position: absolute; left: 395px; top: 30px"><a href="" onclick="return jump_to_help(0,document.getElementById('aktiv_bolygo_reszletei').style.display=='block'?2:8)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1 id="aktiv_bolygo_neve"></h1>
	<div id="aktiv_bolygo_alapadatai" style="height: 380px"></div>
	<div id="aktiv_bolygo_reszletei" style="display:none">
		<div style="position: relative; height: 50px">
			<h2 style="position: absolute; top: 0; left: 0px; width: 100px; height: 30px"><a href="" id="menu_aloldal_oko" onclick="return aloldal_nyit('oko')"><?=$lang[$lang_lang]['index_belso.php']['Ökoszféra'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: 130px; width: 100px; height: 30px"><a href="" id="menu_aloldal_gazd" onclick="return aloldal_nyit('gazd')"><?=$lang[$lang_lang]['index_belso.php']['Gazdaság'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: 260px; width: 100px; height: 30px"><a href="" id="menu_aloldal_haboru" onclick="return aloldal_nyit('haboru')"><?=$lang[$lang_lang]['index_belso.php']['Háború'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: <? if ($lang_lang=='hu') echo '370';else echo '350'; ?>px; width: 100px; height: 30px"><a href="" id="menu_aloldal_tozsde" onclick="return aloldal_nyit('tozsde')"><?=$lang[$lang_lang]['index_belso.php']['Tőzsde'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: <? if ($lang_lang=='hu') echo '480';else echo '485'; ?>px; width: 160px; height: 30px"><a href="" id="menu_aloldal_szabadpiac" onclick="return aloldal_nyit('szabadpiac')"><?=$lang[$lang_lang]['index_belso.php']['Szabad piac'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: 640px; width: 100px; height: 30px"><a href="" id="menu_aloldal_egyeb" onclick="return aloldal_nyit('egyeb')"><?=$lang[$lang_lang]['index_belso.php']['Szállítás'];?></a></h2>
		</div>
		<div id="bolygo_aloldal_oko">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,3)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<div id="aktiv_bolygo_kornyezeti_allapota"></div>
			<div id="aktiv_bolygo_fajai"></div>
			<div id="aktiv_bolygo_okoszim"></div>
		</div>
		<div id="bolygo_aloldal_gazd">
			<div style="position: relative; float: left; width: 370px">
				<div id="aktiv_bolygo_gyarai"></div>
				<div id="aktiv_bolygo_volt_gyarai"></div>
				<div id="aktiv_bolygo_leendo_gyarai"></div>
				<div id="aktiv_bolygo_epit_queue"></div>
			</div>
			<div style="position: relative; float: right; width: 380px">
				<div style="position: absolute; right: 15px; top: 0px; z-index: 100"><a href="" onclick="return jump_to_help(0,4)"><img src="img/ikonok/help_ikon.gif" /></a></div>
				<div id="aktiv_bolygo_eroforrasai"></div>
			</div>
			<br style="clear:both" />
		</div>
		<div id="bolygo_aloldal_haboru">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,5)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<h2 style="margin-top: 0px"><?=$lang[$lang_lang]['index_belso.php']['Legyártott űrhajók'];?></h2>
			<div id="aktiv_bolygo_urhajoi"></div>
			<h2><?=$lang[$lang_lang]['index_belso.php']['Állomásozó flották'];?></h2>
			<div id="aktiv_bolygo_flottai"></div>
			<h2><?=$lang[$lang_lang]['index_belso.php']['Űrkikötő'];?></h2>
			<div id="aktiv_bolygo_kikoto"></div>
		</div>
		<div id="bolygo_aloldal_tozsde">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,6)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<h2 style="margin-top: 0px"><a href="#" onclick="return frissit_tozsde()"><?=$lang[$lang_lang]['index_belso.php']['FRISSÍT'];?></a></h2>
			<h2 style="margin-top: 0px"><?=$lang[$lang_lang]['index_belso.php']['Vagyonod'];?>: <span id="tozsde_vagyonod">0</span> SHY</h2>
			<h2 style="margin-top: 0px"><?=$lang[$lang_lang]['index_belso.php']['Teleport kapacitás'];?>: <span id="tozsde_teleport_kapacitas">0 / 0</span></h2>
			<h2 style="margin-top: 0px"><?=$lang[$lang_lang]['index_belso.php']['Napi vételi limit resetelése'];?>: <span id="napi_veteli_limit_reset"></span></h2>
			<div id="tozsde_tablazat"></div>
<? if (premium_szint()==2) { ?>
			<div style="margin: 10px auto 0px auto">
				<h2><?=$lang[$lang_lang]['index_belso.php']['Grafikon'];?></h2>
				<p style="text-align: center">
					<a href="" onclick="tozsde_graf_felbontas=0;return frissit_tozsde_graf()"><?=$lang[$lang_lang]['index_belso.php']['óra'];?></a>
					<a href="" onclick="tozsde_graf_felbontas=1;return frissit_tozsde_graf()"><?=$lang[$lang_lang]['index_belso.php']['harmadnap'];?></a>
					<a href="" onclick="tozsde_graf_felbontas=2;return frissit_tozsde_graf()"><?=$lang[$lang_lang]['index_belso.php']['nap'];?></a>
					(<?=$lang[$lang_lang]['index_belso.php']['ha váltasz, várd meg, amíg betölt'];?>)
				</p>
				<br />
				<p style="text-align: center"><img id="tozsde_graf_img" src="tozsde_graf.php?termek=0" /></p>
			</div>
<? } ?>
		</div>
		<div id="bolygo_aloldal_szabadpiac">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,6)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<h2 style="margin-top: 0px"><a href="#" onclick="return frissit_szabadpiac()"><?=$lang[$lang_lang]['index_belso.php']['FRISSÍT'];?></a></h2>
			<h2 style="margin-top: 0px"><?=$lang[$lang_lang]['index_belso.php']['Vagyonod'];?>: <span id="szabadpiac_vagyonod">0</span> SHY</h2>
			<h2 style="margin-top: 0px"><?=$lang[$lang_lang]['index_belso.php']['Teleport kapacitás'];?>: <span id="szabadpiac_teleport_kapacitas">0 / 0</span></h2>
			<div id="szabadpiac_tablazat"></div>
		</div>
		<div id="bolygo_aloldal_egyeb">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,7)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<h2 style="margin-top: 0px"><?=$lang[$lang_lang]['index_belso.php']['Teleport kapacitás'];?>: <span id="egyeb_teleport_kapacitas">0 / 0</span></h2>
			<div id="bolygo_aloldal_egyeb_szallitas"></div>
		</div>
	</div>
	<div id="aktiv_bolygo_riportjai" style="display:none">
	</div>
</div></div></div>

<div id="oldal_flotta"><div style="margin-left: 15px">
	<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,document.getElementById('aktiv_flotta_reszletei').style.display=='block'?9:10)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1 id="aktiv_flotta_neve"></h1>
	<div id="aktiv_flotta_alapadatai"></div>
	<div id="aktiv_flotta_reszletei" style="display:none">
		<h2><?=$lang[$lang_lang]['index_belso.php']['Összetétel'];?></h2>
		<div id="aktiv_flotta_urhajoi"></div>
		<div id="aktiv_flotta_sajat_reszletei" style="display:none">
			<h2><?=$lang[$lang_lang]['index_belso.php']['Barátságos flották a környéken'];?></h2>
			<div id="aktiv_flotta_kornyezete"></div>
			<h2><?=$lang[$lang_lang]['index_belso.php']['Egyéb flották a környéken'];?></h2>
			<div id="aktiv_flotta_egyeb_kornyezete"></div>
		</div>
		<h2><?=$lang[$lang_lang]['index_belso.php']['Részflották'];?></h2>
		<div id="aktiv_flotta_reszflottai"></div>
	</div>
</div></div>

<div id="oldal_user"><div class="peding">
	<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,29)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1 id="aktiv_user_neve"></h1>
	<div id="aktiv_user_adatai"></div>
	<h2><?=$lang[$lang_lang]['index_belso.php']['Bolygói'];?> (<span id="aktiv_user_bolygoszama">0</span>)</h2>
	<div id="aktiv_user_bolygoi"></div>
</div></div>

<div id="oldal_birod"><div class="peding">
<div style="position: absolute; right: 20px; top: 10px; z-index: 10"><a href="" onclick="return jump_to_help(0,11)"><img src="img/ikonok/help_ikon.gif" /></a></div>
<? /*if ($_REQUEST['reg']=='siker') { ?>
	<div style="border: red 2px solid; background: black; padding: 10px; width: 50%; margin: 0 auto">
		<?=$lang[$lang_lang]['index_belso.php']['Üdv Zandagort világában'];?>
	</div>
<? }*/ ?>
	<div style="position: relative; height: 50px">
		<h2 style="position: absolute; top: 0; left: 10px; width: 70px; height: 30px"><a href="" id="menu_attekintes_aloldal_birodalom" class="aktiv_aloldal" onclick="return attekintes_aloldal_nyit('birodalom')"><?=$lang[$lang_lang]['index_belso.php']['Birodalom'];?></a></h2>
		<h2 style="position: absolute; top: 0; left: <?=($lang_lang=='hu'?150:120)?>px; width: 70px; height: 30px"><a href="" id="menu_attekintes_aloldal_bolygok" onclick="return attekintes_aloldal_nyit('bolygok')"><?=$lang[$lang_lang]['index_belso.php']['Bolygók'];?></a></h2>
		<h2 style="position: absolute; top: 0; left: <?=($lang_lang=='hu'?270:240)?>px; width: 120px; height: 30px"><a href="" id="menu_attekintes_aloldal_flottak" onclick="return attekintes_aloldal_nyit('flottak')"><?=$lang[$lang_lang]['index_belso.php']['Flották'];?></a></h2>
		<h2 style="position: absolute; top: 0; left: <?=($lang_lang=='hu'?380:350)?>px; width: 160px; height: 30px"><a href="" id="menu_attekintes_aloldal_kozos_flottak" onclick="return attekintes_aloldal_nyit('kozos_flottak')"><?=$lang[$lang_lang]['index_belso.php']['Közös flották'];?></a></h2>
		<h2 style="position: absolute; top: 0; left: <?=($lang_lang=='hu'?540:540)?>px; width: 120px; height: 30px"><a href="" id="menu_attekintes_aloldal_hajok" onclick="return attekintes_aloldal_nyit('hajok')"><?=$lang[$lang_lang]['index_belso.php']['Hajók'];?></a></h2>
		<h2 style="position: absolute; top: 0; left: <?=($lang_lang=='hu'?640:650)?>px; width: 90px; height: 30px"><a href="" id="menu_attekintes_aloldal_egyeb" onclick="return attekintes_aloldal_nyit('egyeb')"><?=$lang[$lang_lang]['index_belso.php']['Szállítás'];?></a></h2>
	</div>
	<div id="attekintes_aloldal_birodalom">
		<div id="attekintes_birod"></div>
		<h2><?=$lang[$lang_lang]['index_belso.php']['Tech-szint'];?>: <span id="attekintes_tech"></span></h2>
		<div id="attekintes_most_szint"></div>
		<p id="tech_kov_hatar"></p>
		<div id="attekintes_kov_szint"></div>
		<h2><?=$lang[$lang_lang]['index_belso.php']['Kutatás'];?> <a href="" onclick="return jump_to_help(0,35)"><img src="img/ikonok/help_ikon.gif" /></a></h2>
		<div id="attekintes_kutatas"></div>
		<h2><?=$lang[$lang_lang]['index_belso.php']['Fejlesztés'];?> <a href="" onclick="return jump_to_help(0,36)"><img src="img/ikonok/help_ikon.gif" /></a></h2>
		<div id="attekintes_fejlesztes"></div>
		<h2><?=$lang[$lang_lang]['index_belso.php']['Pénzátutalás'];?> <a href="" onclick="return jump_to_help(0,37)"><img src="img/ikonok/help_ikon.gif" /></a></h2>
		<div id="attekintes_penztranszfer"></div>
		<form onsubmit="return penzatutalas();">
			<p><?=$lang[$lang_lang]['index_belso.php']['Kinek?'];?> <input type="text" id="penzat_kinek_nev" class="bolygonev" /></p>
			<p><?=$lang[$lang_lang]['index_belso.php']['Mennyit?'];?> <input type="text" id="penzat_mennyi" class="bolygonev" /></p>
			<p><input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Küldés'];?>" /></p>
		</form>
	</div>
	<div id="attekintes_aloldal_bolygok">
		<div id="attekintes_bolygoid"></div>
	</div>
	<div id="attekintes_aloldal_flottak">
		<div id="attekintes_flottaid"></div>
	</div>
	<div id="attekintes_aloldal_kozos_flottak">
		<div id="attekintes_kozos_flottaid"></div>
	</div>
	<div id="attekintes_aloldal_hajok">
		<div id="attekintes_hajoid"></div>
	</div>
	<div id="attekintes_aloldal_egyeb">
		<div id="attekintes_transzfer_osszefoglalo"></div>
	</div>
</div></div>

<div id="oldal_profil"><div class="peding">
	<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,28)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h2><?=$lang[$lang_lang]['index_belso.php']['Profilod'];?></h2>
	<div id="profil_alap"></div>
	<h2><?=$lang[$lang_lang]['index_belso.php']['Játékos kereső'];?></h2>
	<form onsubmit="return jatekos_kereso();">
		<p><input type="text" id="jatekos_kereso_nev" class="bolygonev" /></p>
		<p><input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Megnézem'];?>" /></p>
	</form>
<? if ($adataim['epp_most_helyettes_id']==0) { ?>
	<h2><?=$lang[$lang_lang]['index_belso.php']['Jelszó módosítása'];?></h2>
	<form onsubmit="return jelszo_modositasa();">
		<table>
		<tr><td><?=$lang[$lang_lang]['index_belso.php']['régi jelszó'];?>:</td><td><input type="password" id="jelszo_mod_regi" class="bolygonev" /></td></tr>
		<tr><td><?=$lang[$lang_lang]['index_belso.php']['új jelszó'];?>:</td><td><input type="password" id="jelszo_mod_uj1" class="bolygonev" /></td></tr>
		<tr><td><?=$lang[$lang_lang]['index_belso.php']['új még egyszer'];?>:</td><td><input type="password" id="jelszo_mod_uj2" class="bolygonev" /></td></tr>
		</table>
		<p><input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Módosítom'];?>" /></p>
	</form>
	<h2><a href="" onclick="return toggle('reg_torles_div')"><?=$lang[$lang_lang]['index_belso.php']['Regisztráció törlése'];?></a></h2>
	<div id="reg_torles_div" style="display:none">
	<form action="." method="post" onsubmit="return confirm('<?=$lang[$lang_lang]['index_belso.php']['Egész biztosan? Nem lehet visszacsinálni.'];?>');">
		<p><?=$lang[$lang_lang]['index_belso.php']['A törlés végleges, úgyhogy csak akkor tedd, ha valóban ezt szeretnéd (pl újra akarsz regisztrálni szimpatikusabb helyen).'];?></p>
		<table>
		<tr><td><?=$lang[$lang_lang]['index_belso.php']['jelszó'];?>:</td><td><input type="password" name="jelszo" class="bolygonev" /></td></tr>
		</table>
		<p><input type="submit" name="del_reg" value="<?=$lang[$lang_lang]['index_belso.php']['Törölni szeretném magam'];?>" /></p>
	</form>
	</div>
<? } ?>
</div></div>

<div id="oldal_szovetseg"><div class="peding">
	<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,document.getElementById('aktiv_szovetseg_neve').style.display=='none'?12:(document.getElementById('aktiv_szovetseg_reszletei').style.display=='block'?14:21))"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1 id="aktiv_szovetseg_neve"></h1>
	<div id="aktiv_szovetseg_alapadatai"></div>
	<div id="aktiv_szovetseg_reszletei" style="display:none">
		<div style="position: relative; height: 50px">
			<h2 style="position: absolute; top: 0; left: 30px; width: 70px; height: 30px"><a href="" id="menu_szov_aloldal_forum" onclick="return szov_aloldal_nyit('forum')"><?=$lang[$lang_lang]['index_belso.php']['Fórum'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: 150px; width: 120px; height: 30px"><a href="" id="menu_szov_aloldal_diplo" onclick="return szov_aloldal_nyit('diplo')"><?=$lang[$lang_lang]['index_belso.php']['Diplomácia'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: <?=($lang_lang=='hu'?325:305)?>px; width: 160px; height: 30px"><a href="" id="menu_szov_aloldal_kozos" onclick="return szov_aloldal_nyit('kozos')"><?=$lang[$lang_lang]['index_belso.php']['Közös flották'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: 505px; width: 120px; height: 30px"><a href="" id="menu_szov_aloldal_tagok" onclick="return szov_aloldal_nyit('tagok')"><?=$lang[$lang_lang]['index_belso.php']['Tagok'];?></a></h2>
			<h2 style="position: absolute; top: 0; left: <?=($lang_lang=='hu'?630:660)?>px; width: <?=($lang_lang=='hu'?120:90)?>px; height: 30px"><a href="" id="menu_szov_aloldal_tisztek" onclick="return szov_aloldal_nyit('tisztek')"><?=$lang[$lang_lang]['index_belso.php']['Tisztségek'];?></a></h2>
		</div>
		<div id="szovetseg_aloldal_forum">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,15)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<div id="szov_forum_temak_div">
				<h2><a href="" onclick="return frissit_szov_forum()"><?=$lang[$lang_lang]['index_belso.php']['Témák'];?></a> <a href="" onclick="return frissit_szov_forum()" id="van_e_olvasatlan_ikon" style="display:none"><span id="van_e_olvasatlan_szam" style="font-size:8pt"></span><img src="img/ikonok/exclamation.gif" /></a><span id="szov_forum_uj_tema_span"> <a href="" onclick="szov_forum_aktiv_tema_id=0;return frissit_szov_forum_tema()" style="font-size:8pt">(<?=$lang[$lang_lang]['index_belso.php']['új téma'];?>)</a></span></h2>
				<div id="szov_forum_tema_lista"></div>
			</div>
			<div id="szov_forum_kommentek_div" style="display:none">
				<form id="szov_forum_komment_form" onsubmit="return szov_forum_uj_komment_kuldese();">
					<input type="hidden" id="szov_forum_regi_tema_id" />
					<div id="szov_forum_regi_tema_div">
						<h2><a href="" onclick="return frissit_szov_forum()"><?=$lang[$lang_lang]['index_belso.php']['Témák'];?></a> / <span id="szov_forum_aktiv_tema_cime"></span> <span id="szov_forum_szerk_tema_span" style="font-size:8pt">(<a href="" onclick="return toggle('szov_forum_szerk_tema_div')"><?=$lang[$lang_lang]['index_belso.php']['szerk'];?></a>)</span> <a href="" onclick="return kommentek_elozo_oldal()" style="font-size:8pt"><img src="img/ikonok/arrow_left.gif" id="kommentek_elozo_oldal_ikon" /> <?=$lang[$lang_lang]['index_belso.php']['előző oldal'];?></a> <span style="font-size:8pt"><span id="komment_oldalszam_span"></span>/<span id="komment_oldalak_szama_span"></span></span> <a href="" onclick="return kommentek_kovetkezo_oldal()" style="font-size:8pt"><img src="img/ikonok/arrow_right.gif" id="kommentek_kovetkezo_oldal_ikon" /> <?=$lang[$lang_lang]['index_belso.php']['következő oldal'];?></a></h2>
						<div id="szov_forum_szerk_tema_div" style="display:none">
							<h2><span id="szov_forum_szerk_tema_belso_span" style="font-size: 8pt; display: none">(<input type="checkbox" id="szov_forum_szerk_tema_belso" /> <?=$lang[$lang_lang]['index_belso.php']['belső'];?>) </span><span id="szov_forum_szerk_tema_vendeg_span" style="font-size: 8pt; display: none">(<input type="checkbox" id="szov_forum_szerk_tema_vendeg" /> <?=$lang[$lang_lang]['index_belso.php']['vendég'];?>) </span><input type="text" class="bolygonev" id="szov_forum_szerk_tema" /> <a href="" onclick="return szov_forum_tema_szerkesztese()" title="<?=$lang[$lang_lang]['index_belso.php']['Mentés'];?>"><img src="img/ikonok/disk.gif" alt="" /></h2>
						</div>
						<h3><a href="" onclick="return toggle('szov_forum_uj_komment_div')"><?=$lang[$lang_lang]['index_belso.php']['Új komment'];?>...</a></h3>
					</div>
					<div id="szov_forum_uj_tema_div" style="display:none">
						<h2><?=$lang[$lang_lang]['index_belso.php']['Új téma'];?>: <span id="szov_forum_uj_tema_belso_span" style="font-size: 8pt; display: none">(<input type="checkbox" id="szov_forum_uj_tema_belso" /> <?=$lang[$lang_lang]['index_belso.php']['belső'];?>) </span><span id="szov_forum_uj_tema_vendeg_span" style="font-size: 8pt; display: none">(<input type="checkbox" id="szov_forum_uj_tema_vendeg" /> <?=$lang[$lang_lang]['index_belso.php']['vendég'];?>) </span><input type="text" class="bolygonev" id="szov_forum_uj_tema" /> <a href="" onclick="return frissit_szov_forum()" title="<?=$lang[$lang_lang]['index_belso.php']['Mégse'];?>"><img src="img/ikonok/cross.gif" alt="" /></a></h2>
					</div>
					<div id="szov_forum_uj_komment_div" style="display:none">
						<p><textarea id="szov_forum_uj_komment" class="szovegdoboz" style="width: 600px; height: 200px"></textarea></p>
						<p><input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Ezt mondom'];?>" /></p>
					</div>
				</form>
				<div id="szov_forum_aktiv_tema"></div>
				<div id="szov_forum_regi_tema_div_also">
					<h2><a href="" onclick="return kommentek_elozo_oldal()" style="font-size:8pt"><img src="img/ikonok/arrow_left.gif" id="kommentek_elozo_oldal_ikon_also" /> <?=$lang[$lang_lang]['index_belso.php']['előző oldal'];?></a> <span style="font-size:8pt"><span id="komment_oldalszam_span_also"></span>/<span id="komment_oldalak_szama_span_also"></span></span> <a href="" onclick="return kommentek_kovetkezo_oldal()" style="font-size:8pt"><img src="img/ikonok/arrow_right.gif" id="kommentek_kovetkezo_oldal_ikon_also" /> <?=$lang[$lang_lang]['index_belso.php']['következő oldal'];?></a></h2>
				</div>
			</div>
		</div>
		<div id="szovetseg_aloldal_diplo">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,17)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<div id="aktiv_szovetseg_diplo_statuszok"></div>
			<div id="aktiv_szovetseg_diplo_leendo_statuszok"></div>
			<div id="aktiv_szovetseg_diplo_uj_statusz"></div>
			<div id="aktiv_szovetseg_diplo_ajanlatok"></div>
			<div id="aktiv_szovetseg_diplo_uj_ajanlat"></div>
			<div id="aktiv_szovetseg_diplo_vendegek"></div>
			<div id="aktiv_szovetseg_diplo_uj_vendeg"></div>
		</div>
		<div id="szovetseg_aloldal_kozos">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,34)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<div id="aktiv_szovetseg_kozos_flottai"></div>
		</div>
		<div id="szovetseg_aloldal_tagok">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,19)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<div id="aktiv_szovetseg_tagjai"></div>
			<div id="aktiv_szovetseg_tagjai_egyebek"></div>
		</div>
		<div id="szovetseg_aloldal_tisztek">
			<div style="position: absolute; right: 15px; top: 0px"><a href="" onclick="return jump_to_help(0,20)"><img src="img/ikonok/help_ikon.gif" /></a></div>
			<h2 style="margin-top: 0px"><?=$lang[$lang_lang]['index_belso.php']['Tisztségek jogosultságai'];?><span style="font-size:8pt" id="tiszt_jog_szerk_gomb"></span></h2>
			<div id="aktiv_szovetseg_tiszt_jogai"></div>
		</div>
	</div>
	<div id="szovetseg_vendegek_div">
		<br />
		<hr style="width: 80%; margin: 0 auto" />
		<h1><?=$lang[$lang_lang]['index_belso.php']['Szövetségek, ahol vendég vagy'];?></h1>
		<div id="szovetseg_vendegek_lista"></div>
	</div>
	<br />
	<hr style="width: 80%; margin: 0 auto" />
	<div>
		<h1><?=$lang[$lang_lang]['index_belso.php']['Szövetség kereső'];?></h1>
		<form onsubmit="return szovetseg_kereso();">
			<p><input type="text" id="szovetseg_kereso_nev" class="bolygonev" /></p>
			<p><input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Megnézem'];?>" /></p>
		</form>
		<h1><a href="#" onclick="return toggle('szovetsegek_listaja')"><?=$lang[$lang_lang]['index_belso.php']['Szövetségek listája'];?></a></h1>
		<div id="szovetsegek_listaja" style="padding-right: 10px"></div>
	</div>
</div></div>

<div id="oldal_terkep">
<div style="position: absolute; right: 20px; top: 3px"><a href="" onclick="return jump_to_help(0,25)"><img src="img/ikonok/help_ikon.gif" /></a></div>
<div id="terkep_kulso_keret">
	<div id="terkep_fejlec"></div>
	<div id="terkep_keret">
		<div id="terkep_attekinto" onclick="return attekinto_terkep_klikk(event)"><img id="terkep_attekinto_img" src="minimap.php" /></div>
		<div id="terkep_mozgo_hatter">
			<img src="img/hatter.jpg" /><img src="img/hatter.jpg" /><br />
			<img src="img/hatter.jpg" /><img src="img/hatter.jpg" />
		</div>
		<div id="terkep_klikkento" onclick="return terkep_klikk(event)" oncontextmenu="return clear_celpont_mod()" onmousedown="return terkep_eger(event,0)" onmouseup="return terkep_eger(event,1)" onmouseout="return terkep_eger(event,1)" onmousemove="return terkep_eger(event,2)"></div>
		<div id="terkep_x_koord1"></div><div id="terkep_y_koord1"></div>
		<div id="terkep_x_koord2"></div><div id="terkep_y_koord2"></div>
		<div id="terkep_context"></div>
		<div id="terkep_hover"></div>
	</div>
	<div id="terkep_lablec"></div>
</div>
<div id="terkep_alatt">
	<table align="center" style="margin: 0 auto"><tr>
	<td style="text-align: center">
		<a href="" onclick="return toggle('terkep_attekinto')" title="<?=$lang[$lang_lang]['index_belso.php']['áttekintő térkép'];?>"><img src="img/ikonok/galaxis_32.gif" style="border: solid white 1px" /></a>
<? if (premium_szint()>0) { ?>
		<br />
		<?=$lang[$lang_lang]['index_belso.php']['játékos/szövetség szűrés'];?>:<br />
		<form style="display:inline" onsubmit="document.getElementById('terkep_attekinto_img').src='minimap.php?q='+encodeURIComponent(document.getElementById('attekinto_szures_nev').value)+'&r='+Math.random();document.getElementById('terkep_attekinto').style.display='block';return false"><input type="text" id="attekinto_szures_nev" value="" class="bolygonev" /></form>
<? if (premium_szint()==2) { ?>
		<br /><br /><a href="" onclick="return toggle('fullscreen_terkep')"><?=$lang[$lang_lang]['index_belso.php']['Nagy térkép'];?></a>
<? } ?>
<? } ?>
	</td>
	<td>
		<table><tr>
			<td></td>
			<td><a href="" onclick="return terkep_scroll(0,100)"><img src="img/ikonok/arrow_up.gif" /></a></td>
			<td></td>
		</tr><tr>
			<td><a href="" onclick="return terkep_scroll(100,0)"><img src="img/ikonok/arrow_left.gif" /></a></td>
			<td></td>
			<td><a href="" onclick="return terkep_scroll(-100,0)"><img src="img/ikonok/arrow_right.gif" /></a></td>
		</tr><tr>
			<td></td>
			<td><a href="" onclick="return terkep_scroll(0,-100)"><img src="img/ikonok/arrow_down.gif" /></a></td>
			<td></td>
		</tr></table>
	</td>
	<td>
		<table><tr><td>
			<a href="" onclick="return terkep_zoom_in()"><img src="img/ikonok/add-ff.gif" id="terkep_zoom_in_ikon" /></a>
		</td></tr><tr><td>
			<a href="" onclick="return terkep_zoom_out()"><img src="img/ikonok/delete.gif" id="terkep_zoom_out_ikon" /></a>
		</td></tr></table>
	</td>
	<td>
		<form onsubmit="return gotoxy()">
		<table>
			<tr><td>
				<input type="text" id="terkep_gotoxy" value="" class="bolygonev" onfocus="this.select()" /> <input type="image" src="img/ikonok/bullet_go.gif" title="<?=$lang[$lang_lang]['index_belso.php']['ugorj ide'];?>!" />
			</td></tr>
			<tr><td style="text-align: center">
				(<?=$lang[$lang_lang]['index_belso.php']['bolygó neve vagy koordináta'];?>)
			</td></tr>
		</table>
		</form>
	</td>
	</tr></table>
</div>
</div>

<div id="oldal_felder"><div class="peding">
<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,26)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1><?=$lang[$lang_lang]['index_belso.php']['Áttekintés'];?></h1>
	<div id="felder_attekintes"></div>
	<h1><?=$lang[$lang_lang]['index_belso.php']['Ügynökcsoportok'];?></h1>
	<div id="felder_reszletek"></div>
</div></div>

<div id="oldal_komm"><div class="peding">
<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,27)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1><?=$lang[$lang_lang]['index_belso.php']['Levelek'];?> <span id="olv_lev_1"></span> <a href="" onclick="window.open('level_irasa.php','','width=600,height=350');return false;" style="font-size:8pt"><img src="img/ikonok/mail_edit.gif" /> <?=$lang[$lang_lang]['index_belso.php']['új levél'];?></a> <a href="" onclick="return levelek_elozo_oldal(1)" style="font-size:8pt"><img src="img/ikonok/arrow_left.gif" id="levelek_elozo_oldal_ikon" /> <?=$lang[$lang_lang]['index_belso.php']['előző oldal'];?></a> <span style="font-size:8pt"><span id="level_oldalszam_span"></span>/<span id="level_oldalak_szama_span"></span></span> <a href="" onclick="return levelek_kovetkezo_oldal(1)" style="font-size:8pt"><img src="img/ikonok/arrow_right.gif" id="levelek_kovetkezo_oldal_ikon" /> <?=$lang[$lang_lang]['index_belso.php']['következő oldal'];?></a><?
	if (premium_szint()==2) {
	?><span id="level_kereso_span" style="display:none"> <a href="" onclick="toggle('level_kereso_div');return frissit_aktiv_oldal()"><img src="img/ikonok/keres.gif" /></a></span><?
	}
	?></h1>
	<div id="level_kereso_div" style="display:none">
		<form onsubmit="levelek_oldal=0;return frissit_komm(1);">
		<table>
			<tr>
				<th style="width: 400px"><?=$lang[$lang_lang]['index_belso.php']['Tárgy/Tartalom'];?></th>
				<th style="width: 200px"><?=$lang[$lang_lang]['index_belso.php']['Feladó/Címzett'];?></th>
				<th style="width: 120px"><?=$lang[$lang_lang]['index_belso.php']['Mappa'];?></th>
				<td style="width: 24px">&nbsp;</td>
			</tr>
			<tr>
				<td style="text-align: center"><input type="text" id="level_kereso_input_targy" class="szovegmezo" style="width: 390px" /></th>
				<td style="text-align: center"><input type="text" id="level_kereso_input_felado" class="szovegmezo" style="width: 190px" /></th>
				<td style="text-align: center"><input type="text" id="level_kereso_input_mappa" class="szovegmezo" style="width: 110px" /></th>
				<td style="text-align: center"><input type="image" src="img/ikonok/bullet_go.gif" /></td>
			</tr>
		</table>
		</form>
		<div id="level_kereso_mappak"></div>
	</div>
	<div id="levelek_div"></div>
	<div id="egy_konkret_level"></div>
</div></div>

<div id="oldal_komm2"><div class="peding">
<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,32)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1><?=$lang[$lang_lang]['index_belso.php']['Események'];?> <span id="olv_lev_2"></span> <a href="" onclick="return levelek_elozo_oldal(2)" style="font-size:8pt"><img src="img/ikonok/arrow_left.gif" id="levelek_elozo_oldal_ikon2" /> <?=$lang[$lang_lang]['index_belso.php']['előző oldal'];?></a> <span style="font-size:8pt"><span id="level_oldalszam_span2"></span>/<span id="level_oldalak_szama_span2"></span></span> <a href="" onclick="return levelek_kovetkezo_oldal(2)" style="font-size:8pt"><img src="img/ikonok/arrow_right.gif" id="levelek_kovetkezo_oldal_ikon2" /> <?=$lang[$lang_lang]['index_belso.php']['következő oldal'];?></a><?
	?></h1>
	<div id="levelek_div2"></div>
</div></div>

<div id="oldal_komm3"><div class="peding">
<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,33)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1><?=$lang[$lang_lang]['index_belso.php']['Csatajelentések'];?> <span id="olv_lev_3"></span> <a href="" onclick="return levelek_elozo_oldal(3)" style="font-size:8pt"><img src="img/ikonok/arrow_left.gif" id="levelek_elozo_oldal_ikon3" /> <?=$lang[$lang_lang]['index_belso.php']['előző oldal'];?></a> <span style="font-size:8pt"><span id="level_oldalszam_span3"></span>/<span id="level_oldalak_szama_span3"></span></span> <a href="" onclick="return levelek_kovetkezo_oldal(3)" style="font-size:8pt"><img src="img/ikonok/arrow_right.gif" id="levelek_kovetkezo_oldal_ikon3" /> <?=$lang[$lang_lang]['index_belso.php']['következő oldal'];?></a><?
	?></h1>
	<div id="levelek_div3"></div>
</div></div>


<div id="oldal_cset"><div class="peding">
<div style="position: absolute; right: 20px; top: 10px; z-index: 10"><a href="" onclick="return jump_to_help(0,30)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<div style="position: relative; height: 50px">
		<ul id="cset_tabok">
			<li id="cset_tab_1"<? if ($user_beallitasok['chat_hu']) { ?> class="cset_tab_visible"<? } ?>><a href="#" onclick="return katt_cset_tab(1)"<? if ($user_beallitasok['chat_hu']) { ?> class="aktiv_aloldal"<? } ?>><?=$lang[$lang_lang]['index_belso.php']['Magyar chat'];?></a></li>
			<li id="cset_tab_2"<? if ($user_beallitasok['chat_en']) { ?> class="cset_tab_visible"<? } ?>><a href="#" onclick="return katt_cset_tab(2)"<? if ($user_beallitasok['chat_hu']==0 && $user_beallitasok['chat_en']) { ?> class="aktiv_aloldal"<? } ?>><?=$lang[$lang_lang]['index_belso.php']['Angol chat'];?></a></li>
			<li id="cset_tab_3" class="cset_tab_visible"><a href="#" onclick="return katt_cset_tab(3)"<? if ($user_beallitasok['chat_hu']==0 && $user_beallitasok['chat_en']==0) { ?> class="aktiv_aloldal"<? } ?>><?=$lang[$lang_lang]['index_belso.php']['Szövi chat'];?></a></li>
			<li id="cset_tab_4"><a href="#" onclick="return katt_cset_tab(4)"></a><span id="cset_tab_4_hiv"></span></li>
			<li id="cset_tab_5"><a href="#" onclick="return katt_cset_tab(5)"></a><span id="cset_tab_5_hiv"></span></li>
			<li id="cset_tab_6"><a href="#" onclick="return katt_cset_tab(6)"></a><span id="cset_tab_6_hiv"></span></li>
			<li id="cset_tab_7" class="cset_tab_visible"><a href="#" onclick="return katt_cset_tab(7)">[<?=$lang[$lang_lang]['index_belso.php']['Egyéb'];?>]</a></li>
		</ul>
	</div>
	<div id="cset_aloldal_1">
		<div id="cset_felsoresz_1" class="cset_felsoresz">
<? if (false) { ?>
			<div id="cset_csat_1" class="cset_csat_w cset_szoveg" onscroll="$('cset_uj_hozzaszolas_1').value+='s';return false"></div>
<? } else { ?>
			<div id="cset_csat_1" class="cset_csat_w cset_szoveg"></div>
<? } ?>
		</div>
		<form onsubmit="return csetek_hozzaszol(1)" style="position:relative">
			<p><input type="text" id="cset_uj_hozzaszolas_1" class="cset_szoveg" style="width: 600px" />
			<input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Szólok'];?>" /></p>
			<div style="position: absolute; right: 23px; bottom: 3px"><a href="#" onclick="return torol_csetek_ablak(1)" title="<?=$lang[$lang_lang]['index_belso.php']['Chat ablak ürítése'];?>"><img src="img/ikonok/cross.gif" /></a></div>
		</form>
		<div id="cset_alsoresz_1"></div>
	</div>
	<div id="cset_aloldal_2">
		<div id="cset_felsoresz_2" class="cset_felsoresz">
			<div id="cset_csat_2" class="cset_csat_w cset_szoveg"></div>
		</div>
		<form onsubmit="return csetek_hozzaszol(2)" style="position:relative">
			<p><input type="text" id="cset_uj_hozzaszolas_2" class="cset_szoveg" style="width: 600px" />
			<input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Szólok'];?>" /></p>
			<div style="position: absolute; right: 23px; bottom: 3px"><a href="#" onclick="return torol_csetek_ablak(2)" title="<?=$lang[$lang_lang]['index_belso.php']['Chat ablak ürítése'];?>"><img src="img/ikonok/cross.gif" /></a></div>
		</form>
		<div id="cset_alsoresz_2"></div>
	</div>
	<div id="cset_aloldal_3">
		<div id="cset_vendeglista_div"></div>
		<div id="cset_felsoresz_3" class="cset_felsoresz">
			<div id="cset_csat_3" class="cset_csat cset_szoveg"></div>
			<div id="cset_online_3" class="cset_online cset_szoveg"></div>
		</div>
		<form onsubmit="return csetek_hozzaszol(3)" style="position:relative">
			<p><input type="text" id="cset_uj_hozzaszolas_3" class="cset_szoveg" style="width: 600px" />
			<input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Szólok'];?>" /></p>
			<div style="position: absolute; right: 23px; bottom: 3px"><a href="#" onclick="return torol_csetek_ablak(3)" title="<?=$lang[$lang_lang]['index_belso.php']['Chat ablak ürítése'];?>"><img src="img/ikonok/cross.gif" /></a></div>
		</form>
		<div id="cset_alsoresz_3"></div>
	</div>
<? for($cs=4;$cs<=6;$cs++) { ?>
	<div id="cset_aloldal_<?=$cs?>">
		<div id="cset_felsoresz_<?=$cs?>" class="cset_felsoresz">
			<div id="cset_csat_<?=$cs?>" class="cset_csat_d cset_szoveg"></div>
			<div id="cset_online_<?=$cs?>" class="cset_online_d cset_szoveg"></div>
		</div>
		<form onsubmit="return csetek_hozzaszol(<?=$cs?>)" style="position:relative">
			<p><input type="text" id="cset_uj_hozzaszolas_<?=$cs?>" class="cset_szoveg" style="width: 600px" />
			<input type="submit" value="<?=$lang[$lang_lang]['index_belso.php']['Szólok'];?>" /></p>
			<div style="position: absolute; right: 23px; bottom: 3px"><a href="#" onclick="return torol_csetek_ablak(<?=$cs?>)" title="<?=$lang[$lang_lang]['index_belso.php']['Chat ablak ürítése'];?>"><img src="img/ikonok/cross.gif" /></a></div>
		</form>
		<div id="cset_alsoresz_<?=$cs?>"></div>
	</div>
<? } ?>
	<div id="cset_aloldal_7">
		<div id="cset_szoba_admin">
			<h2><?=$lang[$lang_lang]['index_belso.php']['Új szoba nyitása'];?></h2>
			<p><a href="#" onclick="return cset_szobat_nyit(0)"><?=$lang[$lang_lang]['index_belso.php']['privát'];?></a></p>
			<p><a href="#" onclick="return cset_szobat_nyit(1)"><?=$lang[$lang_lang]['index_belso.php']['hivatalos'];?></a></p>
			<h2><?=$lang[$lang_lang]['index_belso.php']['Saját szobák'];?></h2>
			<div id="cset_szobak_sajatok"></div>
		</div>
		<h2><?=$lang[$lang_lang]['index_belso.php']['Tagságaim'];?></h2>
		<div id="cset_szobak_masoke_tagsagok"></div>
		<h2><?=$lang[$lang_lang]['index_belso.php']['Meghívók'];?></h2>
		<div id="cset_szobak_masoke_meghivok"></div>
		<h2><?=$lang[$lang_lang]['index_belso.php']['Hivatalos szobák'];?></h2>
		<div id="cset_szobak_hivatalos_szobak"></div>
	</div>
</div></div>




<div id="oldal_help"><div class="peding">
<div style="position: absolute; right: 20px; top: 10px"><a href="" onclick="return jump_to_help(0,31)"><img src="img/ikonok/help_ikon.gif" /></a></div>
	<h1>Help</h1>
<div id="help_toc_toc">
	<div id="help_toc_1">
		<h1><?=$lang[$lang_lang]['index_belso.php']['Mindenféle'];?></h1>
		<ul>
			<li><a href="<?=$lang[$lang_lang]['index_belso.php']['EZ-url'];?>" class="kulso_link" target="_blank">Encyclopaedia Zandagortica</a></li>
			<li><a href="<?=$zanda_tutorial_url[$lang_lang]?>" class="kulso_link" target="_blank">Tutorial</a></li>
			<li><a href="<?=$zanda_game_url[$lang_lang]?>map/" class="kulso_link" target="_blank"><?=$lang[$lang_lang]['index_belso.php']['Térkép'];?></a></li>
			<li><a href="<?=$zanda_game_url[$lang_lang]?>top/" class="kulso_link" target="_blank"><?=$lang[$lang_lang]['index_belso.php']['Toplista'];?></a></li>
			<li><a href="<?=$zanda_game_url[$lang_lang]?>top/?menu=xp" class="kulso_link" target="_blank"><?=$lang[$lang_lang]['index_belso.php']['Harci toplista'];?></a></li>
			<li><a href="<?=$zanda_game_url[$lang_lang]?>top/?menu=xp7" class="kulso_link" target="_blank"><?=$lang[$lang_lang]['index_belso.php']['Heti harci toplista'];?></a></li>
			<? if ($lang_lang=='hu') { ?>
			<li><a href="<?=$zanda_homepage_url[$lang_lang]?>csataszim/" class="kulso_link" target="_blank"><?=$lang[$lang_lang]['index_belso.php']['Csataszimulátor'];?></a></li>
			<li><a href="<?=$zanda_homepage_url[$lang_lang]?>okoszim/" class="kulso_link" target="_blank"><?=$lang[$lang_lang]['index_belso.php']['Ökoszimulátor'];?></a></li>
			<li><a href="<?=$zanda_forum_url[$lang_lang]?>" class="kulso_link" target="_blank">Fórum</a></li>
			<? } else { ?>
			<li><a href="<?=$zanda_homepage_url[$lang_lang]?>battlesim/" class="kulso_link" target="_blank"><?=$lang[$lang_lang]['index_belso.php']['Csataszimulátor'];?></a></li>
			<li><a href="<?=$zanda_homepage_url[$lang_lang]?>ecosim/" class="kulso_link" target="_blank"><?=$lang[$lang_lang]['index_belso.php']['Ökoszimulátor'];?></a></li>
			<li><a href="<?=$zanda_forum_url[$lang_lang]?>" class="kulso_link" target="_blank">Forums</a></li>
			<? } ?>
			<li><a href="<?=$facebook_link;?>" class="kulso_link" target="_blank">Facebook</a></li>
		</ul>
		<h1><?=$lang[$lang_lang]['index_belso.php']['Űrhajók'];?></h1>
<?
$tipusok=array(
array($lang[$lang_lang]['index_belso.php']['Cirkálók'],array(201,207,212,213)),
array($lang[$lang_lang]['index_belso.php']['Vadászok'],array(202,206,208,214)),
array($lang[$lang_lang]['index_belso.php']['Rombolók'],array(203,209,215,218)),
array($lang[$lang_lang]['index_belso.php']['Interceptorok'],array(204,210,216)),
array($lang[$lang_lang]['index_belso.php']['Csatahajók'],array(205,211,217)),
array($lang[$lang_lang]['index_belso.php']['Idegen hajók'],array(219,220,221,222,223,224))
);
for($i=0;$i<count($tipusok);$i++) {
	echo '<h2>'.$tipusok[$i][0].'</h2>';
	for($j=0;$j<count($tipusok[$i][1]);$j++) {
		$er=mysql_query('select e.*,l.kep from eroforrasok e, leirasok l where l.domen=2 and l.id='.$tipusok[$i][1][$j].' and e.id='.$tipusok[$i][1][$j]);
		$aux=mysql_fetch_array($er);
		echo '<li>';
		echo '<a href="" onclick="return jump_to_help(2,'.$aux['id'].')">';
		echo '<img src="img/ikonok/'.$aux['kep'].'_index.gif" width="32" height="32" style="vertical-align: top; margin: 2px 0" />';
		echo ''.$aux['nev'.$lang__lang].'</a>';
		echo '</li>';
	}
}
?>
		<h1><?=$lang[$lang_lang]['index_belso.php']['K+F'];?></h1>
		<ul>
<?$er=mysql_query('select id,nev'.$lang__lang.' from kutatasi_temak where id>2 order by id');
while($aux=mysql_fetch_array($er)) {
?>
			<li><a href="" onclick="return jump_to_help(2,<?=(150+$aux[0]);?>)"><?=$aux[1];?></a></li>
<? } ?>
		</ul>
	</div>
	<div id="help_toc_2">
		<h1><?=$lang[$lang_lang]['index_belso.php']['Gyárak'];?></h1>
<?

function print_help_list($nev,$tol,$ig) {
	global $lang__lang;
	echo '<h2>'.$nev.'</h2>';
	$er=mysql_query('select gy.*,l.kep from gyartipusok gy left join leirasok l on l.domen=1 and l.id=gy.id order by id limit '.($tol-1).','.($ig-$tol+1));
	echo '<ul>';
	while($aux=mysql_fetch_array($er)) if ($aux['id']!=3) {//Dyson-gomb skip
		echo '<li>';
		echo '<a href="" onclick="return jump_to_help(1,'.$aux['id'].')">';
		echo '<img src="img/ikonok/'.$aux['kep'].'_index.jpg" width="32" height="32" style="vertical-align: top; margin: 2px 0" />';
		echo $aux['nev'.$lang__lang].'</a>';
		echo '</li>';
	}
	echo '</ul>';
}
print_help_list($lang[$lang_lang]['index_belso.php']['Erőművek'],1,13);
print_help_list($lang[$lang_lang]['index_belso.php']['Élelmiszeripar'],14,17);
print_help_list($lang[$lang_lang]['index_belso.php']['Kitermelés'],18,22);
print_help_list($lang[$lang_lang]['index_belso.php']['Feldolgozó ipar'],23,27);
print_help_list($lang[$lang_lang]['index_belso.php']['Hadiipar'],28,28);
print_help_list($lang[$lang_lang]['index_belso.php']['Települések'],29,30);
print_help_list($lang[$lang_lang]['index_belso.php']['Speciális épületek'],31,34);
?>
		<h1><?=$lang[$lang_lang]['index_belso.php']['Erőforrások'];?></h1>
<?
$er=mysql_query('select e.*,l.kep from eroforrasok e left join leirasok l on l.domen=2 and l.id=e.id where e.tipus='.EROFORRAS_TIPUS_EROFORRAS.' order by e.id');
echo '<ul>';
while($aux=mysql_fetch_array($er)) {
	echo '<li>';
	echo '<a href="" onclick="return jump_to_help(2,'.$aux['id'].')">';
	echo '<img src="img/ikonok/'.$aux['kep'].'_index.jpg" width="32" height="32" style="vertical-align: top; margin: 2px 0" />';
	echo ''.$aux['nev'.$lang__lang].'</a>';
	echo '</li>';
}
echo '</ul>';
?>
	</div>
	<div id="help_toc_3">
		<h1><?=$lang[$lang_lang]['index_belso.php']['Bolygó osztályok'];?></h1>
		<ul>
			<li><a href="" onclick="return jump_to_help(-1,1)"><img src="img/ikonok/bolygo_a32.gif" width="32" height="32" style="vertical-align: top; margin: 2px 0" /><?=$lang[$lang_lang]['index_belso.php']['-osztály prefix'];?>A<?=$lang[$lang_lang]['index_belso.php']['-osztály'];?></a></li>
			<li><a href="" onclick="return jump_to_help(-1,2)"><img src="img/ikonok/bolygo_b32.gif" width="32" height="32" style="vertical-align: top; margin: 2px 0" /><?=$lang[$lang_lang]['index_belso.php']['-osztály prefix'];?>B<?=$lang[$lang_lang]['index_belso.php']['-osztály'];?></a></li>
			<li><a href="" onclick="return jump_to_help(-1,3)"><img src="img/ikonok/bolygo_c32.gif" width="32" height="32" style="vertical-align: top; margin: 2px 0" /><?=$lang[$lang_lang]['index_belso.php']['-osztály prefix'];?>C<?=$lang[$lang_lang]['index_belso.php']['-osztály'];?></a></li>
			<li><a href="" onclick="return jump_to_help(-1,4)"><img src="img/ikonok/bolygo_d32.gif" width="32" height="32" style="vertical-align: top; margin: 2px 0" /><?=$lang[$lang_lang]['index_belso.php']['-osztály prefix'];?>D<?=$lang[$lang_lang]['index_belso.php']['-osztály'];?></a></li>
			<li><a href="" onclick="return jump_to_help(-1,5)"><img src="img/ikonok/bolygo_e32.gif" width="32" height="32" style="vertical-align: top; margin: 2px 0" /><?=$lang[$lang_lang]['index_belso.php']['-osztály prefix'];?>E<?=$lang[$lang_lang]['index_belso.php']['-osztály'];?></a></li>
		</ul>
		<h1><?=$lang[$lang_lang]['index_belso.php']['Élővilág'];?></h1>
<?
$oo=1;
for($osztaly=1;$osztaly<=5;$osztaly++) {
	echo '<h2>'.$lang[$lang_lang]['index_belso.php']['-osztályú bolygó prefix'].chr(64+$osztaly).$lang[$lang_lang]['index_belso.php']['-osztályú bolygó'].'</h2>';
	$er=mysql_query('select e.*,l.kep from eroforrasok e left join leirasok l on l.domen=2 and l.id=e.id where e.tipus='.EROFORRAS_TIPUS_FAJ.' and e.bolygo_osztaly&'.$oo.'>0 order by e.trofikus_szint,e.nev');
	echo '<ul>';
	while($aux=mysql_fetch_array($er)) {
		echo '<li>';
		echo '<a href="" onclick="return jump_to_help(2,'.$aux['id'].')">';
		echo '<img src="img/ikonok/'.$aux['kep'].'_index.jpg" width="32" height="32" style="vertical-align: top; margin: 2px 0" />';
		echo ''.$aux['nev'.$lang__lang].'</a>';
		echo '</li>';
	}
	echo '</ul>';
	$oo*=2;
}
?>
	</div>
</div>
</div></div>

</div>


<br style="clear:both" />


</div><div id="lablec"></div>
</div>
<?
if ($adataim['elso_belepes_betoltes']) {
	mysql_query('update userek set elso_belepes_betoltes=0 where id='.$uid);
}
?>
