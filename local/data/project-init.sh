#!/usr/bin/env bash

ENV="dev"
BASE_DIR="/vagrant"

echo "Hello, Provision is starting...."


mysql -u root < /vagrant/data/db.sql

