<?php
ini_set('display_errors', 1);

function sendJSON($data, $statusCode = 200) {
    header("Content-Type: application/json");
    http_response_code($statusCode);
    $json = json_encode($data);
    echo $json;
    exit();
}

$filename = "dogs.json";
$method = $_SERVER["REQUEST_METHOD"];

$dogArray = [
    "Afghan Hound", "Australian Shepherd", "Beagle", "Bearded Collie", "Belgian Malinois", 
    "Bichon Frise", "Bloodhound", "Border Collie", "Boston Terrier", "Boxer",
    "Bulldog", "Cane Corso", "Chihuahua", "Collie", "French Bulldog", "German Shepherd", 
    "Giant Schnauzer", "Golden Retriver", "Greyhound", "Jack Russell Terrier", "Labrador Retriever", "Leonberger", "Miniature Schnauzer", 
    "Pembroke Welsh Corgi", "Pomeranian", "Portuguese Water", "Pug", "Rhodesian Ridgeback", "Rottweiler", "Saluki", "Shetland Sheepdog", 
    "Siberian Husky", "Staffordshire Bull Terrier", "Tibetan Mastiff", 
    "Yorkshire Terrier"
];

if ($method == "GET") {
    if (!file_exists($filename)) {
        file_put_contents($filename, "{}");
    }

    $dogs_json = file_get_contents("dogs.json");
    $dogs = json_decode($dogs_json, true);

    foreach($dogArray as $dog) {
        $image = str_replace(" ", "_", $dog);
        $image_ending = ".jpg";
        $image_start = "images/";
        $image = $image_start . $image . $image_ending;

        $dogs[$dog] = [
            "name" => $dog, 
            "source" => $image
        ];

        $dogs_json = json_encode($dogs, JSON_PRETTY_PRINT);
        file_put_contents("dogs.json", $dogs_json);
    }

    $shuffledDogs = $dogs;
    shuffle($shuffledDogs);
    
    $randomDogs = array_rand($shuffledDogs, 4);
    $random4dogs = [];

    foreach ($randomDogs as $randomDog) {
        $random4dogs[] = $shuffledDogs[$randomDog];
    }

    $correctDogIndex = array_rand($random4dogs);
    $correctDog = $random4dogs[$correctDogIndex];

    $response = [ 
        "image" => $correctDog["source"],
        "alternatives" => []
    ];

    foreach($random4dogs as $randomDog) {
        $alternative = [
            "correct" => ($randomDog == $correctDog),
            "name" => $randomDog["name"]
        ];
        $image = [
            "image" => $randomDog["source"]
        ];
        $response["alternatives"][] = $alternative;
    }
    sendJSON($response);
    
} else if($method != "GET") {
    $error = [
        "message" => "Only GET works.",
    ];
    sendJSON($error, 405);
} else {
    $error = [
        "message" => "I'm not a teapot"
    ];
    sendJSON($error, 418);
}
?>
