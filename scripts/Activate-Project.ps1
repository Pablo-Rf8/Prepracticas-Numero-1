$projectRoot = Split-Path -Parent $PSScriptRoot
$phpDirectory = Join-Path $projectRoot '.tools/php'
$composerFile = Join-Path $projectRoot '.tools/bin/composer.phar'
$wrapperDirectory = Join-Path $PSScriptRoot 'bin'
$toolsBinDirectory = Join-Path $projectRoot '.tools/bin'

if (-not (Test-Path -LiteralPath (Join-Path $phpDirectory 'php.exe') -PathType Leaf)) {
    throw 'Falta PHP local en .tools/php/php.exe. Ejecuta scripts/setup-windows.ps1 desde la raiz del proyecto.'
}

if (-not (Test-Path -LiteralPath $composerFile -PathType Leaf)) {
    throw 'Falta Composer local en .tools/bin/composer.phar. Ejecuta scripts/setup-windows.ps1 desde la raiz del proyecto.'
}

$projectPathEntries = @($phpDirectory, $wrapperDirectory, $toolsBinDirectory)
$existingPathEntries = @($env:PATH -split ';' | Where-Object { $_ -and $_ -notin $projectPathEntries })
$env:PATH = ($projectPathEntries + $existingPathEntries) -join ';'
$env:COMPOSER_HOME = Join-Path $projectRoot '.tools/composer-home'
$env:COMPOSER_CACHE_DIR = Join-Path $projectRoot '.tools/cache/composer'
$env:npm_config_cache = Join-Path $projectRoot '.tools/cache/npm'
