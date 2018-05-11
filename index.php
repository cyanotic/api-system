<?php 
require __DIR__ . '/vendor/autoload.php';
require_once 'include/DbConnect.php';

use \Slim\App;

$app = new App();

$app-> get('/', function(){
    echo "API System";
});


// CORS
$app->options('/{routes:.+}', function ($request, $response, $args) {
    return $response;
});


$app->add(function ($req, $res, $next) {
    $response = $next($req, $res);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
});


/* *
 * URL: https://app.paud-dikmas.kemdikbud.go.id/intern/api-paud/login
 * Parameters: email, password
 * Method: POST
 * */
$app->post('/login', function($request, $response, $args) use($app, $db){
    $data = $request->getParams();
    
    $table = "login";
    $login = $db->$table()
        ->where("username", $data['username'])
        ->where("password", md5($data['password']));
    
    if ($login->fetch()){
        foreach($login as $data){    
            $responseJson["error"]                  = false;
            $responseJson["message"]                = "Login successful";
            $responseJson['data']['username']       = $data['username'];
        }
    } else {
        $responseJson['error']   = true;
        $responseJson['message'] = "Invalid email or password";
    }
    
    return $response->withJson($responseJson);
});

$app->get('/temperature', function($request, $response, $args) use($app, $db){
  
    $table = "incubator";
    $temp = $db->$table();
    
    if ($temp->fetch()){
        foreach($temp as $data){    
            $responseJson["error"]                  = false;
            $responseJson["message"]                = "Temperature Sent";
            $responseJson['temperature']            = $data['temperature'];
            $responseJson['id']                     = $data['id'];
        }
    } else {
        $responseJson['error']   = true;
        $responseJson['message'] = "Cannot connect";
    }
    
    return $response->withJson($responseJson);
});


//run App
$app->run();
