# =====================================================================
#  Moodle 4.5 LTS application image  (PHP 8.2 + Apache + mod_php)
#  Bundles: theme_boost_union, quizaccess_proctoring, mod_vpl,
#           quizaccess_seb (Moodle core).
# =====================================================================
FROM php:8.2-apache

LABEL org.opencontainers.image.title="EAP Moodle" \
      org.opencontainers.image.description="AI-Proctored Assessment Platform - Moodle 4.5 LTS"

# ---- OS packages + PHP extensions --------------------------------
COPY --from=mlocati/php-extension-installer:latest /usr/bin/install-php-extensions /usr/local/bin/
RUN set -eux; \
    install-php-extensions \
        gd zip intl soap mbstring xml curl bcmath opcache exif \
        mysqli pgsql redis sodium ; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        git unzip curl cron ghostscript graphviz python3 rsync \
        default-mysql-client libpng16-16 locales tzdata ca-certificates; \
    sed -i 's/# en_US.UTF-8/en_US.UTF-8/' /etc/locale.gen && locale-gen; \
    rm -rf /var/lib/apt/lists/*

# ---- Apache configuration -------------------------------------
RUN a2enmod rewrite headers expires deflate remoteip
COPY config/apache/000-moodle.conf   /etc/apache2/sites-available/000-default.conf
COPY config/apache/security.conf     /etc/apache2/conf-available/zz-eap-security.conf
RUN a2enconf zz-eap-security

# ---- PHP configuration --------------------------------------
COPY config/php/moodle-php.ini /usr/local/etc/php/conf.d/zz-moodle.ini
RUN touch /var/log/php_errors.log && chown www-data:www-data /var/log/php_errors.log

# ---- Moodle core (shallow clone of the LTS branch) --------------
ARG MOODLE_BRANCH=MOODLE_405_STABLE
RUN git clone --branch "${MOODLE_BRANCH}" --depth 1 \
        https://github.com/moodle/moodle.git /usr/src/moodle \
    && rm -rf /usr/src/moodle/.git

# ---- Third-party plugins (best-effort; build never fails here) ---
# Each fetch is isolated: a plugin download outage must not break the image.
RUN set +e; cd /tmp; \
    install_zip() { \
      curl -fsSL --retry 3 --retry-delay 2 "$1" -o p.zip || return 1; \
      rm -rf ex && mkdir -p ex && unzip -q p.zip -d ex || return 1; \
      d="$(find ex -mindepth 1 -maxdepth 1 -type d | head -n1)"; \
      [ -n "$d" ] || return 1; \
      mkdir -p "/usr/src/moodle/$(dirname "$2")"; \
      rm -rf "/usr/src/moodle/$2" && mv "$d" "/usr/src/moodle/$2"; \
      rm -rf p.zip ex; return 0; \
    }; \
    fetch() { \
      dest="$1"; shift; \
      for url in "$@"; do \
        echo ">> $dest  <-  $url"; \
        if install_zip "$url" "$dest"; then echo "   OK  $dest"; return 0; fi; \
      done; \
      echo "   WARN could not install $dest (continuing without it)"; return 0; \
    }; \
    B="https://github.com"; \
    fetch "theme/boost_union" \
      "$B/moodle-an-hochschulen/moodle-theme_boost_union/archive/refs/heads/MOODLE_405_STABLE.zip" \
      "$B/moodle-an-hochschulen/moodle-theme_boost_union/archive/refs/heads/main.zip"; \
    fetch "mod/quiz/accessrule/proctoring" \
      "$B/Brain-Station-23/moodle-quizaccess_proctoring/archive/refs/heads/main.zip" \
      "$B/Brain-Station-23/moodle-quizaccess_proctoring/archive/refs/heads/master.zip"; \
    fetch "mod/vpl" \
      "$B/jcrodriguez-dis/moodle-mod_vpl/archive/refs/heads/master.zip" \
      "$B/jcrodriguez-dis/moodle-mod_vpl/archive/refs/heads/main.zip"; \
    true

RUN chown -R www-data:www-data /usr/src/moodle

# ---- Entry point -------------------------------------------------
COPY docker/moodle/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

WORKDIR /var/www/html/moodle
EXPOSE 80
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
