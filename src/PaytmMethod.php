<?php

namespace Azuriom\Plugin\Paytm;

use Azuriom\Plugin\Shop\Cart\Cart;
use Azuriom\Plugin\Shop\Models\Payment;
use Azuriom\Plugin\Shop\Payment\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaytmMethod extends PaymentMethod
{
    /**
     * The payment method id name.
     *
     * @var string
     */
    protected $id = 'paytm';

    /**
     * The payment method display name.
     *
     * @var string
     */
    protected $name = 'Paytm UPI';

    /**
     * Start a new payment with this method and return the payment response to the user.
     */
    public function startPayment(Cart $cart, float $amount, string $currency)
    {
        // Create a new pending payment with the cart items
        $payment = $this->createPayment($cart, $amount, $currency);
        
        // Get the configuration values
        $merchantId = $this->gateway->data['merchant-id'];
        $merchantKey = $this->gateway->data['merchant-key'];
        $website = $this->gateway->data['website'] ?? 'DEFAULT';
        $industryType = $this->gateway->data['industry-type'] ?? 'Retail';
        $channelId = $this->gateway->data['channel-id'] ?? 'WEB';
        $callbackUrl = route('shop.payments.notification', $this->id);
        
        // Prepare order data
        $orderId = 'PTYORD_' . $payment->id . '_' . time();
        $customerId = 'CUST_' . $payment->user_id;
        
        // Prepare parameters
        $params = [
            'MID' => $merchantId,
            'ORDER_ID' => $orderId,
            'CUST_ID' => $customerId,
            'INDUSTRY_TYPE_ID' => $industryType,
            'CHANNEL_ID' => $channelId,
            'TXN_AMOUNT' => number_format($amount, 2, '.', ''),
            'WEBSITE' => $website,
            'CALLBACK_URL' => $callbackUrl,
            'EMAIL' => $payment->user->email,
            'PAYMENT_MODE_ONLY' => 'UPI',
        ];
        
        // Generate checksum
        $params['CHECKSUMHASH'] = $this->generateChecksum($params, $merchantKey);
        
        // Save the order ID for future reference
        $payment->update(['transaction_id' => $orderId]);
        
        return view('paytm::payment-form', [
            'params' => $params,
            'action' => 'https://securegw.paytm.in/theia/processTransaction', // Use 'https://securegw-stage.paytm.in/theia/processTransaction' for testing
        ]);
    }

    /**
     * Handle a payment notification request sent by the payment gateway and return a response.
     */
    public function notification(Request $request, ?string $paymentId)
    {
        $merchantKey = $this->gateway->data['merchant-key'];
        
        // Verify checksum to ensure the request is authentic
        $paytmParams = $request->all();
        $checksumHash = $paytmParams['CHECKSUMHASH'] ?? '';
        unset($paytmParams['CHECKSUMHASH']);
        
        $isValidChecksum = $this->verifyChecksum($paytmParams, $merchantKey, $checksumHash);
        
        if (!$isValidChecksum) {
            return response()->json(['status' => 'Invalid checksum'], 400);
        }
        
        // Find the payment by order ID
        $orderId = $paytmParams['ORDERID'] ?? null;
        $payment = Payment::where('transaction_id', $orderId)->first();
        
        if (!$payment) {
            return response()->json(['status' => 'Payment not found'], 404);
        }
        
        // Check payment status
        $txnStatus = $paytmParams['STATUS'] ?? null;
        
        if ($txnStatus === 'TXN_SUCCESS') {
            // Process successful payment
            return $this->processPayment($payment, $paytmParams['TXNID'] ?? null);
        }
        
        // Handle failed payment
        return $this->invalidPayment($payment, $paytmParams['TXNID'] ?? null, 'Payment failed: ' . ($paytmParams['RESPMSG'] ?? 'Unknown error'));
    }

    /**
     * Handle successful payment page.
     */
    public function success(Request $request)
    {
        return redirect()->route('shop.home')
            ->with('success', trans('shop::messages.status.success'));
    }

    /**
     * Get the view for the gateway config in the admin panel.
     */
    public function view()
    {
        return 'paytm::admin.config';
    }

    /**
     * Get the validation rules for the gateway config in the admin panel.
     */
    public function rules()
    {
        return [
            'merchant-id' => ['required', 'string'],
            'merchant-key' => ['required', 'string'],
            'website' => ['required', 'string'],
            'industry-type' => ['required', 'string'],
            'channel-id' => ['required', 'string'],
        ];
    }

    /**
     * Get the payment method image.
     */
    public function image()
    {
        return asset('plugins/paytm/img/paytm.svg');
    }

    /**
     * Generate checksum for Paytm transaction.
     */
    private function generateChecksum($params, $key)
    {
        ksort($params);
        $paramString = "";
        
        foreach ($params as $k => $v) {
            $paramString .= $k . '=' . $v . '|';
        }
        
        $paramString = substr($paramString, 0, -1);
        
        $salt = openssl_random_pseudo_bytes(4);
        $finalString = $paramString . '|' . bin2hex($salt);
        
        $hash = hash("sha256", $finalString);
        $hashString = $hash . bin2hex($salt);
        
        $checksum = base64_encode($hashString);
        
        return $checksum;
    }

    /**
     * Verify checksum from Paytm response.
     */
    private function verifyChecksum($params, $key, $checksumHash)
    {
        ksort($params);
        $paramString = "";
        
        foreach ($params as $k => $v) {
            $paramString .= $k . '=' . $v . '|';
        }
        
        $paramString = substr($paramString, 0, -1);
        
        $checksumVerification = $this->verifyChecksumSignature($paramString, $key, $checksumHash);
        
        return $checksumVerification;
    }

    /**
     * Verify checksum signature.
     */
    private function verifyChecksumSignature($paramString, $key, $checksumHash)
    {
        $paytmHash = base64_decode($checksumHash);
        $salt = substr($paytmHash, -8);
        
        $finalString = $paramString . '|' . $salt;
        
        $calculatedHash = hash("sha256", $finalString);
        $calculatedHashWithSalt = $calculatedHash . $salt;
        
        $encodedCalculatedHash = base64_encode($calculatedHashWithSalt);
        
        return ($encodedCalculatedHash === $checksumHash);
    }
}
