#!/usr/bin/env bash
# Shared Hostinger/production paths. Source from other deploy scripts.

if [ -z "${PHP_BIN:-}" ]; then
    for candidate in \
        /opt/alt/php83/usr/bin/php \
        /opt/alt/php82/usr/bin/php \
        /usr/local/bin/php83 \
        /usr/bin/php83
    do
        if [ -x "$candidate" ]; then
            PHP_BIN="$candidate"
            break
        fi
    done

    if [ -z "${PHP_BIN:-}" ] && command -v php >/dev/null 2>&1; then
        version="$(php -r 'echo PHP_MAJOR_VERSION;' 2>/dev/null || echo 0)"

        if [ "$version" -ge 8 ] 2>/dev/null; then
            PHP_BIN="php"
        fi
    fi

    PHP_BIN="${PHP_BIN:-/opt/alt/php83/usr/bin/php}"
fi

export PHP_BIN
