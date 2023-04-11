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

$usersJSON = file_get_contents($filename);
$users = json_decode($usersJSON, true);

if ($method == "POST") {
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    $username = $data["username"];
    $password = $data["password"];
    $newPoint = $data["points"];

    $userfound = false;

    for($i = 0; $i < count($users); $i++) {
        if ($users[$i]["username"] == $username) {
            $currentPoints = $users[$i]["points"];
            $totalPoints = $currentPoints + $newPoint;
    
            $users[$i]["points"] = $totalPoints;
    
            $userfound = true;
            break;
        }
    }
    
    if($userfound) {
        $updatedJSON = json_encode($users, JSON_PRETTY_PRINT);
        file_put_contents($filename, $updatedJSON);
    
        $response = [
            "points" => $totalPoints
        ];
    
        sendJSON($response);
    }

} else if($method == "GET") {

    $highscoreArray = [];
    foreach ($users as $user) {
        $person = [
            "username" => $user["username"],
            "points" => $user["points"]
        ];
        $highscoreArray[] = $person;
    };

    function Highscore($a, $b) {
        return $b["points"] - $a["points"];
    }

    usort($highscoreArray, "Highscore");

    $response = [];

    if(count($users) < 5) {
        foreach($highscoreArray as $person) {
            $response[] = $person;
        }
        sendJSON($response);
    } else {
        for($i = 0; $i < 5; $i++) {
            $response[] = $highscoreArray[$i];
        };
        sendJSON($response);
    }

} else if($method != "POST" or $method != "GET") {
    $error = [
        "message" => "Only POST and GET works.",
    ];
    sendJSON($error, 405);
} else {
    $error = [
        "message" => "I'm not a teapot"
    ];
    sendJSON($error, 418);
}
?>