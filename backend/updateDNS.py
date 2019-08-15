import os
import sys
import argparse


def main():
    args = parse_arguments()
    try:
        return logToDns(args.domain_name, args.ip_address)
    except:
        return "ERR"


def parse_arguments():
    parser = argparse.ArgumentParser()
    parser.add_argument("--domain_name", default=None, help="Credentials, plz", required=True)
    parser.add_argument("--ip_address", default=None, help="Credentials, plz", required=True)
    args = parser.parse_args()
    return args


def logToDns(new_name, raw_addy):
    usr = "super_secure_dns_user"
    pwd = os.getenv('dns_pswd')
    # You need to write this yourself as it's specific to your environment.

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Interrupted \_[*.*]_/\n')
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)
