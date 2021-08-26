<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2021 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\deleteinactiveusers\migrations;

use phpbb\db\migration\migration;

class deleteinactiveusers_v104 extends migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\deleteinactiveusers\migrations\deleteinactiveusers_v103',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['deleteinactiveusers_version', '1.0.4']],
		];
	}
}
