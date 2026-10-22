$ErrorActionPreference = 'Stop'
$ProgressPreference = 'SilentlyContinue'
$projectRoot = Split-Path -Parent $PSScriptRoot
$backendDirectory = Join-Path $projectRoot 'backend'
$frontendDirectory = Join-Path $projectRoot 'frontend'
$env:COMPOSER_HOME = Join-Path $projectRoot '.tools/composer-home'
$env:COMPOSER_CACHE_DIR = Join-Path $projectRoot '.tools/cache/composer'
$env:npm_config_cache = Join-Path $projectRoot '.tools/cache/npm'

Push-Location -LiteralPath $projectRoot
try {
    if (-not (Test-Path -LiteralPath (Join-Path $backendDirectory 'artisan') -PathType Leaf)) {
        throw 'Falta backend/artisan. Usa una copia completa del proyecto.'
    }
    if (-not (Test-Path -LiteralPath (Join-Path $frontendDirectory 'package-lock.json') -PathType Leaf)) {
        throw 'Falta frontend/package-lock.json. Usa una copia completa del proyecto.'
    }
    if (-not (Get-Command node -ErrorAction SilentlyContinue)) {
        throw 'Instala Node.js 22.12 o superior antes de continuar.'
    }
    $nodeVersion = [version]((node --version).TrimStart('v'))
    if ($nodeVersion -lt [version]'22.12.0') {
        throw 'Se necesita Node.js 22.12 o superior para compilar los estilos.'
    }

    New-Item -ItemType Directory -Path '.tools/downloads', '.tools/php', '.tools/bin' -Force | Out-Null

    if (-not (Test-Path -LiteralPath '.tools/php/php.exe')) {
        Write-Host 'Descargando PHP 8.4 desde php.net...'
        $release = (Invoke-RestMethod 'https://downloads.php.net/~windows/releases/releases.json').'8.4'
        $package = $release.'nts-vs17-x64'.zip
        if (-not $package.path -or -not $package.sha256) {
            throw 'No se encontro el paquete oficial de PHP para Windows x64.'
        }
        $archive = Join-Path $projectRoot ('.tools/downloads/' + $package.path)
        Invoke-WebRequest ('https://downloads.php.net/~windows/releases/' + $package.path) -OutFile $archive -UseBasicParsing
        if ((Get-FileHash -LiteralPath $archive -Algorithm SHA256).Hash -ne $package.sha256) {
            throw 'La descarga de PHP no coincide con su SHA256 oficial.'
        }
        Expand-Archive -LiteralPath $archive -DestinationPath '.tools/php' -Force
    }

    if (-not (Test-Path -LiteralPath '.tools/php/cacert.pem')) {
        Invoke-WebRequest 'https://curl.se/ca/cacert.pem' -OutFile '.tools/php/cacert.pem' -UseBasicParsing
    }
    if (-not (Test-Path -LiteralPath '.tools/php/php.ini')) {
        $phpDirectory = (Resolve-Path '.tools/php').Path.Replace('\', '/')
        $ini = Get-Content -LiteralPath '.tools/php/php.ini-development' -Raw
        $ini = $ini.Replace(';extension_dir = "ext"', ('extension_dir = "' + $phpDirectory + '/ext"'))
        foreach ($extension in @('curl', 'fileinfo', 'mbstring', 'openssl', 'pdo_sqlite', 'sqlite3', 'zip', 'intl')) {
            $ini = $ini.Replace(';extension=' + $extension, 'extension=' + $extension)
        }
        $ini += "`nmemory_limit=512M`ndate.timezone=America/Guatemala`ncurl.cainfo=" + $phpDirectory + "/cacert.pem`nopenssl.cafile=" + $phpDirectory + "/cacert.pem`n"
        # UTF8 sin BOM permite ejecutar el script tambien con Windows PowerShell 5.1.
        [System.IO.File]::WriteAllText((Join-Path $projectRoot '.tools/php/php.ini'), $ini, (New-Object System.Text.UTF8Encoding($false)))
    }

    & '.\.tools\php\php.exe' --version
    if ($LASTEXITCODE -ne 0) {
        throw 'PHP no pudo iniciar. Comprueba que Microsoft Visual C++ Redistributable x64 este instalado.'
    }

    if (-not (Test-Path -LiteralPath '.tools/bin/composer.phar')) {
        Write-Host 'Descargando y verificando Composer...'
        Invoke-WebRequest 'https://getcomposer.org/installer' -OutFile '.tools/downloads/composer-setup.php' -UseBasicParsing
        $signature = (Invoke-RestMethod 'https://composer.github.io/installer.sig').Trim()
        if ((Get-FileHash -LiteralPath '.tools/downloads/composer-setup.php' -Algorithm SHA384).Hash -ne $signature) {
            throw 'La descarga del instalador Composer no coincide con su SHA384 oficial.'
        }
        & '.\.tools\php\php.exe' '.tools/downloads/composer-setup.php' --install-dir=.tools/bin --filename=composer.phar
        if ($LASTEXITCODE -ne 0) { throw 'No se pudo instalar Composer.' }
    }

    . "$PSScriptRoot/Activate-Project.ps1"

    Push-Location -LiteralPath $backendDirectory
    try {
        & composer install --no-interaction --prefer-dist
        if ($LASTEXITCODE -ne 0) { throw 'Fallo composer install dentro de backend.' }

        if (-not (Test-Path -LiteralPath '.env')) {
            Copy-Item -LiteralPath '.env.example' -Destination '.env'
        }
        if (-not (Select-String -LiteralPath '.env' -Pattern '^APP_KEY=.+$' -Quiet)) {
            & php artisan key:generate --no-interaction
            if ($LASTEXITCODE -ne 0) { throw 'No se pudo generar APP_KEY.' }
        }
        if (-not (Test-Path -LiteralPath 'database/database.sqlite')) {
            New-Item -ItemType File -Path 'database/database.sqlite' | Out-Null
        }
        & php artisan migrate --no-interaction
        if ($LASTEXITCODE -ne 0) { throw 'No se pudieron aplicar las migraciones.' }
    }
    finally {
        Pop-Location
    }

    Push-Location -LiteralPath $frontendDirectory
    try {
        & npm.cmd ci
        if ($LASTEXITCODE -ne 0) { throw 'Fallo npm ci dentro de frontend.' }
        & npm.cmd run build
        if ($LASTEXITCODE -ne 0) { throw 'No se pudieron compilar los estilos.' }
    }
    finally {
        Pop-Location
    }

    Write-Host "Proyecto listo. Inicia con: powershell -ExecutionPolicy Bypass -File `"$(Join-Path $PSScriptRoot 'dev.ps1')`""
}
finally {
    Pop-Location
}
