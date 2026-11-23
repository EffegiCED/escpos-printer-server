@echo off
:: ==========================================================
:: Run only as Administrator
:: ==========================================================
net session >nul 2>&1
if %errorlevel% neq 0 (
    echo This script requires Administrator privileges.
    echo Requesting elevation...
    powershell -Command "Start-Process '%~f0' -Verb RunAs"
    exit /b
)
echo Running as Administrator...
echo.

setlocal
set "BASEDIR=%~dp0"
set "PHP=%BASEDIR%php\php.exe"
set "EPS=%BASEDIR%eps"
set "NSSM=%BASEDIR%nssm.exe"

if not exist "%PHP%" (
    echo php.exe not found in %BASEDIR%php\
    exit /b 1
)

if not exist "%EPS%" (
    echo eps.phar not found in %BASEDIR%
    exit /b 1
)

if not exist "%NSSM%" (
    echo nssm.exe not found in %BASEDIR%
    exit /b 1
)

:: Fork EffegiCED: PHP per Windows vuole il Visual C++ Redistributable di Microsoft (VCRUNTIME140.dll). Senza, il servizio parte,
:: si chiude subito e Windows lo mette in pausa senza dire perche'.
"%PHP%" -v >nul 2>&1
if not errorlevel 1 goto phpok
echo.
echo Manca il Visual C++ Redistributable di Microsoft: lo scarico e lo installo...
powershell -NoProfile -ExecutionPolicy Bypass -Command "$f = Join-Path $env:TEMP 'vc_redist.x64.exe'; Invoke-WebRequest -UseBasicParsing -Uri 'https://aka.ms/vs/17/release/vc_redist.x64.exe' -OutFile $f; Start-Process -FilePath $f -ArgumentList '/install','/quiet','/norestart' -Wait"
"%PHP%" -v >nul 2>&1
if not errorlevel 1 goto phpok
echo PHP non parte: installa a mano https://aka.ms/vs/17/release/vc_redist.x64.exe e rilancia questo file.
pause
exit /b 1

:phpok
:: Fork EffegiCED: l'indirizzo di Kassa, l'unico sito da cui l'agente accetta stampe (config\agent.json)
if exist "%BASEDIR%config\agent.json" goto services
echo.
set /p "KASSA_URL=Indirizzo di Kassa (per esempio https://kassa.exxstore.it): "
powershell -NoProfile -ExecutionPolicy Bypass -Command "$u = $null; if (-not [uri]::TryCreate($env:KASSA_URL, 'Absolute', [ref]$u)) { Write-Host 'Indirizzo non valido: scrivilo poi in config\agent.json'; exit 0 }; $origin = $u.GetLeftPart([UriPartial]::Authority); $json = @{ allowed_origins = [string[]]@($origin) } | ConvertTo-Json -Compress; [IO.File]::WriteAllText((Join-Path $env:BASEDIR 'config\agent.json'), $json); Write-Host ('Stampe accettate da ' + $origin)"

:services
echo Installing EPS as Windows services...
"%NSSM%" install EPS-WS "%PHP%" "%EPS%" --role ws
"%NSSM%" install EPS-HTTP "%PHP%" "%EPS%" --role http
:: Fork EffegiCED: gli errori dei servizi nella cartella logs (senza, un servizio che si ferma non dice perche')
if not exist "%BASEDIR%logs" mkdir "%BASEDIR%logs"
"%NSSM%" set EPS-WS AppStdout "%BASEDIR%logs\service-ws.log"
"%NSSM%" set EPS-WS AppStderr "%BASEDIR%logs\service-ws.log"
"%NSSM%" set EPS-HTTP AppStdout "%BASEDIR%logs\service-http.log"
"%NSSM%" set EPS-HTTP AppStderr "%BASEDIR%logs\service-http.log"

"%NSSM%" start EPS-WS
"%NSSM%" start EPS-HTTP

echo EPS services installed and started successfully.
pause
