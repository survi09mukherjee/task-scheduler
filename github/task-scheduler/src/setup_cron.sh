#!/bin/bash

# File: src/setup_cron.sh

CRON_FILE="/tmp/mycron"
PHP_PATH=$(which php)
PROJECT_DIR=$(cd "$(dirname "$0")" && pwd)
CRON_JOB="0 * * * * $PHP_PATH $PROJECT_DIR/cron.php > /dev/null 2>&1"

# Get current crontab into temp file
crontab -l 2>/dev/null > "$CRON_FILE"

# Add new CRON job if not already present
if ! grep -Fxq "$CRON_JOB" "$CRON_FILE"; then
    echo "$CRON_JOB" >> "$CRON_FILE"
    crontab "$CRON_FILE"
    echo "✅ CRON job installed: runs cron.php every hour."
else
    echo "ℹ️ CRON job already exists. No changes made."
fi

# Cleanup
rm "$CRON_FILE"
