===
UPS
===

La gestión de un sistema de alimentación ininterrumpida (UPS - Alimentación Ininterrumpida Suministro) conectado al servidor se le asigna al NUT (Herramientas de UPS de red), la cual realizará una parada en caso de ausencia de potencia. NUT soporta diferentes modelos de UPS, conectados mediante un cable serie o USB.

En este panel se realiza la configuración de los NUT, para ver datos del SAI, utilice el Panel de control .

Habilitar NUT UPS
    Activar o desactivar el servicio NUT.

Modo
====

Maestro
    Este modo debe seleccionarse si el SAI está conectado al servidor directamente a través de serie o cable USB.

Buscar controladores para el modelo
    Le permite buscar un controlador compatible con tu modelo de UPS. Después de seleccionar el modelo de la lista , el campo *Controlador* se va a rellenar con el nombre del controlador adecuado.

Controlador
    El controlador que se utilizará para el modelo de UPS conectado.

Conexión USB
    Seleccione esta opción si el SAI está conectado a través de USB.

Conexión en serie
    Seleccione esta opción si el SAI está conectado a través de cable serie al servidor.

Esclavo
    Este modo se debe utilizar si el SAI no está conectado directamente en el servidor, pero a otro servidor configurado con NUT en el modo Maestro al que se conectará el servidor.

Dirección del servidor maestro
    Dirección IP o nombre de host del servidor maestro. El cliente utilizará el usuario *UPS* para conectar con el servidor maestro. Asegúrese de que el usuario se configura en el servidor maestro.

Contraseña
    La contraseña que especifique aquí es la configurada en el servidor maestro para las conexiones de esclavos.
