import hashlib
import json
import os

current_dir = os.getcwd()

def process(phpcs_data):
    code_climate_results = []

    files = phpcs_data['files']
    for file_path, file_info in files.items():
        file_path = file_path.replace(current_dir, ".")
        if 'messages' in file_info:
            for message in file_info['messages']:
                code_climate = convert_to_code_climate(file_path, message)
                code_climate_results.append(code_climate)

    return code_climate_results

def convert_to_code_climate(file_path, message):
    severity = 'major'
    if message['type'] == 'ERROR':
        severity = 'major'
    if message['type'] == 'WARNING':
        severity = 'minor'
    return {
            "description": message['message'],
            "check_name": message['source'],
            "fingerprint": fingerprint_json(message),
            "severity": severity,
            "location": {
                "path": file_path,
                "lines": {
                    "begin": message['line']
                }
            }
        }

def fingerprint_json(json_obj):
    if isinstance(json_obj, str):
        json_obj = json.loads(json_obj)
    sorted_json = json.dumps(json_obj, sort_keys=True)
    hash_object = hashlib.sha256(sorted_json.encode())
    return hash_object.hexdigest()

with open('phpcs-report.json', 'r') as file:
    phpcs_data = json.load(file)

code_climate_results = process(phpcs_data)

with open('code_climate_phpcs_report.json', 'w') as json_file:
    json.dump(code_climate_results, json_file)