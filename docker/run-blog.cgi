#!/usr/bin/env python
import docker
import sys
import cgi

# Este script se ejecuta cuando el usuario activa el servicio Mi Blog

print ("Content-Type: text/html")
print 
form = cgi.FieldStorage()

if "username" not in form:
    sys.exit()
else:
    username = form["username"].value

client = docker.from_env()

image = 'grav:latest'
env = ('VIRTUAL_HOST=blog%s.mmyschool.es' % username)
img_name = ('miblog-%s' % username)
vol_name = ('blog-%s' % username)
container = client.containers.run(image, detach=True, name=img_name, 
            ports={'80/tcp': None}, network='nginx-proxy', 
environment=[env], volumes={vol_name: {'bind': '/var/www/html'}})
