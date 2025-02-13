# Proyecto Samperita

## Descripción
Este proyecto es una aplicación diseñada para ejecutarse en un entorno Kubernetes, implementada con PHP y Nginx.  
Incluye monitoreo y observabilidad con Prometheus, Grafana y Loki, además de un pipeline CI/CD automatizado con GitHub Actions para construir imágenes Docker y desplegar la aplicación.

---

## Características Principales

### 1. Infraestructura como Código (IaC)
- Uso de Terraform para crear la infraestructura en la nube.  
- Se despliega un clúster Kubernetes con 3 nodos.

### 2. Configuración de Kubernetes
Kubernetes administra la aplicación y sus servicios relacionados.

**Dependencias:**
- Redis  
- MySQL  
- Memcached  
- PHP  

**Contenedores Docker:**
- PHP: Backend de la aplicación.  
- Nginx: Servidor proxy inverso.  

### 3. Monitoreo y Observabilidad
- Prometheus: Recolección de métricas como procesos activos y conexiones aceptadas.  
- Grafana: Visualización de métricas y logs centralizados.  
- Loki: Gestión y búsqueda de logs.  
- Fluentd: Recolección de logs.  

### 4. Pipeline CI/CD Automatizado
- Construcción y publicación de imágenes Docker en Docker Hub usando GitHub Actions.  
- Despliegue automatizado tras cada actualización en la rama `main`.  

### 5. Gestión Segura de Secretos
Los secretos se administran de forma segura mediante **External Secrets**, permitiendo la sincronización automática con **GitLab**.

### 6. Exposición Pública
El servicio se expone al mundo a través de una **IP pública (Load Balancer)**.

---

## Requisitos Previos
- Docker  
- Kubernetes (kubectl)  
- Helm  
- Terraform  
- GitHub con secretos configurados:
  - `DOCKERHUB_USERNAME`
  - `DOCKERHUB_TOKEN`  
  (_Para publicar imágenes en Docker Hub_).

---

### Instrucciones para Despliegue

# Configuración de Infraestructura
Utiliza **Terraform** para provisionar la infraestructura:

```bash
cd terraform/
terraform init
terraform apply
```

# Desplegará un clúster Kubernetes y configurará los recursos necesarios.

## Construcción de Imágenes Docker
Desde el directorio app construye las imágenes Docker para PHP y Nginx con:
```bash
make images
```

## Pipeline CI/CD
El pipeline está configurado en `.github/workflows/pipelines.yaml`. Al hacer un push a la rama `main`, las imágenes se construirán y se publicarán automáticamente en Docker Hub.

### Despliega la aplicación

# Instalar external secrets (CRDS)
```bash
helm install external-secrets external-secrets/external-secrets --namespace external-secrets --create-namespace --set installCRDs=true --wait
```

# Instalar kube-prometheus-stack
```bash
helm install prometheus prometheus-community/kube-prometheus-stack --namespace monitoring --create-namespace --wait
```

# Despliega la app, sus dependencias y configuraciones de la monitorización
```bash
helmfile apply
```

# Desplegar con kustomize loki y fluentd
```bash
kubectl apply -k ./chart/loki
kubectl apply -k ./chart/fluentd
```

## También puedes hacer todo con el uso del Makefile, usando un simple:
```bash
make deploy
```

### 5. Monitoreo y Observabilidad
# Accede a Grafana para visualizar métricas haciendo un port forward
```bash
URL: http://<grafana-ip>:3000
```

## Para poder obtener datos de la API podremos realizar una petición a la siguiente URL:
```bash
http://{server_ip}/charmander
```

El resultado de la petición será un JSON con los datos de la API:
```json
{
    "statusCode": 200,
    "data": {
        "name": "charmander",
        "type": "fuego",
        "count": 1
    }
}
```

## Autores
Raquel Samper - Desarrollo, infraestructura y despliegue.
