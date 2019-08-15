# clo-easyADjoin
REST API to handle Windows VM AD joins onprem

## Things still needed
* [clo-pyWinAD](https://githubprod.prci.com/progressive/clo-pyWinAD) needs added to Artifactory, and then ['Dockerfile'](https://githubprod.prci.com/progressive/clo-easyADjoin/blob/master/Dockerfile) needs comment removed on 'pip install pyWinAD' 
* DNS automation added to ['updateDNS.py'](https://githubprod.prci.com/progressive/clo-easyADjoin/blob/master/backend/updateDNS.py) - then ['logToDNS.php'](https://githubprod.prci.com/progressive/clo-easyADjoin/blob/master/server_side/api/logToDNS.php) needs to have a response changed to show that it's now supported. API container will need rebuilt after. 
* Kerberos sidecar addition. Once API container is kerberos certified, external joins should be allowed (per Joe Camera).
* Needs a 'cloudbase-init-unattended.conf' to be created so that following sysprep a new Windows VM gets the [secret](https://githubprod.prci.com/progressive/clo-easyADjoin/blob/master/internal_config/internal_secrets.txt) it needs, and runs the internal process.


### All of this is wrapped in the python library [clo-pyWinAD](https://githubprod.prci.com/progressive/clo-pyWinAD)

    import pyWinAD as winAD
    client = winAD.WinADClient()

Using pyWinAD like this defaults you to an unauthorized credential, with an External role. This allows you to use none of the methods.

* External is to manage a VM externally

* Internal is to manage a VM internally
    
## External build:

    import pyWinAD as winAD
    client = winAD.WinADClient(creds = <Authorized_API_Credentials>)

## Internal build:

    import pyWinAD as winAD
    client = winAD.WinADClient(role = "Internal")
    
## Needs to run in two phases

### Internal on the VM prior to provisioner
Should run [configure.py](https://githubprod.prci.com/progressive/clo-easyADjoin/blob/master/internal_config/configure.py) within cloudinit - needs the DNS API credential in cloudinit as well. (not currently set up)
This sets the domain name according to convention, and adds itself to the DNS using the VM Internal build from clo-pyWinAD

### External to the VM via the provisioner
Just import pyWinAD, configure yourself as 'External', and use any of the below magic methods. The VM should be tagged in
OpenStack with its domain name and OS.

* **addAPICredential(self, new_creds):**
    Using the admin credentials for this API, you can authorize a new passkey (</new_creds/>) through this method.

* **changeEncryptionKey(self):**
    When enterprise Vault use is approved for Cloud, this functionality can be built out. The idea is that you 
    can periodically change the encryption key used to encrypt credentials by having the rest API pull the key
    from Vault on the server side, and by having the python wrapper pull the key from Vault on the client side.
    I left pathways for this to be done. Right now, this method does nothing.

* **setDomain(self, domain_name):**
    Sets the domain name (</domain_name/>) to be used in all methods later on. This needs to be used if the same
    instance is used to provision many machines.

* **joinMachine(self, ad_domain):**
    Joins machine to the desired Active Directory domain (</ad_domain/>). Uses domain_name currently set in it's
    self.domain_name value.

* **logToDns(self, new_name, raw_addy):**
    This is currently an empty method. This method needs to be fleshed out for this process to work. This would
    mostly be used internally, but I left it open to external use as well.

* **encrypt(self, bare_key):**
    Encrypts credentials using stored AES key. The key used here must match the key used server side.
