destroy:
	docker-compose down --rmi all --volumes --remove-orphans
build:
	docker-compose build --no-cache --force-rm
up:
	docker-compose up -d
down:
	docker-compose down --remove-orphans
rup:
	@make down
	@make build
	@make up
dup:
	@make down
	@make up
php:
	docker-compose exec php bash
comp:
	docker-compose exec php composer install
ps:
	docker-compose ps
env:
	cp .env.example .env
tw:
	(cd src && npm run dev)
tw/build:
	(cd src && npm run build)
tw/install:
	docker compose exec php npm install
	docker compose exec php npm install tailwindcss 
	(cd src && npm install)
	(cd src && npm install tailwindcss)
db:
	docker compose exec db mysql -uroot -proot
db/init:
	rm -rf src/assets/submissions/*
	find src/assets/events ! -name 'event1-banner.svg' ! -name 'event1-main.svg' -type f -delete
	find src/assets/img/profile -type f ! -name 'default-profile.jpeg' -delete
	docker-compose exec db mysql -uroot -proot -e "SOURCE /docker-entrypoint-initdb.d/init.sql;"
init:
	@make build
	@make up
	@make env
	@make comp
	@make tw/install
	@make tw/build
	@make db/init
