import os
import sys
import pyWinAD as winAD


def main():
    cred = os.getenv('api_dns')
    client = winAD.WinADClient(role="Internal")
    # This will generate a new machine name, log the domain into DNS, update OpenStack metadata, change the machine name on the machine, and then restart. 
    client.changeDomain(dnsCred=cred)

if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print('Interrupted \_[*.*]_/\n')
        try:
            sys.exit(0)
        except SystemExit:
            os._exit(0)