<?php
/**
 * Example usage of TAPPaymentPop PHP SDK
 * 
 * This file demonstrates how to use the SDK which uses the
 * official hosted JavaScript SDK from unpkg CDN.
 */

require_once __DIR__ . '/TAPPaymentPop.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TAP Payment CDN Example</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .payment-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 0; }
        .price { font-size: 2em; color: #2563eb; margin: 20px 0; }
        .pay-button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .pay-button:hover { background: #1d4ed8; }
        pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 14px;
        }
        .badge {
            display: inline-block;
            background: #10b981;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>TAP Payment PHP SDK</h1>
    <p>This SDK uses the official hosted JavaScript SDK from unpkg CDN.</p>

    <!-- Callback Functions (must be defined before buttons that reference them) -->
    <script>
    function handlePaymentSuccess(response) {
        console.log('Payment successful:', response);
        alert('Payment completed successfully!');
    }
    
    function handlePaymentClose() {
        console.log('Payment popup was closed');
    }
    </script>

    <!-- Example 1: Quick Payment -->
    <div class="payment-card">
        <h2>Example 1: Quick One-Liner</h2>
        <p>Premium Subscription</p>
        <div class="price">₦5,000.00</div>
        
        <?php
        echo TAPPaymentPop::quickPayment([
            'apiKey' => 'TkdLb2VUMk46ZXRoWEdJSEF0Z24xOnB1WVUzd3dvS1c4bw==',
            'transID' => 'TXN_TXN' . uniqid(),
            'amount' => 5000,
            'email' => 'customer@example.com',
            'phone' => '08012345678',
            'env' => 'sandbox',
            'callback' => 'handlePaymentSuccess',
            'onClose' => 'handlePaymentClose',
        ], 'Pay ₦5,000', ['class' => 'pay-button']);
        ?>
    </div>

    <!-- Example 2: Manual Setup -->
    <div class="payment-card">
        <h2>Example 2: Manual Setup</h2>
        <p>One-time Purchase</p>
        <div class="price">₦10,000.00</div>
        
        <?php
        $payment = new TAPPaymentPop();
        $payment->setup([
            'apiKey' => 'TkdLb2VUMk46ZXRoWEdJSEF0Z24xOnB1WVUzd3dvS1c4bw==',
            'transID' => 'ORDER_' . uniqid(),
            'amount' => 10000,
            'email' => 'buyer@example.com',
            'env' => 'sandbox',
            'customPayload' => [
                'orderId' => 'ORD-12345',
                'productName' => 'Premium Widget',
            ],
            'callback' => 'handlePaymentSuccess',
            'onClose' => 'handlePaymentClose',
        ]);
        
        echo $payment->render('Complete Purchase', ['class' => 'pay-button']);
        ?>
    </div>

    <!-- Example 3: SDK Only + Dynamic JS -->
    <div class="payment-card">
        <h2>Example 3: Dynamic JavaScript Setup</h2>
        <p>Enter amount:</p>
        <input type="number" id="custom-amount" value="1000" min="100" 
               style="padding: 10px; font-size: 16px; width: 150px; margin-bottom: 15px;">
        <br>
        <button id="dynamic-pay-btn" class="pay-button">Pay Custom Amount</button>
        
        <?= TAPPaymentPop::renderSDKOnly() ?>
        
        <script>
        document.getElementById('dynamic-pay-btn').addEventListener('click', function() {
            var amount = document.getElementById('custom-amount').value;
            
            TAPPaymentPop.setup({
                apiKey: 'TkdLb2VUMk46ZXRoWEdJSEF0Z24xOnB1WVUzd3dvS1c4bw==',
                env='sandbox',
                transID: 'DYN_' + Date.now(),
                amount: parseFloat(amount),
                email: 'dynamic@example.com',
                callback: function(response) {
                    console.log('Payment successful!', response);
                    alert('Payment of ₦' + amount + ' was successful!');
                },
                onClose: function() {
                    console.log('Payment popup closed');
                }
            }).openIframe();
        });
        </script>
    </div>

    <!-- Code Examples -->
    <div class="payment-card">
        <h2>Usage Examples</h2>
        
        <h3>1. Quick Payment (One-liner)</h3>
        <pre>&lt;?php
require_once 'TAPPaymentPopCDN.php';

echo TAPPaymentPopCDN::quickPayment([
    'apiKey' => 'api_key_here',
    'transID' => 'TXN_TXN' . uniqid(),
    'env' => 'sandbox',
    'amount' => 5000,
    'email' => 'customer@example.com',
    'callback' => 'handleSuccess',
], 'Pay Now', ['class' => 'btn']);
?&gt;</pre>

        <h3>2. Manual Setup</h3>
        <pre>&lt;?php
$payment = new TAPPaymentPopCDN();
$payment->setup([
    'apiKey' => 'api_key_here',
    'transID' => 'ORDER_' . uniqid(),
    'env' => 'sandbox',
    'amount' => 10000,
    'email' => 'buyer@example.com',
    'callback' => 'onSuccess',
]);

echo $payment->render('Pay Now', ['class' => 'btn']);
?&gt;</pre>

        <h3>3. SDK Only (for dynamic JS)</h3>
        <pre>&lt;?= TAPPaymentPopCDN::renderSDKOnly() ?&gt;

&lt;script&gt;
TAPPaymentPop.setup({
    apiKey: 'api_key_here',
    amount: 5000,
    env: 'sandbox',
    email: 'user@example.com',
    callback: function(res) { alert('Success!'); }
}).openIframe();
&lt;/script&gt;</pre>
    </div>

</body>
</html>
