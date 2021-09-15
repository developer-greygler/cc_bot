<?
 $data = array(


    'bayer_name'    => $name,         // покупатель (Ф.И.О)
    'phone'         => $phone,        // телефон
    'email'         => $email,
    'server'        => $_SERVER,
    'price'			    => $price_new,
    'product_name'  => $product_name, // id товара
    'url'           => $url,
    'utm'           => $utm,
    'ref'       => $referer

);


$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://sv-bot.svdirect.eu/api/orders_ua",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($data),
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Content-Type: application/json",
    "cache-control: no-cache"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);
 
 ?>