# TAP Payment Pop - PHP SDK

A lightweight PHP SDK for integrating Touch and Pay inline payment system into PHP applications.

## Features

- 🚀 Simple one-liner integration
- 📦 Uses official CDN-hosted JavaScript SDK
- ✅ Server-side parameter validation
- 🔒 Secure payment handling
- 🎨 Customizable button attributes

## Installation

### Option 1: Download Release (Recommended)

Download the latest release from the [Releases](https://github.com/TouchAndPay-Technologies/TAP-Payment-PHP/releases) page:

1. Download `TAPPaymentPop.php` from the latest release
2. Include it in your project:

```php
require_once 'path/to/TAPPaymentPop.php';
```

### Option 2: Clone Repository

```bash
git clone https://github.com/TouchAndPay-Technologies/TAP-Payment-PHP.git
```

### Option 3: Composer (Coming Soon)

```bash
composer require touchandpay/tap-inline-php
```

## Quick Start

### One-Liner Integration

```php
<?php
require_once 'TAPPaymentPop.php';

echo TAPPaymentPop::quickPayment([
    'apiKey' => 'your_public_api_key_here',
    'transID' => 'TXN_' . time(),
    'amount' => 5000,
    'email' => 'customer@example.com',
    'env' => 'production',
    'callback' => 'handlePaymentSuccess',
    'onClose' => 'handlePaymentClose',
], 'Pay ₦5,000', ['class' => 'btn btn-primary']);
?>

<script>
function handlePaymentSuccess(response) {
    console.log('Payment successful:', response);
    alert('Payment completed!');
}

function handlePaymentClose() {
    console.log('Payment popup closed');
}
</script>
```

### Manual Setup

```php
<?php
require_once 'TAPPaymentPop.php';

$payment = new TAPPaymentPop();
$payment->setup([
    'apiKey' => 'your_public_api_key_here',
    'transID' => 'ORDER_' . uniqid(),
    'amount' => 10000,
    'email' => 'buyer@example.com',
    'env' => 'production',
    'callback' => 'onSuccess',
]);

echo $payment->render('Complete Purchase', ['class' => 'pay-btn']);
?>
```

### SDK Only (For Dynamic JavaScript)

```php
<?= TAPPaymentPop::renderSDKOnly() ?>

<script>
document.getElementById('pay-btn').addEventListener('click', function() {
    TAPPaymentPop.setup({
        apiKey: 'your_public_api_key_here',
        transID: 'DYN_' + Date.now(),
        amount: 5000,
        email: 'user@example.com',
        env: 'production',
        callback: function(response) {
            alert('Payment successful!');
        }
    }).openIframe();
});
</script>
```

## Transaction Parameters

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `apiKey` | string | Yes | Your TAP public API key |
| `transID` | string | No | Unique transaction ID |
| `amount` | number | Yes | Amount to charge (in Naira) |
| `email` | string | Yes | Customer's email address |
| `phone` | string | No* | Customer's phone number |
| `env` | string | Yes | Environment: `production` or `sandbox` |
| `superAgentFee` | number | No | Additional agent fee |
| `savePaymentDetails` | boolean | No | Save customer payment details |
| `customerReference` | string | No* | Unique customer reference |
| `customPayload` | array | No | Additional data to pass |
| `callback` | string | No | JavaScript function name for success |
| `onClose` | string | No | JavaScript function name for close |

*Required when `savePaymentDetails` is `true`

## Methods Reference

### Static Methods

#### `TAPPaymentPop::quickPayment(array $params, string $buttonText, array $buttonAttrs)`

Quick one-liner to render a complete payment button with script.

```php
echo TAPPaymentPop::quickPayment([
    'apiKey' => 'pk_xxx',
    'transID' => 'TXN_001',
    'amount' => 1000,
    'email' => 'user@example.com',
    'env' => 'production',
], 'Pay Now', ['class' => 'btn', 'id' => 'pay-btn']);
```

#### `TAPPaymentPop::renderSDKOnly()`

Render only the SDK script tag without initialization.

```php
echo TAPPaymentPop::renderSDKOnly();
```

#### `TAPPaymentPop::setCdnURL(string $url)`

Set a custom CDN URL for the JavaScript SDK.

```php
TAPPaymentPop::setCdnURL('https://your-cdn.com/tap-payment.js');
```

### Instance Methods

#### `$payment->setup(array $params)`

Set up transaction parameters with validation.

```php
$payment = new TAPPaymentPop();
$payment->setup([
    'apiKey' => 'pk_xxx',
    'amount' => 1000,
    'email' => 'user@example.com',
    'env' => 'production',
]);
```

#### `$payment->render(string $buttonText, array $buttonAttrs)`

Render a complete payment button with all necessary scripts.

```php
echo $payment->render('Pay Now', ['class' => 'btn btn-primary']);
```

#### `$payment->renderScript()`

Render the SDK initialization script.

```php
echo $payment->renderScript();
```

#### `$payment->getTransactionParamsJSON()`

Get transaction parameters as JSON (useful for AJAX/API endpoints).

```php
header('Content-Type: application/json');
echo $payment->getTransactionParamsJSON();
```

## Usage Examples

### Basic Payment Page

```php
<?php
require_once 'TAPPaymentPop.php';

$payment = new TAPPaymentPop();
$payment->setup([
    'apiKey' => 'pk_live_xxxxx',
    'transID' => 'ORDER_' . uniqid(),
    'amount' => $_POST['total'],
    'email' => $_SESSION['user_email'],
    'env' => 'production',
    'customPayload' => [
        'orderId' => $_POST['order_id'],
    ],
    'callback' => 'onPaymentComplete',
]);
?>
<!DOCTYPE html>
<html>
<head><title>Checkout</title></head>
<body>
    <h1>Complete Your Order</h1>
    <p>Total: ₦<?= number_format($_POST['total'], 2) ?></p>
    
    <?= $payment->render('Pay Now', ['class' => 'pay-btn']) ?>
    
    <script>
    function onPaymentComplete(response) {
        window.location.href = '/order-success.php?ref=' + response.reference;
    }
    </script>
</body>
</html>
```

### AJAX/API Integration

```php
<?php
// api/create-payment.php
require_once '../TAPPaymentPop.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $payment = new TAPPaymentPop();
    $payment->setup([
        'apiKey' => 'pk_live_xxxxx',
        'transID' => 'API_' . uniqid(),
        'amount' => $data['amount'],
        'email' => $data['email'],
        'env' => 'production',
    ]);
    
    echo json_encode([
        'success' => true,
        'params' => json_decode($payment->getTransactionParamsJSON(), true),
    ]);
    
} catch (InvalidArgumentException $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
```

```javascript
// Frontend JavaScript
fetch('/api/create-payment.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ amount: 5000, email: 'user@example.com' })
})
.then(res => res.json())
.then(data => {
    if (data.success) {
        TAPPaymentPop.setup(data.params).openIframe();
    }
});
```

## Error Handling

The SDK throws `InvalidArgumentException` for validation errors:

```php
try {
    $payment = new TAPPaymentPop();
    $payment->setup($params);
} catch (InvalidArgumentException $e) {
    error_log('Payment setup failed: ' . $e->getMessage());
    echo 'Error: ' . $e->getMessage();
}
```

## Security Best Practices

1. **Never expose your secret key** - Only use your public API key in client-side code
2. **Validate on server-side** - Always verify payment status on your server
3. **Use HTTPS** - Ensure your site uses HTTPS for secure communication
4. **Sanitize inputs** - Always sanitize user inputs before using them

## Requirements

- PHP 7.4 or higher
- JSON extension (usually enabled by default)

## Releases

You can download source files directly from the [Releases](https://github.com/TouchAndPay-Technologies/TAP-Payment-PHP/releases) page:

- **TAPPaymentPop.php** - The main SDK file (only file needed for integration)
- **example.php** - Example implementation for reference

### How to Download

1. Go to the [Releases](https://github.com/TouchAndPay-Technologies/TAP-Payment-PHP/releases) page
2. Find the latest version (e.g., v2.0.0)
3. Under "Assets", download:
   - `TAPPaymentPop.php` - The SDK file you need
   - `Source code (zip)` - Full package with examples

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for version history and release notes.

## License

MIT License

## Support

For support, please contact info@touchandpay.me
