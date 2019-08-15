#!/usr/bin/python
import os
import sys
import base64
from Crypto import Random
from Crypto.Cipher import AES

AES_KEY_SIZE = 32

my_key = ""

def main():
    global my_key
    pubkey = ""
    pkey = ""
    with open("aes_key.txt", "rb") as file:
        aes_key = file.read()
        file.close()
    print(aes_key.decode('utf-8'))
    aes_key = base64.urlsafe_b64decode(aes_key)
    user = "easy_join_api_service"
    pwd = "!R3usabl3_Int3rn_W0rk_Baby!"
    my_key = user+":"+pwd
    print("starting key: "+my_key)
    print("")
    encrypted_key = encryptKey(my_key, aes_key)
    print("encrypted: "+str(encrypted_key))
    print("")
    basic_key = decryptKey(encrypted_key, aes_key)
    print("decrypted: "+basic_key)
    print("")
    print("authenticated: "+str(authenticate_key(basic_key)))

def encryptKey(bare_key, aes_key):
    chunk_size = AES.block_size
    offset = 0
    encrypted = b''
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

def authenticate_key(client_key):
    if my_key == client_key:
        return True
    print
    print(my_key+">EOL")
    print(client_key+">EOL")
    return False

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

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Interrupted \_[*.*]_/\n')
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)