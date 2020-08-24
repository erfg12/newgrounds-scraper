<?PHP
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

function cleanString ($s) {
	$s = preg_replace('/(\v|\s)+/', ' ', $s);
	$s = strip_tags($s);
	return trim(str_replace('\n','',$s));//trim(preg_replace("/\r\n|\r|\n/", ' ', $s)[0]);
}

function cleanString2 ($s) {
	$s = htmlentities($s,ENT_NOQUOTES,'UTF-8',false);
	return str_replace(array('&lt;','&gt;'),array('<','>'), $s);
}

function cleanImg ($s) {
	$s = explode("?",$s)[0];
	if (!strstr($s,"https")) $s = "https:".$s;
	return $s;
}

function fatf ($s) { //featured art title fix
	$s = str_replace('-',' ',$s);
	$s = str_replace('can t','can\'t',$s);
	$s = str_replace('don t','don\'t',$s);
	$s = str_replace('arn t','arn\'t',$s);
	return $s;
}

class tagSpider {
	var $crl; // this will hold our curl instance
	var $html; // this is where we dump the html we get
	var $binary; // set for binary type transfer
	var $url; // this is the url we are going to do a pass on

	function ngLogin($username,$password,$check=false) {
		$url = "https://www.newgrounds.com/passport/mode/iframe/appsession";
		$postdata = "username=".$username."&password=".$password."&referrer=http://www.newgrounds.com/&remember=yes";
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		//curl_setopt ($ch, CURLOPT_PROXY, 'PROXY_URL:PORT');
		//curl_setopt ($ch, CURLOPT_PROXYUSERPWD, "PROXY_PASS");
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6");
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_REFERER, $url);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $postdata);
		if ($check == false) curl_setopt ($ch, CURLOPT_POST, 1); //just checking before a real login
		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cooks');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cooks');
		$result = curl_exec ($ch);
		curl_close($ch);
		if (stristr($result, 'You have successfully signed in!'))
			return true; // login success!
		else {
			if ($check) // not logged in? Let's try logging in.
				$this->ngLogin($username,$password);
			else
				return false; // login failed!
		}
	}

	function fetchMobilePage ($url, $user = "", $pass = "", $e = true, $t = true, $m = true, $a = false) {
		if ($user != "" && $pass != "") $this->ngLogin($user, $pass, true);
		$e ? $rate_e = "&view_suitability_e=on" : $rate_e = "";
		$t ? $rate_t = "&view_suitability_t=on" : $rate_t = "";
		$m ? $rate_m = "&view_suitability_m=on" : $rate_m = "";
		$a ? $rate_a = "&view_suitability_a=on" : $rate_a = "";

		$this->url = $url;
		if (isset($this->url)) {
			$this->ch = curl_init ();
			curl_setopt ($this->ch, CURLOPT_POST, true);
			curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $rate_e.$rate_t.$rate_m.$rate_a);
			//curl_setopt ($this->ch, CURLOPT_PROXY, 'PROXY_URL:PORT');
			//curl_setopt ($this->ch, CURLOPT_PROXYUSERPWD, "PROXY_PASS");
			curl_setopt ($this->ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A537a Safari/419.3');
			curl_setopt ($this->ch, CURLOPT_URL, $this->url);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cooks');
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cooks');
			$this->html = curl_exec($this->ch);
			curl_close ($this->ch);
		}
	}

	function fetchPage ($url, $user = "", $pass = "", $e = true, $t = true, $m = true, $a = false) {
		if ($user != "" && $pass != "") $this->ngLogin($user, $pass, true);
		$e ? $rate_e = "&view_suitability_e=on" : $rate_e = "";
		$t ? $rate_t = "&view_suitability_t=on" : $rate_t = "";
		$m ? $rate_m = "&view_suitability_m=on" : $rate_m = "";
		$a ? $rate_a = "&view_suitability_a=on" : $rate_a = "";

		$this->url = $url;
		if (isset($this->url)) {
			$this->ch = curl_init ();
			curl_setopt ($this->ch, CURLOPT_POST, true);
			curl_setopt ($this->ch, CURLOPT_POSTFIELDS, $rate_e.$rate_t.$rate_m.$rate_a);
			//curl_setopt ($this->ch, CURLOPT_PROXY, 'PROXY_URL:PORT');
			//curl_setopt ($this->ch, CURLOPT_PROXYUSERPWD, "PROXY_PASS");
			curl_setopt ($this->ch, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt ($this->ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($this->ch, CURLOPT_URL, $this->url);
			curl_setopt($this->ch, CURLOPT_COOKIEJAR, 'cooks');
			curl_setopt($this->ch, CURLOPT_COOKIEFILE, 'cooks');
			$this->html = curl_exec($this->ch);
			curl_close ($this->ch);
		}
	}

	function parse_array($beg_tag, $close_tag) {
		preg_match_all("($beg_tag.*$close_tag)siU", $this->html, $matching_data);
		return $matching_data[0];
	}
}
