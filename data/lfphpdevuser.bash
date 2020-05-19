#!/usr/bin/env bash
groupadd vagrant
useradd -p 'vagrant' -g vagrant vagrant
cp -rf /etc/skel /home/vagrant
chown -R vagrant:vagrant /home/vagrant
echo 'vagrant:vagrant' | chpasswd
#passwd vagrant