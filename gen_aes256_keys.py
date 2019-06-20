import base64
import hashlib
from Crypto import Random


def main():
    key = genKey("Some String")
    print key
    # KEEP THIS SECRET, KEEP THIS SAFE
    fd = open("aes_key.txt", "wb")
    fd.write(key)
    fd.close()


def genKey(key):
    key = hashlib.sha256(key.encode()).digest()
    return key

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Interrupted \_[*.*]_/\n')
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)