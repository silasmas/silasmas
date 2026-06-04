# Pousse le dossier frontend/ vers le dépôt silasmas-web (site silasmas.com).
# Usage :
#   .\scripts\push-frontend-to-silasmas-web.ps1
#   .\scripts\push-frontend-to-silasmas-web.ps1 -Remote silasmas-web -Branch main

param(
  [string]$Remote = "silasmas-web",
  [string]$Branch = "main"
)

$ErrorActionPreference = "Stop"
$repoRoot = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $repoRoot

Write-Host "Depot : $repoRoot"
Write-Host "Remote cible : $Remote (branche $Branch)"

$remotes = git remote
if ($remotes -notcontains $Remote) {
  Write-Host ""
  Write-Host "Le remote '$Remote' n'existe pas. Ajoutez-le une fois :" -ForegroundColor Yellow
  Write-Host "  git remote add $Remote https://github.com/silasmas/silasmas-web.git"
  exit 1
}

Write-Host ""
Write-Host "Envoi de frontend/ via git subtree push..." -ForegroundColor Cyan
git subtree push --prefix=frontend $Remote $Branch

if ($LASTEXITCODE -eq 0) {
  Write-Host ""
  Write-Host "OK. Declenchez le redeploiement sur Hostinger (silasmas.com)." -ForegroundColor Green
} else {
  Write-Host ""
  Write-Host "Echec. Verifiez les conflits ou faites un commit sur main avant de pousser." -ForegroundColor Red
  exit $LASTEXITCODE
}
