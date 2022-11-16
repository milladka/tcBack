<?php
class ApiService {

    function callAPI($method, $url, $data = false, $headers = false){
        $curl = curl_init();
    
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    
        switch ($method){
           case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
           case "PUT":
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
              break;
           default:
              if ($data)
                 $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        // OPTIONS:
       curl_setopt($curl, CURLOPT_URL, $url);
       if(!$headers){
           curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
           ));
       }else{
           curl_setopt($curl, CURLOPT_HTTPHEADER, array(
              'Content-Type: application/json',
              $headers
           ));
       }
       curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
     
        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result)
        {
            print_r("Connection Failure<br>");
        }
        curl_close($curl);
        return $result;
     }
}
?>