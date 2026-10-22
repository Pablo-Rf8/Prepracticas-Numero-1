param(
    [ValidateRange(1, 65535)]
    [int]$Port = 8000
)

$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot 'Activate-Project.ps1')

$projectRoot = Split-Path -Parent $PSScriptRoot
$backendDirectory = Join-Path $projectRoot 'backend'

if (-not (Test-Path -LiteralPath (Join-Path $backendDirectory 'artisan') -PathType Leaf)) {
    throw 'Falta backend/artisan. Completa la instalacion de Laravel dentro de backend.'
}

if (-not (Test-Path -LiteralPath (Join-Path $backendDirectory 'vendor/autoload.php') -PathType Leaf)) {
    throw 'Faltan las dependencias PHP. Ejecuta composer install dentro de backend.'
}

if (-not (Test-Path -LiteralPath (Join-Path $backendDirectory 'public/build/manifest.json') -PathType Leaf)) {
    throw 'Faltan los recursos compilados. Ejecuta npm ci y npm run build dentro de frontend.'
}

Push-Location -LiteralPath $backendDirectory
try {
    & php artisan serve --host=127.0.0.1 --port=$Port
    $serverExitCode = $LASTEXITCODE
}
finally {
    Pop-Location
}

exit $serverExitCode
