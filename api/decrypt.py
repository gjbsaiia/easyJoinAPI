#!/usr/bin/python

import os
import sys
import argparse
import base64
import Crypto import Random
import Crypto.Cipher import AES

def main():
    args = parse_arguments()
    print(authenticate_key(args[0]))

def parse_arguments():
    parser = argparse.ArgumentParser()
    parser.add_argument("--api_key", default=None, help="Credentials, plz", required=True)
    args = parser.parse_args()
    return args

def authenticate_key(client_key):
    aes_key = os.environ['aes_key']
    bare_key = decryptKey(client_key, aes_key)
    user = os.environ['username']
    pwd = os.environ['password']
    my_key = user+":"+pwd
    if(bare_key == my_key):
        return "ur gud"
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
        piece = cipher.decrypt(chunk)
        decrypted += piece.replace(" ","")
    return decrypted

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Interrupted \_[*.*]_/\n')
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)