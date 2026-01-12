<?php
/**
 * TAPPaymentPop - PHP SDK for Touch and Pay Inline Payment
 * 
 * This class provides a server-side PHP interface for the TAP Inline Payment system.
 * It uses the official hosted JavaScript SDK from unpkg CDN.
 * 
 * @version 2.0.0
 * @author Touch and Pay
 */

class TAPPaymentPop
{
    /**
     * @var string The CDN URL for the JavaScript SDK
     */
    private static string $cdnURL = 'https://unpkg.com/tap-payment-popupjs@2.0.5/dist/umd/index.js';

    /**
     * @var array Transaction parameters
     */
    private array $transactionParams = [];

    /**
     * @var bool Whether the SDK has been initialized
     */
    private bool $isInitialized = false;

    /**
     * @var string|null Callback function name for success
     */
    private ?string $callbackFunction = null;

    /**
     * @var string|null Callback function name for close
     */
    private ?string $onCloseFunction = null;

    /**
     * Constructor
     * 
     * @param array $config Optional configuration array
     */
    public function __construct(array $config = [])
    {
        if (isset($config['cdnURL'])) {
            self::$cdnURL = $config['cdnURL'];
        }
    }

    /**
     * Set the CDN URL for the JavaScript SDK
     * 
     * @param string $url The CDN URL
     */
    public static function setCdnURL(string $url): void
    {
        self::$cdnURL = $url;
    }

    /**
     * Get the current CDN URL
     * 
     * @return string The CDN URL
     */
    public static function getCdnURL(): string
    {
        return self::$cdnURL;
    }

    /**
     * Set up transaction parameters
     * 
     * @param array $transactionDetails Transaction details
     * @return $this
     * @throws InvalidArgumentException If validation fails
     */
    public function setup(array $transactionDetails): self
    {
        $this->transactionParams = [
            'apiKey' => $transactionDetails['apiKey'] ?? '',
            'transID' => $transactionDetails['transID'] ?? '',
            'amount' => $transactionDetails['amount'] ?? '',
            'email' => $transactionDetails['email'] ?? '',
            'env' => $transactionDetails['env'] ?? '',
            'phone' => $transactionDetails['phone'] ?? '',
            'superAgentFee' => $transactionDetails['superAgentFee'] ?? '',
            'savePaymentDetails' => $transactionDetails['savePaymentDetails'] ?? false,
            'customerReference' => $transactionDetails['customerReference'] ?? '',
            'customPayload' => array_merge(
                ['email' => $transactionDetails['email'] ?? ''],
                $transactionDetails['customPayload'] ?? []
            ),
        ];

        // Store callback function names (JavaScript function references)
        $this->callbackFunction = $transactionDetails['callback'] ?? null;
        $this->onCloseFunction = $transactionDetails['onClose'] ?? null;

        // Validate parameters
        $this->validateTransactionParams();

        $this->isInitialized = true;

        return $this;
    }

    /**
     * Validate transaction parameters
     * 
     * @throws InvalidArgumentException If validation fails
     */
    private function validateTransactionParams(): void
    {
        $params = $this->transactionParams;

        // Validate email format
        if (!empty($params['email']) && !filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Attribute email must be a valid email');
        }

        // Validate amount is numeric and positive
        if (!empty($params['amount'])) {
            if (!is_numeric($params['amount']) || floatval($params['amount']) <= 0) {
                throw new InvalidArgumentException('Attribute amount must be a valid positive number');
            }
        }

        // Validate superAgentFee if provided
        if (!empty($params['superAgentFee'])) {
            if (!is_numeric($params['superAgentFee']) || floatval($params['superAgentFee']) < 0) {
                throw new InvalidArgumentException('Attribute superAgentFee must be a valid number');
            }
        }

        // Required field validations
        if (empty($params['apiKey'])) {
            throw new InvalidArgumentException('Please provide your public key via the apiKey attribute');
        }

        if (empty($params['amount'])) {
            throw new InvalidArgumentException('Please provide transaction amount via the amount attribute');
        }

        if (empty($params['email'])) {
            throw new InvalidArgumentException('Please provide customer email via the email attribute');
        }

        if (empty($params['env'])) {
            throw new InvalidArgumentException('Please provide environment via the env attribute (production or sandbox)');
        }

        // Validate env value
        if (!in_array($params['env'], ['production', 'sandbox'], true)) {
            throw new InvalidArgumentException('Attribute env must be either "production" or "sandbox"');
        }

        // Conditional validations for savePaymentDetails
        if (!empty($params['savePaymentDetails']) && $params['savePaymentDetails'] === true) {
            if (empty($params['customerReference'])) {
                throw new InvalidArgumentException('Please provide customerReference when savePaymentDetails is true');
            }
            if (empty($params['phone'])) {
                throw new InvalidArgumentException('Please provide phone when savePaymentDetails is true');
            }
        }
    }

    /**
     * Get clean transaction parameters (removes empty values)
     * 
     * @return array Cleaned transaction parameters
     */
    private function getCleanParams(): array
    {
        $params = $this->transactionParams;
        
        // Remove empty values
        return array_filter($params, function ($value) {
            if (is_array($value)) {
                return !empty($value);
            }
            return $value !== null && $value !== '';
        });
    }

    /**
     * Generate a random ID for DOM elements
     * 
     * @param int $length Length of the ID
     * @return string Random ID
     */
    private function generateRandomId(int $length = 5): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $id = '';
        
        for ($i = 0; $i < $length; $i++) {
            $id .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $id;
    }

    /**
     * Render the SDK script tag only (no initialization)
     * 
     * @return string The script tag
     */
    public static function renderSDKOnly(): string
    {
        return '<script src="' . htmlspecialchars(self::$cdnURL) . '"></script>';
    }

    /**
     * Render the SDK script tag and initialization code
     * 
     * @return string The complete script tags
     */
    public function renderScript(): string
    {
        $cdnURL = htmlspecialchars(self::$cdnURL);
        $params = json_encode($this->getCleanParams(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $callbackFn = $this->callbackFunction ?: 'null';
        $onCloseFn = $this->onCloseFunction ?: 'null';

        return <<<HTML
<script src="{$cdnURL}"></script>
<script>
(function() {
    var tapTransactionParams = {$params};
    tapTransactionParams.callback = {$callbackFn};
    tapTransactionParams.onClose = {$onCloseFn};
    
    window.tapTransactionParams = tapTransactionParams;
    
    if (typeof TAPPaymentPop !== 'undefined') {
        TAPPaymentPop.setup(tapTransactionParams);
    }
})();
</script>
HTML;
    }

    /**
     * Render a complete payment button with all necessary scripts
     * 
     * @param string $buttonText Button text
     * @param array $buttonAttributes Button HTML attributes
     * @return string Complete HTML
     */
    public function render(string $buttonText = 'Pay Now', array $buttonAttributes = []): string
    {
        $buttonId = 'tap-pay-btn-' . $this->generateRandomId();
        $cdnURL = htmlspecialchars(self::$cdnURL);
        $params = json_encode($this->getCleanParams(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $callbackFn = $this->callbackFunction ?: 'function(response) { console.log("Payment successful", response); }';
        $onCloseFn = $this->onCloseFunction ?: 'function() { console.log("Payment closed"); }';
        
        $attrString = '';
        foreach ($buttonAttributes as $key => $value) {
            $attrString .= ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
        }

        return <<<HTML
<script src="{$cdnURL}"></script>
<button id="{$buttonId}" type="button"{$attrString}>{$buttonText}</button>
<script>
(function() {
    var tapButton = document.getElementById('{$buttonId}');
    var tapParams = {$params};
    tapParams.callback = {$callbackFn};
    tapParams.onClose = {$onCloseFn};
    
    tapButton.addEventListener('click', function() {
        if (typeof TAPPaymentPop !== 'undefined') {
            TAPPaymentPop.setup(tapParams).openIframe();
        } else {
            console.error('TAPPaymentPop SDK not loaded');
        }
    });
})();
</script>
HTML;
    }

    /**
     * Get transaction parameters as JSON for AJAX usage
     * 
     * @return string JSON encoded transaction parameters
     */
    public function getTransactionParamsJSON(): string
    {
        return json_encode($this->getCleanParams(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Static helper to quickly render a payment button
     * 
     * @param array $transactionDetails Transaction details
     * @param string $buttonText Button text
     * @param array $buttonAttributes Button HTML attributes
     * @return string Complete HTML with script and button
     */
    public static function quickPayment(array $transactionDetails, string $buttonText = 'Pay Now', array $buttonAttributes = []): string
    {
        $instance = new self();
        $instance->setup($transactionDetails);
        return $instance->render($buttonText, $buttonAttributes);
    }
}
