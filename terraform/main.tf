resource "digitalocean_kubernetes_cluster" "samperita_cluster" {
  name    = "samperita-cluster"
  region  = "nyc3"
  version = "1.31.5-do.0"

  node_pool {
    name       = "samperita-nodes"
    size       = "s-2vcpu-4gb"
    node_count = var.node_count
  }
}

provider "kubernetes" {
  host                   = digitalocean_kubernetes_cluster.samperita_cluster.endpoint
  cluster_ca_certificate = base64decode(digitalocean_kubernetes_cluster.samperita_cluster.kube_config.0.cluster_ca_certificate)
  token                  = digitalocean_kubernetes_cluster.samperita_cluster.kube_config.0.token
}

resource "local_file" "kubeconfig" {
  filename = "${path.module}/kubeconfig.yaml"
  content  = digitalocean_kubernetes_cluster.samperita_cluster.kube_config.0.raw_config
}

resource "kubernetes_namespace" "monitoring" {
  metadata {
    name = "monitoring"
  }
  depends_on = [digitalocean_kubernetes_cluster.samperita_cluster]

}

resource "kubernetes_namespace" "external_secrets" {
  metadata {
    name = "external-secrets"
  }
  depends_on = [digitalocean_kubernetes_cluster.samperita_cluster]
}

resource "kubernetes_secret" "alertmanager_slack_secret" {
  metadata {
    name      = "alertmanager-slack-secret"
    namespace = kubernetes_namespace.monitoring.metadata[0].name
  }

  data = {
    slack-webhook-url = var.slack_webhook_url
  }


  type = "Opaque"
}

resource "kubernetes_secret" "gitlab_access_token" {
  metadata {
    name      = "gitlab-access-token"
    namespace = kubernetes_namespace.external_secrets.metadata[0].name
  }

  data = {
    accessToken = var.gitlab_access_token
  }

  type = "Opaque"
}
