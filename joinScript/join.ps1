Param
(
    [parameter(ParameterSetName="ApiKey", Mandatory=$true)]
    [String]$api_key,
    [parameter(ParameterSetName="ComputerName", Mandatory=$true)]
    [String]$computer_name,
    [parameter(ParameterSetName="AdminGroups", Mandatory=$true)]
    [String]$admin_groups,
    [parameter(ParameterSetName="ADDomain", Mandatory=$true)]
    [String]$domain
)

# generating credentials for service account from secret
$passwd = ConvertTo-SecureString $env:ServicePassword -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential ("SERVICE_ACCOUNT", $passwd)
# calling main
Join-Machine -ComputerName $computer_name -Credential $cred -AdminGroups $admin_groups -ADDomain $domain

# main function, everything branches from here
function Join-Machine {
    param(
        [parameter(ParameterSetName="ComputerName", Mandatory=$true)]
        [String]$name,
        [parameter(ParameterSetName="Credential", Mandatory=$true)]
        [System.Management.Automation.PSCredential]$credential,
        [parameter(ParameterSetName="AdminGroups", Mandatory=$true)]
        [String]$groups,
        [parameter(ParameterSetName="ADDomain", Mandatory=$true)]
        [String]$domain
    )
    # calls python and captures output, decrypting and authenticating key
    $cmd = Write-Host "python decrypt.py --api_key ($api_key)"
    $auth = & $cmd | Out-String
    # runs join
    if($auth -notmatch "ERR"){
        Add-Computer -Credential $credential -DomainName $domain -ComputerName $name
        Add-AdminGroup -ComputerName $name -AdminGroups $groups
        Restart-Computer -ComputerName $name -Credential $credential -Force
    }
    else{
        Write-Warning "ERR"
    }
}

# adds a series of groups (or one group) as administrators to the machine
function Add-AdminGroup {
    param(
        [parameter(ParameterSetName="ComputerName", Mandatory=$true)]]
        [String]$name,
        [parameter(ParameterSetName="AdminGroups", Mandatory=$true)]]
        [String]$groups
    )
    foreach($group in $groups.split(',')){
        $group = Format-Group $group
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
