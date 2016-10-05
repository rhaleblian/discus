@ECHO OFF
set PHPROOT=D:\Programs\php-7.0.11-Win32-VC14-x86

ECHO Starting PHP FastCGI...
set PATH=%PHPROOT%;%PATH%
REM RunHiddenConsole.exe
php-cgi.exe -b 127.0.0.1:9123
