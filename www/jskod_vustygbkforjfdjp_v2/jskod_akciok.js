function set_gyar_aktiv_db(bid,gyid,db) {
	sendRequest('set_gyar_aktiv_db.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&gyar_id='+gyid+'&db='+db);
	return false;
};
function set_gyar_uzemmod_tobb(bid,gyid,u,db) {
	sendRequest('set_gyar_uzemmod_tobb.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&gyar_id='+gyid+'&u='+u+'&db='+db);
	return false;
};
function gyar_epit_tobb(bid,gyid,a,db) {
	sendRequest('gyar_epit_tobb.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&gyar_id='+gyid+'&a='+a+'&db='+db);
	return false;
};
function gyar_epit_cancel(cid) {
	sendRequest('gyar_epit_cancel.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'cron_id='+cid);
	return false;
};
function gyar_epit_aktival(cid,a) {
	sendRequest('gyar_epit_aktival.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'cron_id='+cid+'&a='+a);
	return false;
};

function gyar_lerombol_tobb(bid,gyid,aktiv,db) {
	sendRequest('gyar_lerombol_tobb.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&gyar_id='+gyid+'&a='+aktiv+'&db='+db);
	return false;
};
function gyar_lerombol_cancel(cid) {
	sendRequest('gyar_lerombol_cancel.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'cron_id='+cid);
	return false;
};



function epitkezes_queue_aktival(id,a) {
	sendRequest('epitkezes_queue_aktival.php',function(req) {
		if (req.responseText.length==0) frissit_bolygo_epulo();
		else alert(req.responseText);
	},'id='+id+'&a='+a);
	return false;
};
function epitkezes_queue_szerk_kerdez(id,db) {
	var x=prompt('***Hányat akarsz építeni?***',db);
	if (x===null) return false;
	return epitkezes_queue_szerk(id,parseInt(x));
};
function epitkezes_queue_szerk(id,db) {
	sendRequest('epitkezes_queue_szerk.php',function(req) {
		if (req.responseText.length==0) frissit_bolygo_epulo();
		else alert(req.responseText);
	},'id='+id+'&db='+db);
	return false;
};
function epitkezes_queue_torol(id) {
	sendRequest('epitkezes_queue_torol.php',function(req) {
		if (req.responseText.length==0) frissit_bolygo_epulo();
		else alert(req.responseText);
	},'id='+id);
	return false;
};
function epitkezes_queue_atsorol(id,hova) {
	sendRequest('epitkezes_queue_atsorol.php',function(req) {
		if (req.responseText.length==0) frissit_bolygo_epulo();
		else alert(req.responseText);
	},'id='+id+'&hova='+hova);
	return false;
};
function epitkezes_queue_hozzaad(bid,gyid,a,db,hova) {
	sendRequest('epitkezes_queue_hozzaad.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&gyar_id='+gyid+'&a='+a+'&db='+db+'&hova='+hova);
	return false;
};
function epitkezes_queue_deleteall(bid) {
	sendRequest('epitkezes_queue_deleteall.php',function(req) {
		if (req.responseText.length==0) frissit_bolygo_epulo();
		else alert(req.responseText);
	},'bolygo_id='+bid);
	return false;
};
function epitkezes_queue_befagy(a) {
	sendRequest('epitkezes_queue_befagy.php',function(req) {
		if (req.responseText.length==0) frissit_bolygo_epulo();
		else alert(req.responseText);
	},'bolygo_id='+aktiv_bolygo+'&a='+a);
	return false;
};




function levelet_elkuld() {
	sendRequest('levelet_elkuld.php',function(req) {
		if (req.responseText.length==0) {
			$('uj_level_cimzettek').value='';
			$('uj_level_targy').value='';
			$('uj_level_uzenet').value='';
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'cimzettek='+encodeURIComponent($('uj_level_cimzettek').value)+
	'&targy='+encodeURIComponent($('uj_level_targy').value)+
	'&uzenet='+encodeURIComponent($('uj_level_uzenet').value));
	return false;
};
function levelet_torol(id) {
	sendRequest('levelet_torol.php',function(req) {
		if (req.responseText.length==0) {
			kovetkezo_level_megnyitasa(id);
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+id);
	return false;
};
function rendszerlevelet_torol(id) {
	sendRequest('levelet_torol.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+id);
	return false;
};
function levelet_atmappaz(id) {
	sendRequest('levelet_atmappaz.php',function(req) {
		if (req.responseText.length==0) levelet_megnyit(id);
		else alert(req.responseText);
	},'id='+id+'&mappa='+encodeURIComponent($('megnyitott_level_ujmappa').value));
	return false;
};
function kovetkezo_level_megnyitasa(id) {
	sendRequest('kovetkezo_level.php?id='+id,function(req) {
		var valasz=json2obj(req.responseText);
		if (valasz.id) levelet_megnyit(valasz.id);
		else $('egy_konkret_level').innerHTML='';
	});
	return false;
};
function osszes_levelet_torol(id_lista) {
	sendRequest('osszes_levelet_torol.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id_lista='+encodeURIComponent(id_lista));
	return false;
};
function csatajelentest_torol(id) {
	sendRequest('csatajelentest_torol.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+id);
	return false;
};
function osszes_csatajelentest_torol(id_lista) {
	sendRequest('osszes_csatajelentest_torol.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id_lista='+encodeURIComponent(id_lista));
	return false;
};



function szov_cset_hozzaszol() {
	sendRequest('cset_hozzaszol.php',function(req) {
		if (req.responseText.length==0) {
			$('szov_cset_uj_hozzaszolas').value='';
			$('szov_cset_csat').scrollTop=$('szov_cset_csat').scrollHeight;
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'mit='+encodeURIComponent($('szov_cset_uj_hozzaszolas').value)+'&csat=-1');
	return false;
};
function cset_hozzaszol() {
	sendRequest('cset_hozzaszol.php',function(req) {
		if (req.responseText.length==0) {
			$('cset_uj_hozzaszolas').value='';
			$('cset_csat').scrollTop=$('cset_csat').scrollHeight;
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'mit='+encodeURIComponent($('cset_uj_hozzaszolas').value));
	return false;
};
function csetek_hozzaszol(cstab) {
	sendRequest('cset_hozzaszol_v2.php',function(req) {
		if (req.responseText.length==0) {
			$('cset_uj_hozzaszolas_'+cstab).value='';
			$('cset_csat_'+cstab).scrollTop=$('cset_csat_'+cstab).scrollHeight;
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'mit='+encodeURIComponent($('cset_uj_hozzaszolas_'+cstab).value)+'&cstab='+cstab+'&szov='+cset_vendeg_szov);
	return false;
};
function cset_szobat_nyit(hiv) {
	sendRequest('cset_szobat_nyit.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'hiv='+hiv);
	return false;
};
function cset_szobat_zar(szoba) {
	sendRequest('cset_szobat_zar.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+szoba);
	return false;
};
function cset_meghivot_kuld(szoba) {
	sendRequest('cset_meghivot_kuld.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+szoba+'&kit='+encodeURIComponent($('meghiv_nev_'+szoba).value));
	return false;
};
function cset_meghivot_visszavon(szoba,kit) {
	sendRequest('cset_meghivot_visszavon.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+szoba+'&kit='+kit);
	return false;
};
function cset_meghivot_elfogad(szoba) {
	sendRequest('cset_meghivot_elfogad.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+szoba);
	return false;
};
function cset_meghivot_elutasit(szoba) {
	sendRequest('cset_meghivot_elutasit.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+szoba);
	return false;
};
function cset_szobabol_kilep(szoba) {
	sendRequest('cset_szobabol_kilep.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+szoba);
	return false;
};
function cset_szobabol_kirug(szoba,kit) {
	sendRequest('cset_szobabol_kirug.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+szoba+'&kit='+kit);
	return false;
};




function tozsdezik(vetel,e) {
	sendRequest('tozsdezik.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'termek_id='+e+
	'&bolygo_id='+aktiv_bolygo+
	'&mennyiseg='+encodeURIComponent($('uj_ajanlat_mennyiseg_'+e).value)+
	'&vetel='+vetel+
	'&regio='+aktiv_regio);
	return false;
};
function tozsde_kov_regio_mentes() {
	sendRequest('tozsde_kov_regio_mentes.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'regio1='+$('kov_regio_1').options[$('kov_regio_1').selectedIndex].value+
	'&regio2='+$('kov_regio_2').options[$('kov_regio_2').selectedIndex].value);
	return false;
};
function tozsde_regio_valtas_most() {
	sendRequest('tozsde_regio_valtas_most.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'regio1='+$('kov_regio_1').options[$('kov_regio_1').selectedIndex].value+
	'&regio2='+$('kov_regio_2').options[$('kov_regio_2').selectedIndex].value);
	return false;
};



function szabadpiaci_ajanlatot_kuld(vetel,e) {
	sendRequest('szabadpiaci_ajanlatot_kuld.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'termek_id='+e+
	'&bolygo_id='+aktiv_bolygo+
	'&mennyiseg='+encodeURIComponent(document.getElementById('uj_ajanlat_mennyiseg_'+vetel+'_'+e).value)+
	'&arfolyam='+encodeURIComponent(document.getElementById('uj_ajanlat_arfolyam_'+vetel+'_'+e).value)+
	'&vetel='+vetel);
	return false;
};
function szabadpiaci_ajanlatot_visszavon(melyiket) {
	sendRequest('szabadpiaci_ajanlatot_visszavon.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+melyiket);
	return false;
};
function szabadpiaci_ajanlatot_modosit(melyiket,arfolyam) {
	sendRequest('szabadpiaci_ajanlatot_modosit.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+melyiket+'&arfolyam='+arfolyam);
	return false;
};
function szabadpiaci_ajanlatot_modosit_kerdez(melyiket,arfolyam) {
	var x=prompt('***Hány SHY-ra szeretnéd módosítani az árat?***',arfolyam);
	if (x===null) return false;
	return szabadpiaci_ajanlatot_modosit(melyiket,parseInt(x));
};




function flotta_parancs_multi(p) {
	switch(p) {
		case 1:flotta_parancs_megy_bolygo(aktiv_flotta,0,$('parancs_celpont_nev').value);break;
		case 2:flotta_parancs_patrol(aktiv_flotta,0,0,$('parancs_celpont_nev').value);break;
		case 3:flotta_parancs_tamad_bolygo(aktiv_flotta,0,$('parancs_celpont_nev').value);break;
		case 4:flotta_parancs_raid_bolygo(aktiv_flotta,0,$('parancs_celpont_nev').value);break;
		case 5:flotta_parancs_megy_flotta_nev(aktiv_flotta,$('parancs_celflotta_nev').value);break;
		case 6:flotta_parancs_tamad_flotta_nev(aktiv_flotta,$('parancs_celflotta_nev').value);break;
	};
	return false;
};
function flotta_parancs_megy_bolygo(fid,bid,nev) {
	sendRequest('flotta_parancs_megy_bolygo.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&flotta_id='+fid+'&nev='+(nev?encodeURIComponent(nev):''));
	return false;
};
function flotta_parancs_megy_xy(fid,x,y) {
	sendRequest('flotta_parancs_megy_xy.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid+'&x='+x+'&y='+y);
	return false;
};
function flotta_parancs_megy_flotta(fid,fid2) {
	sendRequest('flotta_parancs_megy_flotta.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid+'&flotta2_id='+fid2);
	return false;
};
function flotta_parancs_megy_flotta_nev(fid,nev) {
	sendRequest('flotta_parancs_megy_flotta_nev.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid+'&nev='+(nev?encodeURIComponent(nev):''));
	return false;
};
function flotta_parancs_patrol(fid,x,y,nev) {
	sendRequest('flotta_parancs_patrol.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid+'&x='+x+'&y='+y+'&nev='+(nev?encodeURIComponent(nev):''));
	return false;
};
function flotta_parancs_tamad_bolygo(fid,bid,nev) {
	sendRequest('flotta_parancs_tamad_bolygo.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&flotta_id='+fid+'&nev='+(nev?encodeURIComponent(nev):''));
	return false;
};
function flotta_parancs_tamad_flotta(fid,fid2) {
	sendRequest('flotta_parancs_tamad_flotta.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid+'&flotta2_id='+fid2);
	return false;
};
function flotta_parancs_tamad_flotta_nev(fid,nev) {
	sendRequest('flotta_parancs_tamad_flotta_nev.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid+'&nev='+(nev?encodeURIComponent(nev):''));
	return false;
};
function flotta_parancs_raid_bolygo(fid,bid,nev) {
	sendRequest('flotta_parancs_raid_bolygo.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&flotta_id='+fid+'&nev='+(nev?encodeURIComponent(nev):''));
	return false;
};
function flotta_parancs_vissza(fid) {
	sendRequest('flotta_parancs_vissza.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid);
	return false;
};
function flotta_parancs_allj(fid) {
	sendRequest('flotta_parancs_allj.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid);
	return false;
};

function flotta_atrendezes(honnan,hova) {
	var x='';
	for(var i=0;i<eroforrasok_neve.length;i++) if ($(honnan+'_hany_hajot_rendez_'+i)) {
		x+=i+':'+sanitint($(honnan+'_hany_hajot_rendez_'+i).value)+',';
	}
	var poszt_adatok='';
	if (honnan=='flotta') {
		poszt_adatok='honnan=flotta';
		poszt_adatok+='&forras_id='+aktiv_flotta;
	} else {
		poszt_adatok='honnan=bolygo';
		poszt_adatok+='&forras_id='+aktiv_bolygo;
	}
	poszt_adatok+='&cel_id='+hova;
	poszt_adatok+='&mennyisegek='+encodeURIComponent(x);
	if (hova==0) poszt_adatok+='&uj_flotta_nev='+encodeURIComponent($(honnan+'_uj_flotta_nev').value);
	sendRequest('flotta_atrendezes.php',function(req) {
		if (req.responseText.length==0) frissit_terkep(0,1);
		else {
			if (req.responseText.substring(0,3)=='###') {
				frissit_terkep(0,1);
				flotta_katt(req.responseText.substring(3));
			} else alert(req.responseText);
		}
	},poszt_adatok);
	return false;
};

function eroforras_transzfer(hova) {
	var x='';
	for(var i=0;i<eroforrasok_neve.length;i++) if ($('bolygo_hany_eroforrast_rendez_'+i)) {
		x+=i+':'+sanitint($('bolygo_hany_eroforrast_rendez_'+i).value)+',';
	}
	var poszt_adatok='';
	poszt_adatok='forras_id='+aktiv_bolygo;
	poszt_adatok+='&cel_id='+hova;
	if (hova==0) poszt_adatok+='&cel_nev='+encodeURIComponent($('szallitas_bolygo_nev').value);
	poszt_adatok+='&mennyisegek='+encodeURIComponent(x);
	sendRequest('eroforras_transzfer.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else {
			if (req.responseText.substring(0,3)=='***') {
				frissit_aktiv_oldal();
				alert(req.responseText.substring(3));
			} else alert(req.responseText);
		}
	},poszt_adatok);
	return false;
};

function eroforras_auto_transzfer() {
	var poszt_adatok='';
	poszt_adatok='forras_id='+aktiv_bolygo;
	poszt_adatok+='&cel_nev='+encodeURIComponent($('auto_szallitas_bolygo_nev').value);
	poszt_adatok+='&ef_id='+$('auto_szallitas_ef').options[$('auto_szallitas_ef').selectedIndex].value;
	poszt_adatok+='&darab='+encodeURIComponent($('auto_szallitas_darab').value);
	sendRequest('eroforras_auto_transzfer.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},poszt_adatok);
	return false;
};
function eroforras_auto_transzfer_del(id) {
	sendRequest('eroforras_auto_transzfer_del.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id);
	return false;
};
function eroforras_auto_transzfer_mod(id,darab) {
	sendRequest('eroforras_auto_transzfer_mod.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id+'&darab='+sanitint(darab));
	return false;
};
function eroforras_auto_transzfer_mod_kerdez(id,darab) {
	var x=prompt('***Mennyi menjen át körönként?***',darab);
	if (x===null) return false;
	return eroforras_auto_transzfer_mod(id,x);
};

function eroforras_auto_tozsde() {
	var poszt_adatok='';
	poszt_adatok='forras_id='+aktiv_bolygo;
	poszt_adatok+='&ef_id='+$('auto_tozsde_ef').options[$('auto_tozsde_ef').selectedIndex].value;
	poszt_adatok+='&darab='+encodeURIComponent($('auto_tozsde_darab').value);
	if ($('auto_tozsde_regio_slot')) poszt_adatok+='&regio_slot='+$('auto_tozsde_regio_slot').options[$('auto_tozsde_regio_slot').selectedIndex].value;
	sendRequest('eroforras_auto_tozsde.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},poszt_adatok);
	return false;
};



function penzatutalas() {
	sendRequest('penzatutalas.php',function(req) {
		if (req.responseText.length==0) {
			alert('***Sikerült.***');
			frissit_aktiv_oldal();
		}
		else alert(req.responseText);
	},'kinek='+encodeURIComponent($('penzat_kinek_nev').value)+'&mennyit='+encodeURIComponent(sanitint($('penzat_mennyi').value)));
	return false;
};




function ugynok_atrendezes(bid) {
	var x='';
	var inputlista=$('felder_ucs_form_'+bid).elements;
	for(var i=0;i<inputlista.length-1;i++) if (inputlista[i].id.substr(0,18)=='felder_ucs_atcsop_') x+=inputlista[i].id.substring(18)+':'+sanitint(inputlista[i].value)+',';
	sendRequest('ugynok_atrendezes.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'mennyisegek='+encodeURIComponent(x)+'&hova='+encodeURIComponent(sanitint(inputlista[inputlista.length-1].value)));
	return false;
};
function felder_mod_show(ucs) {
	$('felder_ucs_'+ucs+'_tevekenyseg_span').style.display='none';
	$('felder_ucs_'+ucs+'_tevekenyseg_input').style.display='inline';
	$('felder_ucs_'+ucs+'_idotartam_span').style.display='none';
	$('felder_ucs_'+ucs+'_idotartam_input').style.display='inline';
	$('felder_ucs_'+ucs+'_koltopenz_span').style.display='none';
	$('felder_ucs_'+ucs+'_koltopenz_input').style.display='inline';
	//
	$('felder_mod_link_'+ucs).style.display='none';
	$('felder_save_link_'+ucs).style.display='inline';
	$('felder_cancel_link_'+ucs).style.display='inline';
	$('felder_ucs_'+ucs+'_uj_bolygo_input').style.display='inline';
	felder_uj_bolygo_obj=actb($('felder_ucs_'+ucs+'_uj_bolygo_input'),'ajax_autocomplete_bolygok',0);
	return false;
};
function felder_mod_save(ucs) {
	sendRequest('ugynok_parancs.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+ucs
	+'&tevekenyseg='+encodeURIComponent($('felder_ucs_'+ucs+'_tevekenyseg_input').options[$('felder_ucs_'+ucs+'_tevekenyseg_input').selectedIndex].value)
	+'&idotartam='+encodeURIComponent($('felder_ucs_'+ucs+'_idotartam_input').value)
	+'&koltopenz='+encodeURIComponent($('felder_ucs_'+ucs+'_koltopenz_input').value)
	+'&uj_bolygo='+encodeURIComponent($('felder_ucs_'+ucs+'_uj_bolygo_input').value)
	);
	return false;
};
function felder_mod_hide(ucs) {
	$('felder_ucs_'+ucs+'_tevekenyseg_span').style.display='inline';
	$('felder_ucs_'+ucs+'_tevekenyseg_input').style.display='none';
	$('felder_ucs_'+ucs+'_idotartam_span').style.display='inline';
	$('felder_ucs_'+ucs+'_idotartam_input').style.display='none';
	$('felder_ucs_'+ucs+'_koltopenz_span').style.display='inline';
	$('felder_ucs_'+ucs+'_koltopenz_input').style.display='none';
	//
	$('felder_mod_link_'+ucs).style.display='inline';
	$('felder_save_link_'+ucs).style.display='none';
	$('felder_cancel_link_'+ucs).style.display='none';
	$('felder_ucs_'+ucs+'_uj_bolygo_input').style.display='none';
	$('felder_ucs_'+ucs+'_uj_bolygo_input').blur();
	removeEvent($('felder_ucs_'+ucs+'_uj_bolygo_input'),'focus',felder_uj_bolygo_obj.actb_setup);
	return false;
};


function bolygo_uj_kezelo(bid) {
	sendRequest('bolygo_uj_kezelo.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();}
		else alert(req.responseText);
	},'bolygo_id='+bid+'&kezelo='+encodeURIComponent($('uj_bolygo_kezelo_nev').value));
	return false;
};
function bolygo_tutort_visszadob(bid) {
	sendRequest('bolygo_tutort_visszadob.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();}
		else alert(req.responseText);
	},'bolygo_id='+bid);
	return false;
};
function flotta_kozosbe(fid) {
	sendRequest('flotta_kozosbe.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();}
		else alert(req.responseText);
	},'flotta_id='+fid);
	return false;
};
function flotta_kozosbol_ki(fid) {
	sendRequest('flotta_kozosbol_ki.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();}
		else alert(req.responseText);
	},'flotta_id='+fid);
	return false;
};
function flotta_iranyitas_atadasa(kinek) {
	sendRequest('flotta_iranyitas_atadasa.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();}
		else alert(req.responseText);
	},'flotta_id='+aktiv_flotta+'&kinek='+parseInt(kinek));
	return false;
};
function flotta_kivonasa() {
	sendRequest('flotta_kivonasa.php',function(req) {
		if (req.responseText.length==0) {frissit_terkep(0,1);frissit_aktiv_oldal();frissit_menu();}
		else {
			if (req.responseText.substring(0,3)=='###') {
				frissit_terkep(0,1);frissit_aktiv_oldal();frissit_menu();
				flotta_katt(req.responseText.substring(3));
			} else alert(req.responseText);
		}
		//
	},'flotta_id='+aktiv_flotta);
	return false;
};


function bolygo_move_fantom() {
	sendRequest('bolygo_move.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();frissit_terkep(0,1);}
		else alert(req.responseText);
	},'forras_bolygo_id='+$('bolygo_move_fantom_id').options[$('bolygo_move_fantom_id').selectedIndex].value+'&bolygo_id='+aktiv_bolygo);
	return false;
};
function bolygo_move() {
	sendRequest('bolygo_move.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();frissit_terkep(0,1);}
		else alert(req.responseText);
	},'bolygo_id='+aktiv_bolygo);
	return false;
};
function bolygo_reset(bid) {
	sendRequest('bolygo_reset.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();frissit_terkep(0,1);}
		else alert(req.responseText);
	},'bolygo_id='+bid);
	return false;
};
function bolygo_rename(bid,nev) {
	sendRequest('bolygo_rename.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();frissit_terkep(0,1);}
		else alert(req.responseText);
	},'bolygo_id='+bid+'&nev='+encodeURIComponent(nev));
	return false;
};
function bolygo_rename_kerdez(bid,nev) {
	var x=prompt('***Mi legyen a bolygó neve?***',nev);
	if (x===null) return false;
	return bolygo_rename(bid,x);
};
function flotta_rename(fid,nev) {
	sendRequest('flotta_rename.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();frissit_terkep(0,1);}
		else alert(req.responseText);
	},'flotta_id='+fid+'&nev='+encodeURIComponent(nev));
	return false;
};
function flotta_rename_kerdez(fid,nev) {
	var x=prompt('***Mi legyen a flotta neve?***',nev);
	if (x===null) return false;
	return flotta_rename(fid,x);
};
function flotta_rebazis(fid) {
	sendRequest('flotta_rebazis.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'flotta_id='+fid+'&nev='+encodeURIComponent($('uj_bazis_nev').value));
	return false;
};


function genbanktol_rendel(bid,fid,db) {
	sendRequest('genbanktol_rendel.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&faj_id='+fid+'&db='+db);
	return false;
};
function genbanktol_rendel_kerdez(bid,fid,tsz) {
	var egysegar=100;
	if (tsz==2) egysegar=1000;
	if (tsz==3) egysegar=10000;
	var x=prompt('***Mennyit szeretnél rendelni ***'+egysegar+'*** SHY-os egységáron?***');
	if (x===null) return false;
	return genbanktol_rendel(bid,fid,sanitint(x));
};

function kornyezetet_fejleszt(bid,kp) {
	sendRequest('kornyezetet_fejleszt.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'bolygo_id='+bid+'&kp='+kp);
	return false;
};
function kornyezetet_fejleszt_kerdez(bid) {
	var x=prompt('***Hány KP-t szeretnél erre a bolygóra fordítani?***');
	if (x===null) return false;
	return kornyezetet_fejleszt(bid,sanitint(x));
};

function fejleszt(tema,kp) {
	sendRequest('fejleszt.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'tema='+tema+'&kp='+kp);
	return false;
};
function fejleszt_kerdez(tema) {
	var x=prompt('***Hány KP-t szeretnél erre a témára fordítani?***');
	if (x===null) return false;
	return fejleszt(tema,sanitint(x));
};

function szovetseg_alapitasa() {
	sendRequest('szovetseg_alapitasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'nev='+encodeURIComponent($('uj_szovetseg_nev').value)+
	'&rovid_nev='+encodeURIComponent($('uj_szovetseg_rovid_nev').value)+
	'&alapnev='+encodeURIComponent($('uj_szovetseg_alapnev').value)+
	'&motto='+encodeURIComponent($('uj_szovetseg_motto').value)+
	'&zart='+($('uj_szovetseg_zart').checked?1:0)+
	'&udvozlet='+encodeURIComponent($('uj_szovetseg_udvozlet').value)+
	'&szabalyzat='+encodeURIComponent($('uj_szovetseg_szabalyzat').value));
	return false;
};
function szovetseg_szerkesztese() {
	sendRequest('szovetseg_szerkesztese.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'nev='+encodeURIComponent($('szerk_szovetseg_nev').value)+
	'&rovid_nev='+encodeURIComponent($('szerk_szovetseg_rovid_nev').value)+
	'&alapnev='+encodeURIComponent($('szerk_szovetseg_alapnev').value)+
	'&motto='+encodeURIComponent($('szerk_szovetseg_motto').value)+
	'&zart='+($('szerk_szovetseg_zart').checked?1:0)+
	'&udvozlet='+encodeURIComponent($('szerk_szovetseg_udvozlet').value)+
	'&szabalyzat='+encodeURIComponent($('szerk_szovetseg_szabalyzat').value));
	return false;
};
function uj_tisztseg_felvetele() {
	var x='';
	for(var i=1;i<=12;i++) if ($('uj_tisztseg_jog_'+i)) x+='&jog'+i+'='+($('uj_tisztseg_jog_'+i).checked?1:0);
	sendRequest('uj_tisztseg_felvetele.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'nev='+encodeURIComponent($('uj_tisztseg_nev').value)+x);
	return false;
};
function tisztsegek_szerkesztese() {
	var x='';
	var inputlista=$('tiszt_jog_szerk_form').elements;
	for(var i=0;i<inputlista.length;i++) if (inputlista[i].id.length>0) {
		if (inputlista[i].id.substring(10,11)=='n') x+='&nev_'+inputlista[i].id.substring(14)+'='+encodeURIComponent(inputlista[i].value);
		else x+='&jog_'+inputlista[i].id.substring(10)+'='+(inputlista[i].checked?1:0);
	}
	sendRequest('tisztsegek_szerkesztese.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},x.substring(1));
	return false;
};
function tisztseg_torlese(id) {
	sendRequest('tisztseg_torlese.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+id);
	return false;
};
function tag_kinevezese() {
	sendRequest('tag_kinevezese.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'kit='+encodeURIComponent($('kinevez_kit').value)+
	'&hova='+$('kinevez_hova').options[$('kinevez_hova').selectedIndex].value);
	return false;
};
function tag_kirugasa() {
	sendRequest('tag_kirugasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();frissit_menu();frissit_terkep(0,1);
		} else alert(req.responseText);
	},'kit='+encodeURIComponent($('kirug_kit').value));
	return false;
};
function tag_meghivasa() {
	sendRequest('tag_meghivasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'kit='+encodeURIComponent($('meghiv_kit').value)+
	'&megjegyzes='+encodeURIComponent($('meghiv_megjegyzes').value)+
	'&csatol='+($('meghiv_csatol').checked?1:0));
	return false;
};
function meghivo_visszavonasa(kit) {
	sendRequest('meghivo_visszavonasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'kit='+kit);
	return false;
};
function meghivo_elfogadasa(id) {
	sendRequest('meghivo_elfogadasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();frissit_terkep(0,1);
		} else alert(req.responseText);
	},'id='+id);
	return false;
};
function meghivo_elutasitasa(id) {
	sendRequest('meghivo_elutasitasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'id='+id);
	return false;
};
function kilepes_szovetsegbol() {
	var x='';
	if ($('uj_alapito_neve')) x='helyettes='+encodeURIComponent($('uj_alapito_neve').value);
	sendRequest('kilepes_szovetsegbol.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();frissit_menu();frissit_terkep(0,1);
		} else alert(req.responseText);
	},x);
	return false;
};
function meghivo_kerelem(hova) {
	sendRequest('meghivo_kerelem.php',function(req) {
		if (req.responseText.length==0) {
			alert('***Sikerült.***');
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'hova='+hova);
	return false;
};
function meghivo_kerelem_visszavonasa(hova) {
	sendRequest('meghivo_kerelem_visszavonasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'hova='+hova);
	return false;
};
function meghivo_kerelem_elfogadasa(kit) {
	sendRequest('meghivo_kerelem_elfogadasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();frissit_terkep(0,1);
		} else alert(req.responseText);
	},'kit='+kit);
	return false;
};
function meghivo_kerelem_elutasitasa(kit) {
	sendRequest('meghivo_kerelem_elutasitasa.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		} else alert(req.responseText);
	},'kit='+kit);
	return false;
};

function bolygo_feladasa() {
	sendRequest('bolygo_feladasa.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();}
		else alert(req.responseText);
	},'bolygo_id='+aktiv_bolygo);
	return false;
};
function flotta_torlese() {
	sendRequest('flotta_torlese.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_menu();}
		else alert(req.responseText);
	},'flotta_id='+aktiv_flotta);
	return false;
};
function flotta_visszavonasa() {
	sendRequest('flotta_visszavonasa.php',function(req) {
		if (req.responseText.substring(0,3)=='***') {
			frissit_menu();
			bolygo_katt(parseInt(req.responseText.substring(3)),1);//haborus aloldal
		}
		else alert(req.responseText);
	},'flotta_id='+aktiv_flotta);
	return false;
};

function diplo_uj_statusz() {
	sendRequest('diplo_uj_statusz.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'kinek='+encodeURIComponent($('statusz_kinek').value)+
	'&mirol='+$('statusz_mirol').options[$('statusz_mirol').selectedIndex].value+
	'&szoveg='+encodeURIComponent($('statusz_szoveg').value));
	return false;
};
function diplo_uj_ajanlat() {
	sendRequest('diplo_uj_ajanlat.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'kinek='+encodeURIComponent($('ajanlat_kinek').value)+
	'&mirol='+$('ajanlat_mirol').options[$('ajanlat_mirol').selectedIndex].value+
	'&lejarat='+parseInt($('ajanlat_lejarat').value)+
	'&szoveg='+encodeURIComponent($('ajanlat_szoveg').value));
	return false;
};
function diplo_ajanlat_visszavon(id) {
	sendRequest('diplo_ajanlat_visszavon.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_terkep(0,1);}
		else alert(req.responseText);
	},'id='+id);
	return false;
};
function diplo_ajanlat_elutasit(id) {
	sendRequest('diplo_ajanlat_elutasit.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_terkep(0,1);}
		else alert(req.responseText);
	},'id='+id);
	return false;
};
function diplo_ajanlat_elfogad(id) {
	sendRequest('diplo_ajanlat_elfogad.php',function(req) {
		if (req.responseText.length==0) {frissit_aktiv_oldal();frissit_terkep(0,1);}
		else alert(req.responseText);
	},'id='+id);
	return false;
};
function diplo_uj_vendeg() {
	sendRequest('diplo_uj_vendeg.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'kit='+encodeURIComponent($('vendeg_kit').value));
	return false;
};
function diplo_vendeg_kirug(kit) {
	sendRequest('diplo_vendeg_kirug.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'kit='+kit);
	return false;
};


function jelszo_modositasa() {
	sendRequest('jelszo_modositasa.php',function(req) {
		if (req.responseText.length==0) {
			$('jelszo_mod_regi').value='';
			$('jelszo_mod_uj1').value='';
			$('jelszo_mod_uj2').value='';
			alert('***Sikeres jelszómódosítás.***');
		}
		else alert(req.responseText);
	},'regi='+encodeURIComponent($('jelszo_mod_regi').value)+
	'&uj1='+encodeURIComponent($('jelszo_mod_uj1').value)+
	'&uj2='+encodeURIComponent($('jelszo_mod_uj2').value));
	return false;
};

function jatekos_kereso() {
	sendRequest('jatekos_kereso.php',function(req) {
		if (req.responseText.length>0) {
			var valasz=json2obj(req.responseText);
			user_katt(valasz.id);
		}
	},'nev='+encodeURIComponent($('jatekos_kereso_nev').value));
	return false;
};
function szovetseg_kereso() {
	sendRequest('szovetseg_kereso.php',function(req) {
		if (req.responseText.length>0) {
			var valasz=json2obj(req.responseText);
			szovetseg_katt(valasz.id);
		}
	},'nev='+encodeURIComponent($('szovetseg_kereso_nev').value));
	return false;
};

function szov_forum_uj_komment_kuldese() {
	sendRequest('szov_forum_uj_komment_kuldese.php',function(req) {
		if (req.responseText.substring(0,3)=='***') {
			$('szov_forum_uj_tema').value='';
			$('szov_forum_uj_tema_belso').checked=false;
			$('szov_forum_uj_tema_vendeg').checked=false;
			$('szov_forum_uj_komment').value='';
			szov_forum_aktiv_tema_id=parseInt(req.responseText.substring(3));
			frissit_szov_forum_tema();
		}
		else alert(req.responseText);
	},'regi_tema_id='+parseInt($('szov_forum_regi_tema_id').value)+
	'&uj_tema='+encodeURIComponent($('szov_forum_uj_tema').value)+
	'&uj_tema_belso='+($('szov_forum_uj_tema_belso').checked?1:0)+
	'&uj_tema_vendeg='+($('szov_forum_uj_tema_vendeg').checked?1:0)+
	'&uj_komment='+encodeURIComponent($('szov_forum_uj_komment').value));
	return false;
};
function szov_forum_tema_torlese(tid) {
	sendRequest('szov_forum_tema_torlese.php',function(req) {
		if (req.responseText.length==0) frissit_szov_forum();
		else alert(req.responseText);
	},'tema_id='+tid);
	return false;
};
function szov_forum_tema_szerkesztese() {
	sendRequest('szov_forum_tema_szerkesztese.php',function(req) {
		if (req.responseText.substring(0,3)=='***') {
			szov_forum_aktiv_tema_id=parseInt(req.responseText.substring(3));
			frissit_szov_forum_tema();
		}
		else alert(req.responseText);
	},'tema_id='+parseInt($('szov_forum_regi_tema_id').value)+
	'&tema='+encodeURIComponent($('szov_forum_szerk_tema').value)+
	'&tema_belso='+($('szov_forum_szerk_tema_belso').checked?1:0)+
	'&tema_vendeg='+($('szov_forum_szerk_tema_vendeg').checked?1:0));
	return false;
};
function szov_forum_komment_torlese(kid) {
	sendRequest('szov_forum_komment_torlese.php',function(req) {
		if (req.responseText.length==0) frissit_szov_forum_tema();
		else if (req.responseText=='***') frissit_szov_forum();
		else alert(req.responseText);
	},'komment_id='+kid);
	return false;
};


function set_user_email() {
	sendRequest('set_user_email.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+aktiv_user+'&email='+encodeURIComponent($('user_uj_email_cime').value));
	return false;
};
function set_user_meghivo() {
	sendRequest('set_user_meghivo.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+aktiv_user+'&nev='+encodeURIComponent($('meghivo_neve').value));
	return false;
};
function set_user_premium(mod) {
	sendRequest('set_user_premium.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+aktiv_user+'&nap='+encodeURIComponent($('premium_plusz_hany_nap').value)+'&mod='+parseInt(mod)+'&upgrade='+encodeURIComponent($('premium_upgrade_is').value));
	return false;
};
function set_user_premium_ft(mod,ft) {
	sendRequest('set_user_premium.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+aktiv_user+'&ft='+ft+'&mod='+parseInt(mod));
	return false;
};
function upgrade_premium() {
	sendRequest('upgrade_premium.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	});
	return false;
};
function select_karrier(k,i) {
	sendRequest('select_karrier.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'k='+k+'&i='+i);
	return false;
};
function select_speci(k) {
	sendRequest('select_speci.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'k='+k);
	return false;
};



function jegyzet_atsorol(id,hova) {
	sendRequest('jegyzet_atsorol.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id+'&hova='+hova);
	return false;
};
function jegyzet_szerk_toggle(id) {
	toggle('jegyzet_p_'+id);
	toggle('jegyzet_div_'+id);
	return false;
};
function jegyzet_szerk_save(id) {
	sendRequest('jegyzet_szerk_save.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id+'&szoveg='+encodeURIComponent($('jegyzet_ta_'+id).value));
	return false;
};
function jegyzet_torol(id) {
	sendRequest('jegyzet_torol.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id);
	return false;
};


function user_avatar_torlese() {
	sendRequest('user_avatar_torlese.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	});
	return false;
};
function user_nagy_avatar_torlese() {
	sendRequest('user_nagy_avatar_torlese.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	});
	return false;
};
function szov_minicimer_torlese() {
	sendRequest('szov_minicimer_torlese.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	});
	return false;
};
function szov_cimer_torlese() {
	sendRequest('szov_cimer_torlese.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	});
	return false;
};


function usertag_atsorol(id,hova) {
	sendRequest('usertag_atsorol.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id+'&hova='+hova);
	return false;
};
function usertag_szerk(id,cim,szoveg) {
	var x=prompt('***Mi legyen a tulajdonság?***',cim);
	if (x===null) return false;
	var y=prompt('***Mi legyen a tartalom?***',szoveg);
	if (y===null) return false;
	return usertag_szerk_save(id,x,y);
};
function usertag_szerk_save(id,cim,szoveg) {
	sendRequest('usertag_szerk_save.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id+'&cim='+encodeURIComponent(cim)+'&szoveg='+encodeURIComponent(szoveg));
	return false;
};
function usertag_torol(id) {
	sendRequest('usertag_torol.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id);
	return false;
};


function szamla_szerk_toggle(x) {
	toggle('szamla_'+x+'_sima_div');
	toggle('szamla_'+x+'_szerk_div');
	return false;
};
function szamla_szerk_save(x) {
	sendRequest('szamla_szerk_save.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'mit='+x+'&szoveg='+encodeURIComponent($('szamla_'+x+'_szerk_input').value));
	return false;
};
function szamla_szerk_save_masnak(id,x) {
	sendRequest('szamla_szerk_save_masnak.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id+'&mit='+x+'&szoveg='+encodeURIComponent($('szamla_'+x+'_szerk_input').value));
	return false;
};



function aktivitas_megosztasa() {
	sendRequest('aktivitas_megosztasa.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'nev='+encodeURIComponent($('aktivitas_megosztas_ujnev').value));
	return false;
};
function aktivitas_megosztas_torlese(id) {
	sendRequest('aktivitas_megosztas_torlese.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'id='+id);
	return false;
};

function set_new_proxy() {
	sendRequest('set_new_proxy.php',function(req) {
		if (req.responseText.length==0) frissit_aktiv_oldal();
		else alert(req.responseText);
	},'nev='+encodeURIComponent($('uj_helyettes_nev').value));
	return false;
};

function user_beallitasok(mit,mire) {
	sendRequest('user_beallitasok.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
			if (mit=='kozos_flottak_listaban') frissit_menu();
		}
		else alert(req.responseText);
	},'mit='+mit+'&mire='+mire);
	return false;
};

function set_badge_pub(mit,mire) {
	sendRequest('set_badge_pub.php',function(req) {
		if (req.responseText.length==0) {
			frissit_aktiv_oldal();
		}
		else alert(req.responseText);
	},'mit='+mit+'&mire='+mire);
	return false;
};
