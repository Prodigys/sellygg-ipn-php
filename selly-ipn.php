<?php

// file_put_contents('ipn_info.txt', var_export($content, true), FILE_APPEND);
// Debug any vars with var_export

$secret_key = ''; // Insert your secret key found in account settings

abstract class sellyOrderStatus {

    const NO_PAYMENT        = 0;
    const ORDER_BLOCKED     = 52;
    const PARTIAL_PAYMENT   = 53;
    const REFUNDED          = 56;
    const SUCCEDED          = 100;

    // Paypal Constants
    const PAYPAL_DISPUTE    = 51;
    const PAYPAL_PENDING    = 55;

    // Crypto Constants
    const CRYPTO_CONFIRMING = 54;

}

/*
 * $content['id']                   - string (fd87d909-fbfc-466c-964a-5478d5bc066a)
 * $content['product_id]            - string (upgrade)
 * $content['email']                - string (alishia@yahoo.com)
 * $content['ip_address']           - string (88.96.129.5)
 * $content['country_code']         - string (US)
 * $content['user_agent']           - string (Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/43.0.2357.124 Safari/537.36)
 * $content['value']                - float  (0.03)
 * $content['currency']             - string (USD)
 * $content['gateway']              - string (Bitcoin)
 * $content['risk_level']           - int    (10)
 * $content['status']               - int    (100)
 * $content['delivered']            - string (SERIAL-12345-12345)
 * $content['crypto_address']       - string (1MpSbqnvKu7ckmRbhQ7Mb7vfWBFNvkfS9s)
 * $content['crypto_value']         - int    (1020304)
 * $content['crypto_received']      - int    (0)
 * $content['crypto_confirmations'] - int    (0)
 * $content['referral']             - string (null)
 * $content['usd_value']            - float  (0.03)
 * $content['exchange_rate']        - float  (1.0)
 * $content['custom']               - array  (0,1,2,3,4,5)
 * ----------------------------
 * Example of a custom field:
 * ----------------------------
 * $content['custom'][0]            - string (76561198024064726)
 * ----------------------------
 * $content['created_at']           - datetime (2016-11-27T14:20:34.000Z)
 * $content['updated_at']           - datetime (2016-12-05T21:31:15.000Z)
 */

//Make sure that it is a POST request.
if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0) // If the method being requested doesn't include POST
    die(http_response_code(500));

$contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

if(strcasecmp($contentType, 'application/json') != 0)
    die(http_response_code(500));

$content  = json_decode(trim(file_get_contents("php://input")), true); // Get Raw POST Data and Decode it from JSON

$signature = hash_hmac('sha512', trim(file_get_contents("php://input")), trim($secret_key)); // Try to decode signature

if(hash_equals($signature, $_SERVER['HTTP_X_SELLY_SIGNATURE'])) // If signatures match than execute an opperation because it's a verified call
{
    switch($content['status'])
    {
        /*
         * Global Vars
         */
        case sellyOrderStatus::NO_PAYMENT:
            // No payment has been received
            break;

        case sellyOrderStatus::ORDER_BLOCKED:
            // Order blocked due to risk level exceeding the maximum for the product
            break;

        case sellyOrderStatus::PARTIAL_PAYMENT:
            // Partial payment. When crypto currency orders do not receive the full amount required due to fees, etc.
            break;

        case sellyOrderStatus::REFUNDED:
            // Refunded
            break;

        case sellyOrderStatus::SUCCEDED:
            // Payment complete
            break;
        /*
         * PayPal Vars
         */
        case sellyOrderStatus::PAYPAL_DISPUTE:
            // PayPal dispute/reversal was open
            break;

        case sellyOrderStatus::PAYPAL_PENDING:
            // Payment pending on PayPal. Most commonly due to e-checks.
            break;
        /*
         * Crypto Vars
         */
        case sellyOrderStatus::CRYPTO_CONFIRMING:
            // Crypto currency transaction confirming
            break;
    }
}
else
    die(http_response_code(500));

?>
