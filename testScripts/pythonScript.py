#!/usr/bin/env python3

print('hello world')


f = open('/var/www/html/acquaint/test.txt','w')
f.write('Text to write to file.')
f.close()