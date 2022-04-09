# Secrets

## deploy-test.yml

- `TEST_SSH_USER` - SSH username
- `TEST_SSH_KEY` - SSH private key. Generate one with: `ssh-keygen -t ed25519 -f server.key`
- `TEST_SSH_HOST` - Server hostname or IP address
- `TEST_PROJECT_DIR` - Base project dir. See below an example layout

## deploy-prod.yml

- `PROD_SSH_USER` - SSH username
- `PROD_SSH_KEY` - SSH private key. Generate one with: `ssh-keygen -t ed25519 -f server.key`
- `PROD_SSH_HOST` - Server hostname or IP address
- `PROD_PROJECT_DIR` - Base project dir. Here's an example layout

```bash
 ./
 ../
 files/                     # Drupal 'files' folder symlinked in the deployment instance in web/sites/default/files
 live -> release-1cfc009/   # Symlink to live instance
 release-13acb31/           # Deployment instance 1
 release-1cfc009/           # Deployment instance 2
 settings.local.php         # Drupal 'settings.local.php' file symlinked in web/sites/default/settings.local.php
```
