<?php  

		   	$client_secret = "32deb748-b3d8-4df6-9ea5-67f44029706c";
		    	$client_id = "d84b8c47-4f7d-4de3-b55c-9ee27045020b";
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "https://sandbox.bca.co.id/api/oauth/token");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
		curl_setopt($ch, CURLOPT_POST, 1);

		$headers = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
		$headers[] = "Authorization: Basic " . base64_encode($client_id . ':' . $client_secret);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
		    echo 'Error:' . curl_error($ch);
		}
		curl_close ($ch);

		echo $result;




?>