<?php

include_once('common.' . substr(strrchr(__FILE__, '.'), 1));

$template = gettemplate('dark_matter', true);
if(isset(sn_module_payment::$bonus_table) && is_array(sn_module_payment::$bonus_table))
{
  foreach(sn_module_payment::$bonus_table as $sum => $discount)
  {
    if($discount)
    {
      $template->assign_block_vars('discount', array(
        'SUM' => $sum,
        'DISCOUNT' => $discount * 100,
        'TEXT' => sprintf($lang['sys_dark_matter_purchase_text_bonus'], pretty_number($sum), $discount * 100),
      ));
    }
  }
}

if($payment_id = sys_get_param_id('payment_id'))
{
  $payment = doquery("SELECT * FROM {{payment}} WHERE `payment_id` = {$payment_id} LIMIT 1;", true);
  if($payment && $payment['payment_user_id'] == $user['id'])
  {
    if($payment['payment_status'] == PAYMENT_STATUS_COMPLETE || $payment['payment_status'] == PAYMENT_STATUS_TEST)
    {
      $template->assign_block_vars('result', array('MESSAGE' => sprintf($lang['sys_dark_matter_purchase_result_complete'], $payment['payment_dark_matter_paid'], $payment['payment_module_name'], $payment['payment_dark_matter_gained'])));
    }
    if($payment['payment_status'] == PAYMENT_STATUS_NONE)
    {
      $template->assign_block_vars('result', array(
        'MESSAGE' => sprintf($lang['sys_dark_matter_purchase_result_incomplete'], $payment['payment_dark_matter_paid'], $payment['payment_module_name']),
        'STATUS' => 1,
      ));
    }
    if($payment['payment_status'] == PAYMENT_STATUS_TEST)
    {
      $template->assign_block_vars('result', array(
        'MESSAGE' => sprintf($lang['sys_dark_matter_purchase_result_test']),
        'STATUS' => -1,
      ));
    }
  }
}

$dm_amount_list = &sn_module_payment::$bonus_table;

$request = array(
  'dark_matter' => sys_get_param_float('dark_matter'),
);
$request['dark_matter'] = isset($dm_amount_list[$request['dark_matter']]) ? $request['dark_matter'] : 0;
if(!$request['dark_matter'])
{
  unset($_POST);
}

$payment_module_valid = false;
$payment_module = sys_get_param_str('payment_module');
foreach($sn_module as $module_name => $module)
{
  if(!is_object($module) || $module->manifest['package'] != 'payment' || !$module->manifest['active'])
  {
    continue;
  }

  lng_include($module_name, $module->manifest['root_relative']);

  $template->assign_block_vars('payment_module', array(
    'ID' => $module_name,
    'NAME' => $lang["module_{$module_name}_name"],
    'DESCRIPTION' => $lang["module_{$module_name}_description"],
  ));

  $payment_module_valid = $payment_module_valid || $module_name == $payment_module;
}
// If payment_module invalid - making it empty OR if there is only one payment_module - selecting it
$payment_module = $payment_module_valid ? $payment_module : (count($template->_tpldata['payment_module']) == 1 ? $template->_tpldata['payment_module'][0]['ID'] : '');

foreach($lang['pay_currency_list'] as $key => $value)
{
  $var_name = 'payment_currency_exchange_' . strtolower($key);
  $course = $config->$var_name;
  if(!$course || $key == $config->payment_currency_default)
  {
    continue;
  }
  $template->assign_block_vars('exchange', array(
    'SYMBOL' => $key,
    'TEXT' => $value,
    'COURSE_DIRECT' => pretty_number($course, 4),
    'COURSE_REVERSE' => pretty_number(1 / $course, 4),
    'DM_PER_UNIT' => sn_module_payment::currency_convert(1, $key, 'DM_'),
    'UNIT_PER_LOT' => sn_module_payment::currency_convert(2500, 'DM_', $key),
  ));
}// 1/0,24712 = 4,046617028164

if($request['dark_matter'] && $payment_module)
{
  try
  {
    // Any possible errors about generating paylink should be raised in module!
    $pay_link = $sn_module[$payment_module]->compile_request($request);

    if(is_array($pay_link['RENDER']))
    {
      foreach($pay_link['RENDER'] as $html_data)
      {
        $template->assign_block_vars('render', $html_data);
        if(isset($html_data['VALUE']) && is_array($html_data['VALUE']))
        {
          foreach($html_data['VALUE'] as $value_id => $value_value)
          {
            $template->assign_block_vars('render.value', array(
              'FIELD' => $value_id,
              'VALUE' => $value_value,
            ));
          }
        }
      }
    }

    if(is_array($pay_link['DATA']))
    {
      foreach($pay_link['DATA'] as $key => $value)
      {
        $template->assign_block_vars('pay_link_data', array(
          'FIELD' => $key,
          'VALUE' => $value,
        ));
      }
    }

    if(!is_string($pay_link) && isset($pay_link['METHOD']) && $pay_link['METHOD'] == 'STEP')
    {
/*
      $template->assign_vars(array(
        'ANOTHER_STEP' => $pay_link['METHOD'],
      ));
*/
/*
        if(is_string($html_data))
        {
          $template->assign_block_vars('render', array(
            'TYPE' => 'text',
            'ELEMENTS' => $html_data,
          ));
        }
        elseif(is_array($html_data))
        {
          $template->assign_block_vars('render', array(
            'NAME' => $html_name,
            'TYPE' => $html_data['TYPE'],
          ));
        }
*/
    }
    if(is_string($pay_link) && strpos($pay_link, 'http') === 0)
    {
      $template->assign_vars(array(
        'PAY_LINK' => $pay_link,
        'PAY_LINK_METHOD' => 'LINK',
      ));
    }
    elseif(is_array($pay_link) && in_array($pay_link['METHOD'], array('POST', 'GET')))
    {
      $template->assign_vars(array(
        'PAY_LINK' => $pay_link['URL'],
        'PAY_LINK_METHOD' => $pay_link['METHOD'],
      ));
    }
    else
    {
      throw new exception($lang['pay_msg_request_paylink_unsupported'], SN_PAYMENT_REQUEST_PAYLINK_UNSUPPORTED);
    }
  }
  catch(exception $e)
  {
    $response = array(
      'result'  => $e->getCode(),
      'message' => $e->getMessage(),
    );
  }
}

foreach($dm_amount_list as $dm_amount => $discount)
{
  $template->assign_block_vars('dm_amount', array(
    'VALUE' => $dm_amount,
    'TEXT' => pretty_number($dm_amount),
  ));
}

lng_include('infos');
$template->assign_vars(array(
  'URL_DARK_MATTER' => $config->url_dark_matter,
  'PAYMENT_MODULE' => $payment_module,
  'PAYMENT_MODULE_NAME' => $lang["module_{$payment_module}_name"],
  'PAYMENT_MODULE_DESCRIPTION' => $lang["module_{$payment_module}_description"],
  'DARK_MATTER' => (float)$request['dark_matter'],
  'DARK_MATTER_TEXT' => pretty_number($request['dark_matter']),
  'UNIT_DESCRIPTION' => $lang['info'][RES_DARK_MATTER]['description'],
  'PAYMENT_CURRENCY_EXCHANGE_DEFAULT' => $config->payment_currency_exchange_dm_,
  'PAYMENT_CURRENCY_DEFAULT_TEXT' => $lang['pay_currency_list'][$config->payment_currency_default],

  'PAYMENT_AVAILABLE' => sn_module_get_active_count('payment') && !defined('SN_GOOGLE'),

  'PAGE_HEADER' =>$lang['sys_dark_matter'],
));

display($template, $lang['sys_dark_matter']);
