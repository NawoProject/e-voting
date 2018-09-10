<?php
/*class to handle error reporting

*/
function error_response($code, $message)
{
    // clear the old headers
    header_remove();
    // set the actual code
    http_response_code($code);
    // set the header to make sure cache is forced
    header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
    // treat this as json
    header('Content-Type: application/json');
    $status = array(
        200 => '200 OK',
        201 => 'Record created',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        403 => '403 Permission Denied',
        422 => 'Unprocessable Entity',
        500 => '500 Internal Server Error'
        );
    // ok, validation error, or failure
    header('Status: '.$status[$code]);
    // return the encoded json
    return json_encode(array(
        'status' => $code, // success or not?
        'message' => $message
        ));
}
 ?>
