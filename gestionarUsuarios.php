<?php

 function alta_usuario($conexion,$nuevoUsuario) { 
	$fechaNacimiento = date('d/m/Y', strtotime($nuevoUsuario["fechaNacimientoUsuario"]));
	 
    try {
		$stmt = $conexion->prepare("CALL INSERTAR_USUARIOS(:dniUsuario,:nombreCompletoUsuario,:nickUsuario,:emailUsuario,:fechaNacimientoUsuario,:numTelefonoUsuario,:passUsuario,:confirmPassUsuario)");
		$stmt->bindParam(':dniUsuario',$nuevoUsuario['dniUsuario']);
		$stmt->bindParam(':nombreCompletoUsuario',$nuevoUsuario['nombreCompletoUsuario']);
		$stmt->bindParam(':nickUsuario',$nuevoUsuario['nickUsuario']);
		$stmt->bindParam(':emailUsuario',$nuevoUsuario['emailUsuario']);
		$stmt->bindParam(':fechaNacimientoUsuario',$fechaNacimiento);
		$stmt->bindParam(':numTelefonoUsuario',$nuevoUsuario['numTelefonoUsuario']);
		$stmt->bindParam(':passUsuario',$nuevoUsuario['passUsuario']);
		$stmt->bindParam(':confirmPassUsuario',$nuevoUsuario['confirmPassUsuario']);
		$stmt->execute();
		return asignar_seguimientos_usuario($conexion,$nuevoUsuario['dniUsuario'],$nuevoUsuario['seguimientos']);
	}catch(PDOException $e ) {
		$_SESSION['excepcion'] = "El usuario ya existe en la base de datos.".$e->GetMessage();
		return false;
	}
 }
 
 function asignar_seguimientos_usuario($conexion,$dniUsuario,$seguimientos){
 	try{
		$stmt=$conexion->prepare("CALL INSERTAR_SEGUIMIENTOS(:dniUsuario,:dniJugador,NULL)");
		//Hacemos un foreach porque un usuario puede tener más de un seguimiento.
 		foreach($seguimientos as $dniJugador){
 			$stmt->bindParam(':dniUsuario',$dniUsuario);
 			$stmt->bindParam(':dniJugador',$dniJugador);
			$stmt->execute();
 		}
		return true;
 	}catch(PDOException $e){
 		$_SESSION['excepcion'] = "Error al asignar los seguimientos del usuario.".$e->GetMessage();
		return false;
 	}
 }
 
 function consultarUsuario($conexion,$nickUsuario,$passUsuario) {
	try{
	 	$consulta = "SELECT COUNT(*) AS TOTAL FROM USUARIOS WHERE nickUsuario=:nickUsuario AND 
	 	passUsuario=:passUsuario";
		$stmt = $conexion->prepare($consulta);
		$stmt->bindParam(':nickUsuario',$nickUsuario);
		$stmt->bindParam(':passUsuario',$passUsuario);
		$stmt->execute();
		return $stmt->fetchColumn();
	}catch(PDOException $e) {
		$_SESSION['excepcion'] = $e->GetMessage();
		header("Location: excepcion.php");
    }
 }

 function eliminarSeguimiento($conexion, $oid_seg){
	try{
		$consulta = "DELETE from seguimientos where oid_seg =: oid_seg";
		$stmt=$conexion->prepare($consulta);
		$stmt->bindParam(':oid_seg',$oid_seg);
		$stmt->execute();
		return true;
 	}catch(PDOException $e){
 		$_SESSION['excepcion'] = "Error al eliminar el seguimiento del usuario.".$e->GetMessage();
		return false;
 	}
 }
 function creaSeguimiento($conexion,$dniUsuario,$dniJugador){
	try{
		$consulta = "CALL INSERTAR_SEGUIMIENTOS(:dniUsuario,:dniJugador,NULL)";
		$stmt=$conexion->prepare($consulta);
		$stmt->bindParam(':dniUsuario',$dniUsuario);
		$stmt->bindParam(':dniJugador',$dniJugador);
		$stmt->execute();
		return true;
 	}catch(PDOException $e){
 		$_SESSION['excepcion'] = "Error al eliminar el seguimiento del usuario.".$e->GetMessage();
		return false;
 	}
 }

?>