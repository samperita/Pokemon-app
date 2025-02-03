DOCKER_REPO=samperitar
IMAGE_PHP=$(DOCKER_REPO)/pokemon-php
IMAGE_NGINX=$(DOCKER_REPO)/pokemon-nginx
PLATFORM=linux/amd64
HELM_CHART=./helmfile.yaml

build-nginx:
	docker build \
		--platform $(PLATFORM) \
		-t $(IMAGE_NGINX):latest \
		-f ./.docker/rootfs/nginx/Dockerfile .

build-php:
	docker build \
		--platform $(PLATFORM) \
		-t $(IMAGE_PHP):latest \
		-f ./.docker/Dockerfile .

images: build-nginx build-php

push-docker:
	docker push $(IMAGE_NGINX):latest
	docker push $(IMAGE_PHP):latest

todo: images push-docker

deploy:
	helm install external-secrets external-secrets/external-secrets \
	--namespace external-secrets \
	--create-namespace \
	--set installCRDs=true \
	--wait
	helm install prometheus prometheus-community/kube-prometheus-stack -f ./env/prometheus.yaml \
	--namespace monitoring \
	--create-namespace \
	--wait
	helmfile apply
	kubectl apply -k ./chart/loki
	kubectl apply -k ./chart/fluentd
	kubectl apply -f chart/monitoring-extra/templates/alertmanager-config.yaml

deploy-app:
	helmfile apply
	kubectl apply -k ./chart/loki
	kubectl apply -k ./chart/fluentd

undeploy:
	helmfile destroy
	helm uninstall external-secrets -n external-secrets
	helm uninstall prometheus -n monitoring

update:
	helm repo add bitnami https://charts.bitnami.com/bitnami
	helm repo update

template:
	helmfile -f $(HELM_CHART) template > template.yaml
clean:
	docker rmi $(IMAGE_NGINX):latest $(IMAGE_PHP):latest || true

crds:
	helm install external-secrets external-secrets/external-secrets \
	--namespace external-secrets \
	--create-namespace \
	--set installCRDs=true \
	--wait
	helm install prometheus prometheus-community/kube-prometheus-stack \
	--namespace monitoring \
	--create-namespace \
	--wait