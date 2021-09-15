<?
class Notif 
{
	public function push($chanel, $title, $body)
	{
		$message=Array
(
    'interests' => Array(
            '0' => $chanel,
        ),

    'web' => Array(
            'notification' => Array(
                    'title' => TITLE.":\n{$title}",
                    'body' => $body,
                ),

        ),

);
 

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://".INSTANCEID.".pushnotifications.pusher.com/publish_api/v1/instances/".INSTANCEID."/publishes",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode($message), //'{"interests":["hello"],"web":{"notification":{"title":"Hello","body":"Hello, world!"}}}'
  CURLOPT_HTTPHEADER => array(
    "Accept: application/json",
    "Content-Type: application/json",
    "cache-control: no-cache",
    "Authorization: Bearer ".AUTORIZATION
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
// echo $response;
curl_close($curl);
	}
}