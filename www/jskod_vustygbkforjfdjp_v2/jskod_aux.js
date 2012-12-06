function csikiro(db,skala,csikgif,nev,maxw) {
	var darab=db;
	if (db>maxw*skala) darab=maxw*skala;
	var s='<span style="font-weight: bold">';
	s+=nev+'<br /><span class="csik" style="width: '+Math.round(darab/skala)+'px; background: url(img/'+csikgif+'.gif) repeat-x;">'+ezresito(db)+'</span>';
	if (darab!=db) s+='...';
	s+='</span>';
	return s;
};
function fajcsik(db,skala,celszam,nev,maxw,akt_b,faj_id,par_s2) {
	var csikgif=5;
	if (db<0.9*celszam) csikgif=4;
	if (db<0.67*celszam) csikgif=3;
	if (db<0.50*celszam) csikgif=2;
	if (db<0.33*celszam) csikgif=1;
	var darab=db;
	if (db*340>maxw*celszam) darab=Math.round(maxw*celszam/340);
	var s='<span style="font-weight: bold">';
	s+=nev+' <a href="" onclick="return genbanktol_rendel_kerdez('+akt_b+','+faj_id+','+par_s2+')" title="***vásárlás a génbankból***"><img src="img/ikonok/rainbow.gif" /></a><br />';
	s+='<span class="csik" style="width: '+Math.round(darab/celszam*340)+'px; background: url(img/stat_csik_'+csikgif+'.gif) repeat-x;">'+ezresito(db);
	s+='&nbsp;('+Math.round(db/celszam*100)+'%)';
	s+='</span>';
	if (darab!=db) s+='...';
	s+='</span>';
	return s;
};
function statcsik(db,skala) {
	var csikgif=5;
	if (db<80) csikgif=4;
	if (db<60) csikgif=3;
	if (db<40) csikgif=2;
	if (db<20) csikgif=1;
	return '<span class="csik" style="vertical-align: -3px; width: '+Math.round(db/skala)+'px; background: url(img/stat_csik_'+csikgif+'.gif) repeat-x;"></span> '+ezresito(db)+'%';
};
function flottamoralcsik(db) {
	var csikgif=5;
	if (db<800) csikgif=4;
	if (db<600) csikgif=3;
	if (db<400) csikgif=2;
	if (db<200) csikgif=1;
	return '<span class="csik" style="vertical-align: -3px; width: '+Math.round(db/5)+'px; background: url(img/stat_csik_'+csikgif+'.gif) repeat-x;"></span> '+Math.floor(db/10)+'***,***'+(db%10)+'%';
};
function munkaerocsik(igeny,ero,skala) {
	var db=0;
	if (ero) {
		db=Math.round(igeny/ero*100);
		if (db>140) db=140;
	} else db=140;
	var csikgif=1;
	var szoveg='***beavatkozást igényel***';
	if (db==100) if (igeny<=ero) {csikgif=5;szoveg='***optimális***';}
	if (db<100) if (db>=95) {csikgif=5;szoveg='***optimális***';}
	if (db<95) if (db>=90) {csikgif=4;szoveg='***kissé laza***';}
	if (db<90) if (db>=75) {csikgif=3;szoveg='***nagyon laza***';}
	if (db<75) {csikgif=2;szoveg='***nem hatékony***';}
	return '<span class="csik" style="vertical-align: -3px; width: '+Math.round(db/skala)+'px; background: url(img/stat_csik_'+csikgif+'.gif) repeat-x;"></span> '+szoveg;
};
function epuletszuro(tomb) {
	var x=new Array();
	for(var i=0;i<tomb.length;i++) if (tomb[i][3]>0) {
		x.push([0,tomb[i]]);
		x.push([1,tomb[i]]);
	}
	x.push([0,[]]);
	x.push([1,[]]);
	return x;
};
function hajoszuro(tomb) {
	var x=new Array();
	for(var i=0;i<tomb.length;i++) if (tomb[i][2]>0) x.push(tomb[i]);
	return x;
};
function hajoszuro6(tomb) {
	var x=new Array();
	for(var i=0;i<tomb.length;i++) if (tomb[i][6]>0) x.push(tomb[i]);
	return x;
};
function transzefszuro(tomb) {
	var x=new Array();
	for(var i=0;i<tomb.length;i++) if (tomb[i][8]>0) x.push(tomb[i]);
	return x;
};

function json2obj(x) {
	//ha hibas a cucc, pl mysql error van benne vagy ures, akkor nullt kene visszaadni
	return eval('('+x.substring(2,x.length-4)+')');
};

function json2table(x,fejlec,mezo_transz,szelessegek,sor_transz,paritas_transz,mezo_align) {
	var s='<table>';
	if (fejlec.length) {
		s+='<tr>';
		for(var i=0;i<fejlec.length;i++) {
			s+='<th';
			if (szelessegek) s+=' style="width: '+szelessegek[i]+'"';
			s+='>'+fejlec[i]+'</th>';
		}
		s+='</tr>';
	}
	for(var i=0;i<x.length;i++) {
		s+='<tr';
		if (sor_transz) s+=sor_transz(x[i]);
		if (paritas_transz) s+=paritas_transz(i%2);
		s+='>';
		if (mezo_transz) {
			for(var j=0;j<mezo_transz.length;j++) {
				s+='<td style="';
				if (fejlec.length==0) if (i==0) if (szelessegek) s+='width: '+szelessegek[j];
				if (mezo_align) s+=';text-align: '+mezo_align[j];
				s+='"';
				if (typeof mezo_transz[j]=='number') {
					if (!mezo_align) if (typeof x[i][mezo_transz[j]]=='number') s+=' class="szam"';
					s+='>'+x[i][mezo_transz[j]]+'</td>';
				} else {
					var tr=mezo_transz[j](x[i],i);
					if (!mezo_align) if (typeof tr=='number') s+=' class="szam"';
					s+='>'+tr+'</td>';
				}
			}
		} else {
			for(var j=0;j<x[i].length;j++) {
				s+='<td style="';
				if (fejlec.length==0) if (i==0) if (szelessegek) s+='width: '+szelessegek[j];
				if (mezo_align) s+=';text-align: '+mezo_align[j];
				s+='"';
				if (typeof x[i][j]=='number') s+=' class="szam"';
				s+='>'+x[i][j]+'</td>';
			}
		}
		s+='</tr>';
	}
	s+='</table>';
	return s;
};

function transzfer_adat(x) {
	if (x==0) return '';
	if (x>0) {
		if (x>=10000000000) return '<span class="ajanlat_veteli">'+ezresito(Math.floor(x/1000000000))+'G</span>';
		if (x>=10000000) return '<span class="ajanlat_veteli">'+ezresito(Math.floor(x/1000000))+'M</span>';
		if (x>=10000) return '<span class="ajanlat_veteli">'+ezresito(Math.floor(x/1000))+'k</span>';
		return '<span class="ajanlat_veteli">'+ezresito(x)+'</span>';
	}
	if (x<=-10000000000) return '<span class="ajanlat_eladasi">-'+ezresito(Math.floor(-x/1000000000))+'G</span>';
	if (x<=-10000000) return '<span class="ajanlat_eladasi">-'+ezresito(Math.floor(-x/1000000))+'M</span>';
	if (x<=-10000) return '<span class="ajanlat_eladasi">-'+ezresito(Math.floor(-x/1000))+'k</span>';
	return '<span class="ajanlat_eladasi">-'+ezresito(-x)+'</span>';
};
function sec2hms(y) {
	if (y>=0) x=y;else x=-y;
	var m=Math.floor(x/60);
	var s=x-60*m;
	var h=Math.floor(m/60);
	m=m-60*h;
	if (s<10) s='0'+s;
	if (m<10) m='0'+m;
	if (y>=0) return h+':'+m+':'+s;
	else return '-'+h+':'+m+':'+s;
};
function sec2hm(y) {
	var m=Math.ceil(y/60);
	var h=Math.floor(m/60);
	m=m-60*h;
	if (h>0) return h+' ***ó*** '+m+' ***p***';
	else return m+' ***p***';
};
function min2hm(y) {
	var m=y;
	var h=Math.floor(m/60);
	m=m-60*h;
	if (h>0) return h+' ***ó*** '+m+' ***p***';
	else return m+' ***p***';
};
function sec2hm_rip(y) {
	var m=Math.ceil(y/60);
	var h=Math.floor(m/60);
	m=m-60*h;
	if (h>0) {
		if (m>0) return h+' ***óra*** '+m+' ***perce***';
		else return h+' ***órája***';
	} else return m+' ***perce***';
};
function min2hm_mulva(y) {
	var m=y;
	var h=Math.floor(m/60);
	m=m-60*h;
	if (h>0) return '*** múlva prefix***'+h+' ***ó*** '+m+' ***p***'+'*** múlva***';
	else return '*** múlva prefix***'+m+' ***p***'+'*** múlva***';
};

function toggle(x) {
	if ($(x).style.display=='block') $(x).style.display='none';
	else $(x).style.display='block';
	return false;
};
function inline_toggle(x) {
	if ($(x).style.display=='inline' || $(x).style.display=='') $(x).style.display='none';
	else $(x).style.display='inline';
	return false;
};
function tr_toggle(x) {
	if ($(x).style.display=='table-row') $(x).style.display='none';
	else $(x).style.display='table-row';
	return false;
};
function show_help_uzemmod(melyiket,ossz) {
	for(var i=0;i<ossz;i++) $('help_uzemmod_'+i).style.display=(i==melyiket)?'block':'none';
	return false;
};


function hany_hajot_rendez_mindet(honnan) {
	var x='';
	for(var i=0;i<eroforrasok_neve.length;i++) if ($(honnan+'_hany_hajot_rendez_'+i)) {
		$(honnan+'_hany_hajot_rendez_'+i).value=$('max_'+honnan+'_hajoszam_'+i).title;
	}
	return false;
};

function hanyat_lehet_epiteni(tipus) {
	var hanyat=-1;
	for(var i in epuletek_gyartasi_koltsege_tomb[tipus]) {
		var ke=0;
		if ($('keszlet_span_'+i)) ke=parseInt($('keszlet_span_'+i).title);
		if (hanyat==-1) {
			hanyat=Math.floor(ke/epuletek_gyartasi_koltsege_tomb[tipus][i]);
		} else {
			if (hanyat>Math.floor(ke/epuletek_gyartasi_koltsege_tomb[tipus][i])) hanyat=Math.floor(ke/epuletek_gyartasi_koltsege_tomb[tipus][i]);
		}
	}
	if (hanyat==-1) return 0;
	else return hanyat;
};
function mikor_lehet_epiteni(tipus) {
	var mikor=0;
	var hiany='';
	for(var i in epuletek_gyartasi_koltsege_tomb[tipus]) {
		var ke=0;
		if ($('keszlet_span_'+i)) ke=parseInt($('keszlet_span_'+i).title);
		var nt=0;
		if ($('netto_term_span_'+i)) nt=parseInt($('netto_term_span_'+i).title);
		if (mikor==-1) {//vegtelen
		} else {
			if (ke<epuletek_gyartasi_koltsege_tomb[tipus][i]) {
				if (nt>0) {
					if (mikor<Math.ceil((epuletek_gyartasi_koltsege_tomb[tipus][i]-ke)/nt)) mikor=Math.ceil((epuletek_gyartasi_koltsege_tomb[tipus][i]-ke)/nt);
				} else mikor=-1;
			}
		}
		if (epuletek_gyartasi_koltsege_tomb[tipus][i]>ke) hiany=hiany+', '+ezresito(epuletek_gyartasi_koltsege_tomb[tipus][i]-ke)+' '+eroforrasok_neve[i]+' ('+(nt>0?(Math.ceil((epuletek_gyartasi_koltsege_tomb[tipus][i]-ke)/nt)):'***végtelen***')+' ***kör***)';
	}
	return [mikor,hiany.substring(2)];
};

function hanyat_lehet_epiteni_biztonsagosan(tipus) {
	var hanyat=-1;
	for(var i in epuletek_gyartasi_koltsege_tomb[tipus]) {
		var ke=0;
		if ($('keszlet_span_'+i)) ke=parseInt($('keszlet_span_'+i).title);
		if ($('brutto_fogy_span_'+i)) ke-=parseInt($('brutto_fogy_span_'+i).title);
		if (hanyat==-1) {
			hanyat=Math.floor(ke/epuletek_gyartasi_koltsege_tomb[tipus][i]);
		} else {
			if (hanyat>Math.floor(ke/epuletek_gyartasi_koltsege_tomb[tipus][i])) hanyat=Math.floor(ke/epuletek_gyartasi_koltsege_tomb[tipus][i]);
		}
	}
	if (hanyat==-1) return 0;
	else return hanyat;
};
function mikor_lehet_epiteni_biztonsagosan(tipus) {
	var mikor=0;
	var hiany='';
	for(var i in epuletek_gyartasi_koltsege_tomb[tipus]) {
		var ke=0;
		if ($('keszlet_span_'+i)) ke=parseInt($('keszlet_span_'+i).title);
		if ($('brutto_fogy_span_'+i)) ke-=parseInt($('brutto_fogy_span_'+i).title);
		var nt=0;
		if ($('netto_term_span_'+i)) nt=parseInt($('netto_term_span_'+i).title);
		if (mikor==-1) {//vegtelen
		} else {
			if (ke<epuletek_gyartasi_koltsege_tomb[tipus][i]) {
				if (nt>0) {
					if (mikor<Math.ceil((epuletek_gyartasi_koltsege_tomb[tipus][i]-ke)/nt)) mikor=Math.ceil((epuletek_gyartasi_koltsege_tomb[tipus][i]-ke)/nt);
				} else mikor=-1;
			}
		}
		if (epuletek_gyartasi_koltsege_tomb[tipus][i]>ke) hiany=hiany+', '+ezresito(epuletek_gyartasi_koltsege_tomb[tipus][i]-ke)+' '+eroforrasok_neve[i]+' ('+(nt>0?(Math.ceil((epuletek_gyartasi_koltsege_tomb[tipus][i]-ke)/nt)):'***végtelen***')+' ***kör***)';
	}
	return [mikor,hiany.substring(2)];
};

function mikor_lehet_megepiteni_a_teljes_listat_biztonsagosan(darabszamok) {
	var darab_tomb=new Array();
	for(var i=0;i<darabszamok.length;i++) darab_tomb[darabszamok[i][0]]=darabszamok[i][1];
	var keszlet_tomb=new Array();
	var netto_termeles_tomb=new Array();
	var epkoltseg_tomb=new Array();
	for(var tipus in epuletek_gyartasi_koltsege_tomb) {
		for(var i in epuletek_gyartasi_koltsege_tomb[tipus]) {
			if (keszlet_tomb[i]==undefined) {
				keszlet_tomb[i]=0;
				if ($('keszlet_span_'+i)) keszlet_tomb[i]=parseInt($('keszlet_span_'+i).title);
				if ($('brutto_fogy_span_'+i)) keszlet_tomb[i]-=parseInt($('brutto_fogy_span_'+i).title);
			}
			if (netto_termeles_tomb[i]==undefined) {
				netto_termeles_tomb[i]=0;
				if ($('netto_term_span_'+i)) netto_termeles_tomb[i]=parseInt($('netto_term_span_'+i).title);
			}
			if (darab_tomb[tipus]==undefined) {//nem epul ilyen
			} else {
				if (epkoltseg_tomb[i]==undefined) epkoltseg_tomb[i]=darab_tomb[tipus]*epuletek_gyartasi_koltsege_tomb[tipus][i];
				else epkoltseg_tomb[i]+=darab_tomb[tipus]*epuletek_gyartasi_koltsege_tomb[tipus][i];
			}
		}
	}
	var hiany='';
	for(var i in epkoltseg_tomb) {
		if (epkoltseg_tomb[i]>keszlet_tomb[i]) hiany=hiany+', '+ezresito(epkoltseg_tomb[i]-keszlet_tomb[i])+' '+eroforrasok_neve[i]+' ('+(netto_termeles_tomb[i]>0?(Math.ceil((epkoltseg_tomb[i]-keszlet_tomb[i])/netto_termeles_tomb[i])):'***végtelen***')+' ***kör***)';
	}
	return hiany.substring(2);
};




function setSelRange(inputEl, selStart, selEnd) {
	if (inputEl.setSelectionRange) {
		inputEl.focus();
		inputEl.setSelectionRange(selStart, selEnd);
	} else if (inputEl.createTextRange) {
		var range = inputEl.createTextRange();
		range.collapse(true);
		range.moveEnd('character', selEnd);
		range.moveStart('character', selStart);
		range.select();
	}
};

function ezresito(nStr) {
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) x1=x1.replace(rgx,'$1'+'&nbsp;'+'$2');
	return x1 + x2;
};
function ezresito_title(nStr) {
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) x1=x1.replace(rgx,'$1'+' '+'$2');
	return x1 + x2;
};

function xkoordinata(x) {
	if (x<0) return '***NY***&nbsp;'+ezresito(Math.round(-x/2));
	if (x>0) return '***K***&nbsp;'+ezresito(Math.round(x/2));
	return 0;
};
function ykoordinata(y) {
	if (y<0) return '***É***&nbsp;'+ezresito(Math.round(-y/2));
	if (y>0) return '***D***&nbsp;'+ezresito(Math.round(y/2));
	return 0;
};
function szazadresz(a) {
	var b=Math.round(a);
	return ezresito(Math.floor(b/100))+'***tizedes elválasztó***'+((b%100<10)?'0':'')+(b%100);
};
function szazadresz_title(a) {
	var b=Math.round(a);
	return ezresito_title(Math.floor(b/100))+'***tizedes elválasztó***'+((b%100<10)?'0':'')+(b%100);
};
function leftpad(number,length) {
	var s=''+number;
	while(s.length < length) s='0'+s;
	return s;
};
function htmlspecialchars(ch) {
	ch = ch.replace(/&/g,"&amp;");
	ch = ch.replace(/\"/g,"&quot;");
	ch = ch.replace(/\'/g,"&#039;");
	ch = ch.replace(/</g,"&lt;");
	ch = ch.replace(/>/g,"&gt;");
	return ch;
};
function nl2br(ch) {
	ch = ch.replace(/\n/g,"<br />");
	return ch;
};
function in_array(needle,haystack) {
	var key='';
	for (key in haystack) if (haystack[key]==needle) return true;
	return false;
};

function sanitint(x) {
	var y='';
	//for(var i=0;i<x.length;i++) if ('0123456789'.indexOf(x[i])!=-1) y+=x[i];
	for(var i=0;i<x.length;i++) if ('0123456789kmg'.indexOf(x.substr(i,1))!=-1) y+=x.substr(i,1);
	return y;
};
function isTouchDevice() {
	return 'ontouchstart' in window;
};

function $(x) {
	return document.getElementById(x);
};
function sendRequest(url,callback,postData) {
	var req = createXMLHTTPObject();
	if (!req) return;
	var method = postData?'POST':'GET';
	req.open(method,url,true);
	req.setRequestHeader('User-Agent','XMLHTTP/1.0');
	if (postData) {
		req.setRequestHeader('Content-type','application/x-www-form-urlencoded');
		req.setRequestHeader('Content-length',postData.length);//may help
	}
	req.onreadystatechange = function () {
		if (req.readyState != 4) return;
		if (req.status != 200 && req.status != 304) return;
		callback(req);
	};
	if (req.readyState == 4) return;
	req.send(postData);
};
function XMLHttpFactories() {
	return [
		function () {return new XMLHttpRequest()},
		function () {return new ActiveXObject('Msxml2.XMLHTTP')},
		function () {return new ActiveXObject('Msxml3.XMLHTTP')},
		function () {return new ActiveXObject('Microsoft.XMLHTTP')}
	];
};
function createXMLHTTPObject() {
	var xmlhttp = false;
	var factories = XMLHttpFactories();
	for (var i=0;i<factories.length;i++) {
		try {
			xmlhttp = factories[i]();
		} catch (e) {
			continue;
		}
		break;
	}
	return xmlhttp;
};
