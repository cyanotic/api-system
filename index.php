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

$app->get('/temp', function($request, $response, $args) use($app, $db){
  
    $table = "temp";
    $temp = $db->$table();
    
    if ($temp->fetch()){
        foreach($temp as $data){    
            $responseJson["error"]                  = false;
            $responseJson["message"]                = "Temperature Sent";
            $responseJson['id']                     = $data['id'];
            $responseJson['temperature']            = $data['temperature'];
        }
    } else {
        $responseJson['error']   = true;
        $responseJson['message'] = "Cannot connect";
    }
    
    return $response->withJson($responseJson);
});

$app->get('/info_lamp', function($request, $response, $args) use($app, $db){
    $table = "lamp";
    $info_lamp = $db->$table();

    if ($info_lamp->fetch()){
        $responseJson["error"]                  = false;
        $responseJson["message"]                = "Info Lamp Sent";
        foreach($info_lamp as $data){
            $responseJson['data'][]               = array(
                "id" => $data['id'],
                "power" => $data['power'],
                "percentage" => $data['percentage']
            );
        }
    } else {
        $responseJson['error']   = true;
        $responseJson['message'] = "Cannot connect";
    }

    return $response->withJson($responseJson);
});


//Parameter : power, percentage
$app->post('/lamp', function($request, $response, $args) use($app, $db){

    $table = "lamp";
    $post = $request->getParams();
    for($i=1; $i<4; $i++) {
        $lamp = $db->$table()
            ->where("id",$i);
        if($lamp->fetch()){
            if($post['lamp_'.$i]!='OFF'){
                $update['power']=1;
                $update['percentage'] = $post['lamp_'.$i];
            } else {
                $update['power']=0;
                $update['percentage'] = 0;
            }
            $result = $lamp->update($update);
            if($result){
                $data[$i] = "Berhasil";
            } else {
                foreach ($lamp as $value) {
                    if($value['percentage']==$update['percentage'])
                        $data[$i] = "Berhasil";
                    else{
                        $responseJson['error']   = true;
                        $responseJson['message'] = "Data cannot be update";
                        return $response->withJson($responseJson);
                    }
                }
            }
        }
    }

    $responseJson['error'] = false;
    $responseJson['message'] = "Data has been update";
    $responseJson['data'] = $data;

    return $response->withJson($responseJson);
});

//Parameter : status
$app->post('/fan', function($request, $response, $args) use($app, $db){

    $table = "fan";
    $post = $request->getParams();
    $fan = $db->$table()->where('id',1);
    $update['status']=$post['status'];
    $result = $fan->update($update);

    $responseJson['error']=false;
    if($result)
    $responseJson['message']="Data has been update";
    $responseJson['data']=$update;

    return $response->withJson($responseJson);
});

//run App
$app->run();
