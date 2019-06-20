#!/usr/bin/python

import os
import sys
import requests
import argparse
import base64
import time
from Crypto import Random
from Crypto.Cipher import AES

# base url
join_api = "somthing.easyJoinAPI.com/api/join.php"

# headers
headers = {
    #'content-type': "multipart/form-data; boundary=----WebKitFormBoundary7MA4YWxkTrZu0gW",
    'Content-Type': "application/json; charset=UTF-8",
    'Authorization': "Bearer Some_Token",
    'Cache-Control': "no-cache",
    'Postman-Token': "81389ca6-f794-465c-a029-fb9c26c765ab"
}

def parse_arguments():
    parser = argparse.ArgumentParser()
    parser.add_argument("--ad_domain", default="SOME_AD", help="Which Domain? Default's ____", required=False);
    parser.add_argument("--admin_groups", default="SOME_GROUP", help="Do you need special groups? (format: group1,group2,group3)", required=False)
    args = parser.parse_args()
    return args

def recoverKey():
    user = os.environ['username']
    pwd = os.environ['password']
    basic_key = encryptKey(user+":"+pwd, os.environ['aes_key'])
    return basic_key

def main():
    args = parse_arguments()
    auth = { "Authorization": "Bearer "+recoverKey()}
    headers.update(auth)
    #payload = "------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"name\"\r\n\r\n\""+args[0]+"\"\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"domain\"\r\n\r\n\""+args[1]+"\"\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW\r\nContent-Disposition: form-data; name=\"groups\"\r\n\r\n\""+args[2]+"\"\r\n------WebKitFormBoundary7MA4YWxkTrZu0gW--"
    if "sa-a-cloud-t4" not in args[1]:
        args[1] += ",sa-a-cloud-t4"
    name = os.environ['COMPUTERNAME']
    querystring = {"name":name,"domain":args[0],"groups":args[1]}
    i = 0
    req(i, headers, querystring)

def req(i, headers, querystring):
    response = requests.request("POST", join_api, headers=headers, params=querystring)
    with open('AD_Join.log', 'w+') as log:
        log.write(response+'\n')
        log.close
    if("ERR" in response):
        i+=1
        if(i < 4):
            with open('AD_Join.log', 'a') as log:
                log.write("Waiting 5 seconds...\n")
                time.sleep(5)
                log.write("Trying again, attempt "+i+"\n")
                log.close()
            req(i)

def encryptKey(bare_key, aes_key):
    chunk_size = AES.block_size
    offset = 0
    encrypted = ""
    end_loop = False
    while not end_loop:
        chunk = bare_key[offset:offset + chunk_size]
        # padding
        if len(chunk) % chunk_size != 0:
            end_loop = True
            chunk = chunk + (AES.block_size - len(chunk) % AES.block_size) * " "
        iv = Random.new().read(AES.block_size)
        cipher = AES.new(aes_key, AES.MODE_CBC, iv)
        encrypted += (iv + cipher.encrypt(chunk))
        offset += chunk_size
    return base64.urlsafe_b64encode(encrypted)

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Interrupted \_[*.*]_/\n')
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)
