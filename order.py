import json
import csv
from collections import defaultdict
from datetime import datetime

def main():
    # Get current timestamp strings for row data and filenames
    now = datetime.now()
    timestamp_str = now.strftime('%Y-%m-%d %H:%M:%S')
    timestamp_file_str = now.strftime('%Y%m%d_%H%M%S')

    # Load Laravel route list JSON exported via:
    # php artisan route:list --json > routes.json
    try:
        with open('routes.json', 'r', encoding='utf-8') as f:
            routes = json.load(f)
    except Exception as e:
        print(f"Error loading routes.json: {e}")
        return

    grouped_routes = defaultdict(list)
    all_routes = []

    for route in routes:
        http_method = route.get('method', '')
        uri = route.get('uri', '')
        route_name = route.get('name', '')
        middleware_list = route.get('middleware', [])
        middleware = ", ".join(middleware_list) if middleware_list else ""

        action = route.get('action')
        controller = "Closure_or_None"  # Fallback for routes without controller

        # Extract controller name if possible
        if isinstance(action, dict):
            full_controller = action.get('controller')
            if full_controller:
                # Extract only the controller class name (no namespace)  
                controller = full_controller.split('@')[0].split('\\')[-1]
        elif isinstance(action, str):
            # If action is string (like "Closure")
            controller = action

        # Compose route info dictionary with timestamp
        route_info = {
            'URI': uri,
            'HTTP Method': http_method,
            'Route Name': route_name,
            'Controller': controller,
            'Middleware': middleware,
            'Timestamp': timestamp_str
        }

        all_routes.append(route_info)
        grouped_routes[controller].append(route_info)

    # Optional: Sort all routes by Controller then URI for better organization
    all_routes.sort(key=lambda x: (x['Controller'], x['URI']))

    # Define CSV columns
    csv_columns = ['URI', 'HTTP Method', 'Route Name', 'Controller', 'Middleware', 'Timestamp']
    csv_filename = f'organized_routes_{timestamp_file_str}.csv'

    # Write routes to CSV file
    try:
        with open(csv_filename, 'w', newline='', encoding='utf-8') as csvfile:
            writer = csv.DictWriter(csvfile, fieldnames=csv_columns)
            writer.writeheader()
            for entry in all_routes:
                writer.writerow(entry)
        print(f"CSV file created successfully: {csv_filename}")
    except Exception as e:
        print(f"Error writing CSV file: {e}")

    # Write grouped routes by controller to JSON file
    json_filename = f'laravel_routes_grouped_{timestamp_file_str}.json'
    try:
        with open(json_filename, 'w', encoding='utf-8') as jsonfile:
            # Write with indentation for readability
            json.dump(grouped_routes, jsonfile, indent=4)
        print(f"Grouped JSON file created successfully: {json_filename}")
    except Exception as e:
        print(f"Error writing JSON file: {e}")

if __name__ == "__main__":
    main()
