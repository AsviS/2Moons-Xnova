<?php

include('common.' . substr(strrchr(__FILE__, '.'), 1));

if($user['authlevel'] < AUTH_LEVEL_DEVELOPER) {
  $error_message = $config->game_mode == GAME_BLITZ ? 'sys_blitz_page_disabled' : (
    !$config->game_blitz_register ? 'sys_blitz_registration_disabled' : ''
  );

  if($error_message) {
    message($lang[$error_message], $lang['sys_error'], 'overview.php', 10);
    die();
  }

//  if($config->game_mode == GAME_BLITZ) {
//    message($lang['sys_blitz_page_disabled'], $lang['sys_error'], 'overview.php', 10);
//    die();
//  }
//
//  if(!$config->game_blitz_register) { //  && $user['authlevel'] < AUTH_LEVEL_DEVELOPER
//    message($lang['sys_blitz_registration_disabled'], $lang['sys_error'], 'overview.php', 10);
//    die();
//  }
}

$current_round = intval($config->db_loadItem('game_blitz_register_round'));
$current_price = intval($config->db_loadItem('game_blitz_register_price'));

if($config->db_loadItem('game_blitz_register') == BLITZ_REGISTER_OPEN && (sys_get_param_str('register_me') || sys_get_param_str('register_me_not'))) {
  sn_db_transaction_start();
  $user = db_user_by_id($user['id'], true);
  $is_registered = doquery("SELECT `id` FROM {{blitz_registrations}} WHERE `user_id` = {$user['id']} AND `round_number` = {$current_round} FOR UPDATE;", true);
  if(sys_get_param_str('register_me')) {
    if(empty($is_registered) && mrc_get_level($user, null, RES_METAMATTER) >= $current_price) {
      doquery("INSERT IGNORE INTO {{blitz_registrations}} SET `user_id` = {$user['id']}, `round_number` = {$current_round};");
      mm_points_change($user['id'], RPG_BLITZ_REGISTRATION, -$current_price, "Регистрация в раунде {$current_round} Блица");
    }
  } elseif (sys_get_param_str('register_me_not') && !empty($is_registered)) {
    doquery("DELETE FROM {{blitz_registrations}} WHERE `user_id` = {$user['id']} AND `round_number` = {$current_round};");
    mm_points_change($user['id'], RPG_BLITZ_REGISTRATION_CANCEL, $current_price, "Отмена регистрации в раунде {$current_round} Блица");
  }
  $registered_count = doquery("SELECT count(`id`) AS `count` FROM {{blitz_registrations}} WHERE `round_number` = {$current_round};", true);
  $config->db_saveItem('game_blitz_register_users', $registered_count['count']);
  sn_db_transaction_commit();
}

$blitz_generated = array();
$blitz_result = array();
$blitz_prize_players_active = 0;
$blitz_players = 0;
$blitz_prize_dark_matter = 0;
$blitz_prize_places = 0;
if($user['authlevel'] >= AUTH_LEVEL_DEVELOPER) {
  if(sys_get_param_str('generate')) {
    $next_id = 0;
    $query = doquery("SELECT `id` FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY RAND();");
    while($row = db_fetch($query)) {
      $next_id++;
      $blitz_name = 'Игрок' . $next_id;
      $blitz_password = sys_random_string(8);
      doquery("UPDATE {{blitz_registrations}} SET blitz_name = '{$blitz_name}', blitz_password = '{$blitz_password}' WHERE `id` = {$row['id']} AND `round_number` = {$current_round};");
    }
  } elseif(sys_get_param_str('import_generated')) {
    // ЭТО НА БЛИЦЕ!!!
    doquery("DELETE FROM {{users}} WHERE username like 'Игрок%';");
    doquery("DELETE FROM {{planets}} WHERE id_owner not in (SELECT `id` FROM {{users}});");

    $imported_string = explode(';', sys_get_param_str('generated_string'));
    shuffle($imported_string);

    $new_players = count($imported_string);
    $system_count = ceil($new_players / $config->game_maxGalaxy);
    $system_step = floor($config->game_maxSystem / $system_count);

pdump($system_count, '$system_count');
pdump($system_step, '$system_step');

    $skin = DEFAULT_SKINPATH;
    $language = DEFAULT_LANG;

    $galaxy = 1;
    $system = $system_step;
    $planet = round($config->game_maxPlanet / 2);

    foreach($imported_string as &$string_data) {
      $string_data = explode(',', $string_data);
      $username_safe = $string_data[0];

      $user_new = player_create($username_safe, $string_data[1], '', array(
        'galaxy' => $galaxy,
        'system' => $system,
        'planet' => $planet,
      ));


//      $user_new = classSupernova::db_ins_record(LOC_USER, "`email` = '', `email_2` = '', `username` = '{$username_safe}',
//      `dpath` = '{$skin}', `lang` = '{$language}', `register_time` = " . SN_TIME_NOW . ",
//      `options` = 'opt_mnl_spy^1|opt_email_mnl_spy^0|opt_email_mnl_joueur^0|opt_email_mnl_alliance^0|opt_mnl_attaque^1|opt_email_mnl_attaque^0|opt_mnl_exploit^1|opt_email_mnl_exploit^0|opt_mnl_transport^1|opt_email_mnl_transport^0|opt_email_msg_admin^1|opt_mnl_expedition^1|opt_email_mnl_expedition^0|opt_mnl_buildlist^1|opt_email_mnl_buildlist^0|opt_int_navbar_resource_force^1|';");
//
//      sec_password_change($user_new, $string_data[1], false); // OK
//
//      doquery("REPLACE INTO {{player_name_history}} SET `player_id` = {$user_new['id']}, `player_name` = \"{$username_safe}\"");
//
//      $new_planet_id = uni_create_planet($galaxy, $system, $planet, $user_new['id'], $username_unsafe . ' ' . $lang['sys_capital'], true);
//      sys_player_new_adjust($user_new['id'], $new_planet_id);
//
//      db_user_set_by_id($user_new['id'], "`id_planet` = '{$new_planet_id}', `current_planet` = '{$new_planet_id}', `galaxy` = '{$galaxy}', `system` = '{$system}', `planet` = '{$planet}'");
//

      $moon_row = uni_create_moon($galaxy, $system, $planet, $user_new['id'], 30, '', false);

      // $system += $system_step;
      // $system >= $config->game_maxSystem ? $galaxy++ : false;
      if(($system += $system_step) >= $config->game_maxSystem) {
        $galaxy++;
        $system = $system_step;
      }
    }
    doquery('UPDATE {{users}} SET dark_matter = 50000, dark_matter_total = 50000;');

    $config->db_saveItem('users_amount', $config->users_amount + $new_players);
    // pdump($imported_string);
    // generated_string
  } elseif(sys_get_param_str('import_result') && ($blitz_result_string = sys_get_param_str('blitz_result_string'))) {
    $blitz_result = explode(';', $blitz_result_string);
    $blitz_last_update = $blitz_result[0]; // Пока не используется
    unset($blitz_result[0]);
    foreach($blitz_result as $blitz_result_data) {
      $blitz_result_data = explode(',', $blitz_result_data);
      if(count($blitz_result_data) == 5) {
        $blitz_result_data[1] = db_escape($blitz_result_data[1]);
        doquery(
          "UPDATE `{{blitz_registrations}}` SET
            `blitz_player_id` = '{$blitz_result_data[0]}',
            `blitz_online` = '{$blitz_result_data[2]}',
            `blitz_place` = '{$blitz_result_data[3]}',
            `blitz_points` = '{$blitz_result_data[4]}'
          WHERE `blitz_name` = '{$blitz_result_data[1]}' AND `round_number` = {$current_round};");
      }
    }
    $blitz_result = array();
  }

  if($config->game_mode == GAME_BLITZ) {
    $blitz_result = array($config->db_loadItem('var_stat_update'));
    $query = doquery("SELECT id, username, total_rank, total_points, onlinetime FROM {{users}} ORDER BY `id`;");
    while($row = db_fetch($query)) {
      $blitz_result[] = "{$row['id']},{$row['username']},{$row['onlinetime']},{$row['total_rank']},{$row['total_points']}";
    }
  } else {
    $query = doquery("SELECT blitz_name, blitz_password, blitz_online FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY `id`;");
    while($row = db_fetch($query)) {
      $blitz_generated[] = "{$row['blitz_name']},{$row['blitz_password']}";
      $row['blitz_online'] ? $blitz_prize_players_active++ : false;
      $blitz_players++;
    }
    $blitz_prize_dark_matter = $blitz_prize_players_active * 20000;
    $blitz_prize_places = ceil($blitz_prize_players_active / 5);
    /*
    'Игрок10'
    'Игрок14'
    'Игрок23'
    'Игрок32'
    'Игрок40'
    */

    if(sys_get_param_str('prize_calculate') && $blitz_prize_players_active && ($blitz_prize_dark_matter_actual = sys_get_param_int('blitz_prize_dark_matter'))) {
      // $blitz_prize_dark_matter_actual = sys_get_param_int('blitz_prize_dark_matter');
      $blitz_prize_places_actual = sys_get_param_int('blitz_prize_places');
      sn_db_transaction_start();
      $query = doquery("SELECT * FROM {{blitz_registrations}} WHERE `round_number` = {$current_round} ORDER BY `blitz_place` FOR UPDATE;");
      while($row = db_fetch($query)) {
        if(!$row['blitz_place']) {
          continue;
        }

        $blitz_prize_dark_matter_actual = round($blitz_prize_dark_matter_actual / 2);
        $blitz_prize_places_actual--;

        $reward = $blitz_prize_dark_matter_actual - $row['blitz_reward_dark_matter'];
pdump("{{$row['id']}} {$row['blitz_name']}, Place {$row['blitz_place']}, Prize places {$blitz_prize_places_actual}, Prize {$reward}",$row['id']);
        if($reward) {
          rpg_points_change($row['user_id'], RPG_BLITZ, $reward, sprintf(
            $lang['sys_blitz_reward_log_message'], $row['blitz_place'], $row['blitz_name']
          ));
          doquery("UPDATE {{blitz_registrations}} SET blitz_reward_dark_matter = blitz_reward_dark_matter + ($reward) WHERE id = {$row['id']} AND `round_number` = {$current_round};");
        }

        if(!$blitz_prize_places_actual || $blitz_prize_dark_matter_actual < 1000) {
          break;
        }
      }
      sn_db_transaction_commit();
    }

  }
}


$template = gettemplate('blitz_register', true);

$player_registered = false;
$query = doquery(
  "SELECT u.*, br.blitz_name, br.blitz_password, br.blitz_place, br.blitz_status, br.blitz_points, br.blitz_reward_dark_matter
    FROM {{blitz_registrations}} AS br
    JOIN {{users}} AS u ON u.id = br.user_id
  WHERE br.`round_number` = {$current_round}
  order by `blitz_place`, `timestamp`;");
while($row = db_fetch($query)) {
  $tpl_player_data = array(
    'NAME' => player_nick_render_to_html($row, array('icons' => true, 'color' => true, 'ally' => true)),
  );

  if($config->game_blitz_register == BLITZ_REGISTER_DISCLOSURE_NAMES) {
    // Вот так хитро, что бы не было не единого шанса попадания на страницу данных об игроках Блиц-сервера до закрытия раунда
    $tpl_player_data = array_merge($tpl_player_data, array(
      'ID' => $row['id'],
      'BLITZ_NAME' => $row['blitz_name'],
      // 'BLITZ_STATUS' => $row['blitz_status'],
      'BLITZ_PLACE' => $row['blitz_place'],
      'BLITZ_POINTS' => $row['blitz_points'],
      'BLITZ_REWARD_DARK_MATTER' => $row['blitz_reward_dark_matter'],
    ));
  }

  $template->assign_block_vars('registrations', $tpl_player_data);
  if($row['id'] == $user['id']) {
    $player_registered = $row;
  }
}

$template->assign_vars(array(
  'GAME_BLITZ' => $config->game_mode == GAME_BLITZ,

  'REGISTRATION_OPEN' => $config->game_blitz_register == BLITZ_REGISTER_OPEN,
  'REGISTRATION_CLOSED' => $config->game_blitz_register == BLITZ_REGISTER_CLOSED,
  'REGISTRATION_SHOW_LOGIN' => $config->game_blitz_register == BLITZ_REGISTER_SHOW_LOGIN,
  'REGISTRATION_DISCLOSURE_NAMES' => $config->game_blitz_register == BLITZ_REGISTER_DISCLOSURE_NAMES,

  'PLAYER_REGISTERED' => !empty($player_registered),
  'BLITZ_NAME' => $player_registered['blitz_name'],
  'BLITZ_PASSWORD' => $player_registered['blitz_password'],

  'BLITZ_GENERATED' => implode(';', $blitz_generated),
  'BLITZ_RESULT' => implode(';', $blitz_result),
  'BLITZ_PRIZE_PLAYERS_ACTIVE' => $blitz_prize_players_active,
  'BLITZ_PRIZE_DARK_MATTER' => $blitz_prize_dark_matter,
  'BLITZ_PRIZE_PLACES' => $blitz_prize_places,
));

display($template, $lang['sys_blitz_global_button']);
