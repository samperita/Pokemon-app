# Etapa base: Utiliza la imagen oficial de nginx basada en Alpine
FROM nginx:alpine AS base

# Instalar herramientas comunes necesarias
RUN apk add --no-cache bash curl

# Establecer el directorio de trabajo
WORKDIR /var/www/html/public

# Copiar los archivos públicos de la aplicación al contenedor
COPY /code/public/ /var/www/html/

# Copiar configuraciones personalizadas de NGINX
COPY .docker/rootfs/nginx/default.conf /etc/nginx/conf.d/default.conf

# Exponer el puerto que NGINX usará
EXPOSE 80

# Comando predeterminado para ejecutar NGINX
CMD ["nginx", "-g", "daemon off;"]
