<?php
/**
*
* @package phpBB Extension - Delete Inactive Users
* @copyright (c) 2019 dmzx - https://www.dmzx-web.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace dmzx\deleteinactiveusers\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use phpbb\language\language;
use phpbb\config\config;

class listener implements EventSubscriberInterface
{
	/** @var language */
	protected $language;

	/** @var config */
	protected $config;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param language			$language
	 * @param config			$config
	 * @param string			$root_path
	 * @param string			$php_ext
	 */
	public function __construct(
		language $language,
		config $config,
		$root_path,
		$php_ext
	)
	{
		$this->lang				= $language;
		$this->config			= $config;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return [
			'core.ucp_register_register_after' => 'ucp_register_register_after',
		];
	}

	public function ucp_register_register_after($event)
	{
		if ($this->config['deleteinactiveusers_allow'] && $this->config['deleteinactiveusers_enable_mes'])
		{
			$this->lang->add_lang('deleteinactiveusers', 'dmzx/deleteinactiveusers');

			$minimum_post = $this->lang->lang('DELETE_INACTIVE_USERS_TOPIC', $this->config['deleteinactiveusers_posts'] + 1);

			$period_ary = [
				0 => $this->lang->lang('7_DAYS'),
				1 => $this->lang->lang('1_MONTH'),
				2 => $this->lang->lang('3_MONTHS'),
				3 => $this->lang->lang('6_MONTHS'),
				4 => $this->lang->lang('1_YEAR')
			];

			$period = [
				7 	=> 0,
				30 	=> 1,
				90 	=> 2,
				180 => 3,
				365 => 4
			];

			foreach($period_ary as $key => $value)
			{
				$selected = $period[$this->config['deleteinactiveusers_period']];
				$period_active = $period_ary[$selected];
			}

			$message = $event['message'];
			$message = $message . '<br /><br />' . $this->lang->lang('DELETE_INACTIVE_USERS_MESSAGE_REG', $this->config['deleteinactiveusers_posts'], $period_active, $minimum_post) . '<span style="float: right;">' . $this->lang->lang('DELETE_INACTIVE_USERS_MESSAGE_RETURN', '<a href="' . append_sid("{$this->root_path}index.$this->php_ext") . '">', '</a>') . '</span>';
			trigger_error($message);

			$event['message'] = $message;
		}
	}
}
