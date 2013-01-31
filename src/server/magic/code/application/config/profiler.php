<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Profiler
 *
 * Array of section names to display in the Profiler, TRUE to display all of them.
 * Built in sections are benchmarks, database, session, post and cookies, custom sections can be used too.
 */
$config['show'] = TRUE;

$config['post'] = FALSE;
$config['cookie'] = FALSE;
$config['session'] = FALSE;
$config['database'] = true;
$config['benchmarks'] = TRUE;
