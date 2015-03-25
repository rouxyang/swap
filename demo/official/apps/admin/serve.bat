@echo off

cd %~dp0
php.exe -S 0.0.0.0:8080 -t web
