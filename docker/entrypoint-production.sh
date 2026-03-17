#!/bin/bash
set -e

echo "🚀 Iniciando aplicação Avaliação 360..."

# Detecta o tipo de container (app, queue, scheduler)
CONTAINER_ROLE=${CONTAINER_ROLE:-app}

echo "📦 Container role: $CONTAINER_ROLE"

# Cria diretórios necessários
mkdir -p /run/php
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Ajusta permissões
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Aguarda um pouco para o banco estar pronto (apenas em produção)
if [ "$APP_ENV" = "production" ] && [ "$CONTAINER_ROLE" = "app" ]; then
    echo "⏳ Aguardando banco de dados ficar disponível..."
    sleep 10
fi

# Execuções específicas por tipo de container
case $CONTAINER_ROLE in
    app)
        echo "🌐 Iniciando container de aplicação web..."

        # Gera chave da aplicação se não existir
        if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
            echo "🔑 Gerando chave da aplicação..."
            php artisan key:generate --force
        fi

        # Executa migrações (apenas no container principal)
        echo "🗄️ Executando migrações do banco de dados..."
        php artisan migrate --force

        # Cache de configuração para produção
        if [ "$APP_ENV" = "production" ]; then
            echo "⚡ Otimizando para produção..."
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
        fi

        # Cria link simbólico para storage
        php artisan storage:link

        # Garante que o app não fique em maintenance após reboot
        php artisan up || true

        # Inicia nginx e php-fpm via supervisor
        echo "🎯 Iniciando serviços web..."
        exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
        ;;

    queue)
        echo "⚙️ Iniciando worker de filas..."

        # Aguarda o container principal estar pronto
        sleep 15

        # Inicia worker de filas
        exec php artisan queue:work --tries=3 --timeout=90 --memory=256
        ;;

    scheduler)
        echo "⏰ Iniciando agendador de tarefas..."

        # Aguarda o container principal estar pronto
        sleep 20

        # Inicia scheduler
        exec php artisan schedule:work
        ;;

    *)
        echo "❌ Tipo de container desconhecido: $CONTAINER_ROLE"
        exit 1
        ;;
esac
