<?php
// Inicia sessão 
session_start();
// Verifica se existe os dados da sessão de login e se está autenticado
if (isset($_SESSION["autenticado"]) ? $_SESSION["autenticado"] == false : true) {
    header("Location: ../login.php");
    exit;
}

require "navbar.php";
