#!/usr/bin/env bash
groupadd vagrant
useradd -g vagrant vagrant
cp -rf /etc/skel /home/vagrant
chown -R vagrant:vagrant /home/vagrant
passwd vagrant