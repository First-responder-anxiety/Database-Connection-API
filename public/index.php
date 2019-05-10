<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
 
require '../vendor/autoload.php';
require '../includes/DbOperations.php';
$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails'=>true
    ]
]);

/**
 * endpoint: usersignup
 * Parameters: user_name, password, f_name, l_name
 * method: POST
 */
$app->post('/usersignup', function(Request $request, Response $response) {
    if (!hasEmptyParams(array('user_name', 'password', 'f_name', 'l_name'), $request, $response)) {
        $request_data = $request->getParsedBody();

        $user_name = $request_data['user_name'];
        $password = $request_data['password'];
        $f_name = $request_data['f_name'];
        $l_name = $request_data['l_name']; 

        $hash_password = password_hash($password, PASSWORD_DEFAULT);

        $db = new DbOperations; 
        $result = $db->createUser($user_name, $hash_password, $f_name, $l_name);
        
        if ($result == USER_CREATED){
            $message = array(); 
            $message['error'] = false; 
            $message['message'] = 'User created successfully';

            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(201);

        } else if ($result == USER_FAILURE) {
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'Some error occurred';

            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        } else if ($result == USER_EXISTS) {
            $message = array(); 
            $message['error'] = true; 
            $message['message'] = 'User Already Exists';

            $response->write(json_encode($message));
            return $response
                        ->withHeader('Content-type', 'application/json')
                        ->withStatus(422);    

        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});

/**
 * endpoint: userlogin
 * Parameters: user_name, password
 * method: POST
 */

/**
 * endpoint: insertparent
 * Parameters: user_id, first_name (optional), last_name (optional), occupation
 * method: POST
 */

/**
 * endpoint: insertshift
 * Parameters: parent_id, start_time, end_time, date
 * method: POST
 */

/**
 * endpoint: insertdata
 * Parameters: user_id, user_access_token, fittness_age, data_date
 * method: POST
 */

/**
 * endpoint: insertstressdata
 * Parameters: data_id, duration, average_stress_level, max_stress_level, 
 *  high_stress_duration, medium_stress_duration, low_stress_duration
 * method: POST
 */

/**
 * endpoint: insertsleepdata
 * Parameters: data_id, total_sleep_time, deep_sleep_time, 
 *  light_sleep_time, awake_time, 
 * method: POST
 */

/**
 * endpoint: insertheartratedata
 * Parameters: data_id, average_heart_rate, max_heart_rate, min_heart_rate
 *  resting_heart_rate
 * method: POST
 */

/**
 * endpoint: getuserdata
 * Parameters: user_id
 * method: POST
 */

/**
 * endpoint: usersleepcheck
 * Parameters: data_id (multiple)
 * method: POST
 */

/**
 * endpoint: userheartratecheck
 * Parameters: data_id (multiple)
 * method: POST
 */

/**
 * endpoint: userstresscheck
 * Parameters: data_id (multiple)
 * method: POST
 */

/**
 * endpoint: userlogin
 * Parameters: user_name, password
 * method: POST
 */
$app->post('/userlogin', function(Request $request, Response $response) {
    if (!hasEmptyParams(array('user_name', 'password'), $request, $response)) {
        $request_data = $request->getParsedBody(); 

        $user_name = $request_data['user_name'];
        $password = $request_data['password'];
        
        $db = new DbOperations; 
        $result = $db->userLogin($user_name, $password);
        if($result == USER_AUTHENTICATED){
            
            $user = $db->getUserByuser_name($user_name);

            $response_data = array();
            $response_data['error']=false; 
            $response_data['message'] = 'Login Successful';
            $response_data['user']=$user; 
            $response->write(json_encode($response_data));

            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);   

        } else if ($result == USER_NOT_FOUND) {
            $response_data = array();
            $response_data['error']=true; 
            $response_data['message'] = 'User not exist';
            $response->write(json_encode($response_data));

            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);    

        } else if ($result == USER_PASSWORD_DO_NOT_MATCH) {
            $response_data = array();
            $response_data['error']=true; 
            $response_data['message'] = 'Invalid credential';
            $response->write(json_encode($response_data));

            return $response
                ->withHeader('Content-type', 'application/json')
                ->withStatus(200);  
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);    
});

/**
 * endpoint: allusers
 * parameters: none
 * method: GET
 */
$app->get('/allusers', function(Request $request, Response $response){
    $db = new DbOperations; 
    $users = $db->getAllUsers();
    $response_data = array();
    $response_data['error'] = false; 
    $response_data['users'] = $users; 
    $response->write(json_encode($response_data));
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);  
});
 
/**
 * endpoint: updateuser/{id}
 * parameters: user_name, name, school
 * method: PUT
 */
// $app->put('/updateuser/{id}', function(Request $request, Response $response, array $args){
//     $id = $args['id'];
//     if (!hasEmptyParams(array('user_name','name','school'), $request, $response)) {
//         $request_data = $request->getParsedBody(); 
//         $user_name = $request_data['user_name'];
//         $f_name = $request_data['name'];
//         $l_name = $request_data['school']; 
     
//         $db = new DbOperations; 
//         $if($db->updateUser($user_name, $f_name, $l_name, $id)){
//             $response_data = array(); 
//             $response_data['error'] = false; 
//             $response_data['message'] = 'User Updated Successfully';
//             $user = $db->getUserByuser_name($user_name);
//             $response_data['user'] = $user; 
//             $response->write(json_encode($response_data));
//             return $response
//             ->withHeader('Content-type', 'application/json')
//             ->withStatus(200);  
        
//         } else {
//             $response_data = array(); 
//             $response_data['error'] = true; 
//             $response_data['message'] = 'Please try again later';
//             $user = $db->getUserByuser_name($user_name);
//             $response_data['user'] = $user; 
//             $response->write(json_encode($response_data));
//             return $response
//             ->withHeader('Content-type', 'application/json')
//             ->withStatus(200);  
              
//         }
//     }
    
//     return $response
//     ->withHeader('Content-type', 'application/json')
//     ->withStatus(200);  
// });

/**
 * endpoint: updatepassword
 * parameters: currentpassword, newpassword, user_name
 * method: PUT
 */
$app->put('/updatepassword', function(Request $request, Response $response) {
    if(!hasEmptyParams(array('currentpassword', 'newpassword', 'user_name'), $request, $response)) {
        
        $request_data = $request->getParsedBody(); 
        $currentpassword = $request_data['currentpassword'];
        $newpassword = $request_data['newpassword'];
        $user_name = $request_data['user_name']; 
        $db = new DbOperations; 
        $result = $db->updatePassword($currentpassword, $newpassword, $user_name);
        if($result == PASSWORD_CHANGED) {
            $response_data = array(); 
            $response_data['error'] = false;
            $response_data['message'] = 'Password Changed';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        } else if($result == PASSWORD_DO_NOT_MATCH) {
            $response_data = array(); 
            $response_data['error'] = true;
            $response_data['message'] = 'You have given wrong password';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        } else if($result == PASSWORD_NOT_CHANGED) {
            $response_data = array(); 
            $response_data['error'] = true;
            $response_data['message'] = 'Some error occurred';
            $response->write(json_encode($response_data));
            return $response->withHeader('Content-type', 'application/json')
                            ->withStatus(200);
        }
    }
    return $response
        ->withHeader('Content-type', 'application/json')
        ->withStatus(422);  
});
 
$app->delete('/deleteuser/{id}', function(Request $request, Response $response, array $args) {
    $id = $args['id'];
    $db = new DbOperations; 
    $response_data = array();
    if($db->deleteUser($id)){
        $response_data['error'] = false; 
        $response_data['message'] = 'User has been deleted';    
    }else{
        $response_data['error'] = true; 
        $response_data['message'] = 'Plase try again later';
    }
    $response->write(json_encode($response_data));
    return $response
    ->withHeader('Content-type', 'application/json')
    ->withStatus(200);
});
 
function hasEmptyParams($required_params, $request, $response) {
    $error = false; 
    $error_params = '';
    $request_params = $request->getParsedBody(); 
    foreach ($required_params as $param){
        if (!isset($request_params[$param]) || strlen($request_params[$param])<=0){
            $error = true; 
            $error_params .= $param . ', ';
        }
    }
    if ($error) {
        $error_detail = array();
        $error_detail['error'] = true; 
        $error_detail['message'] = 'Required parameters ' . substr($error_params, 0, -2) . ' are missing or empty';
        $response->write(json_encode($error_detail));
    }
    return $error; 
}
 
$app->run();