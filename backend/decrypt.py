#!/usr/bin/python

import os
import sys
import argparse
import base64
import re
from Crypto import Random
from Crypto.Cipher import AES

def main():
    args = parse_arguments()
    aes_key = getEncKey()
    try:
        if(int(args.checkAuth) > 0):
            print(authenticate_key(args.api_key, aes_key))
        else:
            print(decryptKey(args.api_key, aes_key))
    except TypeError:
        print("ERR")

def parse_arguments():
    parser = argparse.ArgumentParser()
    parser.add_argument("--api_key", default=None, help="Credentials, plz", required=True)
    parser.add_argument("--checkAuth", default="1", help="Should we check if this user exists? 1/0", required=False)
    args = parser.parse_args()
    return args

def authenticate_key(client_key, aes_key):
    bare_key = decryptKey(client_key, aes_key)+"\n"
    authorized = readManifest()
    if bare_key in authorized["admin"]:
        return "hello_admin"
    if bare_key in authorized["dnsCred"]:
        return "dns_access_granted"
    if bare_key in authorized["users"]:
        return "ur_gud"
    return "ERR"

def decryptKey(client_key, aes_key):
    enc = base64.urlsafe_b64decode(client_key)
    chunk_size = AES.block_size
    offset = 0
    decrypted = ""
    while offset < len(enc):
        iv = enc[offset:AES.block_size+offset]
        offset += AES.block_size
        chunk = enc[offset:offset + chunk_size]
        offset+=chunk_size
        cipher = AES.new(aes_key, AES.MODE_CBC, iv)
        piece = (cipher.decrypt(chunk)).decode('utf-8')
        decrypted += piece.replace(" ","")
    return decrypted

def readManifest():
    authed_users = {}
    authed_users.update({"user": []})
    try:
        with open("/var/www/backend/manifest.txt", "r+") as man:
            authed_users = man.readlines()
            man.close()
    except:
        with open("manifest.txt", "r+") as man:
            authed_users = man.readlines()
            man.close()
    for user in authed_users:
        splitted = re.split(r'\t+', user)
        if splitted[0] not in ["admin", "dnsCred"]:
            authed_users["users"].append(splitted[1].replace("\n", ""))
        else:
            authed_users.update({splitted[0]: splitted[1].replace("\n", "")})
    return authed_users

# This is where you would pull the key down from vault
def getEncKey():
    # try:
    #     with open("/var/www/backend/aes_key.txt", "rb") as file:
    #         aes_key = file.read()
    #         file.close()
    # except:
    #     with open("aes_key.txt", "rb") as file:
    #         aes_key = file.read()
    #         file.close()
    key = os.getenv('encry_key')
    return base64.urlsafe_b64decode(key)

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Interrupted \_[*.*]_/\n')
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)