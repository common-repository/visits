<?php
  /*
   Plugin Name: Visits
   Plugin URI: http://www.geekflog.net/wordpress/plugins/visits
   Description: Visitas en nuestro WP
   Author: V0ltr4n
   Version: 1.2
   Author URI: http://www.geekflog.net
   */
  ob_start();
  set_time_limit(0);
  register_activation_hook(__FILE__, 'visits_instalar');
  register_deactivation_hook(__FILE__, 'visits_desinstalar');
  function menu_visits()
  {
      add_submenu_page('index.php', 'Visits', 'Visits', 10, __FILE__, 'funciones_visits');
  }
  add_action('admin_menu', 'menu_visits');
  function funciones_visits()
  {
      content();
  }
  function visits_instalar()
  {
      global $wpdb;
      $creartabla = 'CREATE TABLE ' . $wpdb->prefix . 'visitas( 
id int(5) NOT NULL AUTO_INCREMENT PRIMARY KEY ,  
ip varchar(30)  NULL ,  
fecha varchar(30) NULL )';
      $wpdb->query($creartabla);
      $creartabla2 = 'CREATE TABLE ' . $wpdb->prefix . 'contador(
visitas int(8) NOT NULL )';
      $wpdb->query($creartabla2);
  }
  function visits_desinstalar()
  {
      global $wpdb;
      $borrartabla = 'DROP TABLE ' . $wpdb->prefix . 'visitas';
      $wpdb->query($borrartabla);
      $borrartabla2 = 'DROP TABLE ' . $wpdb->prefix . 'contador';
      $wpdb->query($borrartabla2);
  }
  function mostrar_visitas()
  {
      global $wpdb;
      $ip = $_SERVER['REMOTE_ADDR'];
      $fecha = date("D-M-Y");
      $obtenerdatos = $wpdb->get_row("SELECT ip, fecha FROM " . $wpdb->prefix . "visitas WHERE ip = '$ip' AND fecha = '$fecha'");
      $obtenervisitas = $wpdb->get_row("SELECT * FROM " . $wpdb->prefix . "contador");
      $visitas = $obtenervisitas->visitas + "1";
      if ($obtenervisitas->visitas == null) {
          $wpdb->query("INSERT INTO " . $wpdb->prefix . "contador (visitas) VALUES ('1')");
      } else {
          if ($obtenerdatos->ip != $ip && $obtenerdatos->fecha != $fecha) {
              $wpdb->query("INSERT INTO " . $wpdb->prefix . "visitas (ip, fecha) VALUES ('$ip', '$fecha')");
              $wpdb->query("UPDATE " . $wpdb->prefix . "contador SET visitas='$visitas'");
          } else {
          }
          $hoy = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "visitas WHERE fecha = '$fecha'");
          echo "<b>Totales: " . $obtenervisitas->visitas . "</b>
<br />";
          echo "<b>Hoy: " . count($hoy) . "</b>";
      }
  }
  function content()
  {
      global $wpdb;
      $mostrardatos = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "visitas");
      if ($mostrardatos) {
          echo "
<center>
<form action=\"\" method=\"POST\">
<table cellspacing=\"0\">
<th>&nbsp;&nbsp;Ips</th>
<th>Fecha</th>";
          foreach ($mostrardatos as $list) {
              echo "
<tr>
<td>" . $list->ip . "&nbsp;</td>
<td>" . $list->fecha . "</td>
</tr>";
          }
          echo "
</table>
<input type=\"submit\" name=\"borrar\" value=\"Vaciar tabla\">
</form>
</center>";
      } else {
          echo "
<br />
<center>
<label>La tabla esta vacia</label>
</center>";
      }
      $file = $_SERVER['PHP_SELF'];
      if ($_POST['borrar']) {
          $del = "DELETE FROM " . $wpdb->prefix . "visitas";
          $delete = $wpdb->query($del);
          if ($delete) {
              header("Location: $file?page=visits/visits.php");
          }
      }
  }
?>
