from tika import parser
import sys

args = sys.argv[0]

file_data = parser.from_file(args)
text = file_data["content"]
print(text)