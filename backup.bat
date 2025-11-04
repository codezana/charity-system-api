@echo off
set DB_NAME=generator
set BACKUP_PATH=Z:\Backup
set MAX_BACKUPS=10
set MYSQLDUMP="C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqldump.exe"

:: Generate timestamp for the backup filename
for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set TIMESTAMP=%datetime:~0,4%-%datetime:~4,2%-%datetime:~6,2%_%datetime:~8,2%-%datetime:~10,2%-%datetime:~12,2%
set BACKUP_FILE=%BACKUP_PATH%\%DB_NAME%_%TIMESTAMP%.sql

:: Ensure the backup directory exists
if not exist "%BACKUP_PATH%" mkdir "%BACKUP_PATH%"

:: Run MySQL backup without password
%MYSQLDUMP% -u root %DB_NAME% > "%BACKUP_FILE%"

:: Keep only the latest 10 backups
for /f "skip=%MAX_BACKUPS% delims=" %%F in ('dir /b /o-d "%BACKUP_PATH%\%DB_NAME%_*.sql"') do (
    del "%BACKUP_PATH%\%%F"
)

exit
