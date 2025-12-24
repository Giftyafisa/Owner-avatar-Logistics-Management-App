# Database Import Script for PostgreSQL on Render
# This script imports the database schema using psql

Write-Host "====================================" -ForegroundColor Cyan
Write-Host "PostgreSQL Database Import Script" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

# Get database credentials from environment variables or prompt
$DB_HOST = if ($env:DB_HOST) { $env:DB_HOST } else { Read-Host "Enter DB_HOST" }
$DB_USER = if ($env:DB_USER) { $env:DB_USER } else { Read-Host "Enter DB_USER" }
$DB_NAME = if ($env:DB_NAME) { $env:DB_NAME } else { Read-Host "Enter DB_NAME" }
$DB_PASS = if ($env:DB_PASS) { $env:DB_PASS } else { Read-Host "Enter DB_PASS" -AsSecureString | ConvertFrom-SecureString -AsPlainText }

Write-Host "Database Configuration:" -ForegroundColor Yellow
Write-Host "- Host: $DB_HOST"
Write-Host "- User: $DB_USER"
Write-Host "- Database: $DB_NAME"
Write-Host ""

# Check if psql is installed
$psqlPath = Get-Command psql -ErrorAction SilentlyContinue
if (-not $psqlPath) {
    Write-Host "ERROR: psql command not found!" -ForegroundColor Red
    Write-Host ""
    Write-Host "ALTERNATIVE SOLUTION:" -ForegroundColor Yellow
    Write-Host "1. Go to: https://dashboard.render.com" -ForegroundColor White
    Write-Host "2. Click on your PostgreSQL database (dbname_odlo_mxqy)" -ForegroundColor White
    Write-Host "3. Click 'Connect' > 'External Connection' > 'PSQL Command'" -ForegroundColor White
    Write-Host "4. Press Enter to connect" -ForegroundColor White
    Write-Host "5. Copy the contents of db\import_postgres.sql" -ForegroundColor White
    Write-Host "6. Paste into the terminal and press Enter" -ForegroundColor White
    Write-Host ""
    Write-Host "Or open the SQL file to copy:" -ForegroundColor Yellow
    Write-Host "   .\db\import_postgres.sql" -ForegroundColor Cyan
    exit 1
}

# Set password environment variable
$env:PGPASSWORD = $DB_PASS

# Import the SQL file
$sqlFile = Join-Path $PSScriptRoot "db\import_postgres.sql"
if (-not (Test-Path $sqlFile)) {
    Write-Host "ERROR: SQL file not found at $sqlFile" -ForegroundColor Red
    exit 1
}

Write-Host "Importing database from: $sqlFile" -ForegroundColor Yellow
Write-Host ""

try {
    # Execute the SQL file
    psql -h $DB_HOST -U $DB_USER -d $DB_NAME -f $sqlFile
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "====================================" -ForegroundColor Green
        Write-Host "Import Complete!" -ForegroundColor Green
        Write-Host "====================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "Default Admin Credentials:" -ForegroundColor Yellow
        Write-Host "Email: admin@digitalwebplus.com" -ForegroundColor White
        Write-Host "Password: admin123" -ForegroundColor White
        Write-Host "====================================" -ForegroundColor Green
    } else {
        Write-Host ""
        Write-Host "Import failed with exit code: $LASTEXITCODE" -ForegroundColor Red
    }
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
} finally {
    # Clear password from environment
    Remove-Item Env:\PGPASSWORD -ErrorAction SilentlyContinue
}
