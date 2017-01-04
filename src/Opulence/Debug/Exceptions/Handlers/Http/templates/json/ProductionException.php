<?php
echo json_encode([
    "error" => [
        "code" => $statusCode,
        "message" => "There was a technical error while handling your request"
    ]
]);
