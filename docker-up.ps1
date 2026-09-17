# Wraps "docker compose up" but detects the host's LAN IP first and passes it in
# as HOST_LAN_IP, so table QR codes point somewhere customers' phones can reach.
# Usage: .\docker-up.ps1 [-d]

$socket = New-Object System.Net.Sockets.Socket(
    [System.Net.Sockets.AddressFamily]::InterNetwork,
    [System.Net.Sockets.SocketType]::Dgram,
    [System.Net.Sockets.ProtocolType]::Udp
)
$lanIp = $null
try {
    $socket.Connect("8.8.8.8", 65530)
    $lanIp = $socket.LocalEndPoint.Address.ToString()
} catch {
    $lanIp = $null
} finally {
    $socket.Close()
}

if (-not $lanIp -or $lanIp -eq "0.0.0.0") {
    Write-Host "Could not detect a LAN IP (is this machine online?) - QR codes will only work on this machine." -ForegroundColor Yellow
} else {
    Write-Host "Detected LAN IP: $lanIp" -ForegroundColor Cyan
    Write-Host "Customers on the same Wi-Fi can reach the site at http://${lanIp}:8080/" -ForegroundColor Cyan
}

$env:HOST_LAN_IP = $lanIp
docker compose up @args
