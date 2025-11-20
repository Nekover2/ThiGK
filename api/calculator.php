<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header('Content-Type: application/json');


// if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
//     http_response_code(200);
//     exit();
// }


// if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
//     echo json_encode(['error' => 'Method Not Allowed']);
//     exit();
// }

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);


$action = isset($input['action']) ? $input['action'] : null;
$number1 = isset($input['number1']) ? $input['number1'] : null;
$number2 = isset($input['number2']) ? $input['number2'] : null;


if (!is_numeric($number1) || !is_numeric($number2)) {
    echo json_encode(['error' => 'Input numbers are invalid']);
    exit();
}

if (!in_array($action, [1, 2, 3, 4])) {
    echo json_encode(['error' => 'Invalid action']);
    exit();
}

$result = 0;


switch ($action) {
    case 1: 
        $result = $number1 + $number2;
        break;
    case 2: 
        $result = $number1 - $number2;
        break;
    case 3:
        $result = $number1 * $number2;
        break;
    case 4: 
        if ((float)$number2 == 0) {
            echo json_encode(['error' => 'Cannot divide by zero']);
            exit();
        }
        
        $tempResult = $number1 / $number2;
        
        $result = floor($tempResult * 100) / 100;
        break;
}

echo json_encode([
    "result" => $result
]);
?>