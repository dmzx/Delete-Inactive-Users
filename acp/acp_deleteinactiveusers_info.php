<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2019 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\deleteinactiveusers\acp;

class acp_deleteinactiveusers_info
{
	function module()
	{
		return [
			'filename'	=> '\dmzx\deleteinactiveusers\acp\acp_deleteinactiveusers_module',
			'title'		=> 'ACP_DELETE_INACTIVE_USERS_TITLE',
			'modes'		=> [
				'settings'	=> ['title' => 'ACP_DELETE_INACTIVE_USERS_SETTINGS', 'auth' => 'ext_dmzx/deleteinactiveusers && acl_a_board', 'cat' => ['ACP_DELETE_INACTIVE_USERS_TITLE']],
			],
		];
	}
}
