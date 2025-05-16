FROM php:8.3-apache

# Habilita o mod_rewrite (rotas customizadas)
RUN a2enmod rewrite

# Instala o MySQL
RUN apt-get update && apt-get install -y && docker-php-ext-install pdo pdo_mysql mysqli

# Define o DocumentRoot como /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Atualiza os VirtualHosts para apontar para a nova DocumentRoot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

# Copia um arquivo customizado para habilitar AllowOverride All (rotas customizadas)
COPY apache-custom.conf /etc/apache2/conf-available/custom.conf
RUN a2enconf custom

# Copia os arquivos do projeto
COPY . /var/www/html

# Corrige permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
