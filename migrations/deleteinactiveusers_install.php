<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2019 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\deleteinactiveusers\migrations;

use phpbb\db\migration\migration;

class deleteinactiveusers_install extends migration
{
	public function update_data()
	{
		return [
			// Add config
			['config.add', ['deleteinactiveusers_version', '1.0.0']],
			['config.add', ['deleteinactiveusers_allow', 0]],
			['config.add', ['deleteinactiveusers_posts', 0]],
			['config.add', ['deleteinactiveusers_gc', 86400]],
			['config.add', ['deleteinactiveusers_last_gc', '0', true]],
			['config.add', ['deleteinactiveusers_period', '180', true]],
			['config.add', ['deleteinactiveusers_group_exceptions', '4, 5', '0']],

			// ACP module
			['module.add', [
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_DELETE_INACTIVE_USERS_TITLE'
			]],
			['module.add', [
				'acp',
				'ACP_DELETE_INACTIVE_USERS_TITLE',
				[
					'module_basename'	=> '\dmzx\deleteinactiveusers\acp\acp_deleteinactiveusers_module',
				],
			]],
		];
	}
}
