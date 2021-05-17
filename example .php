

<?php

$KEY = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$SECRET = "XXXXXXXXXXXXXXXXXXXXXXXXX";



 $BASE_URL = 'https://api.binance.com/'; 

function signature($query_string, $secret) {
    return hash_hmac('sha256', $query_string, $secret);
}

function sendRequest($method, $path) {
  global $KEY;
  global $BASE_URL;
  
  $url = "${BASE_URL}${path}";

  //echo "requested URL: ". PHP_EOL;
  //echo $url. PHP_EOL;
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-MBX-APIKEY:'.$KEY));    
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, $method == "POST" ? true : false);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $execResult = curl_exec($ch);
  $response = curl_getinfo($ch);
    
  // echo print_r($response);

  curl_close ($ch);
  return json_decode($execResult, true);
}

function signedRequest($method, $path, $parameters = []) {
  global $SECRET;

  $parameters['timestamp'] = round(microtime(true) * 1000);
  $query = buildQuery($parameters);
  $signature = signature($query, $SECRET);
  return sendRequest($method, "${path}?${query}&signature=${signature}");
}

function buildQuery(array $params)
{
    $query_array = array();
    foreach ($params as $key => $value) {
        if (is_array($value)) {
            $query_array = array_merge($query_array, array_map(function ($v) use ($key) {
                return urlencode($key) . '=' . urlencode($v);
            }, $value));
        } else {
            $query_array[] = urlencode($key) . '=' . urlencode($value);
        }
    }
    return implode('&', $query_array);
}




  function withdraw_bnb(string $asset, string $address, $amount, $addressTag = null, $addressName = "API Withdraw", bool $transactionFeeFlag = false,$network = null)
    {
$response = signedRequest('POST', 'wapi/v3/withdraw.html', [
            "asset" => $asset,
            "address" => $address,
            "amount" => $amount,
            "transactionFeeFlag" => $transactionFeeFlag,
]);
return $response;
    }
    
    
      function withdrawFee_bnb(string $asset)
    {
$response = signedRequest('GET', 'wapi/v3/assetDetail.html', []);
       if (isset($response['success'], $response['assetDetail'], $response['assetDetail'][$asset]) && $response['success']) {
            return $response['assetDetail'][$asset]["withdrawFee"];
        }
    }


?>
