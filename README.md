Proyecto Samperita
### Descripción
Este proyecto es una aplicación diseñada para ejecutarse en un entorno Kubernetes, implementada con PHP y Nginx. Incluye monitoreo y observabilidad con Prometheus, Grafana y Loki, además de un pipeline CI/CD automatizado con GitHub Actions para construir imágenes Docker y desplegar la aplicación.

### Características Principales
1. Infraestructura como Código (IaC):
    -  Uso de Terraform para crear la infraestructura en la nube. (3 nodos)
2. Configuración de Kubernetes para desplegar la aplicación y servicios relacionados.
Dependencias:
    - Redis
    - MySQL
    - Memcached
    - PHP
Contenedores Docker:
    - PHP: Backend de la aplicación.
    - Nginx: Servidor proxy inverso.
3. Monitoreo y Observabilidad:
    - Prometheus: Recolección de métricas como procesos activos y conexiones aceptadas.
    - Grafana: Visualización de métricas y logs centralizados.
    - Loki: Gestión y búsqueda de logs.
    - Fluentd: Recolección de logs
4. Pipeline CI/CD Automatizado:
Construcción y publicación de imágenes Docker en Docker Hub usando GitHub Actions.
5. Gestión Segura de Secretos:
Configuración de secretos en Kubernetes mediante External Secrets, para configurar los secretos de forma segura, sigue estos pasos:
    1. Copia los archivos de ejemplo `mi_archivo.yaml.dist` y renómbralo a `mi_archivo.yaml`.
    2. Edita el archivo `mi_archivo.yaml` y reemplaza los valores de ejemplo con tus claves y configuraciones reales.
6. Exposición Pública:
Servicio expuesto al mundo a través de una IP pública (Load Balancer)

## Requisitos Previos
- Docker
- Kubernetes (kubectl)
- Helm
- Terraform
- GitHub Account con secretos configurados:
DOCKERHUB_USERNAME y DOCKERHUB_TOKEN para publicar imágenes en Docker Hub.

## Instrucciones para despliegue
1. Configuración de Infraestructura
    - Utiliza Terraform para provisionar la infraestructura:
cd terraform/
terraform init
terraform apply
# Desplegará un clúster Kubernetes y configurará los recursos necesarios.
2. Construcción de Imágenes Docker
    - Desde el directorio app construye las imágenes Docker para PHP y Nginx con:
Make images

3. Pipeline CI/CD
# El pipeline está configurado en .github/workflows/pipelines.yaml. Al hacer un push a la rama main, las imágenes se construirán y se publicarán automáticamente en Docker Hub.

4. Despliegue de la Aplicación
### Despliega la aplicación 
# Instalar external secrets (CRDS)
	helm install external-secrets external-secrets/external-secrets \
	--namespace external-secrets \
	--create-namespace \
	--set installCRDs=true \
	--wait
# Instalar kube-prometheus-stack
	helm install prometheus prometheus-community/kube-prometheus-stack \
	--namespace monitoring \
	--create-namespace \
	--wait
# Despliega la app, sus dependencias y configuraciones de la monitorización
	helmfile apply
# Desplegar con kustomize loki y fluentd
	kubectl apply -k ./chart/loki
	kubectl apply -k ./chart/fluentd
#### Tambien puedes hacer todo con el uso del makefile, usando un simple:
    make deploy

5. Monitoreo y Observabilidad
# Accede a Grafana para visualizar métricas haciendo un port forward
URL: http://<grafana-ip>:3000

#### Para poder obtener datos de la API podremos realizar una petición a la siguiente URL:

```
http://{server_ip}/charmander
```

El resultado de la petición será un JSON con los datos de la API:

```
{
    "statusCode": 200,
    "data": {
        "name": "charmander",
        "type": "fuego",
        "count": 1
    }
}
```
Autores
Raquel Samper - Desarrollo, infraestructura y despliegue.