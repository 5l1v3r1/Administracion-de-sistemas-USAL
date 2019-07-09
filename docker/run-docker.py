#!/usr/bin/env python

import docker
import mysql.connector
import os
import subprocess

# Este script se ejecuta al encender el servidor para lanzar los servicios de los usuarios

mydb = mysql.connector.connect(
  host="localhost",
  user="admin",
  passwd="password",
  database="Admin_sis"
)
client = docker.from_env()
docks = client.containers.list(all=True)
for i in docks:
    i.remove(force = True)

# docker run -d -p 8080:80 --name nginx-proxy --net nginx-proxy 
# -v /var/run/docker.sock:/tmp/docker.sock jwilder/nginx-proxy
image = 'jwilder/nginx-proxy'
prts = {'8080/tcp':80}
container = client.containers.run(image, detach=True, ports={'80/tcp':8080},
            name='nginx-proxy', network='nginx-proxy', 
            volumes={'/var/run/docker.sock': {'bind': '/tmp/docker.sock'}})

mycursor = mydb.cursor()
mycursor.execute("SELECT * FROM Cuenta")
myresult = mycursor.fetchall()
for i in myresult:
    username = i[0]
    web = i[3]
    blog = i[4]

    if web == 1:
        image = 'apache_php'
        html_dir = ('/home/%s/miWeb' % username)
        env = ('VIRTUAL_HOST=%s.mmyschool.es' % username)
        img_name = ('miweb-%s' % username)
        container = client.containers.run(image, detach=True, name=img_name, 
                ports={'80/tcp': None}, network='nginx-proxy', 
                environment=[env], volumes={html_dir: {'bind': '/var/www/html'}})

    if blog == 1:
      image = 'grav:latest'
      env = ('VIRTUAL_HOST=blog%s.mmyschool.es' % username)
      img_name = ('miblog-%s' % username)
      vol_name = ('blog-%s' % username)
      container = client.containers.run(image, detach=True, name=img_name, 
                  ports={'80/tcp': None}, network='nginx-proxy', 
environment=[env], volumes={vol_name: {'bind': '/var/www/html'}})
