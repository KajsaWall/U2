<?php
ini_set('display_errors', 1);

function sendJSON($data, $statusCode = 200) {
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $json = json_encode($data);
    echo $json;
    exit();
}

$filename = "users.json";
$method = $_SERVER["REQUEST_METHOD"];

if($method == "POST") {

    if(!file_exists($filename)) {
        file_put_contents($filename, "{}");
    }

    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    $username = $data["username"];
    $password = $data["password"];
    
    $usersJSON = file_get_contents($filename);
    $users = json_decode($usersJSON, true);
    
    if(!$username == "" or !$password == "") {
        foreach($users as $USER) {
        if($USER["username"] == $username) {
            $error = [
                "message" => "Conflict (the username is already taken)"
            ];
            sendJSON($error, 409);        
        } 
    } 

    $newUser = [
        "username" => $username,
        "password" => $password,
        "points" => 0
    ];
    
    $users[] = $newUser;

    $users_JSON = json_encode($users, JSON_PRETTY_PRINT);
    file_put_contents($filename, $users_JSON);

    sendJSON($newUser);
    } else {
        $error = [
            "message" => "Bad Request (empty values)"
        ];
        sendJSON($error, 400);
    }

} else if($method != "POST") {
    $error = [
        "message" => "Only POST works.",
    ];
    sendJSON($error, 405);
} else {
    $error = [
        "message" => "I'm not a teapot"
    ];
    sendJSON($error, 418);
}
?>
