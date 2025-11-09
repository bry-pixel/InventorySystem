<?php
/**
 * chatbot.php
 * 
 * Smart Local Chatbot for Inventory System
 * No internet required — uses built-in Q&A rules.
 * Works for Admin, Cashier, and Viewer roles.
 */

header('Content-Type: application/json');

// 🧩 STEP 1: Define all possible questions and responses
$faq = [

    // Basic greetings
    "hello" => "Hi there! 👋 How can I assist you today?",
    "hi" => "Hello! Need help with inventory or reports?",
    "good morning" => "Good morning! Hope your workday goes smoothly 🌞",
    "good afternoon" => "Good afternoon! How can I help you?",
    "good evening" => "Good evening! Working late, huh? 😅 What do you need help with?",
    "how are you" => "I'm doing great, thanks for asking! How can I help you today?",

    // System navigation help
    "how to add product" => "To add a product: go to *Inventory → Add Product*, fill out the details, and click *Save* ✅.",
    "how to edit product" => "Go to *Inventory → Manage Products*, find the product, and click the *Edit* button ✏️.",
    "how to delete product" => "Only admins can delete products. Go to *Manage Products*, then click *Delete* beside the item 🗑️.",
    "how to view products" => "Head to *Inventory → Manage Products* to see the full product list 📦.",
    "how to view low stock" => "Go to *Reports → Low Stock* to see all items below their reorder level ⚠️.",

    // Reports & Sales
    "how to generate report" => "Go to *Reports → Sales Report*. Select your date range and click *Generate Report* 📊.",
    "how to export report" => "In the Sales Report page, click the *Export to Excel* button to download your data as an Excel file 📁.",
    "how to print report" => "Click the *Print Report* button to open a printer-friendly version 🖨️.",
    "how to view sales" => "Visit *Sales → Sales History* to view past transactions.",
    "sales report" => "You can generate sales reports under *Reports → Sales Report*. Choose a date range to see totals and profit.",

    // Stock control
    "how to adjust stock" => "Only Cashiers and Managers can adjust stock. Go to *Inventory → Stock Adjustment* and update the quantity 🔧.",
    "how to check stock" => "Go to *Inventory → Stock List* to view current item quantities and units 📦.",
    "low stock items" => "Check *Reports → Low Stock* to find which products need restocking 🚨.",

    // Cashier help
    "how to record sale" => "Cashiers can record a sale under *Sales → New Sale*. Select product, quantity, and complete the transaction 💰.",
    "how to process payment" => "During a sale, after adding items, click *Checkout* and confirm the payment method 🧾.",
    "refund" => "Refunds are handled by Admins. Please inform your manager if you need to reverse a transaction.",

    // Viewer help
    "what can viewer do" => "The Viewer role can only view inventory, stock levels, and reports — no editing or deleting 🔒.",
    "viewer dashboard" => "The Viewer dashboard shows quick summaries like total products, low stock count, and sales overview 📈.",
    "viewer restrictions" => "Viewers cannot add, edit, or delete items. They can only read data.",

    // Admin help
    "what can admin do" => "Admins manage all — users, products, suppliers, reports, and system settings ⚙️.",
    "how to add user" => "Admins can add users under *Users → Add New User*, setting their roles and permissions.",
    "how to change password" => "Go to *Profile → Change Password* to update your credentials 🔐.",
    "how to add supplier" => "Go to *Suppliers → Add Supplier* and fill in company details 🏢.",

    // System / general help
    "how to login" => "Go to the login page, enter your username and password, then click *Login* 🔑.",
    "how to logout" => "Click your profile icon on the top-right corner, then select *Logout* 🚪.",
    "system purpose" => "This system helps track inventory, monitor sales, generate reports, and manage stock efficiently ⚡.",
    "what is inventory system" => "An inventory system manages stock levels, sales, suppliers, and reports to keep your business organized.",

    // Small talk
    "thank you" => "You're welcome! 😊 Happy to help anytime.",
    "thanks" => "Anytime! 👍 Need anything else?",
    "bye" => "Goodbye! 👋 Have a productive day!",
    "good night" => "Good night! 🌙 Don’t forget to log out before you rest!",
    "joke" => "Why did the computer go to therapy? It had too many bugs! 🐛😂",

    // Default / unknown queries
    "default" => "I'm not sure how to answer that yet 🤔. Try asking about products, reports, or stock."
];

// 🧠 STEP 2: Get user input
$data = json_decode(file_get_contents("php://input"), true);
$userMessage = strtolower(trim($data['message'] ?? ''));

// 🧩 STEP 3: Match question to response
$response = "default";
foreach ($faq as $key => $answer) {
    if (strpos($userMessage, $key) !== false) {
        $response = $key;
        break;
    }
}

// 🧾 STEP 4: Send JSON response
echo json_encode([
    "reply" => $faq[$response] ?? $faq["default"]
]);
?>
