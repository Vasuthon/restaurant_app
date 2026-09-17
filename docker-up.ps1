# Use this instead of "docker compose up" directly.
# Detects this machine's real LAN IP (the one phones on the same Wi-Fi can reach) and
# passes it into the container via the HOST_LAN_IP env var, so the staff dashboard can
# build correct customer links / table QR codes.
# If the machine's IP changes later (e.g. router restart), just rerun this script —
# the links/QR codes will pick up the new IP automatically.
#
# Usage: .\docker-up.ps1        (same as docker compose up)
#        .\docker-up.ps1 -d     (background, same as docker compose up -d)

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
