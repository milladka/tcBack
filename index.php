<?php
include("class/RSA.php");
require("class/SecurityService.php");
require("class/ApiService.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = file_get_contents('php://input');
    $data = json_decode($data);
    $Model["MerchantId"] = $data->MerchantId;
    $Model["TerminalId"] = $data->TerminalId;
    $Model["Action"] = $data->Action;
    $Model["Amount"] = $data->Amount;
    $Model["InvoiceNumber"] = $data->InvoiceNumber;
    $Model["LocalDateTime"] = date("Y/m/d H:m:s");
    $Model["ReturnUrl"] = 'http://localhost/GCPay-PHP/phptest/ConfirmPayment.php';
    $Model["AdditionalData"] = $data->AdditionalData;
    $Model["ConsumerId"] = $data->ConsumerId;

    $securityService = new SecurityService();
    $securityService->set_model($Model);
    $data = $securityService->GetData($Model);

    $sha256Data = $securityService->ToSHA256($data);
    $SignData = $securityService->SignData(2048, ParameterClass::PrivateKey, $sha256Data);
    $Model["SignData"] = $SignData;

    $rsa = new Crypt_RSA();
    $apiService = new ApiService();
    $make_call = $apiService->callAPI('POST', ParameterClass::URL.'/api/v2/Request/PaymentRequest', json_encode($Model));
    $response = json_decode($make_call, true);

    if($response["resCode"] > 0)
    {
        $data = [
            'status' => false,
            'message' => $response["description"],
            'code' => $response["resCode"]
        ];
        echo json_encode($data);
    }
    else
    {
        $dataRes = $response["data"];
        $data = [
            'status' => true,
            'token' => $dataRes['token']
        ];
        echo json_encode($data);
    }
}else{
    echo 'TcPay';
}