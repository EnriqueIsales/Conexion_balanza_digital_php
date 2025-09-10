Conexion_balanza_digital_php
Este es un proyecto simple en PHP que demuestra cómo establecer una conexión y comunicación con una balanza digital a través del puerto serial (COM). El objetivo es leer el peso que la balanza está midiendo y mostrarlo en una interfaz web. Este proyecto es útil como punto de partida para integraciones más complejas con sistemas de inventario, puntos de venta (POS) o cualquier otra aplicación que requiera datos de pesaje en tiempo real.

Características
Conexión con balanza digital a través de puerto COM.

Lectura del peso en tiempo real.

Interfaz web simple para mostrar el dato del peso.

Código PHP fácil de entender y modificar.

Requisitos
Para que este proyecto funcione, necesitas lo siguiente:

Servidor web: Un servidor web como Apache, Nginx o cualquier otro que soporte PHP.

PHP: Versión 7.0 o superior.

Módulo de PHP: La extensión php_serial es necesaria para la comunicación serial. Puedes encontrarla en diversas fuentes en línea.

Balanza digital: Una balanza que pueda conectarse a una computadora a través de un puerto serial (COM) o un adaptador USB a serial.

Puerto serial: Un puerto COM disponible en tu computadora.

Instalación y Configuración
Sigue estos pasos para poner en marcha el proyecto:

Clona el repositorio:

Bash

git clone https://github.com/EnriqueIsales/Conexion_balanza_digital_php.git
Mueve los archivos: Copia los archivos del repositorio en el directorio raíz de tu servidor web (por ejemplo, htdocs en XAMPP o www en WampServer).

Configura el módulo de PHP: Asegúrate de que la extensión php_serial esté correctamente instalada y habilitada en tu archivo php.ini.

Ajusta el código: Abre el archivo principal de PHP y modifica la configuración del puerto COM para que coincida con el puerto al que está conectada tu balanza.

PHP

$serial = new phpSerial();
$serial->deviceSet("COM3"); // Reemplaza "COM3" con el puerto de tu balanza
Abre la página web: Inicia tu servidor web y abre la página en tu navegador para ver el peso en tiempo real.

Uso
Una vez configurado, simplemente abre la página en tu navegador y la balanza comenzará a enviar datos. El peso se actualizará automáticamente en la interfaz web.

Contribuciones
Las contribuciones son bienvenidas. Si encuentras un error o tienes una mejora, por favor, abre un issue o envía un pull request.

Licencia
Este proyecto está bajo la Licencia MIT. Para más detalles, consulta el archivo LICENSE.
