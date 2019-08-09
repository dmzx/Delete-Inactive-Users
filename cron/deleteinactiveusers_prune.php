<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2019 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\deleteinactiveusers\cron;

use phpbb\config\config;
use phpbb\cron\task\base;
use phpbb\db\driver\driver_interface;
use phpbb\log\log_interface;
use phpbb\user;

class deleteinactiveusers_prune extends base
{
	/** @var user */
	protected $user;

	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var log_interface */
	protected $log;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/**
	 * @param user				$user
	 * @param config			$config
	 * @param driver_interface	$db
	 * @param log_interface		$log
	 * @param string			$root_path
	 * @param string			$php_ext
	 */
	public function __construct(
		user $user,
		config $config,
		driver_interface $db,
		log_interface $log,
		$root_path,
		$php_ext
	)
	{
		$this->user				= $user;
		$this->config			= $config;
		$this->db				= $db;
		$this->log				= $log;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;
	}

	public function run()
	{
		$inactive_time = time() - ($this->config['deleteinactiveusers_period'] * 86400);

		$sql = 'SELECT u.user_id, u.user_type, u.username, u.user_posts, u.user_regdate, u.user_lastvisit
			FROM ' . USERS_TABLE . ' u
			WHERE ' . $this->db->sql_in_set('group_id', explode(',', $this->config['deleteinactiveusers_group_exceptions']), true) . '
				AND u.user_id <> ' . ANONYMOUS . '
				AND u.user_type = ' . USER_NORMAL . '
				AND u.user_posts <= ' . $this->config['deleteinactiveusers_posts'] . '
				AND u.user_regdate < ' . $inactive_time . '
				AND u.user_lastvisit < ' . $inactive_time;
		$result = $this->db->sql_query($sql);

		$expired_users = array();

		while ($row = $this->db->sql_fetchrow($result))
		{
			$expired_users[(int) $row['user_id']] = $row['username'];
		}
		$this->db->sql_freeresult($result);

		if ($expired_users)
		{
			if (!function_exists('user_delete'))
			{
				include($this->root_path . 'includes/functions_user.' . $this->php_ext);
			}

			user_delete('remove', array_keys($expired_users));
		}
		$this->config->set('deleteinactiveusers_last_gc', time());
	}

	public function is_runnable()
	{
		return $this->config['deleteinactiveusers_allow'];
	}

	public function should_run()
	{
		return $this->config['deleteinactiveusers_last_gc'] < time() - $this->config['deleteinactiveusers_gc'];
	}
}
