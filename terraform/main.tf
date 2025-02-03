
resource "digitalocean_kubernetes_cluster" "samperita_cluster" {
  name    = "samperita-cluster"
  region  = "nyc3"
  version = "1.31.1-do.5"

  node_pool {
    name       = "samperita-nodes"
    size       = "s-2vcpu-4gb"
    node_count = var.node_count
  }
}

resource "local_file" "kubeconfig" {
  filename = "${path.module}/kubeconfig.yaml"
  content  = digitalocean_kubernetes_cluster.samperita_cluster.kube_config[0].raw_config
}

resource "null_resource" "kubeconfig_setup" {
  depends_on = [digitalocean_kubernetes_cluster.samperita_cluster]

  provisioner "local-exec" {
    command = <<EOT
      sleep 30
      doctl kubernetes cluster kubeconfig save ${digitalocean_kubernetes_cluster.samperita_cluster.id}
      kubectl config use-context do-nyc3-${digitalocean_kubernetes_cluster.samperita_cluster.name}
    EOT
  }
}
