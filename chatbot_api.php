<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// ========== CONFIGURATION ==========
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "inventory_system"; // change if yours differs
$apiKey = "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"; // Replace this with your real OpenAI key

// Connect to DB
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["reply" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Read input
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = strtolower(trim($input['message'] ?? ''));

if (empty($userMessage)) {
    echo json_encode(["reply" => "Please type a message."]);
    exit;
}

// ========== SIMPLE KEYWORD DETECTION ==========
$reply = "";

if (strpos($userMessage, "low stock") !== false || strpos($userMessage, "below reorder") !== false) {
    $sql = "SELECT name, quantity, reorder_level FROM products WHERE quantity < reorder_level";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $reply = "⚠️ Products below reorder level:\n";
        while ($row = $result->fetch_assoc()) {
            $reply .= "- {$row['name']} (Qty: {$row['quantity']}, Reorder: {$row['reorder_level']})\n";
        }
    } else {
        $reply = "✅ All products are above their reorder levels.";
    }

} elseif (strpos($userMessage, "total sales today") !== false || strpos($userMessage, "sales today") !== false) {
    $sql = "SELECT SUM(price * qty) AS total_sales FROM sales WHERE DATE(date) = CURDATE()";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    $totalSales = $data['total_sales'] ?? 0;
    $reply = "💰 Total sales today: ₱" . number_format($totalSales, 2);

} elseif (strpos($userMessage, "total products") !== false || strpos($userMessage, "number of products") !== false) {
    $sql = "SELECT COUNT(*) AS total FROM products";
    $result = $conn->query($sql);
    $data = $result->fetch_assoc();
    $reply = "📦 There are currently {$data['total']} products in the inventory.";

} elseif (preg_match('/price of (.+)/', $userMessage, $matches)) {
    $product = trim($matches[1]);
    $stmt = $conn->prepare("SELECT name, sale_price FROM products WHERE name LIKE ?");
    $like = "%" . $product . "%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $reply = "💲 The price of {$row['name']} is ₱" . number_format($row['sale_price'], 2);
    } else {
        $reply = "I couldn’t find that product in the system.";
    }
} else {
    // If no direct match → use OpenAI GPT for natural response
    $ch = curl_init("https://api.openai.com/v1/chat/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]);

    $data = [
        "model" => "gpt-3.5-turbo",
        "messages" => [
            ["role" => "system", "content" => "You are a helpful AI assistant for an Inventory Management System. You can answer questions about products, stock, and sales."],
            ["role" => "user", "content" => $userMessage]
        ],
        "temperature" => 0.7
    ];

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $reply = $result['choices'][0]['message']['content'] ?? "Sorry, I couldn’t understand that.";
}

$conn->close();
echo json_encode(["reply" => $reply]);
?>
