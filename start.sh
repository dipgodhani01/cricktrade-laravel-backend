# Create the directory and file
mkdir -p docker
cat > ./start.sh << 'EOF'
#!/bin/bash
if [ -z "$(grep '^APP_KEY=' .env)" ] || [ "$(grep '^APP_KEY=' .env | cut -d= -f2)" = "" ]; then
    echo "Generating application key..."
    php artisan key:generate --force
fi
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF

# Make it executable
chmod +x docker/start.sh