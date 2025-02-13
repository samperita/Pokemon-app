variable "digitalocean_token" {
  description = "Token de API de DigitalOcean"
  type        = string
  sensitive   = true
}

variable "node_count" {
  description = "Número de nodos en el clúster"
  type        = number
  default     = 3
}

variable "gitlab_access_token" {
  description = "Token de acceso de GitLab para Kubernetes"
  type        = string
  sensitive   = true
}

variable "slack_webhook_url" {
  description = "URL del webhook de Slack"
  type        = string
  sensitive   = true
}
