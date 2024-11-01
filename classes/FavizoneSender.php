<?php
/**
 * Created by PhpStorm.
 * User: appsnsites-user
 * Date: 08/11/2017
 * Time: 15:52
 */

/**
 * Class FavizoneSender
 */
class FavizoneSender
{
    /**
     * Favizone post request
     * @param $favizone_host
     * @param $favizone_path
     * @param array $favizone_body
     * @return bool|null|string
     */
    public function favizone_post_request($favizone_host, $favizone_path, $favizone_body = array()){
        $favizone_body = wp_json_encode($favizone_body);
        if(function_exists("curl_exec")){
            $favizone_return = $this->favizone_post_by_curl($favizone_host.$favizone_path, $favizone_body);
        }
        if(isset($favizone_return) and $favizone_return){
            return $favizone_return;
        }
        $favizone_return = $this->favizone_post_by_file_get_contents($favizone_host, $favizone_path, $favizone_body);
        if(isset($favizone_return) and $favizone_return){
            return $favizone_return;
        }
        $favizone_return = $this->favizone_post_by_fsock($favizone_host, $favizone_path, $favizone_body);
        if(isset($favizone_return) and $favizone_return){
            return $favizone_return;
        }
        return null;
    }

    /**
     * Favizone post by curl
     * @param $favizone_url
     * @param $favizone_body
     * @return string
     */
    private function favizone_post_by_curl($favizone_url, $favizone_body)
    {
        try {
            $favizone_response = wp_remote_post( $favizone_url, array(
                    'method' => 'POST',
                    'timeout' => 10,
                    'redirection' => 10,
                    'httpversion' => '1.0',
                    'blocking' => true,
                    'headers' =>  array( 'Content-Type' => 'application/json' ),
                    'body' => $favizone_body,
                    'cookies' => array()
                )
            );
            if ( is_wp_error( $favizone_response ) ) {
                $favizone_error_message = $favizone_response->get_error_message();
                return "Something went wrong: $favizone_error_message";
            } else {
                return  wp_remote_retrieve_body( $favizone_response ) ;
            }
        } catch(Exception $e) {
            return sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage());
        }
    }

    /**
     * Favizone post by file get contents
     * @param $favizone_host
     * @param $favizone_path
     * @param $favizone_body
     * @return bool|string
     */
    private function favizone_post_by_file_get_contents($favizone_host, $favizone_path, $favizone_body)
    {
        $favizone_options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => $favizone_body,
                'timeout' => 6,
                'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n".
                    // 'Content-Length: ' . Tools::strlen($body)."\r\n".
                    'Origin : '.$_SERVER['SERVER_NAME']."\r\n"
            )
        ) ;
        $favizone_context = stream_context_create($favizone_options) ;
        $favizone_result = file_get_contents($favizone_host.$favizone_path, false, $favizone_context) ;
        if (!$favizone_result) {
            return false ;
        }
        return $favizone_result ;
    }

    /**
     * Favizone post by fsock
     * @param $favizone_url
     * @param $favizone_path
     * @param $favizone_body
     * @return bool|string
     */
    private function favizone_post_by_fsock($favizone_url, $favizone_path, $favizone_body)
    {
        $favizone_url1 = parse_url($favizone_url) ;
        $favizone_host = $favizone_url1['host'] ;
        $favizone_port = $favizone_url1['port'] ;
        $favizone_fp = fsockopen(gethostbyname($favizone_host), $favizone_port, $errno, $errstr, 1000) ;
        if ($favizone_fp) {
            socket_set_timeout($favizone_fp, 6) ;
            $out = "POST ".$favizone_path." HTTP/1.0\r\n" ;
            $out .= "Host: ".$favizone_host."\r\n" ;
            //$out .= 'Content-Length: '.Tools::strlen($body)."\r\n" ;
            $out .= 'Accept : application/json'."\r\n" ;
            $out .= 'Origin : '.$_SERVER['SERVER_NAME']."\r\n" ;
            $out .= 'Content-Type : application/json'."\r\n" ;
            $out .= "Connection: Close\r\n\r\n" ;
            @fwrite($favizone_fp, $out.$favizone_body) ;
            $tmp = '' ;
            while (!feof($favizone_fp)) {
                $tmp .= trim(fgets($favizone_fp, 1024)) ;
            }
            $favizone_result = $tmp ;
            fclose($favizone_fp) ;
        }
        return (isset($favizone_result) ? $favizone_result : false) ;
    }
}