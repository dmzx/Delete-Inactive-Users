<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2019 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\deleteinactiveusers\migrations;

class deleteinactiveusers_v101 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array(
			'\dmzx\deleteinactiveusers\migrations\deleteinactiveusers_install',
		);
	}

	public function update_data()
	{
		return [
			['config.update', ['deleteinactiveusers_version', '1.0.1']],
			['config.add', ['deleteinactiveusers_enable_mes', 0]],
		];
	}
}
