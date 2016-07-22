<?php
/*
 * debug.class.php ::  Clase Debug, maneja reporte de eventos
 *
 * V4.0 copyright 2010-2011 by Gorlum for http://supernova.ws
 *  [!] Merged `errors` to `logs`
 *  [+] Now debugger can work with database detached. All messages would be dumped to page
 *  [+] Now `logs` has both human-readable and machine-readable fields
 *
 * V3.0 copyright 2010 by Gorlum for http://supernova.ws
 *  [+] Full rewrtie & optimize
 *  [*] Now there is fallback procedure if no $link to db detected
 *
 * V2.0 copyright 2010 by Gorlum for http://supernova.ws
 *  [*] Now error also contains backtrace - to see exact way problem comes
 *  [*] New method 'warning' sends message to dedicated SQL-table for non-errors
 *
 * V1.0 Created by Perberos. All rights reversed (C) 2006
 *
 *  Experiment code!!!
 *
 * vamos a experimentar >:)
 * le veo futuro a las classes, ayudaria mucho a tener un codigo mas ordenado...
 * que esperabas!!! soy newbie!!! D':<
*/

if(!defined('INSIDE'))
{
  die("attemp hacking");
}

class debug
{
  var $log, $numqueries;
  var $log_array;

  function debug()
  {
    $this->vars = $this->log = '';
    $this->numqueries = 0;
  }

  function add($mes)
  {
    $this->log .= $mes;
    $this->numqueries++;
  }

  function add_to_array($mes)
  {
    $this->log_array[] = $mes;
  }

  function echo_log()
  {
    echo '<br><table><tr><td class=k colspan=4><a href="' . SN_ROOT_PHYSICAL . "admin/settings.php\">Debug Log</a>:</td></tr>{$this->log}</table>";
    die();
  }

  function compact_backtrace($backtrace, $long_comment = false)
  {
    static $exclude_functions = array('doquery', 'db_query', 'db_get_record_list', 'db_user_by_id', 'db_get_user_by_id');

    $result = array();
    $transaction_id = classSupernova::db_transaction_check(false) ? classSupernova::$transaction_id : classSupernova::$transaction_id++;
    $result[] = "tID {$transaction_id}";
    foreach($backtrace as $a_trace)
    {
      if(in_array($a_trace['function'], $exclude_functions)) continue;
      $function =
        ($a_trace['type']
          ? ($a_trace['type'] == '->'
            ? "({$a_trace['class']})" . get_class($a_trace['object'])
            : $a_trace['class']
          ) . $a_trace['type']
          : ''
        ) . $a_trace['function'] . '()';

      $file = str_replace(SN_ROOT_PHYSICAL, '', str_replace('\\', '/', $a_trace['file']));

      // $result[] = "{$function} ({$a_trace['line']})'{$file}'";
      $result[] = "{$function} - '{$file}' Line {$a_trace['line']}";

      if(!$long_comment) break;
    }


    // $result = implode(',', $result);

    return $result;
  }

  function dump($dump = false, $force_base = false, $deadlock = false)
  {
    if($dump === false)
    {
      return;
    }

    $error_backtrace = array();
    $base_dump = false;

    if($force_base === true)
    {
      $base_dump = true;
    }

    if($dump === true)
    {
      $base_dump = true;
    }
    else
    {
      if(!is_array($dump))
      {
        $dump = array('var' => $dump);
      }

      foreach($dump as $dump_var_name => $dump_var)
      {
        if($dump_var_name == 'base_dump')
        {
          $base_dump = $dump_var;
        }
        else
        {
          $error_backtrace[$dump_var_name] = $dump_var;
        }
      }
    }

    if($deadlock && ($q = db_fetch(__db_query('SHOW ENGINE INNODB STATUS')))) {
      $error_backtrace['deadlock'] = explode("\n", $q['Status']);
      $error_backtrace['locks'] = classSupernova::$locks;
      $error_backtrace['cSN_data'] = classSupernova::$data;
      foreach($error_backtrace['cSN_data'] as &$location)
        foreach($location as $location_id => &$location_data)
//          $location_data = $location_id;
          $location_data = isset($location_data['username']) ? $location_data['username'] :
            (isset($location_data['name']) ? $location_data['name'] : $location_id);
      $error_backtrace['cSN_queries'] = classSupernova::$queries;
    }

    if($base_dump)
    {
      if(is_array($this->log_array) && count($this->log_array) > 0);
      {
        foreach($this->log_array as $log)
        {
          $error_backtrace['queries'][] = $log;
        }
      }

      $error_backtrace['backtrace'] = debug_backtrace();
      unset($error_backtrace['backtrace'][1]);
      unset($error_backtrace['backtrace'][0]);
      // $error_backtrace['query_log'] = "\r\n\r\nQuery log\r\n<table><tr><th>Number</th><th>Query</th><th>Page</th><th>Table</th><th>Rows</th></tr>{$this->log}</table>\r\n";
      $error_backtrace['$_GET'] = $_GET;
      $error_backtrace['$_POST'] = $_POST;
      $error_backtrace['$_REQUEST'] = $_REQUEST;
      $error_backtrace['$_COOKIE'] = $_COOKIE;
      $error_backtrace['$_SESSION'] = $_SESSION;
      $error_backtrace['$_SERVER'] = $_SERVER;
      global $user, $planetrow;
      $error_backtrace['user'] = $user;
      $error_backtrace['planetrow'] = $planetrow;
    }

    return $error_backtrace;
  }

  function error($message = 'There is a error on page', $title = 'Internal Error', $error_code = 500, $dump = true) {
    global $config, $link, $sys_stop_log_hit, $lang, $sys_log_disabled, $user;

    sn_db_transaction_rollback();

    if($config->debug == 1) {
      echo "<h2>{$title}</h2><br><font color=red>{$message}</font><br><hr>";
      echo "<table>{$this->log}</table>";
    }


    require(SN_ROOT_PHYSICAL . 'config.' . PHP_EX);

    if(!$link) {
      sn_db_connect();
      // $link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']);
      // mysql_query("/*!40101 SET NAMES 'utf8' */");
      // mysql_select_db($dbsettings['name']);

      if(!$link) {
        // TODO Здесь надо писать в файло
        die('SQL server currently unavailable. Please contact Administration...');
      }
    }

    $fatal_error = 'Fatal error: cannot write to `logs` table. Please contact Administration...';

    $error_text = db_escape($message);
    $error_backtrace = $this->dump($dump, true, strpos($message, 'Deadlock') !== false);

    if(!$sys_log_disabled) {
      $query = "INSERT INTO `{{logs}}` SET
        `log_time` = '".time()."', `log_code` = '" . db_escape($error_code) . "', `log_sender` = '" . db_escape($user['id']). "',
        `log_username` = '" . db_escape($user['user_name']). "', `log_title` = '" . db_escape($title) . "',  `log_text` = '".db_escape($message)."',
        `log_page` = '".db_escape(strpos($_SERVER['SCRIPT_NAME'], SN_ROOT_RELATIVE) === false ? $_SERVER['SCRIPT_NAME'] : substr($_SERVER['SCRIPT_NAME'], strlen(SN_ROOT_RELATIVE)))."'" .
        ($error_backtrace ? ", `log_dump` = '" . db_escape(serialize($error_backtrace)) . "'" : '') . ";";
      __db_query($query) or die($fatal_error . db_error());

      $message = "Пожалуйста, свяжитесь с админом, если ошибка повторится. Ошибка №: <b>" . db_insert_id() . "</b>";

      $sys_stop_log_hit = true;
      $sys_log_disabled = true;
      !function_exists('message') ? die($message) : message($message, 'Ошибка', '', 0, false);
    } else {
      ob_start();
      print("<hr>User ID {$user['id']} raised error code {$error_code} titled '{$title}' with text '{$error_text}' on page {$_SERVER['SCRIPT_NAME']}");

      foreach($error_backtrace as $name => $value) {
        print('<hr>');
        pdump($value, $name);
      }
      ob_end_flush();
      die();
    }
  }

  function warning($message, $title = 'System Message', $log_code = 300, $dump = false) {
    global $link, $user, $lang, $sys_log_disabled;

    require(SN_ROOT_PHYSICAL . 'config.' . PHP_EX);

    if(!$link) {
      sn_db_connect();
//      $link = mysql_connect($dbsettings['server'], $dbsettings['user'], $dbsettings['pass']);
//      mysql_query('/*!40101 SET NAMES \'utf8\' */');
//      mysql_select_db($dbsettings['name']);
    }

    $error_backtrace = $this->dump($dump, false);

    if(!$sys_log_disabled) {
      $query = "INSERT INTO `{{logs}}` SET
        `log_time` = '".time()."', `log_code` = '" . db_escape($log_code) . "', `log_sender` = '" . db_escape($user['id']). "',
        `log_username` = '" . db_escape($user['user_name']). "', `log_title` = '" . db_escape($title) . "',  `log_text` = '".db_escape($message)."',
        `log_page` = '".db_escape(strpos($_SERVER['SCRIPT_NAME'], SN_ROOT_RELATIVE) === false ? $_SERVER['SCRIPT_NAME'] : substr($_SERVER['SCRIPT_NAME'], strlen(SN_ROOT_RELATIVE)))."'" .
        ($error_backtrace ? ", `log_dump` = '" . db_escape(serialize($error_backtrace)) . "'" : '') . ";";
      __db_query($query);
    } else {
      print("<hr>User ID {$user['id']} made log entry with code {$log_code} titled '{$title}' with text '{$message}' on page {$_SERVER['SCRIPT_NAME']}");
    }
  }
}

// Copyright (c) 2009-2010 Gorlum for http://supernova.ws
// Dump variables nicer then var_dump()

function dump($value, $varname = null, $level=0, $dumper = '')
{
  if (isset($varname)) $varname .= " = ";

  if ($level==-1)
  {
    $trans[' ']='&there4;';
    $trans["\t"]='&rArr;';
    $trans["\n"]='&para;;';
    $trans["\r"]='&lArr;';
    $trans["\0"]='&oplus;';
    return strtr(htmlspecialchars($value),$trans);
  }
  if ($level==0) $dumper = '<pre>' . mt_rand(10, 99) . '|' . $varname;

  $type = gettype($value);
  $dumper .= $type;

  if ($type=='string')
  {
    $dumper .= '(' . strlen($value) . ')';
    $value = dump($value, '', -1);
  }
  elseif ($type=='boolean') $value= ($value?'true':'false');
  elseif ($type=='object')
  {
    $props= get_class_vars(get_class($value));
    $dumper .= '('.count($props).') <u>'.get_class($value).'</u>';
    foreach($props as $key=>$val)
    {
      $dumper .= "\n".str_repeat("\t",$level+1).$key.' => ';
      $dumper .= dump($value->$key,'',$level+1);
    }
    $value= '';
  }
  elseif ($type=='array')
  {
    $dumper .= '('.count($value).')';
    foreach($value as $key=>$val)
    {
      $dumper .= "\n".str_repeat("\t",$level+1).dump($key,'',-1).' => ';
      $dumper .= dump($val,'',$level+1);
    }
    $value= '';
  }
  $dumper .= " <b>$value</b>";
  if ($level==0) $dumper .= '</pre>';
  return $dumper;
}

function pdump($value, $varname = null)
{
  print('<span style="text-align: left">' . dump($value, $varname) . '</span>');
}

function debug($value, $varname = null)
{
  return pdump($value, $varname);
}

function pr($prePrint = false){
  if($prePrint)
    print("<br>");
  print(mt_rand() . "<br>");
}

function pc($prePrint = false){
  global $_PRINT_COUNT_VALUE;
  $_PRINT_COUNT_VALUE++;

  if($prePrint)
    print("<br>");
  print($_PRINT_COUNT_VALUE . "<br>");
}
