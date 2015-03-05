@echo off

cd %~dp0
php.exe -S 127.0.0.1:8080 -t web
