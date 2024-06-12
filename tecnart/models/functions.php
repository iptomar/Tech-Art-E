<?php

if(!isset($_SESSION))
  session_start();

if (!isset($_SESSION["lang"])) {
  $_SESSION["lang"] = "pt";
}

$_SESSION["basename"] = $_SERVER['PHP_SELF'];

if (strlen($_SERVER["QUERY_STRING"]) > 0) {
  $_SESSION["basename"] = $_SESSION["basename"] . "?" . $_SERVER["QUERY_STRING"];
}
function template_header($title)
{

  //::::::CABECALHO PRINCIPAL::::::
  
  include "models/templates/header.php";
}

function template_footer()
{

  //variaveis para passar valores de dicionarios

  //imagens

  //::::::RODAPE PRINCIPAL::::::
  
  include "models/templates/footer.php";
}

if ($_SESSION["lang"] == "en") {
  include 'locale/en.php';
} elseif ($_SESSION["lang"] == "pt") {
  include 'locale/pt.php';
}

function change_lang($dicElem)
{
  $langArray = require 'locale/' . $_SESSION["lang"] . '.php';
  return $langArray[$dicElem];
}

function alert_redirect($msg, $redirect)
{
  echo "<script>
        alert('$msg');
        window.location.href = '$redirect';
        </script>";
  exit();
}

function show_error($error)
{
  echo '<div class="w-100">
          <div class="mx-auto alert alert-danger alert-dismissible fade show d-flex align-items-center justify-content-center" style="min-height:150px;" role="alert">
            <div>' . $error . '</div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
        </div>';
}

function change_lan($key)
{
  $translations = array(
    "previous" => "Previous",
    "next" => "Next",
    // Add more translations as needed
  );

  return isset($translations[$key]) ? $translations[$key] : "";
}
