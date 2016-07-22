<?php

/*
#############################################################################
#  Filename: tech.mo
#  Project: SuperNova.WS
#  Website: http://www.supernova.ws
#  Description: Massive Multiplayer Online Browser Space Startegy Game
#
#  Copyright © 2009 Gorlum for Project "SuperNova.WS"
#  Copyright © 2009 MSW
#############################################################################
*/

/**
*
* @package language
* @system [Russian]
* @version 39a8.17
*
*/

/**
* DO NOT CHANGE
*/

if (!defined('INSIDE')) die();


//$lang = array_merge($lang,
//$lang->merge(
$a_lang_array = (array(
//  'tech_storage_max' => 'Хранилища',
  'tech_storage_max' => 'Макс. емкость хранилища',
  'tech_storage' => 'В хранилищах',
  'Tech' => 'Технология',
  'Requirements' => 'Требуется',
  'No_requirements' => 'Нет требований',
  'Metal' => 'Металл',
  'Crystal' => 'Кристалл',
  'Deuterium' => 'Дейтерий',
  'Energy' => 'Энергия',
  'dark_matter' => 'Тёмная Материя',
  'ds' => 'Сообщения',
  'Message' => 'Сообщения',
  'level' => 'Уровень',
  'treeinfo' => '[i]',
  'comingsoon' => 'Скоро',
  'te_dt_tx_pre' => 'Слабая добыча',
  'type_mission' => array(
    MT_ATTACK => 'Атака',
    MT_AKS => 'Совместная атака',
    MT_TRANSPORT => 'Транспорт',
    MT_RELOCATE => 'Передислокация',
    MT_HOLD => 'Удержание',
    MT_SPY => 'Шпионаж',
    MT_COLONIZE => 'Колонизация',
    MT_RECYCLE => 'Переработать',
    MT_DESTROY => 'Уничтожение',
    MT_MISSILE => 'Ракетная атака',
    MT_EXPLORE => 'Экспедиция',
  ),

  'tech' => array(
    UNIT_STRUCTURES => 'Постройки',
    STRUC_MINE_METAL => 'Рудник',
    STRUC_MINE_CRYSTAL => 'Синтезатор кристаллов',
    STRUC_MINE_DEUTERIUM => 'Синтезатор дейтерия',
    STRUC_MINE_SOLAR => 'Солнечная электростанция',
    STRUC_MINE_FUSION => 'Термоядерная электростанция',
    STRUC_FACTORY_ROBOT => 'Фабрика роботов',
    STRUC_FACTORY_NANO => 'Нанофабрика',
    STRUC_FACTORY_HANGAR => 'Верфь',
    STRUC_STORE_METAL => 'Хранилище металла',
    STRUC_STORE_CRYSTAL => 'Хранилище кристаллов',
    STRUC_STORE_DEUTERIUM => 'Емкость для дейтерия',
    STRUC_LABORATORY => 'Лаборатория',
    STRUC_TERRAFORMER => 'Терраформер',
    STRUC_ALLY_DEPOSIT => 'Склад альянса',
    STRUC_LABORATORY_NANO => 'Нанолаборатория',

    UNIT_STRUCTURES_SPECIAL => 'Особые постройки',
    STRUC_MOON_STATION => 'Лунная база',
    STRUC_MOON_PHALANX => 'Сенсорная фаланга',
    STRUC_MOON_GATE => 'Межгалактические врата',
    STRUC_SILO => 'Ракетная шахта',

    UNIT_TECHNOLOGIES => 'Технологии',
    TECH_ENERGY => 'Энергетическая технология',
    TECH_COMPUTER => 'Компьютерная технология',
    TECH_ARMOR => 'Броня космических кораблей',
    TECH_WEAPON => 'Оружейная технология',
    TECH_SHIELD => 'Щитовая технология',
    TECH_ENGINE_CHEMICAL => 'Химический двигатель',
    TECH_ENGINE_ION => 'Ионный двигатель',
    TECH_ENGINE_HYPER => 'Гипердвигатель',
    TECH_LASER => 'Лазерная технология',
    TECH_ION => 'Ионная технология',
    TECH_PLASMA => 'Плазменная технология',
    TECH_HYPERSPACE => 'Гипертехнология',
    TECH_SPY => 'Технология шпионажа',
    TECH_EXPEDITION => 'Экспедиционная технология',
    TECH_COLONIZATION => 'Колонизационная технология',
    TECH_ASTROTECH => 'Астрокартография',
    TECH_GRAVITON => 'Гравитационная технология',
    TECH_RESEARCH => 'Исследовательская сеть',

    UNIT_SHIPS => 'Флот',
    SHIP_SATTELITE_SOLAR => 'Солнечный спутник',
    SHIP_SPY => 'Шпионский зонд',
    SHIP_CARGO_SMALL => 'Малый транспорт',
    SHIP_CARGO_BIG => 'Большой транспорт',
    SHIP_CARGO_SUPER => 'Супертранспорт',
    SHIP_CARGO_HYPER => 'Гипертранспорт',
    SHIP_RECYCLER => 'Переработчик',
    SHIP_COLONIZER => 'Колонизатор',
    SHIP_SMALL_FIGHTER_LIGHT => 'Лёгкий истребитель',
    SHIP_SMALL_FIGHTER_HEAVY => 'Тяжёлый истребитель',
    SHIP_MEDIUM_FRIGATE => 'Эсминец',
    SHIP_LARGE_CRUISER => 'Крейсер',
    SHIP_LARGE_BOMBER => 'Бомбардировщик',
    SHIP_LARGE_BATTLESHIP => 'Линейный крейсер',
    SHIP_LARGE_DESTRUCTOR => 'Уничтожитель',
    SHIP_HUGE_DEATH_STAR => 'Звезда смерти',
    SHIP_HUGE_SUPERNOVA => 'Крейсер класса &quot;Сверхновая&quot;',

    UNIT_DEFENCE => 'Оборона',
    UNIT_DEF_TURRET_MISSILE => 'Ракетная установка',
    UNIT_DEF_TURRET_LASER_SMALL => 'Легкий лазер',
    UNIT_DEF_TURRET_LASER_BIG => 'Тяжёлый лазер',
    UNIT_DEF_TURRET_GAUSS => 'Пушка Гаусса',
    UNIT_DEF_TURRET_ION => 'Ионное орудие',
    UNIT_DEF_TURRET_PLASMA => 'Плазменное орудие',
    UNIT_DEF_SHIELD_SMALL => 'Малый щитовой купол',
    UNIT_DEF_SHIELD_BIG => 'Большой щитовой купол',
    UNIT_DEF_SHIELD_PLANET => 'Планетарная защита',
    UNIT_DEF_MISSILE_INTERCEPTOR => 'Ракета-перехватчик',
    UNIT_DEF_MISSILE_INTERPLANET => 'Межпланетная ракета',

    UNIT_MERCENARIES => 'Наемники',
    MRC_STOCKMAN => 'Карго-мастер',
    MRC_SPY => 'Шпион',
    MRC_ACADEMIC => 'Академик',
//    MRC_DESTRUCTOR => 'Разрушитель',
    MRC_ADMIRAL => 'Адмирал',
    MRC_COORDINATOR => 'Координатор',
    MRC_NAVIGATOR => 'Навигатор',
//    MRC_ASSASIN => 'Ассасин',

    UNIT_GOVERNORS => 'Губернаторы',
    MRC_TECHNOLOGIST => 'Технолог',
    MRC_ENGINEER => 'Инженер',
    MRC_FORTIFIER => 'Фортификатор',

    UNIT_RESOURCES => 'Ресурсы',
    RES_METAL => 'Металл',
    RES_CRYSTAL => 'Кристалл',
    RES_DEUTERIUM => 'Дейтерий',
    RES_ENERGY => 'Энергия',
    RES_DARK_MATTER => 'Тёмная Материя',
    RES_METAMATTER => 'Метаматерия',

    UNIT_ARTIFACTS     => 'Артефакты',
    ART_LHC            => 'Большой Адронный Коллайдер',
    ART_HOOK_SMALL     => 'Малый Крюк',
    ART_HOOK_MEDIUM    => 'Средний Крюк',
    ART_HOOK_LARGE     => 'Большой Крюк',
    ART_RCD_SMALL      => 'Малый АКК',
    ART_RCD_MEDIUM     => 'Средний АКК',
    ART_RCD_LARGE      => 'Большой АКК',
    ART_HEURISTIC_CHIP => 'Эвристический чип',
    ART_NANO_BUILDER   => 'Наностроитель',
    ART_DENSITY_CHANGER => 'Матрица трансмутации',

    UNIT_PLANS => 'Чертежи',
    UNIT_PLAN_STRUC_MINE_FUSION => 'Чертеж &quot;Термоядерная электростанция&quot;',
    UNIT_PLAN_SHIP_CARGO_SUPER => 'Чертеж &quot;Супертранспорт&quot;',
    UNIT_PLAN_SHIP_CARGO_HYPER => 'Чертеж &quot;Гипертранспорт&quot;',
    UNIT_PLAN_SHIP_DEATH_STAR => 'Чертеж &quot;Звезда Смерти&quot;',
    UNIT_PLAN_SHIP_SUPERNOVA => 'Чертеж крейсер &quot;Сверхновая&quot;',
    UNIT_PLAN_DEF_SHIELD_PLANET => 'Чертеж &quot;Планетарная защита&quot;',

    UNIT_PREMIUM => 'Премиум',
    UNIT_CAPTAIN => 'Капитан',

    UNIT_PLANET_DENSITY => 'Плотность',
  ),

));
