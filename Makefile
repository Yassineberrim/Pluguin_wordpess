GREEN=\033[0;32m]
BLUE=\033[0;34m]
RED=\033[0;31m]
NC=\033[0m]
all: check docker_setup wait_for_db setup_wordpress verify
check:
	@command -v docker > /dev/null || { echo -e "$(RED)[ERROR] Docker n'est pas installé. Veuillez l'installer d'abord.$(NC)"; exit 1; }
	@command -v docker-compose > /dev/null || { echo -e "$(RED)[ERROR] Docker Compose n'est pas installé. Veuillez l'installer d'abord.$(NC)"; exit 1; }
	@echo -e "$(BLUE)[INFO] Création des répertoires...$(NC)"
	@mkdir -p wp-content/plugins/simple-chatbot wp-content/themes wp-content/uploads
docker_setup:
	@echo -e "$(BLUE)[INFO] Nettoyage de l'environnement précédent...$(NC)"
	@docker-compose down -v > /dev/null
	@echo -e "$(BLUE)[INFO] Construction et démarrage des conteneurs Docker...$(NC)"
	@docker-compose build
	@docker-compose up -d
wait_for_db:
	@echo -e "$(BLUE)[INFO] Attente du démarrage des services...$(NC)"
	@attempt=1; max_attempts=30; \
	until docker-compose exec -T db mysqladmin ping -h localhost -u yberrim --password=yberrim > /dev/null 2>&1; do \
	    if [ $$attempt -eq $$max_attempts ]; then \
	        echo -e "$(RED)[ERROR] Impossible de se connecter à la base de données après $$max_attempts tentatives.$(NC)"; \
	        exit 1; \
	    fi; \
	    echo -e "$(BLUE)[INFO] Attente de la base de données... ($$attempt/$$max_attempts)$(NC)"; \
	    sleep 5; \
	    attempt=$$((attempt + 1)); \
	done
setup_wordpress:
	@echo -e "$(BLUE)[INFO] Configuration de WordPress...$(NC)"
	@sleep 10  # Attente pour que WordPress soit complètement prêt
	@docker-compose exec -T --user www-data wordpress wp core install \
	    --path=/var/www/html \
	    --url=http://localhost:8000 \
	    --title="Mon Site WordPress" \
	    --admin_user=berrim \
	    --admin_password=berrim \
	    --admin_email=berrim@gmail.com \
	    --skip-email
	@echo -e "$(BLUE)[INFO] Activation du plugin simple_chatbot...$(NC)"
	@docker-compose exec -T --user www-data wordpress wp plugin activate simple-chatbot
	@echo -e "$(BLUE)[INFO] Configuration des permissions...$(NC)"
	@docker-compose exec -T wordpress chown -R www-data:www-data /var/www/html/wp-content
verify:
	@if curl -s -I http://localhost:8000 | grep -q "200 OK"; then \
	    echo -e "$(GREEN)[SUCCESS] Installation terminée avec succès!$(NC)"; \
	    echo -e "$(GREEN)WordPress est accessible sur: http://localhost:8000$(NC)"; \
	    echo -e "$(GREEN)Identifiants admin:$(NC)"; \
	    echo -e "  Username: berrim"; \
	    echo -e "  Password: berrim"; \
	else \
	    echo -e "$(RED)[ERROR] L'installation semble avoir échoué. Vérifiez les logs avec 'docker-compose logs'$(NC)"; \
	fi
clean:
	@echo -e "$(BLUE)[INFO] Nettoyage de l'environnement Docker...$(NC)"
	@docker-compose down -v > /dev/null
	@echo -e "$(GREEN)[SUCCESS] Nettoyage terminé.$(NC)"
