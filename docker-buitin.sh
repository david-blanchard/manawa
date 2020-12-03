#!/bin/sh
docker build --pull --rm -f "docker/php/Dockerfile" -t phink-builtin:latest "docker/php"

