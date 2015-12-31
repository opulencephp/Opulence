<?php
echo json_encode([
    "error" => [
        "code" => htmlentities($statusCode, ENT_QUOTES, "UTF-8"),
        "message" => "Please check back later"
    ]
]);