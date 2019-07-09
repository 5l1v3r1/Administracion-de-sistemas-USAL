#!/usr/bin/env python
import cgi
import sys
import docker

# Este script se ejecuta cuando el usuario activa el servicio Mi Web

print ("Content-Type: text/html")
print 
form = cgi.FieldStorage()

if "username" not in form:
    sys.exit()
else:
    username = form["username"].value

client = docker.from_env()

image = 'apache_php'
html_dir = ('/home/%s/miWeb' % username)
env = ('VIRTUAL_HOST=%s.mmyschool.es' % username)
img_name = ('miweb-%s' % username)

container = client.containers.run(image, detach=True, name=img_name, 
            ports={'80/tcp': None}, network='nginx-proxy', 
environment=[env], volumes={html_dir: {'bind': '/var/www/html'}})
