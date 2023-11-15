#!/usr/bin/env bash
#ddev-generated
set -e

# Ensure "dev" (or alternate SOLR_CORENAME) core config is always up to date even after the
# core has been created. This does not execute the first time,
# when solr-precreate has not yet run.
CORENAME=${SOLR_CORENAME:-dev}
if [ -d /var/solr/data/${CORENAME}/conf ]; then
    cp /solr-conf/conf/* /var/solr/data/${CORENAME}/conf
fi
