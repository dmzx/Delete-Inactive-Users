<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2019 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\deleteinactiveusers\controller;

use phpbb\config\config;
use phpbb\template\template;
use phpbb\log\log_interface;
use phpbb\user;
use phpbb\request\request_interface;
use phpbb\db\driver\driver_interface;

class admin_controller
{
	/** @var config */
	protected $config;

	/** @var template */
	protected $template;

	/** @var log_interface */
	protected $log;

	/** @var user */
	protected $user;

	/** @var request_interface */
	protected $request;

	/** @var driver_interface */
	protected $db;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param config				$config
	 * @param template				$template
	 * @param log_interface			$log
	 * @param user					$user
	 * @param request_interface		$request
	 * @param driver_interface		$db
	 *
	 */
	public function __construct(
		config $config,
		template $template,
		log_interface $log,
		user $user,
		request_interface $request,
		driver_interface $db
	)
	{
		$this->config 			= $config;
		$this->template 		= $template;
		$this->log 				= $log;
		$this->user 			= $user;
		$this->request 			= $request;
		$this->db				= $db;
	}

	public function display_options()
	{
		add_form_key('acp_deleteinactiveusers');

		// Is the form being submitted to us?
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('acp_deleteinactiveusers'))
			{
				trigger_error('FORM_INVALID');
			}

			// Set the options the user configured
			$this->set_options();

			// Add option settings change action to the admin log
			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_DELETE_INACTIVE_USERS_SAVED');

			trigger_error($this->user->lang('DELETE_INACTIVE_USERS_SAVED') . adm_back_link($this->u_action));
		}

		$period_ary = [0 => $this->user->lang['7_DAYS'], 1 => $this->user->lang['1_MONTH'], 2 => $this->user->lang['3_MONTHS'], 3 => $this->user->lang['6_MONTHS'], 4 => $this->user->lang['1_YEAR']];
		$times = [0 => 7, 1 => 30, 2 => 90, 3 => 180, 4 => 365];
		$period = [7 => 0, 30 => 1, 90 => 2, 180 => 3, 365 => 4];
		$s_options = '';

		foreach ($period_ary as $key => $value)
		{
			$selected = ($key == $period[$this->config['deleteinactiveusers_period']]) ? ' selected="selected"' : '';
			$s_options .= '<option value="' . $times[$key]	. '"' . $selected . '>' . $period_ary[$key];
		}
		$s_options .= '</option>';

		$sql = 'SELECT group_id, group_type, group_name
			FROM ' . GROUPS_TABLE;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);

		$deleteinactiveusers_group_exceptions_options = '';

		while ($row = $this->db->sql_fetchrow($result))
		{
			if ($row['group_name'] != 'BOTS')
			{
				$group_name = ($row['group_type'] == GROUP_SPECIAL) ? $this->user->lang['G_' . $row['group_name']] : $row['group_name'];

				if (in_array($row['group_id'], explode(',', $this->config['deleteinactiveusers_group_exceptions'])))
				{
					$deleteinactiveusers_group_exceptions_options .= '<option value="' . $row['group_id'] . '" selected="selected">' . $group_name . '</option>';
				}
				else
				{
					$deleteinactiveusers_group_exceptions_options .= '<option value="' . $row['group_id'] . '">' . $group_name . '</option>';
				}
			}
		}
		$this->db->sql_freeresult($result);

		$this->template->assign_vars(array(
			'U_ACTION'									=> $this->u_action,
			'DELETE_INACTIVE_USERS_ALLOW'				=> $this->config['deleteinactiveusers_allow'],
			'DELETE_INACTIVEUSERS_ENABLE_MES'			=> $this->config['deleteinactiveusers_enable_mes'],
			'DELETE_INACTIVEUSERS_ENABLE_EMAIL'			=> $this->config['deleteinactiveusers_enable_email'],
			'DELETE_INACTIVE_USERS_GC'			 		=> $this->config['deleteinactiveusers_gc'] / 3600,
			'DELETE_INACTIVE_USERS_HOURS'				=> $this->user->lang('DELETE_INACTIVE_USERS_HOURS', $this->config['deleteinactiveusers_gc'] / 3600),
			'DELETE_INACTIVE_USERS_POSTS'				=> $this->config['deleteinactiveusers_posts'],
			'DELETE_INACTIVE_USERS_VERSION'				=> $this->config['deleteinactiveusers_version'],
			'DELETE_INACTIVE_USERS_GROUP_EXCEPTIONS' 	=> $deleteinactiveusers_group_exceptions_options,
			'S_DELETE_INACTIVE_USERS_PERIOD_SELECT'		=> $s_options
		));
	}

	protected function set_options()
	{
		$deleteinactiveusers_group_exceptions = $this->request->variable('deleteinactiveusers_group_exceptions', array(0 => 0));

		$this->config->set('deleteinactiveusers_allow', $this->request->variable('deleteinactiveusers_allow', 1));
		$this->config->set('deleteinactiveusers_enable_mes', $this->request->variable('deleteinactiveusers_enable_mes', 0));
		$this->config->set('deleteinactiveusers_enable_email', $this->request->variable('deleteinactiveusers_enable_email', 0));
		$this->config->set('deleteinactiveusers_gc', (int) $this->request->variable('deleteinactiveusers_gc', 0) * 3600);
		$this->config->set('deleteinactiveusers_posts', (int) $this->request->variable('deleteinactiveusers_posts', ''));
		$this->config->set('deleteinactiveusers_period', $this->request->variable('deleteinactiveusers_period', ''));
		$this->config->set('deleteinactiveusers_group_exceptions', implode(',' ,$deleteinactiveusers_group_exceptions));
	}

	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
