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
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    $username = $data["username"];
    $password = $data["password"];

    $usersJSON = file_get_contents($filename);
    $users = json_decode($usersJSON, true);

    if($username == "" or $password == "") {
        $error = [
            "message" => "Username or password was not sent"
        ];
        sendJSON($error, 400);
    }

    $userfound = false;

    for($i = 0; $i < count($users); $i++) {
        if($users[$i]["username"] == $username) {
            if($users[$i]["password"] == $password) {
                $userfound = true;
                sendJSON($users[$i]);
            } else {
                $error = [
                    "message" => "Not Found"
                ];
                sendJSON($error, 404);
            }
        }
    }

    if(!$userfound) {
        $error = [
            "message" => "Not Found"
        ];
        sendJSON($error, 404);
    }

   // $OK = [];

   // foreach($users as $user) {
    //    if($user["username"] == $username) {
    //        if($user["password"] == $password) {
    //            $OK[] = $user;
    //            break;
    //        } else {
    //            $error = [
    //               "message" => "Not Found"
    //            ];
    //            sendJSON($error, 404);
    //        }
    //    }
    //}

   // if(!$OK) {
    //    $error = [
    //        "message" => "Not Found"
    //    ];
    //    sendJSON($error, 404);
    //} else {
    //    foreach($OK as $user) {
    //        sendJSON($user);
    //    }
    //}

} else if($method != "POST") {
    $error = [
        "message" => "Only POST works."
    ];
    sendJSON($error, 405);
} else {
    $error = [
        "message" => "I'm not a teapot"
    ];
    sendJSON($error, 418);
}
?>
