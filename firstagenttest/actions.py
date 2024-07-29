# functions/actions the agent can take/use
import os
import requests
from SimplerLLM.tools.rapid_api import RapidAPIClient
from dotenv import load_dotenv
from datetime import datetime

# load environment variables
load_dotenv()


def get_seo_page_report(url: str):
    api_url = "https://website-seo-analyzer.p.rapidapi.com/seo/seo-audit-basic"
    api_params = {
        'url': url,
    }
    api_client = RapidAPIClient()
    response = api_client.call_api(api_url, method='GET', params=api_params)
    return response


def get_current_weather(city: str):
    api_key = os.getenv('WEATHER_API_KEY')
    if not api_key:
        raise ValueError("API key not found. Please set WEATHER_API_KEY in your .env file.")

    api_url = "http://api.weatherapi.com/v1/current.json"
    api_params = {
        'key': api_key,
        'q': city
    }

    try:
        response = requests.get(api_url, params=api_params)
        response.raise_for_status()
        return response.json()
    except requests.exceptions.RequestException as e:
        return {"error": str(e)}


def get_current_time(timezone: str):
    api_url = f"http://worldtimeapi.org/api/timezone/{timezone}"
    try:
        response = requests.get(api_url)
        response.raise_for_status()
        data = response.json()
        iso_datetime = data['datetime']

        # Parse the ISO 8601 datetime string
        dt = datetime.fromisoformat(iso_datetime)

        # Format the time and date
        formatted_time = dt.strftime('%H:%M:%S')
        formatted_date = dt.strftime('%Y-%m-%dT')
        formatted_offset = dt.strftime('%z')
        formatted_offset = formatted_offset[:3] + ':' + formatted_offset[3:]  # Convert to ±HH:MM

        return f"{formatted_time} [{formatted_date}, {formatted_offset}]"

    except requests.exceptions.RequestException as e:
        return {"error": str(e)}


def get_order_status(order_id: str) -> str:
    """Fetches the status of a given order ID."""
    # Mock data for example purposes
    order_statuses = {
        "12345": "Shipped",
        "67890": "Processing",
        "11223": "Delivered"
    }
    return order_statuses.get(order_id, "Order ID not found.")


def initiate_return(order_id: str, reason: str) -> str:
    """Initiates a return for a given order ID with a specified reason."""
    # Mock data for example purposes
    if order_id in ["12345", "67890", "11223"]:
        return f"Return initiated for order {order_id} due to: {reason}."
    else:
        return "Order ID noinitiate_returnt found. Cannot initiate return."


def cancel_order(order_id: str) -> str:
    """Cancels a given order ID if possible."""
    # Mock data for example purposes
    order_statuses = {
        "12345": "Shipped",
        "67890": "Processing",
        "11223": "Delivered"
    }
    if order_id in order_statuses:
        if order_statuses[order_id] == "Processing":
            return f"Order {order_id} has been canceled successfully."
        else:
            return f"Order {order_id} cannot be canceled as it is already {order_statuses[order_id]}."
    else:
        return "Order ID not found. Cannot cancel order."


def update_shipping_address(order_id: str, new_address: str) -> str:
    """Updates the shipping address for a given order ID."""
    # Mock data for example purposes
    if order_id in ["12345", "67890", "11223"]:
        return f"Shipping address for order {order_id} has been updated to: {new_address}."
    else:
        return "Order ID not found. Cannot update shipping address."


def track_shipment(tracking_number: str) -> str:
    """Tracks the shipment with the given tracking number."""
    # Mock data for example purposes
    tracking_info = {
        "TRACK123": "In Transit",
        "TRACK456": "Delivered",
        "TRACK789": "Out for Delivery"
    }
    return tracking_info.get(tracking_number, "Tracking number not found.")


def apply_discount(order_id: str, discount_code: str) -> str:
    """Applies a discount to the given order ID."""
    # Mock data for example purposes
    valid_discount_codes = ["DISCOUNT10", "SAVE20"]
    if order_id in ["12345", "67890", "11223"]:
        if discount_code in valid_discount_codes:
            return f"Discount code {discount_code} applied to order {order_id}."
        else:
            return f"Invalid discount code: {discount_code}."
    else:
        return "Order ID not found. Cannot apply discount."


def change_payment_method(order_id: str, payment_method: str) -> str:
    """Changes the payment method for a given order ID."""
    # Mock data for example purposes
    if order_id in ["12345", "67890", "11223"]:
        return f"Payment method for order {order_id} has been changed to: {payment_method}."
    else:
        return "Order ID not found. Cannot change payment method."


def provide_invoice(order_id: str) -> str:
    """Provides an invoice for the given order ID."""
    # Mock data for example purposes
    if order_id in ["12345", "67890", "11223"]:
        return f"Invoice for order {order_id} has been sent to your email."
    else:
        return "Order ID not found. Cannot provide invoice."


def extend_warranty(order_id: str, years: int) -> str:
    """Extends the warranty for a given order ID."""
    # Mock data for example purposes
    if order_id in ["12345", "67890", "11223"]:
        return f"Warranty for order {order_id} has been extended by {years} years."
    else:
        return "Order ID not found. Cannot extend warranty."


def check_product_availability(product_id: str) -> str:
    """Checks the availability of a product with the given product ID."""
    # Mock data for example purposes
    product_availability = {
        "PROD123": "In Stock",
        "PROD456": "Out of Stock",
        "PROD789": "Limited Stock"
    }
    return product_availability.get(product_id, "Product ID not found.")
