import json
import sys
import requests

args = sys.argv[1]

url = "http://zipcloud.ibsnet.co.jp/api/search"
param = {"zipcode": args}

res = requests.get(url, params=param)
response = json.loads(res.text)
address = response["results"][0]

print(address["address1"] + address["address2"] + address["address3"])
print(address["kana1"] + address["kana2"] + address["kana3"])

