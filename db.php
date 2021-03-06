<?php
require_once __DIR__."/db/SleekDB.php";
require_once __DIR__."/db/Store.php";
require_once __DIR__."/db/QueryBuilder.php";
require_once __DIR__."/db/Query.php";
require_once __DIR__."/db/Cache.php";

function add_white_click($data,$reason) {
    $dataDir = __DIR__ . "/logs";
    $wclicksStore = new \SleekDB\Store("whiteclicks", $dataDir);

	$calledIp = $data['ip'];
	$country = $data['country'];
	$dt = new DateTime();
	$time = $dt->getTimestamp();
	$os = $data['os'];
	$isp = str_replace(',',' ',$data['isp']);
	$user_agent = str_replace(',',' ',$data['ua']);

	parse_str($_SERVER['QUERY_STRING'],$queryarr);

	$click =[
		"subid"=>"12342314",
		"time"=>$time,
		"ip"=>$calledIp,
		"country"=>$country,
		"os"=>$os,
		"isp"=>$isp,
		"ua"=>$user_agent,
		"reason"=>$reason,
		"subs"=>$queryarr
    ];
	$wclicksStore->insert($click);
}

function add_black_click($subid,$data,$preland,$land) {
    $dataDir = __DIR__ . "/logs";
    $bclicksStore = new \SleekDB\Store("blackclicks", $dataDir);

	$calledIp = $data['ip'];
	$country = $data['country'];
	$dt = new DateTime();
	$time = $dt->getTimestamp();
	$os = $data['os'];
	$isp = str_replace(',',' ',$data['isp']);
	$user_agent = str_replace(',',' ',$data['ua']);
	$prelanding=isset($_COOKIE['prelanding'])?$_COOKIE['prelanding']:'unknown';
	$landing=isset($_COOKIE['landing'])?$_COOKIE['landing']:'unknown';

	parse_str($_SERVER['QUERY_STRING'],$queryarr);

	$click =[
		"subid"=>$subid,
		"time"=>$time,
		"ip"=>$calledIp,
		"country"=>$country,
		"os"=>$os,
		"isp"=>$isp,
		"ua"=>$user_agent,
		"subs"=>$queryarr,
		"preland"=>$prelanding,
		"land"=>$landing
    ];
	$bclicksStore->insert($click);
}

function add_lead($subid,$name,$phone,$status='Lead') {
    $dataDir = __DIR__ . "/logs";
    $leadsStore = new \SleekDB\Store("leads", $dataDir);

	$fbp=isset($_COOKIE['_fbp'])?$_COOKIE['_fbp']:'';
	$fbclid=isset($_COOKIE['fbclid'])?$_COOKIE['fbclid']:(isset($_COOKIE['_fbc'])?$_COOKIE['_fbc']:'');

	if ($status=='') $status='Lead';

	$dt = new DateTime();
	$time = $dt->getTimestamp();

	$lead=[
		"subid"=>$subid,
		"time"=>$time,
		"name"=>$name,
		"phone"=>$phone,
		"status"=>$status,
		"fbp"=>$fbp,
		"fbclid"=>$fbclid,
    ];
	$leadsStore->insert($lead);
}

function email_exists_for_subid($subid){
    $dataDir = __DIR__ . "/logs";
    $leadsStore = new \SleekDB\Store("leads", $dataDir);
	$lead=$leadsStore->findOneBy([["subid","=",$subid]]);
	if ($lead===null) return false;
    if (array_key_exists("email",$lead)) return true;
	return false;
}

function add_email($subid,$email){
    $dataDir = __DIR__ . "/logs";
    $leadsStore = new \SleekDB\Store("leads", $dataDir);
	$lead=$leadsStore->findOneBy([["subid","=",$subid]]);
	if ($lead===null) return;
	$lead["email"]=$email;
	$leadsStore->update($lead);
}

function add_lpctr($subid,$preland){
    $dataDir = __DIR__ . "/logs";
    $lpctrStore = new \SleekDB\Store("lpctr", $dataDir);
	$dt = new DateTime();
	$time = $dt->getTimestamp();

	$lpctr = [
		"time"=>$time,
		"subid"=>$subid,
		"preland"=>$preland
    ];
	$lpctrStore->insert($lpctr);
}

//??????????????????, ???????? ???? ?? ?????????? ?????????? subid ???????????????? ????????????????????????
//???????? ????????, ?? ?????????? ???????? ?????????? ???? ?????????? - ???????????? ?????? ??????????!
//?? ?????? ???? ?????????? ?????????? ?????? ?? ???? ?? ???? ?????????? ???????????????????? ?????????????? ????!!
function lead_is_duplicate($subid,$phone){
    $dataDir = __DIR__ . "/logs";
    $leadsStore = new \SleekDB\Store("leads", $dataDir);
	if($subid!=''){
        $lead=$leadsStore->findOneBy([["subid","=",$subid]]);
        if ($lead===null) return false;
        header("YWBDuplicate: We have this sub!");
        $phoneexists = ($lead["phone"]===$phone);
        if ($phoneexists){
            header("YWBDuplicate: We have this phone!");
            return true;
        }
        else{
            return false;
        }
	}
	else {
		//???????? ???????? c subid ?? ?????? ????????????-???? ??????, ???? ?????????????????? ???? ???????????? ????????????????
        $lead=$leadsStore->findOneBy([["phone","=",$phone]]);
		if ($lead===null) return false;
		return true;
	}
}
?>