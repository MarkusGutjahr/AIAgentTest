import os

import google.generativeai as genai
from dotenv import load_dotenv
from actions import *
from prompts import react_system_prompt

# load environment variables
load_dotenv()

# list of available actions
available_actions = {
    get_seo_page_report,
    get_current_weather,
    get_current_time,
    get_order_status,
    initiate_return,
    cancel_order,
    update_shipping_address,
    track_shipment,
    apply_discount,
    change_payment_method,
    provide_invoice,
    extend_warranty,
    check_product_availability
}

genai.configure(api_key=os.environ["GEMINI_API_KEY"])

# Create the model
generation_config = {
    "temperature": 1,
    "top_p": 0.95,
    "top_k": 64,
    "max_output_tokens": 8192,
    "response_mime_type": "text/plain",
}

safety_settings = [
    {
        "category": "HARM_CATEGORY_HARASSMENT",
        "threshold": "BLOCK_MEDIUM_AND_ABOVE"
    },
    {
        "category": "HARM_CATEGORY_HATE_SPEECH",
        "threshold": "BLOCK_MEDIUM_AND_ABOVE"
    },
    {
        "category": "HARM_CATEGORY_SEXUALLY_EXPLICIT",
        "threshold": "BLOCK_MEDIUM_AND_ABOVE"
    },
    {
        "category": "HARM_CATEGORY_DANGEROUS_CONTENT",
        "threshold": "BLOCK_MEDIUM_AND_ABOVE"
    }
]

model = genai.GenerativeModel(
    model_name="gemini-1.5-pro",
    generation_config=generation_config,
    safety_settings=safety_settings,
    tools=[available_actions]
)

chat_session = model.start_chat(
    enable_automatic_function_calling=True
)

#user_prompt = "What is the status of order 12345?"
#user_prompt = "I want to return order 11223 because it is defective."
#user_prompt = "Can you check the status of order 11223? If its delivered, please initiat return as it was the wrong order"
#user_prompt = "Can you check the status of order 67890? If its delivered, please initiat return as it was the wrong order else cancel the order."
#user_prompt = "What is the status of order 12345? Can you update the address to 123 Main St, Anytown USA?"

#user_prompt = "What is the weather in Berlin?"

user_prompt = "Whats the current time in Berlin?"

#user_prompt = "what is the response time of google.com?"

messages = [
    {"role": "system", "content": react_system_prompt},
    {"role": "user", "content": user_prompt},
]

print("1" + '-' * 160)
agent_response = chat_session.send_message(user_prompt)
#print(agent_response)

for content in chat_session.history:
    part = content.parts[0]
    print(content.role, "->", type(part).to_dict(part))
    print('-' * 80)


print("2" + '-' * 160)
# Print out each of the function calls requested from this single call.
for content in chat_session.history:
    part = content.parts[0]
    part_dict = type(part).to_dict(part)
    if 'function_call' in part_dict or 'function_response' in part_dict:
        print(content.role, "->", part_dict)
        print('-' * 50)


print("3" + '-' * 160)
print("Final answer:", next((part.text for candidate in agent_response.candidates for part in candidate.content.parts), 'No content available'))
