<?php

require 'vendor/autoload.php';

use Dotenv\Dotenv;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Request;


// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

function get_seo_page_report($url)
{
    $client = new Client();

    try {
        $response = $client->request('GET', 'https://website-seo-analyzer.p.rapidapi.com/seo/seo-audit-basic', [
            'query' => ['url' => $url],
            'headers' => [
                'x-rapidapi-host' => 'website-seo-analyzer.p.rapidapi.com',
                'x-rapidapi-key' => getenv('RAPIDAPI_KEY'),
            ],
        ]);

        return $response->getBody()->getContents();
    } catch (RequestException $e) {
        if ($e->hasResponse()) {
            return $e->getResponse()->getBody()->getContents();
        } else {
            return $e->getMessage();
        }
    }
}

function get_current_weather($city)
{
    $apiKey = getenv('WEATHER_API_KEY');
    if (!$apiKey) {
        throw new Exception("API key not found. Please set WEATHER_API_KEY in your .env file.");
    }

    $apiUrl = "http://api.weatherapi.com/v1/current.json";
    $apiParams = [
        'key' => $apiKey,
        'q' => $city
    ];

    try {
        $client = new Client();
        $response = $client->request('GET', $apiUrl, ['query' => $apiParams]);
        return json_decode($response->getBody(), true);
    } catch (RequestException $e) {
        return ['error' => $e->getMessage()];
    }
}

function get_current_time($timezone)
{
    $apiUrl = "http://worldtimeapi.org/api/timezone/{$timezone}";

    try {
        $client = new Client();
        $response = $client->request('GET', $apiUrl);
        $data = json_decode($response->getBody(), true);
        $isoDatetime = $data['datetime'];
        $dt = new DateTime($isoDatetime);
        $formattedTime = $dt->format('H:i:s');
        $formattedDate = $dt->format('Y-m-d');
        $formattedOffset = $dt->format('P');

        return "{$formattedTime} [{$formattedDate}, {$formattedOffset}]";
    } catch (RequestException $e) {
        return ['error' => $e->getMessage()];
    }
}

function get_order_status($orderId)
{
    $orderStatuses = [
        "12345" => "Shipped",
        "67890" => "Processing",
        "11223" => "Delivered"
    ];
    return $orderStatuses[$orderId] ?? "Order ID not found.";
}

function initiate_return($orderId, $reason)
{
    $orderIds = ["12345", "67890", "11223"];
    if (in_array($orderId, $orderIds)) {
        return "Return initiated for order {$orderId} due to: {$reason}.";
    } else {
        return "Order ID not found. Cannot initiate return.";
    }
}

function cancel_order($orderId)
{
    $orderStatuses = [
        "12345" => "Shipped",
        "67890" => "Processing",
        "11223" => "Delivered"
    ];
    if (isset($orderStatuses[$orderId])) {
        if ($orderStatuses[$orderId] === "Processing") {
            return "Order {$orderId} has been canceled successfully.";
        } else {
            return "Order {$orderId} cannot be canceled as it is already {$orderStatuses[$orderId]}.";
        }
    } else {
        return "Order ID not found. Cannot cancel order.";
    }
}

function update_shipping_address($orderId, $newAddress)
{
    $orderIds = ["12345", "67890", "11223"];
    if (in_array($orderId, $orderIds)) {
        return "Shipping address for order {$orderId} has been updated to: {$newAddress}.";
    } else {
        return "Order ID not found. Cannot update shipping address.";
    }
}

function track_shipment($trackingNumber)
{
    $trackingInfo = [
        "TRACK123" => "In Transit",
        "TRACK456" => "Delivered",
        "TRACK789" => "Out for Delivery"
    ];
    return $trackingInfo[$trackingNumber] ?? "Tracking number not found.";
}

function apply_discount($orderId, $discountCode)
{
    $validDiscountCodes = ["DISCOUNT10", "SAVE20"];
    $orderIds = ["12345", "67890", "11223"];
    if (in_array($orderId, $orderIds)) {
        if (in_array($discountCode, $validDiscountCodes)) {
            return "Discount code {$discountCode} applied to order {$orderId}.";
        } else {
            return "Invalid discount code: {$discountCode}.";
        }
    } else {
        return "Order ID not found. Cannot apply discount.";
    }
}

function change_payment_method($orderId, $paymentMethod)
{
    $orderIds = ["12345", "67890", "11223"];
    if (in_array($orderId, $orderIds)) {
        return "Payment method for order {$orderId} has been changed to: {$paymentMethod}.";
    } else {
        return "Order ID not found. Cannot change payment method.";
    }
}

function provide_invoice($orderId)
{
    $orderIds = ["12345", "67890", "11223"];
    if (in_array($orderId, $orderIds)) {
        return "Invoice for order {$orderId} has been sent to your email.";
    } else {
        return "Order ID not found. Cannot provide invoice.";
    }
}

function extend_warranty($orderId, $years)
{
    $orderIds = ["12345", "67890", "11223"];
    if (in_array($orderId, $orderIds)) {
        return "Warranty for order {$orderId} has been extended by {$years} years.";
    } else {
        return "Order ID not found. Cannot extend warranty.";
    }
}

function check_product_availability($productId)
{
    $productAvailability = [
        "PROD123" => "In Stock",
        "PROD456" => "Out of Stock",
        "PROD789" => "Limited Stock"
    ];
    return $productAvailability[$productId] ?? "Product ID not found.";
}

?>
