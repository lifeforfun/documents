@ECHO OFF
ECHO Starting PHP FastCGI...
start /b "ff" "D:/Program Files/php7.3.5/php-cgi.exe" -b 127.0.0.1:9000
ECHO Starting nginx
start /b "ff" /d "D:/Program Files/nginx-1.14.2/" "D:/Program Files/nginx-1.14.2/nginx.exe"
