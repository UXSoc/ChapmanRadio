$auth = 'ftp://authed_sss_user_agfkljuwn%40rmhsdrama.com:frRlHmXkk595dPhZdY@rmhsdrama.com/';
$ch = curl_init();
$fp = fopen($tempFile, 'r');
curl_setopt($ch, CURLOPT_URL, $auth.$targetPath.'ftpd_'.$targetFile);
curl_setopt($ch, CURLOPT_UPLOAD, 1);
curl_setopt($ch, CURLOPT_INFILE, $fp);
curl_setopt($ch, CURLOPT_INFILESIZE, filesize($tempFile));
curl_exec ($ch);
$error_no = curl_errno($ch);
curl_close ($ch);
if ($error_no == 0) {
	echo 'Success!';
	}