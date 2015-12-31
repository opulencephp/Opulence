<?php
echo json_encode([
    "error" => [
        "code" => htmlentities($statusCode, ENT_QUOTES, "UTF-8"),
        "message" => "There was a technical error while handling your request"
    ]
]);