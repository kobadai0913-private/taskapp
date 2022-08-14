import requests
import json
import pprint

url = 'https://covid19-japan-web-api.now.sh/api/v1/prefectures'

res = requests.get(url)

data = json.loads(res.text)
data = [data[0]['cases'],data[0]['deaths'],data[0]['pcr'],data[0]['hospitalize'],data[0]['discharge']]
print(data[0])
print(data[1])
print(data[2])
print(data[3])
print(data[4])
