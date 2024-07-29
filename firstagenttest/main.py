from SimplerLLM.language.llm import LLM, LLMProvider
from SimplerLLM.tools.json_helpers import extract_json_from_text
from dotenv import load_dotenv

from actions import get_seo_page_report
from prompts import react_system_prompt

# load environment variables
load_dotenv()

llm_instance = LLM.create(LLMProvider.GEMINI, model_name="gemini-1.5-pro")

# list of available actions
available_actions = {
    "get_seo_page_report": get_seo_page_report
}

# the user entered prompt
user_query = """
    what is the response time of google.com?
"""

messages = [
    {"role": "system", "content": react_system_prompt},
    {"role": "user", "content": user_query},
]

turn_count = 1
max_turns = 5

while turn_count < max_turns:
    print(f"Loop: {turn_count}")
    print("_____________________________________________________________________")
    turn_count += 1

    agent_response = llm_instance.generate_response(messages=messages)

    messages.append({"role": "agent", "content": agent_response})

    print(agent_response)

    action_json = extract_json_from_text(agent_response)

    if action_json:
        function_name = action_json[0]['function_name']
        function_params = action_json[0]['function_params']
        if function_name not in available_actions:
            raise Exception(f"Unknown action: {function_name}: {function_params}")
        print(f" -- running {function_name} {function_params}")
        action_function = available_actions[function_name]
        result = action_function(**function_params)
        function_result_message = f"Action Response:{result}"
        messages.append({"role": "user", "content": function_result_message})
        print(function_result_message)
    else:
        break
