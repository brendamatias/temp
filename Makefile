ENV_FILE := .env

.PHONY: up down restart logs status psql reset migrate

up:
	docker compose --env-file $(ENV_FILE) up -d

down:
	docker compose --env-file $(ENV_FILE) down

restart: down up

logs:
	docker compose --env-file $(ENV_FILE) logs -f postgres

status:
	docker compose --env-file $(ENV_FILE) ps

psql:
	docker exec -it nfmei_postgres psql -U $(shell grep DB_USER $(ENV_FILE) | cut -d= -f2) -d $(shell grep DB_NAME $(ENV_FILE) | cut -d= -f2)

reset:
	docker compose --env-file $(ENV_FILE) down -v

migrate:
	docker compose --env-file $(ENV_FILE) run --rm migrate

	pgadmin-up:
	docker compose --env-file $(ENV_FILE) up -d pgadmin

pgadmin-logs:
	docker compose --env-file $(ENV_FILE) logs -f pgadmin

pgadmin-down:
	docker compose --env-file $(ENV_FILE) stop pgadmin