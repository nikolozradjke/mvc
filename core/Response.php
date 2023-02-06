<?php

namespace core;

class Response
{

    public function setStatusCode(int $code){
        http_response_code($code);
    }

    public function response($status,$status_message,$data = null)
    {
        header("HTTP/1.1 ".$status);

        $response['status']=$status;
        $response['status_message']=$status_message;
        $response['data']=$data;

        $json_response = json_encode($response);
        return $json_response;
    }

    public function redirect(string $url){
        header('Location: ' . $url);
    }

}