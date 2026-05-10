# PokePimas: Infraestructura de Alta Disponibilidad y Monitorización

## 1. Introducción y Propósito
**PokePimas** es un proyecto de fin de grado (TFG) que implementa una infraestructura empresarial robusta para una plataforma de gestión y venta de cartas. El objetivo principal es garantizar que el servicio nunca se interrumpa, utilizando técnicas de balanceo de carga, replicación de datos en tiempo real, supervisión y monitorización proactiva.

---

## 2. Topología de Red y Segmentación
La infraestructura se despliega sobre un entorno virtualizado con **VirtualBox**, segmentado mediante un firewall **pfSense** en tres zonas de seguridad:

| Zona | Subred | Función | Componentes |
| :--- | :--- | :--- | :--- |
| **WAN** | DHCP | Salida a Internet | Puerta de enlace pfSense |
| **DMZ** | 192.168.10.0/24 | Servicios expuestos | HAProxy, Web-01, Web-02 |
| **LAN** | 192.168.20.0/24 | Datos y Gestión | DB-01, DB-02, Zabbix Server |

---

## 3. Guía de Importación del Servicio Virtualizado (.ova)
Todo el proyecto (las 6 máquinas virtuales y sus configuraciones de red) ha sido empaquetado en un único archivo *Appliance* `.ova` para facilitar su despliegue y evaluación.

### Pasos para la importación en VirtualBox:
1. Abre **VirtualBox**.
2. Dirígete al menú superior y haz clic en **Archivo > Importar servicio virtualizado...** (o *Import Appliance*).
3. Selecciona el archivo `.ova` del proyecto PokePimas.
4. En la pantalla de ajustes de importación, revisa la opción **Política de direcciones MAC**. Es **CRÍTICO** que selecciones la opción: `Incluir todas las direcciones MAC de los adaptadores de red`. 
   > *Nota técnica:* Si no se incluyen las MAC originales, el sistema operativo de pfSense detectará las tarjetas de red como hardware nuevo y desconfigurará las interfaces WAN/LAN, rompiendo el enrutamiento de todo el clúster.
5. Haz clic en **Importar** y espera a que el proceso termine (puede tardar varios minutos dependiendo del disco duro).

---

## 4. Secuencia de Arranque (¡Muy Importante!)
Para que la infraestructura de alta disponibilidad funcione correctamente sin lanzar falsos positivos de caída de servicio, las máquinas **deben encenderse en el siguiente orden estricto**:

1. **pfSense (Enrutador y Firewall):** Inícialo primero. Espera a que termine de cargar y muestre el menú de consola (1-16) con las IPs asignadas. Esto garantiza que la red interna tiene enrutamiento y las reglas de firewall están activas.
2. **DB-01 y DB-02 (Clúster de Datos y Zabbix):** Enciende ambas máquinas. Al arrancar juntas, el motor MariaDB sincronizará la replicación Master-Master. Además, el servicio Zabbix Server alojado en la DB-01 se activará, quedando a la escucha de los agentes.
3. **Web-01 y Web-02 (IIS y PHP):** Arranca los Windows Server. Al iniciar sus servicios web, buscarán inmediatamente conectarse a la base de datos (que ya está operativa gracias al paso 2).
4. **HAProxy (Balanceador de carga):** Enciéndelo en último lugar. Nada más arrancar, el balanceador lanzará sus *health checks* (comprobaciones de estado) contra las IPs de los servidores web. Como estos ya están listos, el HAProxy los marcará en verde y comenzará a admitir y balancear tráfico en el puerto 80.

---

## 5. Arquitectura Detallada de los Servicios

### A. El Clúster de Datos (MariaDB Master-Master)
La base de datos es el corazón del proyecto y se basa en una replicación circular y redundante:
- **Nodos:** DB-01 (192.168.20.21) y DB-02 (192.168.20.22).
- **Funcionamiento:** Cualquier cambio en la base de datos `pimas` de un nodo se refleja instantáneamente en el otro mediante *Binary Logs* y *GTIDs*. Esto significa que si un nodo se destruye, los datos están seguros en el otro.
- **Seguridad:** Se ha implementado el principio de menor privilegio creando el usuario `user_web@'%'`, el cual solo permite conexiones desde la subred de los servidores web para la gestión del catálogo.

### B. Servidores Web (Windows Server 2019 + IIS)
Los servidores de aplicaciones procesan el frontend y el backend de la tienda de cartas:
- **Entorno:** Se utiliza un entorno WIMP (Windows, IIS, MariaDB, PHP).
- **Configuración PHP:** En el archivo `php.ini` se han habilitado las extensiones `mysqli` y `pdo_mysql` indicando la ruta correcta en `extension_dir`.
- **Conectividad:** Los scripts PHP conectan al backend de datos a través del puerto 3306, procesando las solicitudes de los usuarios y devolviendo el HTML generado.

### C. Balanceo de Carga (HAProxy)
Ubicado en la DMZ, el **HAProxy** (192.168.10.10) actúa como punto de entrada único para los clientes.
- **Algoritmo:** Utiliza *Round Robin* para distribuir la carga de usuarios de forma equitativa entre Web-01 y Web-02.
- **Tolerancia a fallos:** Si un Windows Server se apaga o su servicio IIS se cuelga, HAProxy detecta el fallo en segundos, lo saca de la rotación de tráfico y redirige a todos los usuarios a la máquina superviviente de forma transparente.
- **Estadísticas:** Panel de control en tiempo real accesible desde el navegador en `http://192.168.10.10:8080/stats`.

---

## 6. Monitorización Centralizada (Zabbix 6.0)
El estado de la infraestructura completa se vigila mediante **Zabbix**:
- **Servidor:** Instalado de forma optimizada junto a la DB-01 para reducir latencias en la base de datos de monitorización.
- **Agentes:** Desplegados como servicios de sistema en todos los nodos (tanto los Ubuntu Server como los Windows Server).
- **Configuración de Red:** Debido a la segmentación del pfSense, los agentes están configurados con la directiva `Server=0.0.0.0/0` (modo permisivo de laboratorio) para asegurar que los paquetes de telemetría (CPU, RAM, Red) lleguen al servidor a través del firewall sin bloqueos de NAT.

---

## 7. Mantenimiento y Troubleshooting

### Errores Comunes
* **La web muestra "Could not find driver":** PHP en el Windows Server no encuentra las librerías. Verificar que el `PATH` del sistema incluya la carpeta de PHP y que se haya reiniciado el servicio con el comando `iisreset`.
* **Equipo en rojo en Zabbix:** Comprobar que el firewall de la máquina (UFW en Linux, Windows Defender Firewall en Windows) tiene abierto el puerto TCP **10050** y que el campo `Hostname` del agente coincide exactamente con el registrado en el panel web.
* **Replicación de DB caída:** Ejecutar `SHOW SLAVE STATUS\G` en MariaDB. Comprobar que `Slave_IO_Running` y `Slave_SQL_Running` estén en "Yes".

### Credenciales del Entorno (Laboratorio)
| Sistema | Usuario | Contraseña |
| :--- | :--- | :--- |
| **pfSense (Web/Consola)** | admin | Abc1234 |
| **Panel Zabbix** | Admin | zabbix |
| **Panel HAProxy Stats** | admin | admin |
| **Base de Datos (Conexión Web)** | user_web | Abc1234 |
| **Servidores Ubuntu**| gabriel | Abc1234 |
| **Servidores Windows** | administrador | Abc1234 |
---
**Proyecto desarrollado por:** Gabriel Abellán - Ciclo Formativo de Grado Superior ASIR.