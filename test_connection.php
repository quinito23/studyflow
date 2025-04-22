<?php
   require_once 'DBConnection.php';

   try {
       $database = new DBConnection();
       $db = $database->getConnection();
       echo "Conexión exitosa a la base de datos!";
   } catch (Exception $e) {
       echo "Error: " . $e->getMessage();
   }
   ?>