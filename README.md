# easyADjoin
REST API to handle Windows VM AD joins onprem. This won't work out of the box for u, sorry - this was built specifically for an unnamed company, but it's pretty cool work and won't take that much to rework for other environments.

Run dockerfile to build container, then you can interact with the API through pyWinAD (link below). Have fun.


### All of this is wrapped in the python library [pyWinAD](https://github.com/gjbsaiia/pyWinAD)

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
Should run [configure.py](https://github.com/gjbsaiia/easyJoinAPI/blob/master/internal_config/configure.py) within cloudinit - needs the DNS API credential in cloudinit as well. (not currently set up)
This sets the domain name according to convention, and adds itself to the DNS using the VM Internal build from pyWinAD

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
