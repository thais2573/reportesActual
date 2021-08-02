<?php
/**
 * Conexion a BD y extranccion de datos para el reporte
 */
$coneccion=new \PDO('mysql:host=192.168.107.14 ;dbname=bugtracker', 'ums', 'Diario2019**');

/*
 * Generar consulta dinamica segun los campos seleccionados (esto es la bomba)
 * */
/*FROM and INNER JOIN basico para funcionamiento*/
$form = " FROM cpuChasis INNER JOIN inventario ON cpuChasis.t_bug_id = inventario.t_bug_id";
$select = "cpuChasis.numInventario, inventario.nombreRed";
$where = "";
$columnas = array('No. Inventario', 'Nombre Red');
$columSelect = array('numInventario', 'nombreRed');
if(array_key_exists("cpuChasisPartesInventario", $_POST)) {
  $modeloDatosCPUChasis = $_POST["modeloDatosCPUChasis"];
  $colorDatosCPUChasis = $_POST["colorDatosCPUChasis"];
//  $inventarioDatosCPUChasis = $_POST["inventarioDatosCPUChasis"];
  $selloSegDatosCPUChasis = $_POST["selloSegDatosCPUChasis"];
  if($_POST["fechaMantDatosCPUChasis"]==="")
    $fechaMantDatosCPUChasis = "";
  else{
    $fechaMantDatosCPUChasis = new DateTime($_POST["fechaMantDatosCPUChasis"]);
    $fechaMantDatosCPUChasis = $fechaMantDatosCPUChasis->format('Y-m-d');
  }

  if($modeloDatosCPUChasis !== "Seleccione") {
    $select .= ",cpuChasis.modelo";
    $where .= "cpuChasis.modelo = '$modeloDatosCPUChasis'";
    $columnas[] = 'Modelo CPU';
    $columSelect[] = 'modelo';
  }
  if($colorDatosCPUChasis !== "") {
    $select .= ",cpuChasis.color";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "cpuChasis.color LIKE "."'%".$colorDatosCPUChasis."%'";
    $columnas[] = 'Color CPU';
    $columSelect[] = 'color';
  }
  if($selloSegDatosCPUChasis !== "") {
    $select .= ",cpuChasis.selloSeguridad";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "cpuChasis.selloSeguridad LIKE "."'%".$selloSegDatosCPUChasis."%'";
    $columnas[] = 'Sello CPU';
    $columSelect[] = 'selloSeguridad';
  }
  if($fechaMantDatosCPUChasis !== "") {
    $select .= ",cpuChasis.fechaMantenimiento";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "cpuChasis.fechaMantenimiento = '$fechaMantDatosCPUChasis'";
    $columnas[] = 'Fecha Mtto.';
    $columSelect[] = 'fechaMantenimiento';
  }
}

if(array_key_exists("fuentePartesInventario", $_POST)) {
  $form .= " INNER JOIN fuente ON inventario.t_bug_id = fuente.t_bug_id";
  $marcaDatosFuente = $_POST["marcaDatosFuente"];
  $serieDatosFuente = $_POST["serieDatosFuente"];
  $sataDatosFuente = $_POST["sataDatosFuente"];
  $wattsDatosFuente = $_POST["wattsDatosFuente"];

  if($marcaDatosFuente !== "") {
    $select .= ",fuente.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "fuente.marca LIKE "."'%".$marcaDatosFuente."%'";
    $columnas[] = 'Marca Fuente';
  }
  if($serieDatosFuente !== "") {
    $select .= ",fuente.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "fuente.serie LIKE "."'%".$serieDatosFuente."%'";
    $columnas[] = 'Serie Fuente';
  }
  if($sataDatosFuente !== "Seleccione") {
    $select .= ",fuente.sata";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "fuente.sata = ".'$sataDatosFuente';
    $columnas[] = 'Sata Fuente';
  }
  if($wattsDatosFuente !== "") {
    $select .= ",fuente.watts";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "fuente.watts LIKE "."'%".$wattsDatosFuente."%'";
    $columnas[] = 'Watts';
  }
}

if(array_key_exists("motherboardPartesInventario", $_POST)) {
  $form .= " INNER JOIN motherboard ON inventario.t_bug_id = motherboard.t_bug_id";
  $marcaDatosMotherBoard = $_POST["marcaDatosMotherBoard"];
  $modeloDatosMotherBoard = $_POST["modeloDatosMotherBoard"];
  $serieDatosMotherBoard = $_POST["serieDatosMotherBoard"];
  $lgaDatosMotherBoard = $_POST["lgaDatosMotherBoard"];
  $ramDatosMotherBoard = $_POST["ramDatosMotherBoard"];
  $ranuraVideoDatosMotherBoard = $_POST["ranuraVideoDatosMotherBoard"];

  if($marcaDatosMotherBoard !== "Seleccione") {
    $select .= ",motherboard.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "motherboard.marca = '$marcaDatosMotherBoard'";
    $columnas[] = 'Marca Board';
    $columSelect[] = 'marca';
  }
  if($modeloDatosMotherBoard !== "") {
    $select .= ",motherboard.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "motherboard.modelo LIKE "."'%".$modeloDatosMotherBoard."%'";
    $columnas[] = 'Modelo Board';
    $columSelect[] = 'modelo';
  }
  if($serieDatosMotherBoard !== "") {
    $select .= ",motherboard.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "motherboard.serie LIKE "."'%".$serieDatosMotherBoard."%'";
    $columnas[] = 'Serie Board';
    $columSelect[] = 'serie';
  }
  if($lgaDatosMotherBoard !== "Seleccione") {
    $select .= ",motherboard.lga";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "motherboard.lga = '$lgaDatosMotherBoard'";
    $columnas[] = 'LGA';
    $columSelect[] = 'lga';
  }
  if($ramDatosMotherBoard !== "Seleccione") {
    $select .= ",motherboard.ram";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "motherboard.ram = '$ramDatosMotherBoard'";
    $columnas[] = 'Tipo RAM';
    $columSelect[] = 'lga';
  }
  if($ranuraVideoDatosMotherBoard !== "Seleccione") {
    $select .= ",motherboard.ranuraVideo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "motherboard.ranuraVideo = '$ranuraVideoDatosMotherBoard'";
    $columnas[] = 'Ranura Video';
    $columSelect[] = 'ranuraVideo';
  }
}

if(array_key_exists("microprocesadorPartesInventario", $_POST)) {
  $form .= " INNER JOIN microprocesador ON inventario.t_bug_id = microprocesador.t_bug_id";
  $tipoDatosMicroprocesador = $_POST["tipoDatosMicroprocesador"];
  $lgaDatosMicroprocesador = $_POST["lgaDatosMicroprocesador"];
  $velocidadDatosMicroprocesador = $_POST["velocidadDatosMicroprocesador"];
  $serieDatosMicroprocesador = $_POST["serieDatosMicroprocesador"];

  if($tipoDatosMicroprocesador !== "Seleccione") {
    $select .= ",microprocesador.tipo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "microprocesador.tipo = '$tipoDatosMicroprocesador'";
    $columnas[] = 'Tipo Micro';
  }
  if($lgaDatosMicroprocesador !== "Seleccione") {
    $select .= ",microprocesador.lga";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "microprocesador.lga = '$lgaDatosMicroprocesador'";
    $columnas[] = 'LGA Micro';
  }
  if($velocidadDatosMicroprocesador !== "") {
    $select .= ",microprocesador.velocidad";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "microprocesador.velocidad LIKE "."'%".$velocidadDatosMicroprocesador."%'";
    $columnas[] = 'MHz';
  }
  if($serieDatosMicroprocesador !== "") {
    $select .= ",microprocesador.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "microprocesador.serie LIKE "."'%".$serieDatosMicroprocesador."%'";
    $columnas[] = 'Serie Micro';
  }
}

if(array_key_exists("ramPartesInventario", $_POST)) {
  $form .= " INNER JOIN ram ON inventario.t_bug_id = ram.t_bug_id";
  $marcaDatosRAM = $_POST["marcaDatosRAM"];
  $capacidadDatosRAM = $_POST["capacidadDatosRAM"];
  $serieDatosRAM = $_POST["serieDatosRAM"];

  if($marcaDatosRAM !== "") {
    $select .= ",ram.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "ram.marca LIKE "."'%".$marcaDatosRAM."%'";
    $columnas[] = 'Marca RAM';
  }
  if($capacidadDatosRAM !== "") {
    $select .= ",ram.capacidad";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "ram.capacidad LIKE "."'%".$capacidadDatosRAM."%'";
    $columnas[] = 'Capacidad RAM';
  }
  if($serieDatosRAM !== "") {
    $select .= ",ram.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "ram.serie LIKE "."'%".$serieDatosRAM."%'";
    $columnas[] = 'Serie RAM';
  }
}

if(array_key_exists("tarjetaVideoPartesInventario", $_POST)) {
  $form .= " INNER JOIN tarjetaVideo ON inventario.t_bug_id = tarjetaVideo.t_bug_id";
  $ranuraVideoDatosTarjetaVideo = $_POST["ranuraVideoDatosTarjetaVideo"];
  $marcaDatosTarjetaVideo = $_POST["marcaDatosTarjetaVideo"];
  $velocidadDatosTarjetaVideo = $_POST["velocidadDatosTarjetaVideo"];
  $serieDatosTarjetaVideo = $_POST["serieDatosTarjetaVideo"];

  if($ranuraVideoDatosTarjetaVideo !== "Seleccione") {
    $select .= ",tarjetaVideo.ranuraVideo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "tarjetaVideo.ranuraVideo = '$ranuraVideoDatosTarjetaVideo'";
    $columnas[] = 'Tipo Ranura Targ.Video';
  }
  if($marcaDatosTarjetaVideo !== "Seleccione") {
    $select .= ",tarjetaVideo.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "tarjetaVideo.marca = '$marcaDatosTarjetaVideo'";
    $columnas[] = 'Marca Targ.Video';
  }
  if($velocidadDatosTarjetaVideo !== "") {
    $select .= ",tarjetaVideo.velocidad";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "tarjetaVideo.velocidad LIKE "."'%".$velocidadDatosTarjetaVideo."%'";
    $columnas[] = 'Velocidad Targ.Video';
  }
  if($serieDatosTarjetaVideo !== "") {
    $select .= ",tarjetaVideo.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "tarjetaVideo.serie LIKE "."'%".$serieDatosTarjetaVideo."%'";
    $columnas[] = 'Serie Targ.Video';
  }
}

if(array_key_exists("HDDPartesInventario", $_POST)) {
  $form .= " INNER JOIN hdd ON inventario.t_bug_id = hdd.t_bug_id";
  $marcaDatosHDD = $_POST["marcaDatosHDD"];
  $serieDatosHDD = $_POST["serieDatosHDD"];
  $capacidadDatosHDD = $_POST["capacidadDatosHDD"];
  $sataDatosHDD = $_POST["sataDatosHDD"];

  if($marcaDatosHDD !== "Seleccione") {
    $select .= ",hdd.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "hdd.marca = '$marcaDatosHDD'";
    $columnas[] = 'Marca HDD';
  }
  if($serieDatosHDD !== "") {
    $select .= ",hdd.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "hdd.serie LIKE "."'%".$serieDatosHDD."%'";
    $columnas[] = 'Serie HDD';
  }
  if($capacidadDatosHDD !== "") {
    $select .= ",hdd.capacidad";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "hdd.capacidad LIKE "."'%".$capacidadDatosHDD."%'";
    $columnas[] = 'Capacidad HDD';
  }
  if($sataDatosHDD !== "Seleccione") {
    $select .= ",hdd.sata";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "hdd.sata = '$sataDatosHDD'";
    $columnas[] = 'Sata HDD';
  }
}

if(array_key_exists("lectorPartesInventario", $_POST)) {
  $form .= " INNER JOIN lector ON inventario.t_bug_id = lector.t_bug_id";
  $tipoDatosLector = $_POST["tipoDatosLector"];
  $marcaDatosLector = $_POST["marcaDatosLector"];
  $serieDatosLector = $_POST["serieDatosLector"];

  if($tipoDatosLector !== "Seleccione") {
    $select .= ",lector.tipo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "lector.tipo = '$tipoDatosLector'";
    $columnas[] = 'Tipo Lector';
  }
  if($marcaDatosLector !== "Seleccione") {
    $select .= ",lector.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "lector.marca = '$marcaDatosLector'";
    $columnas[] = 'Marca Lector';
  }
  if($serieDatosLector !== "") {
    $select .= ",lector.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "lector.serie LIKE "."'%".$serieDatosLector."%'";
    $columnas[] = 'Serie Lector';
  }
}

if(array_key_exists("mousePartesInventario", $_POST)) {
  $form .= " INNER JOIN mouse ON inventario.t_bug_id = mouse.t_bug_id";
  $marcaDatosMouse = $_POST["marcaDatosMouse"];
  $serieDatosMouse = $_POST["serieDatosMouse"];
  $modeloDatosMouse = $_POST["modeloDatosMouse"];
  $opticoDatosMouse = $_POST["opticoDatosMouse"];
  $conectorDatosMouse = $_POST["conectorDatosMouse"];

  if($marcaDatosMouse !== "Seleccione") {
    $select .= ",mouse.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "mouse.marca = '$marcaDatosMouse'";
    $columnas[] = 'Marca Mouse';
  }
  if($serieDatosMouse !== "") {
    $select .= ",mouse.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "mouse.serie LIKE "."'%".$serieDatosMouse."%'";
    $columnas[] = 'Serie Mouse';
  }
  if($modeloDatosMouse !== "") {
    $select .= ",mouse.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "mouse.modelo LIKE "."'%".$modeloDatosMouse."%'";
    $columnas[] = 'Modelo Mouse';
  }
  if($opticoDatosMouse !== "Seleccione") {
    $select .= ",mouse.optico";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "mouse.optico = '$opticoDatosMouse'";
    $columnas[] = 'Mouse Optico';
  }
  if($conectorDatosMouse !== "Seleccione") {
    $select .= ",mouse.conector";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "mouse.conector = '$conectorDatosMouse'";
    $columnas[] = 'Tipo Conector Mouse';
  }
}

if(array_key_exists("tecladoPartesInventario", $_POST)) {
  $form .= " INNER JOIN teclado ON inventario.t_bug_id = teclado.t_bug_id";
  $marcaDatosTeclado = $_POST["marcaDatosTeclado"];
  $serieDatosTeclado = $_POST["serieDatosTeclado"];
  $modeloDatosTeclado = $_POST["modeloDatosTeclado"];
  $conectorDatosTeclado = $_POST["conectorDatosTeclado"];

  if($marcaDatosTeclado !== "Seleccione") {
    $select .= ",teclado.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "teclado.marca = '$marcaDatosTeclado'";
    $columnas[] = 'Marca Teclado';
  }
  if($serieDatosTeclado !== "") {
    $select .= ",teclado.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "teclado.serie LIKE "."'%".$serieDatosTeclado."%'";
    $columnas[] = 'Serie Teclado';
  }
  if($modeloDatosTeclado !== "") {
    $select .= ",teclado.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "teclado.modelo LIKE "."'%".$modeloDatosTeclado."%'";
    $columnas[] = 'Modelo Teclado';
  }
  if($conectorDatosTeclado !== "") {
    $select .= ",teclado.conector";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "teclado.conector = ".$conectorDatosTeclado;
    $columnas[] = 'Tipo Conector Teclado';
  }
}

if(array_key_exists("bocinasPartesInventario", $_POST)) {
  $form .= " INNER JOIN bocinas ON inventario.t_bug_id = bocinas.t_bug_id";
  $marcaDatosBocinas = $_POST["marcaDatosBocinas"];
  $serieDatosBocinas = $_POST["serieDatosBocinas"];
  $modeloDatosBocinas = $_POST["modeloDatosBocinas"];

  if($marcaDatosBocinas !== "Seleccione") {
    $select .= ",bocinas.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "bocinas.marca = '$marcaDatosBocinas'";
    $columnas[] = 'Marca Bocinas';
  }
  if($serieDatosBocinas !== "") {
    $select .= ",bocinas.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "bocinas.serie LIKE "."'%".$serieDatosBocinas."%'";
    $columnas[] = 'Serie Bocinas';
  }
  if($modeloDatosBocinas !== "") {
    $select .= ",bocinas.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "bocinas.modelo LIKE "."'%".$modeloDatosBocinas."%'";
    $columnas[] = 'Modelo Bocinas';
  }
}

if(array_key_exists("monitorPartesInventario", $_POST)) {
  $form .= " INNER JOIN monitor ON inventario.t_bug_id = monitor.t_bug_id";
  $marcaDatosMonitor = $_POST["marcaDatosMonitor"];
  $serieDatosMonitor = $_POST["serieDatosMonitor"];
  $modeloDatosMonitor = $_POST["modeloDatosMonitor"];
  $inventarioDatosMonitor = $_POST["inventarioDatosMonitor"];
  $LCDDatosMonitor = $_POST["LCDDatosMonitor"];

  if($marcaDatosMonitor !== "Seleccione") {
    $select .= ",monitor.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "monitor.marca = '$marcaDatosMonitor'";
    $columnas[] = 'Marca Monitor';
  }
  if($serieDatosMonitor !== "") {
    $select .= ",monitor.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "monitor.serie LIKE "."'%".$serieDatosMonitor."%'";
    $columnas[] = 'Serie Monitor';
  }
  if($modeloDatosMonitor !== "") {
    $select .= ",monitor.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "monitor.modelo LIKE "."'%".$modeloDatosMonitor."%'";
    $columnas[] = 'Modelo Monitor';
  }
  if($inventarioDatosMonitor !== "") {
    $select .= ",monitor.numInventario";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "monitor.numInventario LIKE "."'%".$inventarioDatosMonitor."%'";
    $columnas[] = 'No. Inv. Monitor';
  }
  if($LCDDatosMonitor !== "Seleccione") {
    $select .= ",monitor.lcd";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "monitor.lcd = '$LCDDatosMonitor'";
    $columnas[] = 'Monitor LCD';
  }
}

if(array_key_exists("backupPartesInventario", $_POST)) {
  $form .= " INNER JOIN `backup` ON inventario.t_bug_id = `backup`.t_bug_id";
  $marcaDatosBackup = $_POST["marcaDatosBackup"];
  $serieDatosBackup = $_POST["serieDatosBackup"];
  $modeloDatosBackup = $_POST["modeloDatosBackup"];
  $inventarioDatosBackup = $_POST["inventarioDatosBackup"];
  $selloDatosBackup = $_POST["selloDatosBackup"];
  $capacidadDatosBackup = $_POST["capacidadDatosBackup"];

  if($marcaDatosBackup !== "Seleccione") {
    $select .= ",`backup`.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "`backup`.marca = '$marcaDatosBackup'";
    $columnas[] = 'Marca Backup';
  }
  if($serieDatosBackup !== "") {
    $select .= ",`backup`.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "`backup`.serie LIKE "."'%".$serieDatosBackup."%'";
    $columnas[] = 'Serie Backup';
  }
  if($modeloDatosBackup !== "") {
    $select .= ",`backup`.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "`backup`.modelo LIKE "."'%".$modeloDatosBackup."%'";
    $columnas[] = 'Modelo Backup';
  }
  if($inventarioDatosBackup !== "") {
    $select .= ",`backup`.numInventario";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "`backup`.numInventario LIKE "."'%".$inventarioDatosBackup."%'";
    $columnas[] = 'No. Inv. Backup';
  }
  if($selloDatosBackup !== "") {
    $select .= ",`backup`.sello";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "`backup`.sello LIKE "."'%".$selloDatosBackup."%'";
    $columnas[] = 'Sello Backup';
  }
  if($capacidadDatosBackup !== "") {
    $select .= ",`backup`.capacidad";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "`backup`.capacidad LIKE "."'%".$capacidadDatosBackup."%'";
    $columnas[] = 'Capacidad Backup';
  }
}

if(array_key_exists("estabilizadorPartesInventario", $_POST)) {
  $form .= " INNER JOIN estabilizador ON inventario.t_bug_id = estabilizador.t_bug_id";
  $marcaDatosEstabilizador = $_POST["marcaDatosEstabilizador"];
  $serieDatosEstabilizador = $_POST["serieDatosEstabilizador"];
  $modeloDatosEstabilizador = $_POST["modeloDatosEstabilizador"];
  $inventarioDatosEstabilizador = $_POST["inventarioDatosEstabilizador"];
  $capacidadDatosEstabilizador = $_POST["capacidadDatosEstabilizador"];

  if($marcaDatosEstabilizador !== "Seleccione") {
    $select .= ",estabilizador.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "estabilizador.marca = '$marcaDatosEstabilizador'";
    $columnas[] = 'Marca Estabilizador';
  }
  if($serieDatosEstabilizador !== "") {
    $select .= ",estabilizador.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "estabilizador.serie LIKE "."'%".$serieDatosEstabilizador."%'";
    $columnas[] = 'Serie Estabilizador';
  }
  if($modeloDatosEstabilizador !== "") {
    $select .= ",estabilizador.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "estabilizador.modelo LIKE "."'%".$modeloDatosEstabilizador."%'";
    $columnas[] = 'Modelo Estabilizador';
  }
  if($inventarioDatosEstabilizador !== "") {
    $select .= ",estabilizador.numInventario";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "estabilizador.numInventario LIKE "."'%".$inventarioDatosEstabilizador."%'";
    $columnas[] = 'No. Inv. Estabilizador';
  }
  if($capacidadDatosEstabilizador !== "") {
    $select .= ",estabilizador.capacidad";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "estabilizador.capacidad LIKE "."'%".$capacidadDatosEstabilizador."%'";
    $columnas[] = 'Capacidad Estabilizador';
  }
}

if(array_key_exists("scannerPartesInventario", $_POST)) {
  $form .= " INNER JOIN scanner ON inventario.t_bug_id = scanner.t_bug_id";
  $marcaDatosScanner = $_POST["marcaDatosScanner"];
  $serieDatosScanner = $_POST["serieDatosScanner"];
  $modeloDatosScanner = $_POST["modeloDatosScanner"];
  $inventarioDatosScanner = $_POST["inventarioDatosScanner"];
  $tipoDatosScanner = $_POST["tipoDatosScanner"];

  if($marcaDatosScanner !== "Seleccione") {
    $select .= ",scanner.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "scanner.marca = '$marcaDatosScanner'";
    $columnas[] = 'Marca Scanner';
  }
  if($serieDatosScanner !== "") {
    $select .= ",scanner.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "scanner.serie LIKE "."'%".$serieDatosScanner."%'";
    $columnas[] = 'Serie Scanner';
  }
  if($modeloDatosScanner !== "") {
    $select .= ",scanner.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "scanner.modelo LIKE "."'%".$modeloDatosScanner."%'";
    $columnas[] = 'Modelo Scanner';
  }
  if($inventarioDatosScanner !== "") {
    $select .= ",scanner.numInventario";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "scanner.numInventario LIKE "."'%".$inventarioDatosScanner."%'";
    $columnas[] = 'No. Inv. Scanner';
  }
  if($tipoDatosScanner !== "") {
    $select .= ",scanner.tipo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "scanner.tipo LIKE "."'%".$tipoDatosScanner."%'";
    $columnas[] = 'Tipo Scanner';
  }
}

if(array_key_exists("impresoraPartesInventario", $_POST)) {
  $form .= " INNER JOIN impresora ON inventario.t_bug_id = impresora.t_bug_id";
  $marcaDatosImpresora = $_POST["marcaDatosImpresora"];
  $serieDatosImpresora = $_POST["serieDatosImpresora"];
  $modeloDatosImpresora = $_POST["modeloDatosImpresora"];
  $inventarioDatosImpresora = $_POST["inventarioDatosImpresora"];
  $tipoDatosImpresora = $_POST["tipoDatosImpresora"];
  $tonerCartuchoDatosImpresora = $_POST["tonerCartuchoDatosImpresora"];
  $tipoTonerCartuchoDatosImpresora = $_POST["tipoTonerCartuchoDatosImpresora"];

  if($marcaDatosImpresora !== "") {
    $select .= ",impresora.marca";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "impresora.marca LIKE "."'%".$marcaDatosImpresora."%'";
    $columnas[] = 'Marca Impresora';
  }
  if($serieDatosImpresora !== "") {
    $select .= ",impresora.serie";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "impresora.serie LIKE "."'%".$serieDatosImpresora."%'";
    $columnas[] = 'Serie Impresora';
  }
  if($modeloDatosImpresora !== "") {
    $select .= ",impresora.modelo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "impresora.modelo LIKE "."'%".$modeloDatosImpresora."%'";
    $columnas[] = 'Modelo Impresora';
  }
  if($inventarioDatosImpresora !== "") {
    $select .= ",impresora.numInventario";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "impresora.numInventario LIKE "."'%".$inventarioDatosImpresora."%'";
    $columnas[] = 'No. Inv. Impresora';
  }
  if($tipoDatosImpresora !== "Seleccione") {
    $select .= ",impresora.tipo";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "impresora.tipo = '$tipoDatosImpresora'";
    $columnas[] = 'Tipo Impresora';
  }
  if($tonerCartuchoDatosImpresora !== "Seleccione") {
    $select .= ",impresora.tonerCartucho";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "impresora.tonerCartucho = '$tonerCartuchoDatosImpresora'";
    $columnas[] = 'Toner o Cartucho Impresora';
  }
  if($tipoTonerCartuchoDatosImpresora !== "") {
    $select .= ",impresora.tipoTonerCartucho";
    if(strlen($where) > 1)
      $where .= ' AND ';
    $where .= "impresora.tipoTonerCartucho LIKE "."'%".$tipoTonerCartuchoDatosImpresora."%'";
    $columnas[] = 'Tipo Toner o Cartucho Impresora';
  }
}

$query = $coneccion->prepare("SELECT $select $form WHERE $where ORDER BY inventario.nombreRed");
$query->execute();
$resultado =  $query->fetchAll();
$cont = 1;
?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <style type="text/css">
    td{
      border: 0.1px black solid;
    }
    table{
      border-spacing: 0px;
      font-size: 11px;
    }
  </style>
</head>
<body>
<img src="sistema_reportes.jpg" width="100%"/>
<br>
<br>
<table border="1" cellpadding="1" cellspacing="1" style="width:100%">
  <tbody>
  <tr >
<!--    <td colspan="9" style="text-align: center; ; font-weight: bold; font-size: x-large">-->
    <?php
    $long = count($columSelect) + 3;
    echo ('<td colspan="'.$long.'" style="text-align: center; ; font-weight: bold; font-size: x-large">');
    ?>
      <strong>
        <?php $fecha = getdate(); ?>
        <?php echo $fecha[mday]; ?>-<?php echo $fecha[mon]; ?>-<?php echo $fecha[year]; ?>
                                   Partes y Piezas
      </strong>
    </td>
  </tr>
  <?php
  echo('<tr>');
  echo('<td style="text-align: center; font-weight: bold; font-size: large">No</td>');
  foreach($columnas as $col) {
    echo('<td style="text-align: center; font-weight: bold; font-size: large">'.$col.'</td>');
  }
  echo('</tr>');

  foreach($resultado as $datos) {
    echo('<tr>');
    echo('<td>'.$cont++.'</td>');
    for($i = 0; $i < $iMax = count($columSelect)+2; $i++) {
      echo('<td style="font-size: 14px">'.$datos[$i].'</td>');
    }
    echo('</tr>');
  }
  ?>

  </tbody>
</table>
