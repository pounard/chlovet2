#!/bin/bash

if [ ! -e "$PWD/docker-compose.yaml" ]; then
    echo "Please select one of the compose configurations then:"
    echo ""
    echo "    ln -s docker-compose-ENV.yaml docker-compose.yaml"
    echo ""

    exit 1
fi

APP_DIR="$PWD" docker-compose -p chlovet up -d --build --remove-orphans --force-recreate
