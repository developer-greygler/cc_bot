<? class np {


public function send($data)
{

	echo("<pre>");print_r(json_encode($data));echo("</pre>");

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.novaposhta.ua/v2.0/json/",
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
if ((isset($err)) AND ($err!="")) echo("Ошибка: {$err}<br>\n");
curl_close($curl);
return $response;
}





public function tracing($trackarray) // Трекинг по ТТН
{
	$data=array(
        "apiKey" => NP_API, 
		"modelName" => 'TrackingDocument',
		"calledMethod" => 'getStatusDocuments',
		"methodProperties" => array('Documents'=>$trackarray),
);
	return $this->send($data);
}


public function list_ttn($trackarray) // Список ТТН в кабинете
{
    $data=array(
        "apiKey" => NP_API, 
		"modelName" => 'InternetDocument',
		"calledMethod" => 'getDocumentList',
		"methodProperties" => $trackarray,
);
	return $this->send($data);
}


public function np_cityes() // Список Городов НП
{
    $data=array(
        "apiKey" => NP_API, 
		"modelName" => 'Address',
		"calledMethod" => 'getCities',
	//	"methodProperties" => array('Documents'=>$trackarray),
);
	return $this->send($data);
}


public function list_np($trackarray) // Список отделений НП
{
    $data=array(
        "apiKey" => NP_API, 
		"modelName" => 'Address',
		"calledMethod" => 'getWarehouses',
		"methodProperties" => array('Documents'=>$trackarray),
);
	return $this->send($data);
}


public function np_status($StatusCode) {
switch ($StatusCode) {
    case '11': // Відправлення отримано. Грошовий переказ видано одержувачу.
         $status=10; 
        
        break;

    case '9': // Відправлення отримано
        $status=10; 
      
        break;

     case '10': 		// Відправлення отримано %DateReceived%. Протягом доби ви одержите SMS-повідомлення 
     				// про надходження грошового переказу та зможете отримати його в касі відділення «Нова пошта».
     
        $status=10; 
        break;

    case '102': // Відмова від отримання
         $status=11;
        break; 

    case '103': // Відмова від отримання
         $status=11;
        break;

    case '108': // Відмова від отримання
         $status=11;
        break;

    case '7': //Прибув у відділення
        $status=8;
        break;

    case '8': //Прибув у відділення
        $status=8;
        break;

    case '4': //Відправлення у місті Львів
       $status=8;
        break;

     case '5': // прямує до міста.....
       $status=8;
        break;


        
    default:
      $status=1;
}

 return $status;
}

}

/*

{
    "methodProperties": {
        "Documents": [
            {
                "DocumentNumber": "20400048799000",
                "Phone":""
            },
            {
                "DocumentNumber": "20400048799001",
                "Phone":""
            }
        ]
    }
    
} */
