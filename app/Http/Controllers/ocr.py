from tika import parser
import sys

#引数取得
args = sys.argv

file_data = parser.from_file("../files/"+str(args[1]))
text = file_data["content"]
print(text)