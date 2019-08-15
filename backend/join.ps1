[CmdletBinding()] 

Param
(
    [parameter(Mandatory=$false)]
    [String]$computer_name,
    [parameter(Mandatory=$false)]
    [String]$admin_groups,
    [parameter(Mandatory=$false)]
    [String]$domain
)

if(!$domain){
    $domain = Read-Host "Please input valid Active Directory Domain"
}
if(!$computer_name){
    $computer_name = Read-Host "Please input valid Computer Name or 'n' for self"
    if($computer_name -eq 'n'){
        $computer_name = "SELF"
        Write-Warning "Joining self... $computer_name"
    }
}
if(!$admin_groups){
    $admin_groups = Read-Host "Please input groups to be added as Local Admin (ex: group1,group2), or 'n' to skip"
    if($admin_groups -eq 'n'){
        $admin_groups = "NO_GROUPS"
        Write-Warning "Skipping setting Local Admins... $admin_groups"
    }
}

# main function, everything branches from here
function Join-Machine {
    param(
        [parameter(Mandatory=$true)]
        [String]$name,
        [parameter(Mandatory=$true)]
        [System.Management.Automation.PSCredential]$credential,
        [parameter(Mandatory=$true)]
        [String]$groups,
        [parameter(Mandatory=$true)]
        [String]$domain
    )
    if($name -notmatch "SELF"){
        Write-Warning "Joining $name to AD..."
        Add-Computer -Credential $credential -DomainName $domain -ComputerName $name
    }
    else{
        Write-Warning "Joining your machine to AD..."
        Add-Computer -Credential $credential -DomainName $domain
    }
    if($groups -notmatch "NO_GROUPS"){
        Write-Warning "Adding $groups as local admin..."
        Add-AdminGroup $name $groups
    }
    if($name -notmatch "SELF"){
        Write-Warning "Restarting $computer_name"
        Restart-Computer -ComputerName $name -Credential $credential -Force
    }
    else{
        Write-Warning "Restarting..."
        #Restart-Computer -Force
    }
}

# adds a series of groups (or one group) as administrators to the machine
function Add-AdminGroup {
    param(
        [parameter(Mandatory=$true)]
        [String]$name,
        [parameter(Mandatory=$true)]
        [String]$groups
    )
    foreach($group in $groups.split(',')){
        Write-Warning $group
        $group = Format-Group $group
        Write-Warning "$group"
        $err = $false
        if(!$group){
            $err = $true
        }
        else{
            try {
                ([ADSI]"WinNT://$name/Administrators,group").add($group)
            } catch {
                $err = $true
            }
        }
        if($err){
            Write-Warning "ERR"
        }	
    }
}

# ensures group is real, and grabs the proper AD
function Resolve-Group {
    param(
        [string]$group
    )
        try{
            $ad = ([adsisearcher]"(samaccountname=$group)").findone().properties['samaccountname']
        }
        catch{
            $ad = $null
        }

        return $ad
    }

# formats each group for admin listing
function Format-Group {
    param( [String]$group )

    if ($group -notmatch "\\"){
        $ad = Resolve-Group $group
        if(!$ad){
            $group = $null
        }
        else{
            $group = 'WinNT://',"$env:userdomain",'/',$ad -join ''
        }
    } 
    else{
        $ad = ($group -split '\\')[1]
        $domain = ($group -split '\\')[0]
        $group = 'WinNT://',$domain,'/',$ad -join ''
    }

    return $group
}

if($domain -match "hszq" -or $domain -match "HSZQ"){
    Write-Warning "Setting HSZQ Credentials"
    # generating credentials for service account from secret
    $passwd = ConvertTo-SecureString $env:hszq_pswd -AsPlainText -Force
    $cred = New-Object System.Management.Automation.PSCredential ('zadr001q', $passwd)
}
elseif($domain -match "hsz" -or $domain -match "HSZ"){
    Write-Warning "Setting HSZ Credentials"
    # generating credentials for service account from secret
    $passwd = ConvertTo-SecureString $env:hsz_pswd -AsPlainText -Force
    $cred = New-Object System.Management.Automation.PSCredential ("zadr001p", $passwd)
}


Join-Machine $computer_name $cred $admin_groups $domain