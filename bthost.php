<?php
function bthost_MetaData()
{
	return ["DisplayName" => "Bthost", "APIVersion" => "1.0.4", "HelpDoc" => "https://support.wbstudio.org/help-center/articles/1/47/11/bthost%E5%AF%B9%E6%8E%A5%E6%A8%A1%E5%9D%97%E5%AE%89%E8%A3%85%E4%BD%BF%E7%94%A8%E8%AF%B4%E6%98%8E"];
}
function bthost_ConfigOptions()
{
	$group_id = input("param.id/d");
	if ($group_id) {
		$s = \think\Db::name("servers");
		$server = $s->where("gid", $group_id)->find();
	}
	if ($server["secure"] == 1) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $server["hostname"] . "/api/vhost";
	$access_token = $server["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/sort_list?" . $get_data . "&signature=" . $signature;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	$type = [];
	foreach ($res["data"] as $key => $vo) {
		$type[$vo["id"]] = $vo["name"];
	}
	$url1 = $api . "/plans_list?" . $get_data . "&signature=" . $signature;
	$curl1 = curl_init();
	curl_setopt($curl1, CURLOPT_URL, $url1);
	curl_setopt($curl1, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl1, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl1, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl1, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl1, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl1, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl1, CURLOPT_POST, true);
	$output1 = curl_exec($curl1);
	$curlInfo = curl_getinfo($curl1);
	curl_close($curl1);
	$res1 = json_decode($output1, true);
	$plan = [];
	foreach ($res1["data"] as $key => $vos) {
		$plan[$vos["id"]] = $vos["name"];
	}
	return [["type" => "radio", "name" => "是否开启弹性", "description" => "开启的话则需填写可配置选项,不开启的话则Bthost套餐ID在下方选择,宝塔分类ID开启不开启都是在以下栏中选择,具体参考帮助文档", "options" => "开启,不开启", "default" => "不开启", "key" => "txchoose"], ["type" => "dropdown", "name" => "Bthost套餐计划ID", "description" => "该项自动获取，直接下拉选择即可", "options" => $plan, "key" => "planid"], ["type" => "dropdown", "name" => "宝塔分类ID", "description" => "该项自动获取，直接下拉选择即可", "options" => $type, "key" => "sortid"], ["type" => "text", "name" => "升级-网站大小", "description" => "单位M，仅为升级套餐时使用，0=不限制，请填写与\"Bthost套餐计划\"配置一样，可为空", "key" => "a1"], ["type" => "text", "name" => "升级-流量大小", "description" => "单位M，仅为升级套餐时使用，0=不限制，请填写与\"Bthost套餐计划\"配置一样，可为空", "key" => "a2"], ["type" => "text", "name" => "升级-数据库大小", "description" => "仅为升级套餐时使用，请填写与\"Bthost套餐计划\"配置一样，可为空", "key" => "a3"], ["type" => "text", "name" => "升级-可绑定域名数", "description" => "仅为升级套餐时使用，请填写与\"Bthost套餐计划\"配置一样，可为空", "key" => "a4"], ["type" => "text", "name" => "升级-网站可备份数", "description" => "仅为升级套餐时使用，请填写与\"Bthost套餐计划\"配置一样，可为空", "key" => "a5"], ["type" => "text", "name" => "升级-数据库可备份数", "description" => "仅为升级套餐时使用，请填写与\"Bthost套餐计划\"配置一样，可为空", "key" => "a6"], ["type" => "text", "name" => "升级-分类ID", "description" => "仅为升级套餐时使用，分类ID为宝塔面板的分类ID，默认请填0，请填写与\"Bthost套餐计划\"配置一样，可为空", "key" => "a7"], ["type" => "text", "name" => "升级-域名审核", "description" => "仅为升级套餐时使用，0=无需审核,1=需要审核，请填写与\"Bthost套餐计划\"配置一样，可为空", "key" => "a8"], ["type" => "text", "name" => "备注", "description" => "用于前台显示，可不填，但在自适应下可能影响美观。", "key" => "d1"]];
}
function bthost_TestLink($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/index?" . $get_data . "&signature=" . $signature;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if (isset($res["code"]) && $res["code"] == 1) {
		$result["status"] = 200;
		$result["data"]["server_status"] = 1;
	} else {
		$result["status"] = 200;
		$result["data"]["server_status"] = 0;
		$result["data"]["msg"] = $res["msg"];
	}
	return $result;
}
function bthost_CreateAccount($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	if ($params["configoptions"]["txchoose"] == "不开启") {
		$api = $ssl . $params["server_host"] . "/api/vhost";
		$access_token = $params["accesshash"];
		$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
		$data["time"] = time();
		$data["random"] = mt_rand();
		$data["token"] = $access_token;
		$datas = $data;
		sort($data, SORT_STRING);
		$str = implode($data);
		$signature = md5($str);
		$signature = strtoupper($signature);
		unset($datas["token"]);
		$get_data = http_build_query($datas);
		$url = $api . "/user_create?" . $get_data . "&signature=" . $signature;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, $ua);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		$output = curl_exec($curl);
		$curlInfo = curl_getinfo($curl);
		curl_close($curl);
		$res = json_decode($output, true);
		$customid = \think\Db::name("customfields")->where("type", "product")->where("relid", $params["productid"])->where("fieldname", "userid")->value("id");
		if (empty($customid)) {
			$customfields = ["type" => "product", "relid" => $params["productid"], "fieldname" => "userid", "fieldtype" => "text", "adminonly" => 1, "create_time" => time()];
			$customid = \think\Db::name("customfields")->insertGetId($customfields);
		}
		$exist = \think\Db::name("customfieldsvalues")->where("fieldid", $customid)->where("relid", $params["hostid"])->find();
		if (empty($exist)) {
			$data = ["fieldid" => $customid, "relid" => $params["hostid"], "value" => $res["data"]["id"], "create_time" => time()];
			\think\Db::name("customfieldsvalues")->insert($data);
		} else {
			\think\Db::name("customfieldsvalues")->where("id", $exist["id"])->update(["value" => $res["data"]["id"]]);
		}
		$update["domain"] = $res["data"]["username"];
		$update["username"] = $res["data"]["username"];
		$update["password"] = cmf_encrypt($res["data"]["password"]);
		$update["dedicatedip"] = $params["server_ip"];
		\think\Db::name("host")->where("id", $params["hostid"])->update($update);
		if ($params["secure"] == true) {
			$ssl = "https://";
		} else {
			$ssl = "http://";
		}
		$api2 = $ssl . $params["server_host"] . "/api/vhost";
		$access_token2 = $params["accesshash"];
		$timex = $params["nextduedate"];
		$ua2 = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
		$data2["time"] = time();
		$data2["random"] = mt_rand();
		$data2["token"] = $access_token2;
		$datas2 = $data2;
		sort($data2, SORT_STRING);
		$str2 = implode($data2);
		$signature2 = md5($str2);
		$signature2 = strtoupper($signature2);
		unset($datas2["token"]);
		$get_data2 = http_build_query($datas2);
		$url2 = $api2 . "/host_build?" . $get_data2 . "&signature=" . $signature2;
		$postvars2 = ["plans_id" => $params["configoptions"]["planid"], "sort_id" => $params["configoptions"]["sortid"], "user_id" => $res["data"]["id"], "endtime" => "2030-10-01"];
		$postdata2 = http_build_query($postvars2);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url2);
		curl_setopt($curl, CURLOPT_USERAGENT, $ua2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata2);
		$output2 = curl_exec($curl);
		curl_close($curl);
		$curlInfo = curl_getinfo($curl);
		$re = json_decode($output2, true);
		$customid = \think\Db::name("customfields")->where("type", "product")->where("relid", $params["productid"])->where("fieldname", "hostid")->value("id");
		if (empty($customid)) {
			$customfields = ["type" => "product", "relid" => $params["productid"], "fieldname" => "hostid", "fieldtype" => "text", "adminonly" => 1, "create_time" => time()];
			$customid = \think\Db::name("customfields")->insertGetId($customfields);
		}
		$exist = \think\Db::name("customfieldsvalues")->where("fieldid", $customid)->where("relid", $params["hostid"])->find();
		if (empty($exist)) {
			$data = ["fieldid" => $customid, "relid" => $params["hostid"], "value" => $re["data"]["site"]["id"], "create_time" => time()];
			\think\Db::name("customfieldsvalues")->insert($data);
		} else {
			\think\Db::name("customfieldsvalues")->where("id", $exist["id"])->update(["value" => $re["data"]["site"]["id"]]);
		}
		if ($res["code"] == 1 && $re["code"] == 1) {
			return "success";
		} else {
			if ($res["code"] == 0) {
				return $res["msg"];
			} else {
				if ($re["code"] == 0) {
					return $re["msg"];
				} else {
					return "开通失败,未知错误";
				}
			}
		}
	} else {
		$api = $ssl . $params["server_host"] . "/api/vhost";
		$access_token = $params["accesshash"];
		$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
		$data["time"] = time();
		$data["random"] = mt_rand();
		$data["token"] = $access_token;
		$datas = $data;
		sort($data, SORT_STRING);
		$str = implode($data);
		$signature = md5($str);
		$signature = strtoupper($signature);
		unset($datas["token"]);
		$get_data = http_build_query($datas);
		$url = $api . "/user_create?" . $get_data . "&signature=" . $signature;
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, $ua);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
		$output = curl_exec($curl);
		$curlInfo = curl_getinfo($curl);
		curl_close($curl);
		$res = json_decode($output, true);
		$customid = \think\Db::name("customfields")->where("type", "product")->where("relid", $params["productid"])->where("fieldname", "userid")->value("id");
		if (empty($customid)) {
			$customfields = ["type" => "product", "relid" => $params["productid"], "fieldname" => "userid", "fieldtype" => "text", "adminonly" => 1, "create_time" => time()];
			$customid = \think\Db::name("customfields")->insertGetId($customfields);
		}
		$exist = \think\Db::name("customfieldsvalues")->where("fieldid", $customid)->where("relid", $params["hostid"])->find();
		if (empty($exist)) {
			$data = ["fieldid" => $customid, "relid" => $params["hostid"], "value" => $res["data"]["id"], "create_time" => time()];
			\think\Db::name("customfieldsvalues")->insert($data);
		} else {
			\think\Db::name("customfieldsvalues")->where("id", $exist["id"])->update(["value" => $res["data"]["id"]]);
		}
		$update["domain"] = $res["data"]["username"];
		$update["username"] = $res["data"]["username"];
		$update["password"] = cmf_encrypt($res["data"]["password"]);
		$update["dedicatedip"] = $params["server_ip"];
		\think\Db::name("host")->where("id", $params["hostid"])->update($update);
		if ($params["secure"] == true) {
			$ssl = "https://";
		} else {
			$ssl = "http://";
		}
		$api2 = $ssl . $params["server_host"] . "/api/vhost";
		$access_token2 = $params["accesshash"];
		$timex = $params["nextduedate"];
		$ua2 = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
		$data2["time"] = time();
		$data2["random"] = mt_rand();
		$data2["token"] = $access_token2;
		$datas2 = $data2;
		sort($data2, SORT_STRING);
		$str2 = implode($data2);
		$signature2 = md5($str2);
		$signature2 = strtoupper($signature2);
		unset($datas2["token"]);
		$get_data2 = http_build_query($datas2);
		$url2 = $api2 . "/host_build?" . $get_data2 . "&signature=" . $signature2;
		$postvars2 = ["plans_id" => $params["configoptions"]["plan"], "sort_id" => $params["configoptions"]["sortid"], "user_id" => $res["data"]["id"], "endtime" => "2030-10-01"];
		$postdata2 = http_build_query($postvars2);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url2);
		curl_setopt($curl, CURLOPT_USERAGENT, $ua2);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata2);
		$output2 = curl_exec($curl);
		curl_close($curl);
		$curlInfo = curl_getinfo($curl);
		$re = json_decode($output2, true);
		$customid = \think\Db::name("customfields")->where("type", "product")->where("relid", $params["productid"])->where("fieldname", "hostid")->value("id");
		if (empty($customid)) {
			$customfields = ["type" => "product", "relid" => $params["productid"], "fieldname" => "hostid", "fieldtype" => "text", "adminonly" => 1, "create_time" => time()];
			$customid = \think\Db::name("customfields")->insertGetId($customfields);
		}
		$exist = \think\Db::name("customfieldsvalues")->where("fieldid", $customid)->where("relid", $params["hostid"])->find();
		if (empty($exist)) {
			$data = ["fieldid" => $customid, "relid" => $params["hostid"], "value" => $re["data"]["site"]["id"], "create_time" => time()];
			\think\Db::name("customfieldsvalues")->insert($data);
		} else {
			\think\Db::name("customfieldsvalues")->where("id", $exist["id"])->update(["value" => $re["data"]["site"]["id"]]);
		}
		if ($res["code"] == 1 && $re["code"] == 1) {
			return "success";
		} else {
			if ($res["code"] == 0) {
				return $res["msg"];
			} else {
				if ($re["code"] == 0) {
					return $re["msg"];
				} else {
					return "开通失败,未知错误";
				}
			}
		}
	}
}
function bthost_On($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_start?" . $get_data . "&signature=" . $signature;
	$postvars = ["id" => $params["customfields"]["hostid"]];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["code"] == 1) {
		return "success";
	} elseif ($res["code"] == 0) {
		return $res["msg"];
	} else {
		return "开启站点失败,未知错误";
	}
}
function bthost_Off($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_stop?" . $get_data . "&signature=" . $signature;
	$postvars = ["id" => $params["customfields"]["hostid"]];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["code"] == 1) {
		return "success";
	} elseif ($res["code"] == 0) {
		return $res["msg"];
	} else {
		return "关闭站点失败,未知错误";
	}
}
function bthost_SuspendAccount($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_locked?" . $get_data . "&signature=" . $signature;
	$postvars = ["id" => $params["customfields"]["hostid"]];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["code"] == 1) {
		return "success";
	} elseif ($res["code"] == 0) {
		return $res["msg"];
	} else {
		return "暂停失败,未知错误";
	}
}
function bthost_UnSuspendAccount($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_start?" . $get_data . "&signature=" . $signature;
	$postvars = ["id" => $params["customfields"]["hostid"]];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["code"] == 1) {
		return "success";
	} elseif ($res["code"] == 0) {
		return $res["msg"];
	} else {
		return "解除暂停失败,未知错误";
	}
}
function bthost_CrackPassword($params, $new_pass)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_pass?" . $get_data . "&signature=" . $signature;
	$postvars = ["id" => $params["customfields"]["hostid"], "type" => "all", "password" => $new_pass];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["code"] == 1) {
		return "success";
	} elseif ($res["code"] == 0) {
		return $res["msg"];
	} else {
		return "修改密码失败,未知错误";
	}
}
function bthost_Renew($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$timex = $params["nextduedate"];
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_endtime?" . $get_data . "&signature=" . $signature;
	$postvars = ["id" => $params["customfields"]["hostid"], "endtime" => date("Y-m-d", $timex)];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["code"] == 1) {
		return "success";
	} elseif ($res["code"] == 0) {
		return $res["msg"];
	} else {
		return "续费失败,未知错误";
	}
}
function bthost_TerminateAccount($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_recycle?" . $get_data . "&signature=" . $signature;
	$postvars = ["id" => $params["customfields"]["hostid"]];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["code"] == 1) {
		return "success";
	} elseif ($res["code"] == 0) {
		return $res["msg"];
	} else {
		return "删除失败,未知错误";
	}
}
function bthost_ChangePackge($params)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$timex = $params["nextduedate"];
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_edit?" . $get_data . "&signature=" . $signature;
	$postvars = ["id" => $params["customfields"]["hostid"], "site_max" => $params["configoptions"]["a1"], "flow_max" => $params["configoptions"]["a2"], "sql_max" => $params["configoptions"]["a3"], "domain_max" => $params["configoptions"]["a4"], "web_back_num" => $params["configoptions"]["a5"], "sql_back_num" => $params["configoptions"]["a6"], "sort_id" => $params["configoptions"]["a7"], "is_audit" => $params["configoptions"]["a8"]];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["code"] == 1) {
		return "success";
	} elseif ($res["code"] == 0) {
		return $res["msg"];
	} else {
		return "更改套餐失败,未知错误";
	}
}
function bthost_ClientArea($params)
{
	return ["information" => ["name" => "产品详情"]];
}
function bthost_ClientAreaOutput($params, $key)
{
	if ($params["secure"] == true) {
		$ssl = "https://";
	} else {
		$ssl = "http://";
	}
	$api = $ssl . $params["server_host"] . "/api/vhost";
	$access_token = $params["accesshash"];
	$ua = "Mozilla/5.0 (Windows NT 6.1; WOW64; rv:43.0) Gecko/20100101 Firefox/43.0";
	$data["time"] = time();
	$data["random"] = mt_rand();
	$data["token"] = $access_token;
	$datas = $data;
	sort($data, SORT_STRING);
	$str = implode($data);
	$signature = md5($str);
	$signature = strtoupper($signature);
	unset($datas["token"]);
	$get_data = http_build_query($datas);
	$url = $api . "/host_info?" . $get_data . "&signature=" . $signature;
	$dataa["time"] = time();
	$time = $dataa["time"];
	$dataa["random"] = mt_rand();
	$random = $dataa["random"];
	$dataa["token"] = $access_token;
	$dataas = $dataa;
	sort($dataa, SORT_STRING);
	$str2 = implode($dataa);
	$signature2 = md5($str2);
	$signature2 = strtoupper($signature2);
	$sign = $signature2;
	$postvars = ["id" => $params["customfields"]["hostid"]];
	$postdata = http_build_query($postvars);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_USERAGENT, $ua);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
	$output = curl_exec($curl);
	$curlInfo = curl_getinfo($curl);
	curl_close($curl);
	$res = json_decode($output, true);
	if ($res["data"]["status"] == "normal") {
		$res["data"]["status"] = "正在运行";
	}
	if ($res["data"]["status"] == "stop") {
		$res["data"]["status"] = "暂停";
	}
	if ($res["data"]["status"] == "locked") {
		$res["data"]["status"] = "锁定";
	}
	if ($res["data"]["status"] == "expired") {
		$res["data"]["status"] = "过期";
	}
	if ($res["data"]["status"] == "excess") {
		$res["data"]["status"] = "超量";
	}
	if ($res["data"]["status"] == "error") {
		$res["data"]["status"] = "异常";
	}
	if ($key == "information") {
		return ["template" => "templates/information.html", "vars" => ["product_name" => $params["name"], "idd" => $params["customfields"]["hostid"], "loginurl" => $params["server_host"], "username" => $params["username"], "password" => $params["password"], "time" => $time, "random" => $random, "signature" => $sign, "jxdz" => $res["data"]["default_analysis"], "jxfs" => $res["data"]["analysis_type"], "status" => $res["data"]["status"], "site" => $res["data"]["site_size"], "sitemax" => $res["data"]["site_max"], "bdw" => $res["data"]["flow_size"], "bdwmax" => $res["data"]["flow_max"], "db" => $res["data"]["sql_size"], "dbmax" => $res["data"]["sql_max"], "ssl" => $ssl, "d1" => $params["configoptions"]["d1"]]];
	}
}