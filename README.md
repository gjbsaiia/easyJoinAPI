# easyJoinAPI
REST API that handles Windows VM joining to Active Directory
</br>
This project is used to solve the problem of automating Active Directory joins for Windows Virtual Machines.
I hosted this API on a linux container using the Dockerfile listed - but this could just as easily be deployed
on a physical server or virtual machine.
</br>
## basic idea
In your terraform to autobuild your Windows VM, you bake the client_side/request_join.py script into the on-start process.
Additionally, you include all necessary credentials as secrets (which translate to environmental variables within the VM).
This requests API and API handles AD join externally.
