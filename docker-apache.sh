#!/bin/sh
docker build --pull --rm -f "docker/apache/Dockerfile" -t phink-apache:latest "docker/apache"

