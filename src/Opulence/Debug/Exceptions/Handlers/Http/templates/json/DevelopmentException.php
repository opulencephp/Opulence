<?php
$messages = [htmlentities($ex->getMessage(), ENT_QUOTES, "UTF-8")];

while ($ex = $ex->getPrevious()) {
    $messages[] = htmlentities($ex->getMessage(), ENT_QUOTES, "UTF-8");
}

echo json_encode([
    "error" => [
        "code" => htmlentities($statusCode, ENT_QUOTES, "UTF-8"),
        "message" => implode("\n", $messages)
    ]
]);