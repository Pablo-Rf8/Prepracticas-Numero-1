@echo off
rem Usa las herramientas de la raiz sin cambiar el directorio de trabajo.
"%~dp0..\..\.tools\php\php.exe" "%~dp0..\..\.tools\bin\composer.phar" %*
exit /b %errorlevel%
