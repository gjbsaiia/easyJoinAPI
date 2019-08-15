import os
import sys
import argparse


def main():
    args = parse_arguments()
    try:
        resp = addToList(args.new_creds)
        print(resp)
    except TypeError:
        print("ERR")


def parse_arguments():
    parser = argparse.ArgumentParser()
    parser.add_argument("--new_creds", default=None, help="Credentials, plz", required=True)
    args = parser.parse_args()
    return args


def addToList(new_user):
    try:
        with open("/var/www/backend/manifest.txt", "a+") as man:
            man.write("user\t"+new_user+"\n")
            man.close()
        return "user added"
    except:
        with open("manifest.txt", "a+") as man:
            man.write(new_user+"\n")
            man.close()
        return "user added"


if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Interrupted \_[*.*]_/\n')
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)