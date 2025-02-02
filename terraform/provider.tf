terraform {
  required_providers {
    digitalocean = {
      source = "digitalocean/digitalocean"
      version = "~> 2.0"
    }
  }
}

provider "digitalocean" {
  token = var.digitalocean_token
}

# Token de DigitalOcean
variable "digitalocean_token" {
  description = "Token de API de DigitalOcean"
  type        = string
  sensitive = true
}