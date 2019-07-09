# Administracion de sistemas (USAL)
- Gestión de usuarios: **LDAP**.
- Servidor de nombres: **dnsmasq** para los distintos servicios de la web (ej: mail.usal.es).
- Base de datos: **MySQL**.
## Web
> Se utiliza HTTPS en todas las páginas.
- Registro en la plataforma (web y sistema) con mail de confirmación (y posibilidad de reenvio para evitar errores).
- Login con posibilidad de recuperar credenciales en caso de olvido.
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
- Cuotas de disco, se gestiona la asignación de espacio a los usuarios con un script de perl: [newuser.pl](https://www.google.com)
- Copias de seguridad de la base de datos (), de los ficheros de los usuarios (/home) y del LDAP.
- Hosting de la web de los usuarios, para la correcta securización del sistema se encapsulan las webs de los usuarios con docker.

