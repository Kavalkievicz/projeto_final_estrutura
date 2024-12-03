<?php
require_once 'db_config.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$value = isset($input['value']) ? $input['value'] : null;
$heap = isset($input['heap']) ? $input['heap'] : [];

if ($value === null) {
    echo json_encode(['error' => 'Valor inválido ou ausente']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO heap
        (
            value
        )
        VALUES
        (
            :value
        )
    ");

    $stmt->execute(['value' => $value]);
    $heap[] = $value;

    function heapifyUp(&$heap, $index) {
        $parentIndex = intdiv($index - 1, 2);
        if ($index > 0 && $heap[$index] < $heap[$parentIndex]) {
            [$heap[$index], $heap[$parentIndex]] = [$heap[$parentIndex], $heap[$index]];

            heapifyUp($heap, $parentIndex);
        }
    }

    function buildHeap(&$heap) {
        $n = count($heap);
        for ($i = intdiv($n, 2) - 1; $i >= 0; $i--) {
            heapifyDown($heap, $n, $i);
        }
    }

    function heapifyDown(&$heap, $n, $index) {
        $smallest = $index;
        $left = 2 * $index + 1;
        $right = 2 * $index + 2;

        if ($left < $n && $heap[$left] < $heap[$smallest]) {
            $smallest = $left;
        }

        if ($right < $n && $heap[$right] < $heap[$smallest]) {
            $smallest = $right;
        }

        if ($smallest != $index) {
            [$heap[$index], $heap[$smallest]] = [$heap[$smallest], $heap[$index]];
            heapifyDown($heap, $n, $smallest);
        }
    }

    heapifyUp($heap, count($heap) - 1);

    echo json_encode(['heap' => $heap]);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao inserir no banco de dados: ' . $e->getMessage()]);
    exit;
}
?>