cli:
	docker-compose up -d
	docker exec -it binance-api-client_php_1 bash

down:
	docker-compose down
