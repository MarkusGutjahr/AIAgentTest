<?php
require 'vendor/autoload.php';
require 'actions.php';
require 'prompts.php';


use Dotenv\Dotenv;
use GeminiAPI\Client;
use GeminiAPI\Enums\HarmBlockThreshold;
use GeminiAPI\Enums\HarmCategory;
use GeminiAPI\Enums\Role;
use GeminiAPI\GenerationConfig;
use GeminiAPI\GenerativeModel;
use GeminiAPI\Resources\Content;
use GeminiAPI\Resources\Parts\TextPart;
use GeminiAPI\Responses\GenerateContentResponse;
use GeminiAPI\SafetySetting;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Access the API key from the environment variables
$apiKey = $_ENV['GEMINI_API_KEY'] ?? null;
if (!$apiKey) {
    print_r($_ENV);
    die("API key is not set. Please check your .env file.");
}

#$userPrompt = "What is the status of order 12345?";
#$userPrompt = "I want to return order 11223 because it is defective.";
#$userPrompt = "Can you check the status of order 11223? If its delivered, please initiat return as it was the wrong order";
#$userPrompt = "Can you check the status of order 67890? If its delivered, please initiat return as it was the wrong order else cancel the order.";
#$userPrompt = "What is the status of order 12345? Can you update the address to 123 Main St, Anytown USA?";

#$userPrompt = "What is the weather in Berlin?";

$userPrompt = "Whats the current time in Berlin?";

#$userPrompt = "what is the response time of google.com?";

$messages = [
    new Content([new TextPart($react_system_prompt)], Role::Model),
    new Content([new TextPart($userPrompt)], Role::User),
];


// Define available actions
$availableActions = [
    'get_seo_page_report',
    'get_current_weather',
    'get_current_time',
    'get_order_status',
    'initiate_return',
    'cancel_order',
    'update_shipping_address',
    'track_shipment',
    'apply_discount',
    'change_payment_method',
    'provide_invoice',
    'extend_warranty',
    'check_product_availability'
];
/*
$generationConfig = new GenerationConfig([
    'temperature' => 1,
    'top_p' => 0.95,
    'top_k' => 64,
    'max_output_tokens' => 8192,
    'response_mime_type' => 'text/plain',
]);
*/

$generationConfig = (new GenerationConfig())
    ->withTemperature(0.5)
    ->withTopP(0.6)
    ->withTopK(40)
    ->withMaxOutputTokens(40);

// Define safety settings

$safetySetting1 = new SafetySetting(HarmCategory::HARM_CATEGORY_HARASSMENT, HarmBlockThreshold::BLOCK_MEDIUM_AND_ABOVE);
$safetySetting2 = new SafetySetting(HarmCategory::HARM_CATEGORY_HATE_SPEECH, HarmBlockThreshold::BLOCK_MEDIUM_AND_ABOVE);
$safetySetting3 = new SafetySetting(HarmCategory::HARM_CATEGORY_SEXUALLY_EXPLICIT, HarmBlockThreshold::BLOCK_MEDIUM_AND_ABOVE);
$safetySetting4 = new SafetySetting(HarmCategory::HARM_CATEGORY_DANGEROUS_CONTENT, HarmBlockThreshold::BLOCK_MEDIUM_AND_ABOVE);


// Configure the Gemini client
$client = new Client($apiKey);

// Create the AI model
$model = $client->geminiPro()
    ->withAddedSafetySetting($safetySetting1)
    ->withAddedSafetySetting($safetySetting2)
    ->withAddedSafetySetting($safetySetting3)
    ->withAddedSafetySetting($safetySetting4)
    ->withGenerationConfig($generationConfig)
    //->withTools($availableActions)
;

// Start a chat session
$chatSession = $client->geminiPro()
    ->startChat()
    //->enableAutomaticFunctionCalling()
    ->withHistory($messages);


$agentResponse = $chatSession->sendMessage(new TextPart($userPrompt));
//print_r($agentResponse);
print_r($chatSession->history());


// Display the agent response


// Print out the content from the history
echo "1" . str_repeat('-', 160) . "\n";
foreach ($chatSession->history() as $content) {
    $parts = $content->parts[0];
    $role = $content->role->name;
    echo $role . " -> " . json_encode($parts) . "\n";
    echo str_repeat('-', 80) . "\n";
}

// Print out function calls and responses from the history
echo "2" . str_repeat('-', 160) . "\n";
foreach ($chatSession->history() as $content) {
    $part = $content->parts[0];
    $partDict = json_decode(json_encode($part), true);
    if (isset($partDict['function_call']) || isset($partDict['function_response'])) {
        echo $content->role->name . " -> " . json_encode($partDict) . "\n";
        echo str_repeat('-', 50) . "\n";
    }
}


// Extract and print the final answer from the response
echo "3" . str_repeat('-', 160) . "\n";
$finalAnswer = '';
foreach ($agentResponse->candidates as $candidate) {
    foreach ($candidate->content->parts as $part) {
        $finalAnswer = $part->text;
    }
}

echo "Final answer: " . ($finalAnswer ?: 'No content available') . "\n";
