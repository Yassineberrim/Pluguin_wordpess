FROM wordpress:latest
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp
RUN apt-get update && apt-get install -y \
    less \
    && rm -rf /var/lib/apt/lists/*