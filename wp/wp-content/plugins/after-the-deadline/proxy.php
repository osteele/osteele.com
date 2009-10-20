<?php
/*
 *  This script redirects AtD AJAX requests to the AtD service
 */

/* this function directly from akismet.php by Matt Mullenweg.  *props* */
function AtD_http_post( $request, $host, $path, $port = 80 ) {
        $http_request  = "POST $path HTTP/1.0\r\n";
        $http_request .= "Host: $host\r\n";
        $http_request .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $http_request .= "Content-Length: " . strlen($request) . "\r\n";
        $http_request .= "User-Agent: AtD/0.1\r\n";
        $http_request .= "\r\n";
        $http_request .= $request;            

        $response = '';                 

        if( false != ( $fs = @fsockopen($host, $port, $errno, $errstr, 10) ) ) {                 
                fwrite( $fs, $http_request );

                while ( ! feof( $fs ) )
                        $response .= fgets( $fs );

                fclose( $fs );
                $response = explode( "\r\n\r\n", $response, 2 );
        }
        return $response;
}

/* 
 *  This function is called as an action handler to admin-ajax.php
 */
function AtD_redirect_call() {

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
                $postText = trim(  file_get_contents( 'php://input' )  );

        $url = $_GET['url'];
        $data = AtD_http_post( $postText, 'service.afterthedeadline.com', $url );

        header( 'Content-Type: text/xml' );
        echo $data[1];
        die();
}
