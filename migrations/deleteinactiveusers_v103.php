<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2020 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\deleteinactiveusers\migrations;

class deleteinactiveusers_v103 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return [
			'\dmzx\deleteinactiveusers\migrations\deleteinactiveusers_v102',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['deleteinactiveusers_version', '1.0.3']],
		];
	}
}
