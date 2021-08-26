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

		$sql = 'SELECT u.user_id, u.user_type, u.username, u.user_posts, u.user_regdate, u.user_lastvisit, u.user_email, u.user_lang
			FROM ' . USERS_TABLE . ' u
			WHERE ' . $this->db->sql_in_set('group_id', explode(',', $this->config['deleteinactiveusers_group_exceptions']), true) . '
				AND u.user_id <> ' . ANONYMOUS . '
				AND u.user_type = ' . USER_NORMAL . '
				AND u.user_posts <= ' . $this->config['deleteinactiveusers_posts'] . '
				AND u.user_regdate < ' . $inactive_time . '
				AND u.user_lastvisit < ' . $inactive_time;
		$result = $this->db->sql_query($sql);

		$msg_list = $expired_users = [];

		while ($row = $this->db->sql_fetchrow($result))
		{
			$expired_users[(int) $row['user_id']] = $row['username'];

			$msg_list[$row['user_id']] = [
				'name' 		=> $row['username'],
				'email' 	=> $row['user_email'],
				'regdate' 	=> $row['user_regdate'],
				'lang' 		=> $row['user_lang'],
				'time' 		=> time()
			];
		}
		$this->db->sql_freeresult($result);

		if ($expired_users)
		{
			if (!function_exists('user_delete'))
			{
				include($this->root_path . 'includes/functions_user.' . $this->php_ext);
			}

			$this->add_admin_log('LOG_DELETE_INACTIVE_USERS', [
				count($expired_users),
				implode($this->user->lang('COMMA_SEPARATOR'), $expired_users),
			]);

			if ($this->config['deleteinactiveusers_enable_email'] && sizeof($msg_list))
			{
				// Email the inactive and deleted users
				$this->deleteinactiveusers_email($msg_list);
			}

			user_delete('remove', array_keys($expired_users));
		}
		$this->config->set('deleteinactiveusers_last_gc', time());
	}

	protected function add_admin_log($lang_key, $additional_data = [])
	{
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, $lang_key, false, $additional_data);
	}

	private function deleteinactiveusers_email($msg_list)
	{
		if ($this->config['deleteinactiveusers_enable_email'] && sizeof($msg_list))
		{
			if ($this->config['email_enable'])
			{
				if (!class_exists('messenger'))
				{
					include($this->phpbb_root_path . 'includes/functions_messenger.' . $this->php_ext);
				}

				$messenger = new \messenger(false);

				foreach ($msg_list as $key => $value)
				{
					$messenger->template('@dmzx_deleteinactiveusers/deleteinactiveusers_email', $value['lang']);
					$messenger->to($value['email'], $value['name']);
					$messenger->headers('X-AntiAbuse: Board servername - ' . $this->config['server_name']);
					$messenger->headers('X-AntiAbuse: User_id - ' . $key);
					$messenger->headers('X-AntiAbuse: Username - ' . $value['name']);
					$messenger->headers('X-AntiAbuse: User IP - ' . $this->user->ip);
					$messenger->assign_vars([
						'USERNAME'		=> htmlspecialchars_decode($value['name']),
						'REGISTER_DATE'	=> date('g:ia \o\n l jS F Y', $value['regdate'])
					]);
					$messenger->send(NOTIFY_EMAIL);
				}

				$userlist = array_map(function ($entry)
				{
					return $entry['name'];
				}, $msg_list);

				$this->log->add('admin', $this->user->data['user_id'], $this->user->data['session_ip'], 'LOG_DELETE_INACTIVE_USERS_EMAIL', false, [implode(', ', $userlist)]);
			}
		}
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
