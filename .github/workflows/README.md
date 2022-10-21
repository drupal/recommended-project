# How to use automated deployment

When setting up a new project with automated deployment, copy the relevant files from the templates directory here
depending on what you wish to automate. GitHub will automatically execute any YAML in this folder.

# Secrets

- `DISCORD_WEBHOOK` - Discord webhook to send notifications. To obtain URL: `Edit channel > Integrations > Webhooks > Copy webhook URL from there`

## deploy-dev.yml

- `DEV_SSH_USER` - SSH username
- `DEV_SSH_KEY` - SSH private key. Generate one with: `ssh-keygen -t ed25519 -f server.key`
- `DEV_SSH_HOST` - Server hostname or IP address

## deploy-test.yml

- `TEST_SSH_USER` - SSH username
- `TEST_SSH_KEY` - SSH private key. Generate one with: `ssh-keygen -t ed25519 -f server.key`
- `TEST_SSH_HOST` - Server hostname or IP address

## deploy-staging.yml

- `STAGING_SSH_USER` - SSH username
- `STAGING_SSH_KEY` - SSH private key. Generate one with: `ssh-keygen -t ed25519 -f server.key`
- `STAGING_SSH_HOST` - Server hostname or IP address


## deploy-prod.yml

- `PROD_SSH_USER` - SSH username
- `PROD_SSH_KEY` - SSH private key. Generate one with: `ssh-keygen -t ed25519 -f server.key`
- `PROD_SSH_HOST` - Server hostname or IP address
