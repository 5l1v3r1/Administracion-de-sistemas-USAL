# Administracion de sistemas (USAL)
- Gestión de usuarios: **LDAP**.
- Servidor de nombres: **dnsmasq** para los distintos servicios de la web (ej: mail.usal.es).
- Base de datos: **MySQL**.
```sql
CREATE TABLE `Contacto` (`id` int(11) NOT NULL auto_increment, `name`
varchar(64) NOT NULL, `email` varchar(64) NOT NULL, `topic`
varchar(64) NOT NULL, `msg` varchar(2048) NOT NULL, `time` varchar(32)
NOT NULL, PRIMARY KEY (`id`));
CREATE TABLE `Cuenta` (`username` varchar(128) NOT NULL, `email`
varchar(128) NOT NULL, `confirmado` int(11) NOT NULL, `web` int(11)
NOT NULL, `blog` int(11) NOT NULL, PRIMARY KEY (`username`), UNIQUE
INDEX (`email`));
CREATE TABLE `Mail_Conf` (`username` varchar(128) NOT NULL, `token`
varchar(40) NOT NULL, PRIMARY KEY (`username`), PRIMARY KEY
(`token`));
3CREATE TABLE `Reset_pass` (`user_mail` varchar(128) NOT NULL, `token`
varchar(40) NOT NULL, `time` varchar(64) NOT NULL, PRIMARY KEY
(`user_mail`), PRIMARY KEY (`token`));
CREATE TABLE `Session_token` (`username` varchar(128) NOT NULL,
`token` varchar(40) NOT NULL, `time` varchar(64) NOT NULL);
CREATE TABLE `intento` (`intentos` int(2) NOT NULL, `fecha` int(24) NOT
NULL, `ip` varchar(32) NOT NULL, PRIMARY KEY (`ip`));
```
![alt text](https://github.com/manulqwerty/Administracion-de-sistemas-USAL/blob/master/web/images/bd.jpg)
## Web
> Se utiliza HTTPS en todas las páginas.
- Registro en la plataforma (web y sistema) con mail de confirmación (y posibilidad de reenvio para evitar errores).
- Login con posibilidad de recuperar credenciales en caso de olvido. Se evitan ataques de fuerza bruta baneando la ip del atacante unos minutos tras 5 intentos.
- Contacto: Enviar feedback que leerán los adminsitradores (por mail y en su panel).
### Panel de Usuario y Técnico
Los usuarios podrán acceder a las siguientes funcionalidades:
- Revisar los servicios.
- Acceder a ficheros que suben los técnicos (manuales). Los técnicos podrán eliminar y subir los manuales a través de la web.
- Modificar los datos personales (excepto username).
- Acceder a una web y blog personal.
- Acceder a un webmail.
### Panel de Administrador
Los administradores podrán acceder a las siguientes funcionalidades:
- Revisar los servicios.
- Gestión de usuarios (dar de alta o baja técnicos y administradores).
- Revisar los mensajes de contacto.
- Acceder y gestionar manuales.
- Acceder al PHPLDAPAdmin.
- Acceder al Monitorix (herramienta de monitoreo)

## Sistema
- Cuotas de disco, se gestiona la asignación de espacio a los usuarios con un script de perl: [newuser.pl](https://github.com/manulqwerty/Administracion-de-sistemas-USAL/blob/master/scripts/newuser.pl)
- Copias de seguridad de la base de datos [mysql-backup.pl](https://github.com/manulqwerty/Administracion-de-sistemas-USAL/blob/master/scripts/mysql-backup.pl), de los ficheros de los usuarios [home-backup.pl](https://github.com/manulqwerty/Administracion-de-sistemas-USAL/blob/master/scripts/home-backup.pl) y del LDAP.
- Hosting de la web de los usuarios, para la correcta securización del sistema se encapsulan las webs de los usuarios con docker.

