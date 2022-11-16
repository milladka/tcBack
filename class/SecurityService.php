<?php
require "ParameterClass.php";

class SecurityService {
    var $model;
    
    function set_model($new_model) {
        $this->model = $new_model;
    }

    function getTruncatedValue( $value, $precision = 2 )
    {
        //Casts provided value
        $value = ( string )$value;

        if ( strpos( $value, "." ) === false ) {
            $value = $value.".00";
        }

        //Gets pattern matches
        preg_match( "/(-+)?\d+(\.\d{1,".$precision."})?/" , $value, $matches );

        //Returns the full pattern match
        return $matches[0];            
    }

    function GetData() {
        $data = $this->model["MerchantId"]."#".
                $this->model["TerminalId"]."#".
                $this->model["Action"]."#".
                $this->getTruncatedValue($this->model["Amount"])."#".
                $this->model["InvoiceNumber"]."#".
                $this->model["LocalDateTime"]."#".
                $this->model["ReturnUrl"]."#".
                $this->model["AdditionalData"]."#".
                $this->model["ConsumerId"];

                return $data;
    }
    
    function ToSHA256($input) {
        return hash('sha256', $input);
    }

    function SignData($dwKeySize, $privateKey, $stringToBeSigned)
    {
        $rsa = new Crypt_RSA();
        
        $rsa->setHash("sha256");
        $rsa->setMGFHash("sha256");
        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
        $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
        
        $rsa->loadKey($privateKey, CRYPT_RSA_PRIVATE_FORMAT_XML); // private key
        $rsa->loadKey($rsa->getPrivateKey());
        
        $signature = $rsa->sign($stringToBeSigned);

        $outputText = base64_encode($signature);
        return $outputText;
    }

    // function Decrypt($privateKey, $encryptedData)
    // {
    //     $rsa = new Crypt_RSA();
    //     $rsa->loadKey($privateKey, CRYPT_RSA_PRIVATE_FORMAT_XML); // private key
    //     $rsa->loadKey($rsa->getPrivateKey());
        
    //     $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    //     $decrypt = $rsa->decrypt(base64_decode($encryptedData));
    //     return $decrypt;
    // }

    function redirect_post($url, array $data) {
        echo("<html><head></head><body><div style='text-align: center;vertical-align: middle;line-height: 90px;'>Please Wait Redirecting To ... </div>");
        echo("<form name=\"frm\" method=\"POST\" action=\"".$url."\" >");
        foreach ($data as $key => $value) {
            echo "<input name=\"".$key."\" type=\"hidden\" value=\"".$value."\">";
        }
        echo("</form>");
        echo("<script type=\"text/javascript\">document.frm.submit();</script>");
        echo("</body></html>");
    }
}

?>