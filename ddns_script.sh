#!/usr/bin/env bash

# --- Configuración ---
# URL completa proporcionada por IONOS
UPDATE_URL="https://ipv4.api.hosting.ionos.com/dns/v1/dyndns?q=OWE0ZmM3NDRiMDE3NGVkMzg4OTYxN2RkMTAyOTI0YzQuNEtNSEE4S3BSSHBhbkNXOFVwVWFLcDlmT2RLNkFIMUU3WEVpbHBDR3FhODA1cGZMV3FxWXFwYWh1NGw1MlBuUlFlcGVEcjFzaDA2Sm5XOWgxb0hjQ1E"
LOGFILE="/opt/mail/ddns.log"

# --- Ejecución de la petición ---
timestamp() { date "+%Y-%m-%d %H:%M:%S"; }
response=$(curl -s -w "%{http_code}" -X GET "$UPDATE_URL")
http_code="${response: -3}"
body="${response:0:${#response}-3}"

# --- Registro de resultados ---
echo "$(timestamp) HTTP $http_code — $body" >> "$LOGFILE"

# Código de salida != 0 si falla curl o si HTTP ≠ 200
if [[ $? -ne 0 || "$http_code" -ne 200 ]]; then
  echo "$(timestamp) ERROR updating DDNS" >> "$LOGFILE"
  exit 1
fi

exit
