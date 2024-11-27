<?php
require_once 'db_config.php';
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$input = json_decode(file_get_contents("php://input"), true);
$value = isset($input['value']) ? $input['value'] : null;

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
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao inserir no banco de dados: ' . $e->getMessage()]);

    exit;
}

try {
    $stmt = $pdo->query("
        SELECT
            value
        FROM
            heap
    ");

    $heap = $stmt->fetchAll(PDO::FETCH_COLUMN);

    function heapify(&$heap, $n, $i) {
        $smallest = $i;
        $left = 2 * $i + 1;
        $right = 2 * $i + 2;

        if ($left < $n && $heap[$left] < $heap[$smallest]) {
            $smallest = $left;
        }

        if ($right < $n && $heap[$right] < $heap[$smallest]) {
            $smallest = $right;
        }

        if ($smallest != $i) {
            [$heap[$i], $heap[$smallest]] = [$heap[$smallest], $heap[$i]];
            heapify($heap, $n, $smallest);
        }
    }

    function buildHeap(&$heap) {
        $n = count($heap);
        for ($i = intdiv($n, 2) - 1; $i >= 0; $i--) {
            heapify($heap, $n, $i);
        }
    }

    buildHeap($heap);

    echo json_encode(['heap' => array_slice($heap, 0, 20)]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao consultar o banco de dados: ' . $e->getMessage()]);
}
?>