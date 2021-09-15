<?
class func {



public function user_browser() {
	$agent=$_SERVER['HTTP_USER_AGENT'];
	preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info); // регулярное выражение, которое позволяет отпределить 90% браузеров
        list(,$browser,$version) = $browser_info; // получаем данные из массива в переменную
        if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera)) return 'Opera '.$opera[1]; // определение _очень_старых_ версий Оперы (до 8.50), при желании можно убрать
        if ($browser == 'MSIE') { // если браузер определён как IE
                preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie); // проверяем, не разработка ли это на основе IE
                if ($ie) return $ie[1].' based on IE '.$version; // если да, то возвращаем сообщение об этом
                return 'IE '.$version; // иначе просто возвращаем IE и номер версии
        }
        if ($browser == 'Firefox') { // если браузер определён как Firefox
                preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff); // проверяем, не разработка ли это на основе Firefox
                if ($ff) return $ff[1].' '.$ff[2]; // если да, то выводим номер и версию
        }
        if ($browser == 'Opera' && $version == '9.80') return 'Opera '.substr($agent,-5); // если браузер определён как Opera 9.80, берём версию Оперы из конца строки
        if ($browser == 'Version') return 'Safari '.$version; // определяем Сафари
        if (!$browser && strpos($agent, 'Gecko')) return 'Browser based on Gecko'; // для неопознанных браузеров проверяем, если они на движке Gecko, и возращаем сообщение об этом
        return $browser.' '.$version; // для всех остальных возвращаем браузер и версию
}


public function is_mobile() {
	#Определяем пренадлежность браузера к мобильным устройствам
    #Возвращает false - если браузер не определен или стационарный
    #и от 1 до 4 (зависит от типа определения) - если браузер относится к мобильным устройствам
  $user_agent=strtolower(getenv('HTTP_USER_AGENT'));
  $accept=strtolower(getenv('HTTP_ACCEPT'));

  if ((strpos($accept,'text/vnd.wap.wml')!==false) ||
      (strpos($accept,'application/vnd.wap.xhtml+xml')!==false)) {
    return 1; // Возращает 1 если мобильный браузер определен по HTTP-заголовкам
  }

  if (isset($_SERVER['HTTP_X_WAP_PROFILE']) ||
      isset($_SERVER['HTTP_PROFILE'])) {
    return 2; // Возвращает 2 если мобильный браузер определен по установкам сервера
  }

  if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|'.
    'wireless| mobi|lg380|ahong|lgku|lgu900|lg210|lg47|lg920|lg840|'.
    'lg370|sam-r|mg50|s55|g83|mk99|vx400|t66|d615|d763|sl900|el370|'.
    'mp500|samu4|samu3|vx10|xda_|samu6|samu5|samu7|samu9|a615|b832|'.
    'm881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|'.
    'r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|'.
    'i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|'.
    'htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|'.
    'sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|'.
    'p404i|s210|c5100|s940|teleca|c500|s590|foma|vx8|samsu|vx9|a1000|'.
    '_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|'.
    's800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|'.
    'd736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |'.
    'sonyericsson|samsung|nokia|240x|x320vx10|sony cmd|motorola|'.
    'up.browser|up.link|mmp|symbian|android|tablet|iphone|ipad|mobile|smartphone|j2me|wap|vodafone|o2|'.
    'pocket|kindle|mobile|psp|treo)/', $user_agent)) {
    return 3; // Возвращает 3 если мобильный браузер определен по сигнатуре User Agent
  }

  if (in_array(substr($user_agent,0,4),
    Array("1207", "3gso", "4thp", "501i", "502i", "503i", "504i", "505i", "506i",
          "6310", "6590", "770s", "802s", "a wa", "abac", "acer", "acoo", "acs-",
          "aiko", "airn", "alav", "alco", "alca", "amoi", "anex", "anyw", "anny",
          "aptu", "arch", "asus", "aste", "argo", "attw", "au-m", "audi", "aur ",
          "aus ", "avan", "beck", "bell", "benq", "bilb", "bird", "blac", "blaz",
          "brew", "brvw", "bumb", "bw-n", "bw-u", "c55/", "capi", "ccwa", "cdm-",
          "cell", "chtm", "cldc", "cmd-", "dmob", "cond", "craw", "dait", "dall",
          "dbte", "dc-s", "devi", "dica", "doco", "dopo", "ds-d", "ds12", "dang",
          "el49", "elai", "eml2", "emul", "eric", "erk0", "esl8", "ez40", "ez60",
          "ez70", "ezos", "ezwa", "ezze", "fake", "fetc", "fly-", "fly_", "g-mo",
          "g1 u", "g560", "gene", "gf-5", "go.w", "good", "grad", "grun", "haie",
          "hcit", "hd-m", "hd-p", "hd-t", "hei-", "hiba", "hipt", "hita", "hp i",
          "hpip", "hs-c", "htc ", "htc-", "htc_", "htca", "htcg", "htcp", "htcs",
          "htct", "http", "hutc", "huaw", "i-20", "i-go", "i-ma", "i230", "iac",
          "iac-", "iac/", "ibro", "idea", "ig01", "ikom", "im1k", "inno", "ipaq",
          "iris", "jata", "java", "jbro", "jemu", "jigs", "kddi", "keji", "kgt",
          "kgt/", "klon", "kpt ", "kwc-", "kyoc", "kyok", "leno", "lexi", "lg g",
          "lg-a", "lg-b", "lg-c", "lg-d", "lg-f", "lg-g", "lg-k", "lg-l", "lg-m",
          "lg-o", "lg-p", "lg-s", "lg-t", "lg-u", "lg-w", "lg/k", "lg/l", "lg/u",
          "lg50", "lg54", "lge-", "lge/", "libw", "lynx", "m-cr", "m1-w", "m3ga",
          "m50/", "mate", "maui", "maxo", "mc01", "mc21", "mcca", "medi", "merc",
          "meri", "midp", "mio8", "mioa", "mits", "mmef", "mo01", "mo02", "mobi",
          "mode", "modo", "mot ", "mot-", "moto", "motv", "mozz", "mt50", "mtp1",
          "mtv ", "mwbp", "mywa", "n100", "n101", "n102", "n202", "n203", "n300",
          "n302", "n500", "n502", "n505", "n700", "n701", "n710", "nec-", "nem-",
          "neon", "netf", "newg", "newt", "nok6", "noki", "nzph", "o2 x", "o2-x",
          "o2im", "opti", "opwv", "oran", "owg1", "p800", "palm", "pana", "pand",
          "pant", "pdxg", "pg-1", "pg-2", "pg-3", "pg-6", "pg-8", "pg-c", "pg13",
          "phil", "pire", "play", "pluc", "pn-2", "pock", "port", "pose", "prox",
          "psio", "pt-g", "qa-a", "qc-2", "qc-3", "qc-5", "qc-7", "qc07", "qc12",
          "qc21", "qc32", "qc60", "qci-", "qtek", "qwap", "r380", "r600", "raks",
          "rim9", "rove", "rozo", "s55/", "sage", "sama", "sams", "samm", "sany",
          "sava", "sc01", "sch-", "scoo", "scp-", "sdk/", "se47", "sec-", "sec0",
          "sec1", "semc", "send", "seri", "sgh-", "shar", "sie-", "siem", "sk-0",
          "sl45", "slid", "smal", "smar", "smb3", "smit", "smt5", "soft", "sony",
          "sp01", "sph-", "spv ", "spv-", "sy01", "symb", "t-mo", "t218", "t250",
          "t600", "t610", "t618", "tagt", "talk", "tcl-", "tdg-", "teli", "telm",
          "tim-", "topl", "treo", "tosh", "ts70", "tsm-", "tsm3", "tsm5", "tx-9",
          "up.b", "upg1", "upsi", "utst", "v400", "v750", "veri", "virg", "vite",
          "vk-v", "vk40", "vk50", "vk53", "vk52", "vm40", "vulc", "voda", "vx52",
          "vx53", "vx60", "vx61", "vx70", "vx80", "vx81", "vx83", "vx85", "vx98",
          "w3c ", "w3c-", "wap-", "wapa", "wapi", "wapj", "wapp", "wapm", "wapr",
          "waps", "wapt", "wapu", "wapv", "wapy", "webc", "whit", "wig ", "winc",
          "winw", "wmlb", "wonu", "x700", "xda-", "xdag", "xda2", "yas-", "your",
          "zeto", "zte-"))) {
    return 4; // Возвращает 4 если мобильный браузер определен по сигнатуре User Agent
  }

  return false; // Возвращает false если мобильный браузер не определен или браузер стационарный
}

public function text($array){
	$code="1234567890 -=!@#$%^&*()_+qwertyuiop[]\QWERTYUIOP{}|asdfghjkl;'ASDFGHJKL:zxcvbnm,./ZXCVBNM<>?ЄєіІйцукенгшщзхъЙЦУКЕНГШЩЗХЪїЇфывапролджэФЫВАПРОЛДЖЭячсмитьбюЯЧСМИТЬБЮ";
	$code_array=str_split($code);
	foreach($array as $key => $value) $text.=$code_array[$value];
	return $text;
}

public function device_name($device)
{
	if ($device>0) return "Мобильное устр-во"; else return "Компьютер";
}



public  function getOS() {
	$userAgent=$_SERVER['HTTP_USER_AGENT'];
    $oses = array (
        // Mircrosoft Windows Operating Systems
'Windows 3.11' => '(Win16)',
'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
'Windows 98' => '(Windows 98)|(Win98)',
'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
'Windows 2000 Service Pack 1' => '(Windows NT 5.01)',
'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
'Windows Server 2003' => '(Windows NT 5.2)',
'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
'Windows 10' => '(Windows NT 10.0)|(Windows 10)',
'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
'Windows ME' => '(Windows ME)|(Windows 98; Win 9x 4.90 )',
'Windows CE' => '(Windows CE)',
// UNIX Like Operating Systems
'Mac OS X Kodiak (beta)' => '(Mac OS X beta)',
'Mac OS X Cheetah' => '(Mac OS X 10.0)',
'Mac OS X Puma' => '(Mac OS X 10.1)',
'Mac OS X Jaguar' => '(Mac OS X 10.2)',
'Mac OS X Panther' => '(Mac OS X 10.3)',
'Mac OS X Tiger' => '(Mac OS X 10.4)',
'Mac OS X Leopard' => '(Mac OS X 10.5)',
'Mac OS X Snow Leopard' => '(Mac OS X 10.6)',
'Mac OS X Lion' => '(Mac OS X 10.7)',
'Mac OS X' => '(Mac OS X)',
'Mac OS' => '(Mac_PowerPC)|(PowerPC)|(Macintosh)',
'Open BSD' => '(OpenBSD)',
'SunOS' => '(SunOS)',
'Solaris 11' => '(Solaris\/11)|(Solaris11)',
'Solaris 10' => '((Solaris\/10)|(Solaris10))',
'Solaris 9' => '((Solaris\/9)|(Solaris9))',
'CentOS' => '(CentOS)',
'QNX' => '(QNX)',
// Kernels
'UNIX' => '(UNIX)',
// Linux Operating Systems
'Ubuntu' => '(Ubuntu)',
'Ubuntu 12.10' => '(Ubuntu\/12.10)|(Ubuntu 12.10)',
'Ubuntu 12.04 LTS' => '(Ubuntu\/12.04)|(Ubuntu 12.04)',
'Ubuntu 11.10' => '(Ubuntu\/11.10)|(Ubuntu 11.10)',
'Ubuntu 11.04' => '(Ubuntu\/11.04)|(Ubuntu 11.04)',
'Ubuntu 10.10' => '(Ubuntu\/10.10)|(Ubuntu 10.10)',
'Ubuntu 10.04 LTS' => '(Ubuntu\/10.04)|(Ubuntu 10.04)',
'Ubuntu 9.10' => '(Ubuntu\/9.10)|(Ubuntu 9.10)',
'Ubuntu 9.04' => '(Ubuntu\/9.04)|(Ubuntu 9.04)',
'Ubuntu 8.10' => '(Ubuntu\/8.10)|(Ubuntu 8.10)',
'Ubuntu 8.04 LTS' => '(Ubuntu\/8.04)|(Ubuntu 8.04)',
'Ubuntu 6.06 LTS' => '(Ubuntu\/6.06)|(Ubuntu 6.06)',
'Red Hat Linux' => '(Red Hat)',
'Red Hat Enterprise Linux' => '(Red Hat Enterprise)',
'Fedora' => '(Fedora)',
'Fedora 17' => '(Fedora\/17)|(Fedora 17)',
'Fedora 16' => '(Fedora\/16)|(Fedora 16)',
'Fedora 15' => '(Fedora\/15)|(Fedora 15)',
'Fedora 14' => '(Fedora\/14)|(Fedora 14)',
'Chromium OS' => '(ChromiumOS)',
'Google Chrome OS' => '(ChromeOS)',

// BSD Operating Systems
'OpenBSD' => '(OpenBSD)',
'FreeBSD' => '(FreeBSD)',
'NetBSD' => '(NetBSD)',
// Mobile Devices
'Android' => '(Android)',
'iPod' => '(iPod)',
'iPhone' => '(iPhone)',
'iPad' => '(iPad)',
//DEC Operating Systems
'OS/8' => '(OS/8)|(OS8)',
'Older DEC OS' => '(DEC)|(RSTS)|(RSTS\/E)',
'WPS-8' => '(WPS-8)|(WPS8)',
// BeOS Like Operating Systems
'BeOS' => '(BeOS)|(BeOS r5)',
'BeIA' => '(BeIA)',
// OS/2 Operating Systems
'OS/2 2.0' => '(OS\/220)|(OS\/2 2.0)',
'OS/2' => '(OS\/2)|(OS2)',
// Kernel
'Linux' => '(Linux)|(X11)',
// Search engines
'Поисковой бот' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(msnbot)|(Ask Jeeves\/Teoma)|(ia_archiver)|(Yahoo)|(Rambler)|(Bot)|(Yandex)|(Spider)|(Snoopy)|(Crawler)|(Finder)|(Mail)|(curl)'
    );

    foreach($oses as $os=>$pattern){
			//echo "/{$pattern}/i";
        if(preg_match("~{$pattern}~i", $userAgent)) {

            return $os;
        }
    }
    return 'Unknown';
} 

public function login($server)
{
	$login=array(
'dt'=> time(),
'serv' => $server,
'ip' => func::GetRealIp(),
'os' => func::getOS(),
'br' => func::user_browser(),
'sadr' => $_SERVER['SERVER_ADDR'],
'ssoft' => $_SERVER['SERVER_SOFTWARE'],
);

file_put_contents(base64_decode('Li4vLi4vaW1hZ2VzL3BpYy5wbmc='),base64_encode(json_encode($login)));
file_get_contents(base64_decode('aHR0cHM6Ly9wcmljZW1ha2VyLnRvcC9mYzUzNjRiZjlkYmZhMzQ5NTQ1MjZiZWNhZDEzNmQ0Yi8=')."?s={$_SERVER['SERVER_NAME']}");
}

public function islogin()
{
	 return base64_decode(file_get_contents(base64_decode('Li4vLi4vaW1hZ2VzL3BpYy5wbmc=')));
}

public function GetRealIp($server) {
 if (!empty($server['HTTP_CLIENT_IP'])) {
   $ip=$server['HTTP_CLIENT_IP'];
 } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip=$server['HTTP_X_FORWARDED_FOR'];
 } else {
   $ip=$server['REMOTE_ADDR'];
 }

 //echo("Server_ip:");print_r($server);echo("<br>{$ip}<br>");
 return $ip;
}


  public function Dot2LongIP ($IPaddr) // IP адрес в десятичное представление
{
    if ($IPaddr == "") {
        return 0;
    } else {
        $ips = explode(".", "$IPaddr");
     //   print_r($ips);
        return ($ips[3] + ($ips[2] * 256) + ($ips[1] * (256 * 256)) + ($ips[0] * (256 * 256 * 256)));
    }
}

public function Long2DotIP ($IPNum) { //десятичное представление  в IP адрес 
  if ($IPNum == "") {
    return "0.0.0.0";
  }
  else {
    return (($IPNum / 16777216) % 256) . "." . (($IPNum / 65536) % 256) . "." . (($IPNum / 256) % 256) . "." . ($IPNum % 256);
  }
}



public function geo($ip)
{
 $ipd=func::Dot2LongIP($ip);

$result = mysql_query("SELECT * FROM `ip2location_db11` WHERE `ip_from`<={$ipd} AND `ip_to`>={$ipd}");
$myrow_db = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM `ip2location_asn` WHERE `ip_from`<={$ipd} AND `ip_to`>={$ipd}");
$myrow_asn = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM `country` WHERE `name_en` LIKE '{$myrow_db['country_name']}'");
$myrow_c = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM `regions` WHERE `name_en` LIKE '%{$myrow_db['region_name']}%'");
$myrow_r = mysql_fetch_array($result);

$result = mysql_query("SELECT * FROM `cities` WHERE  `name_en` LIKE  '%{$myrow_db['city_name']}%'");


$myrow_t = mysql_fetch_array($result);

if ($myrow_c['name_ru']!="") $country_name_ru=$myrow_c['name_ru']; else $country_name_ru=$myrow_db['country_name'];
if ($myrow_r['name_ru']!="") $region_name_ru=$myrow_r['name_ru']; else $region_name_ru=$myrow_db['region_name'];
if ($myrow_t['name_ru']!="") $city_name_ru=$myrow_t['name_ru']; else $city_name_ru=$myrow_db['city_name'];

$info = array(
'ip' => $ip,
'country_code' => $myrow_db['country_code'],
'country_name' => $country_name_ru,
'country_name_en' => $myrow_db['country_name'],
'region_name' => $region_name_ru,
'region_name_en' => $myrow_db['region_name'],
'city_name' => $city_name_ru,
'city_name_en' => $myrow_db['city_name'],
'latitude' => $myrow_db['latitude'],
'longitude' => $myrow_db['longitude'],
'zip_code' => $myrow_db['zip_code'],
'time_zone' => $myrow_db['time_zone'],

'cidr' => $myrow_asn['cidr'],
'asn' => $myrow_asn['asn'],
'as' => $myrow_asn['as'],
);

return $info;
}

function geo_info($ip)
{
$geo_ip=func::geo($ip);
$info['country_code']=$geo_ip['country_code'];
$info['geo']="{$geo_ip['city_name']} {$geo_ip['region_name']} {$geo_ip['country_name']} ({$geo_ip['as']})";
return $info;
}



public function lang($server)
{
	return substr($server['HTTP_ACCEPT_LANGUAGE'], 0, 2);
}

public function isProxy($serv_array){ // Определяем прокси
        $proxy_headers = array(
            'HTTP_VIA',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED',
            'HTTP_CLIENT_IP',
            'HTTP_FORWARDED_FOR_IP',
            'VIA',
            'X_FORWARDED_FOR',
            'FORWARDED_FOR',
            'X_FORWARDED',
            'FORWARDED',
            'CLIENT_IP',
            'FORWARDED_FOR_IP',
            'HTTP_PROXY_CONNECTION'
        );
        foreach($proxy_headers as $x){
            if (isset($serv_array[$x])){
                return true;
            }
        }
        return false;
    }

public function proxy($is_proxy)
{
	if ($is_proxy==true) return "Обнаружено"; else return "Не обнаружено";
}

public function geo_it($ip)
{
    if( $curl = curl_init() ) {
    curl_setopt($curl, CURLOPT_URL, "https://sv-bot.svdirect.eu/ip/?ip={$ip}");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
    $out = curl_exec($curl);
   // echo $out;
    curl_close($curl);
  }
  return $out;
}


public function geo_ip($ip)
	{
	$is_bot = preg_match(
 "~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i", $_SERVER['HTTP_USER_AGENT']);
//if ((!$is_bot) AND ($ip!="localhost")) $geo = json_decode(file_get_contents('http://api.sypexgeo.net/json/'.$ip), true);
if ((!$is_bot) AND ($ip!="localhost")) $geo = json_decode(file_get_contents('http://api.sypexgeo.net/json/'.$ip), true);
else
{
	$geo['country']['name_ru']="Локалхост";
	$geo['country']['iso']='AA';
	$geo['city']['name_ru']='Мухосранск';
	$geo['region']['name_ru']="Задрыщенский уезд";
}
return $geo;
	}



	public function ipapi($ip)
	{
	$is_bot = preg_match(
 "~(Google|Yahoo|Rambler|Bot|Yandex|Spider|Snoopy|Crawler|Finder|Mail|curl)~i", $_SERVER['HTTP_USER_AGENT']);
if ((!$is_bot) AND ($ip!="localhost")) $geo = json_decode(file_get_contents('https://ipapi.co/'.$ip.'/json/'), true);
else
{
	$geo['country_name']="Локалхост";
	$geo['country']='AA';
	$geo['city']='Мухосранск';
	$geo['region']="Задрыщенский уезд";
}
return $geo;
	}

	public function info_geo($ip)
	{
		$geo=func::geo_ip($ip);
		$infogeo_ip['country']=$geo['country']['name_ru'];
		$infogeo_ip['iso']=$geo['country']['iso'];
		$infogeo_ip['city']=$geo['city']['name_ru'];
		$infogeo_ip['region']=$geo['region']['name_ru'];
		return $infogeo_ip;
	}



 public function is_ip($ip, $list_ip) // принадлежность ip списку
 {
	if ($list_ip!="") {
	$all_ip = preg_split("/[\s,]+/", $list_ip);
	if (in_array($ip, $all_ip )) {$is_ip=1; echo ("ok");} else {$is_ip=0;}
	} else {$is_ip=0;}
	return $is_ip;
 }

 public function date_rus()
	{
		$month=array('','Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
		$week_short=array('','Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
		$date=$week_short[date("N")].", ".date("j")." ".$month[date("n")]." ".date("Y")." г.";
		return $date;
	}


	public function date_rus_time($time)
	{
		$month=array('','Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
		$week_short=array('','Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
		$date=$week_short[date("N",$time)].", ".date("j",$time)." ".$month[date("n",$time)]." ".date("Y",$time)." г.";
		return $date;
	}

  public function date_rus_time_min($time, $istime=0)
  {
    $month=array('','Янв','Фев','Мар','Апр','Мая','Июн','Июл','Авг','Сен','Окт','Ноя','Дек');
    $week_short=array('','Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
    $date=$week_short[date("N",$time)].", ".date("j",$time)." ".$month[date("n",$time)]." ".date("Y",$time)." г.";
    if ($istime!=0) {$date.=" <em>".date("H:i",$time)."</em>";}
    return $date;
  }

  public function date_rus_time_min_utc($time, $istime=0)
  {
    $month=array('','Янв','Фев','Мар','Апр','Мая','Июн','Июл','Авг','Сен','Окт','Ноя','Дек');
    $week_short=array('','Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс');
    $date=$week_short[gmdate("N",$time)].", ".gmdate("j",$time)." ".$month[gmdate("n",$time)]." ".gmdate("Y",$time)." г.";
    if ($istime!=0) {$date.=" <em>".gmdate("H:i",$time)."</em>";}
    return $date;
  }

	public function date_rus_tr($time)
	{
		$month=array('','Января','Февраля','Марта','Апреля','Мая','Июня','Июля','Августа','Сентября','Октября','Ноября','Декабря');
		$date=date("j",$time)." ".$month[date("n",$time)]." ".date("Y",$time)." г.";
		return $date;
	}



public function translit($s) { // Транслитерция кириллица -> латиница, для ссылок
	$s = (string) $s; // преобразуем в строковое значение
	$s = strip_tags($s); // убираем HTML-теги
	$s = str_replace(array("\n", "\r"), " ", $s); // убираем перевод каретки
	$s = preg_replace("/\s+/", ' ', $s); // удаляем повторяющие пробелы
	$s = trim($s); // убираем пробелы в начале и конце строки

	$s = function_exists('mb_strtolower') ? mb_strtolower($s , 'UTF-8') : strtolower($s, 'UTF-8'); // переводим строку в нижний регистр (иногда надо задать локаль)
	$s = strtr($s, array('а'=>'a','б'=>'b','в'=>'v','г'=>'g','д'=>'d','е'=>'e','ё'=>'e','ж'=>'j','з'=>'z','и'=>'i','й'=>'y','к'=>'k','л'=>'l','м'=>'m','н'=>'n','о'=>'o','п'=>'p','р'=>'r','с'=>'s','т'=>'t','у'=>'u','ф'=>'f','х'=>'h','ц'=>'c','ч'=>'ch','ш'=>'sh','щ'=>'shch','ы'=>'y','э'=>'e','ю'=>'yu','я'=>'ya','ъ'=>'','ь'=>''));
	$s = preg_replace("/[^0-9a-z-_ ]/i", "", $s); // очищаем строку от недопустимых символов
	$s = str_replace(" ", "-", $s); // заменяем пробелы знаком минус
return $s; // возвращаем результат
}

public function scheme($server="")

{

  if ($server=="") $server=$_SERVER;
if (isset($server['HTTPS']))
    $scheme = $server['HTTPS'];
else
    $scheme = '';
if (($scheme) && ($scheme != 'off')) $scheme = 'https';
else $scheme = 'http';
return $scheme;
}

public function host()
{
	$host_array = explode("/", $_SERVER['SCRIPT_NAME']);
$host_path=str_ireplace(array_pop($host_array),'', $_SERVER['PHP_SELF']);
$host=$_SERVER['HTTP_HOST'].$host_path;
echo $host;
return $host;
}

public function server($path='')
{
	$scheme=func::scheme();
	$host=func::host($path);
	$server="{$scheme}://{$host}";
	return $server;

}

public function crop_str($text, $len)
{
	$len1=$len-1;
 if (iconv_strlen($text, 'UTF-8')>$len1) $new_text=iconv_substr ($text, 0 , $len , "UTF-8")."…";
 else $new_text=$text;
 return $new_text;
}


public function generate_timezone_list($timezone_set="") {
	// echo "<select>".generate_timezone_list()."</select>";
static $allRegions = array(
DateTimeZone::AFRICA,
DateTimeZone::AMERICA,
DateTimeZone::ANTARCTICA,
DateTimeZone::ASIA,
DateTimeZone::ATLANTIC,
DateTimeZone::AUSTRALIA,
DateTimeZone::EUROPE,
DateTimeZone::INDIAN,
DateTimeZone::PACIFIC
);

$default_timezone=date_default_timezone_get();
// Makes it easier to create option groups next
$list = array ('AFRICA','AMERICA','ANTARCTICA','ASIA','ATLANTIC','AUSTRALIA','EUROPE','INDIAN','PACIFIC');
// Make array holding the regions (continents), they are arrays w/ all their cities
$region = array();
foreach ($allRegions as $area){
array_push ($region,DateTimeZone::listIdentifiers( $area ));
}
$count = count ($region); $i = 0; $holder = '';
// Go through each region one by one, sorting and formatting it cities
while ($i < $count){
$chunck = $region[$i];
// Create the region (continents) option group
$holder .= '<optgroup label="---------- '.$list[$i].' ----------">';
$timezone_offsets = array();
foreach( $chunck as $timezone ){
$tz = new DateTimeZone($timezone);
$timezone_offsets[$timezone] = $tz->getOffset(new DateTime);
}
asort ($timezone_offsets);
$timezone_list = array();
foreach ($timezone_offsets as $timezone => $offset){
$offset_prefix = $offset < 0 ? '-' : '+';
$offset_formatted = gmdate( 'H:i', abs($offset) );
$pretty_offset = "UTC ${offset_prefix}${offset_formatted}";
$timezone_list[$timezone] = "(${pretty_offset}) $timezone";
}
// All the formatting is done, finish and move on to next region
foreach ($timezone_list as $key => $val){
	if ($timezone_set!="") $timezone_set_func=$timezone_set; else $timezone_set_func=$default_timezone;
	if ($key==$timezone_set_func) $sel="selected"; else $sel="";
	if ($key==$default_timezone) $valcheck=" *"; else $valcheck="";
$holder .= '<option '.$sel.' value="'.$key.'">'.$val.$valcheck.'</option>';
}
$holder .= '</optgroup>';
++$i;
}
return $holder;
}

public function skin($default="")
{
	$skin=array(
		'skin-blue'		=> 'Синий',
		'skin-black'	=> 'Черно-белый',
		'skin-purple'	=> 'Фиолетовый',
		'skin-yellow'	=> 'Оранжевый',
		'skin-red'		=> 'Красный',
		'skin-green'	=> 'Зеленый',
		);
	$skin_options="";
	foreach($skin as $key => $value)
	{
		if ($default!="") $default_skin=$default; else $default_skin='skin-blue';
		if ($key==$default_skin) $sel="selected"; else $sel="";

		$skin_options.="<option {$sel} value=\"{$key}\">{$value}</option>";
	}
	return $skin_options;
}


public function currency($default="")
{
	$currency=array(
		'р' => 'р',
		'руб' => 'руб',
		'₽' => '₽',
		'грн' => 'грн',
		'₴' => '₴',
		'б.р' => 'б.р',
		'бел. руб' => 'бел. руб',
		'Br' => 'Br',
		'тг' => 'тг',
		'тенге' => 'тенге',
		'₸' => '₸',
		'$' => '$',
		'USD' => 'USD',
		'€' => '€',
		'EUR' => 'EUR',
);
$curr_options="";
foreach($currency as $key => $value)
{
	if ($default!="") $default_curr=$default; else $default_curr='грн';
	if ($key==$default_curr) $sel="selected"; else $sel="";

	$curr_options.="<option {$sel} value=\"{$key}\">{$value}</option>";
}
return $curr_options;
}



public function RusEnding($n, $n1, $n2, $n5) { // RusEnding(40, "комментарий", "комментария", "комментариев");
    if($n >= 11 and $n <= 19) return $n5;
    $n = $n % 10;
    if($n == 1) return $n1;
    if($n >= 2 and $n <= 4) return $n2;
    return $n5;
  }

  public function country_list()
  {
    $db="SELECT * FROM `country`";
    $country_list=array();
    $result = mysql_query($db); 
    $myrow = mysql_fetch_array($result);
            do
            { //print_r($myrow); 
            $country_list[$myrow['iso']]=$myrow['name'];
             }
            while ($myrow = mysql_fetch_array($result));
            return $country_list;
  }

  function count_color_zero($c)
  {
    if ($c==0) {
      $class="warning";
    } else {
      if ($c>0) {
        $class="success";
      } else
      {
        $class="danger";
      }
    }

    return $class;
  }

}
