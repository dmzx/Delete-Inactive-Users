<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2019 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [
	'DELETE_INACTIVE_USERS_ALLOW'						=> 'Enable Delete Inactive Users',
	'DELETE_INACTIVE_USERS_ALLOW_EXPLAIN'				=> 'If this option is set to Yes, cron on users will be On.',
	'DELETE_INACTIVEUSERS_ENABLE_MES'					=> 'Enable register message',
	'DELETE_INACTIVEUSERS_ENABLE_MES_EXPLAIN'			=> 'Show message after user is registered with details about post count and period.',
	'DELETE_INACTIVE_USERS_TIME_VALUE'					=> 'Set time period for prune/delete users',
	'DELETE_INACTIVE_USERS_TIME_VALUE_EXPLAIN'			=> 'This option will set the prune/delete timer. Default is 24 hours.',
	'DELETE_INACTIVE_USERS_HOURS'	=> array(
		1 => 'Hour',
		2 => 'Hours',
	),
	'DELETE_INACTIVE_USERS_POSTS'						=> 'Set Post count',
	'DELETE_INACTIVE_USERS_POSTS_EXPLAIN'				=> 'Set post count for users. Default is 0.',
	'DELETE_INACTIVE_USERS_INACTIVE_PERIOD'				=> 'Select period',
	'DELETE_INACTIVE_USERS_INACTIVE_PERIOD_EXPLAIN'		=> 'Set period of inactivity by users. Registration date and last activity date.',
	'DELETE_INACTIVE_USERS_SAVED'						=> 'Delete Inactive Users settings saved.',
	'DELETE_INACTIVE_USERS_GROUP_EXCEPTIONS'			=> 'Group exception(s)',
	'DELETE_INACTIVE_USERS_GROUP_EXCEPTIONS_EXPLAIN'	=> 'Exclude the groups here that not will be pruned.<br />Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and clicking.',
]);
