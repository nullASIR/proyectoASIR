# PokePimas - Infraestructura de Alta Disponibilidad

## 1. Descripción del Proyecto
Este repositorio contiene la documentación y los archivos necesarios para desplegar la infraestructura de alta disponibilidad del proyecto **PokePimas**. El entorno se compone de una red segmentada con firewall perimetral, balanceo de carga en capa 7, clúster de bases de datos Master-Master y servidores web en alta disponibilidad.

## 2. Arquitectura del Sistema
La infraestructura se despliega mediante máquinas virtuales independientes que interactúan en una red virtual privada.

* **pfSense:** Firewall/Router perimetral y puerta de enlace.
* **LB-HAProxy:** Balanceador de carga y terminación SSL.
* **DB-01 / DB-02:** Clúster MariaDB (Replicación Master-Master circular).
* **Web-01 / Web-02:** Servidores web (Hosting de la aplicación).

## 3. Prerrequisitos de Despliegue
* **Software:** VirtualBox 7.x o superior instalado.
* **Recursos:** Mínimo 16GB de RAM y 50GB de espacio en disco disponibles.
* **Red:** Configurar una "Red Interna" en VirtualBox con nombre `RedPokePimas`.

## 4. Guía de Despliegue (Orden de Importación)
Es crucial importar las máquinas virtuales en el orden correcto para asegurar la resolución de red:

1.  **pfSense:** Configurar la interfaz WAN (si requiere salida a internet) y LAN conectada a `RedPokePimas`.
2.  **DB-01 / DB-02:** Conectar a `RedPokePimas`. Iniciar servicios de MariaDB.
3.  **LB-HAProxy:** Conectar a `RedPokePimas`.
4.  **Web-01 / Web-02:** Conectar a `RedPokePimas`.

## 5. Post-Instalación: Ajuste de Red
Al importar máquinas `.ova`, VirtualBox puede renombrar las interfaces de red. Si tras iniciar alguna máquina no hay conectividad:
1. Identifica la nueva interfaz: `ip link`
2. Edita el archivo de configuración: `sudo nano /etc/netplan/01-netcfg.yaml` (o el equivalente en tu sistema).
3. Aplica los cambios: `sudo netplan apply`

## 6. Credenciales de Acceso
> **Nota:** Por motivos de seguridad, las contraseñas reales se deben configurar tras la primera importación.

| Servicio | Usuario | Puerto |
| :--- | :--- | :--- |
| **pfSense Web GUI** | admin | 80 |
| **HAProxy Stats** | admin | 8080 |
| **MariaDB (Root)** | root | 3306 |

## 7. Mantenimiento y Automatización
* **Monitorización:** El agente de Zabbix está configurado para arrancar automáticamente al inicio del sistema (`systemctl enable zabbix-agent`).
* **Backups:** Se encuentran disponibles scripts en `/usr/local/bin/backup_db.sh` que realizan volcados comprimidos mediante `mysqldump` y rotación de logs.
* **Hardening:** Se han aplicado reglas de `Fail2Ban` y se ha deshabilitado el acceso SSH por contraseña (`PasswordAuthentication no`).

## 8. Troubleshooting
* **Error 503:** Verifica en el panel de estadísticas del HAProxy (`:8080/stats`) si los nodos están en `DOWN` (rojo). Si es así, comprueba el firewall (`ufw status`) de la máquina correspondiente.
* **Problema de Handshake DB:** Asegúrate de que el usuario `haproxy_check` existe en MariaDB y tiene permisos desde la IP `192.168.10.10`.