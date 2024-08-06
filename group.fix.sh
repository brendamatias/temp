sudo groupadd docker 2>/dev/null || true

sudo usermod -aG docker $USER

newgrp docker

sudo systemctl enable --now docker

docker ps
docker compose version