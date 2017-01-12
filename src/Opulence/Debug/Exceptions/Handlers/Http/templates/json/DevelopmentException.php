<?php
$messages = [$ex->getMessage()];

while ($ex = $ex->getPrevious()) {
    $messages[] = $ex->getMessage();
}

echo json_encode([
    'error' => [
        'code' => $statusCode,
        'message' => implode("\n", $messages)
    ]
]);
