# from PIL import Image
# import pyocr

# # OCRエンジンを取得
# engines = pyocr.get_available_tools()
# engine = engines[0]

# # 対応言語取得
# langs = engine.get_available_languages()
# # print("対応言語:",langs) # ['eng', 'jpn', 'osd']

# # 画像の文字を読み込む
# txt = engine.image_to_string(Image.open('../app/Http/Controllers/レシート.png'), lang="jpn") # 修正点：lang="eng" -> lang="jpn"

# print(txt)

import os
from PIL import Image
import pyocr
import sys

#引数取得
args = sys.argv

#pyocrにTesseractを指定する。
pyocr.tesseract.TESSERACT_CMD = r'C:/Program Files/Tesseract-OCR/tesseract.exe'
tools = pyocr.get_available_tools()
tool = tools[0]

# 対応言語取得
langs = tool.get_available_languages()


#文字を抽出したい画像のパスを選ぶ
img = Image.open("../files/"+str(args[1]))

#画像の文字を抽出
builder = pyocr.builders.TextBuilder(tesseract_layout=6)
text = tool.image_to_string(img, lang="jpn", builder=builder)

print(text)