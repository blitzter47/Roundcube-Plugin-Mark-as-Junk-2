<?php

/**
 * MarkAsJunk2
 *
 * Sample plugin that adds a new button to the mailbox toolbar
 * to mark the selected messages as Junk and move them to the Junk folder
 * or to move messages in the Junk folder to the inbox - moving only the
 * attachment if it is a Spamassassin spam report email
 *
 * @author Philip Weir
 * Based on the Markasjunk plugin by Thomas Bruederli
 * Modified by Chi-Huy Trinh, 2016
 *
 * Copyright (C) 2009-2014 Philip Weir
 *
 * This program is a Roundcube (http://www.roundcube.net) plugin.
 * For more information see README.md.
 * For configuration see config.inc.php.dist.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Roundcube. If not, see http://www.gnu.org/licenses/.
 */
class markasjunk2 extends rcube_plugin
{
	public $task = 'mail';
	private $spam_mbox = null;
	private $ham_mbox = null;
	private $spam_flag = 'JUNK';
	private $ham_flag = 'NOTJUNK';
	private $toolbar = true;

	function init()
	{
		$this->register_action('plugin.markasjunk2.junk', array($this, 'mark_junk'));
		$this->register_action('plugin.markasjunk2.not_junk', array($this, 'mark_notjunk'));

		$rcmail = rcube::get_instance();
		$this->load_config();
		$this->ham_mbox = $rcmail->config->get('markasjunk2_ham_mbox', 'INBOX');
		$this->spam_mbox = $rcmail->config->get('markasjunk2_spam_mbox', $rcmail->config->get('junk_mbox', null));
		$this->toolbar = $rcmail->action == 'show' ? $rcmail->config->get('markasjunk2_cp_toolbar', true) : $rcmail->config->get('markasjunk2_mb_toolbar', true);

		if ($rcmail->action == '' || $rcmail->action == 'show') {
			$this->include_script('markasjunk2.js');
			$this->add_texts('localization', true);
			$this->include_stylesheet($this->local_skin_path() .'/markasjunk2.css');
			if ($rcmail->output->browser->ie && $rcmail->output->browser->ver == 6)
				$this->include_stylesheet($this->local_skin_path() . '/ie6hacks.css');

<<<<<<< HEAD
=======
<<<<<<< HEAD
			$mb_override = ($this->spam_mbox) ? false : true;
			$display_junk = $display_not_junk = '';
			if ($_SESSION['mbox'] == $this->spam_mbox)
				$display_junk = 'display: none;';
			elseif (!$mb_override)
				$display_not_junk = 'display: none;';

>>>>>>> temp
			if ($this->toolbar) {
                //$this->add_button(array('command' => 'plugin.markasjunk2.junk', 'type' => 'link', 'class' => 'button buttonPas markasjunk2 disabled', 'classact' => 'button markasjunk2', 'classsel' => 'button markasjunk2Sel', 'title' => 'markasjunk2.buttonjunk', 'label' => 'junk', 'style' => $display_junk), 'toolbar');
                //$this->add_button(array('command' => 'plugin.markasjunk2.not_junk', 'type' => 'link', 'class' => 'button buttonPas markasnotjunk2 disabled', 'classact' => 'button markasnotjunk2', 'classsel' => 'button markasnotjunk2Sel', 'title' => 'markasjunk2.buttonnotjunk', 'label' => 'markasjunk2.notjunk', 'style' => $display_not_junk), 'toolbar');

				$button = $this->api->output->button(array('command' => 'plugin.markasjunk2.junk', 'type' => 'link', 'class' => 'button buttonPas markasjunk2 disabled', 'classact' => 'button markasjunk2', 'classsel' => 'button markasjunk2Sel', 'title' => 'markasjunk2.buttonjunk', 'label' => 'markasjunk2.junk', 'style' => $display_junk."background-color: lightyellow;"));
                //$submenu = html::span(array('id' => 'spammenulink', 'class' => 'dropbuttontip', 'onclick' => "UI.show_popup('spammenu');return false"), null);
                //$submenu = '<span id="spammenulink" class="dropbuttontip" onclick="show_menu(\'spammenu\', this);return false"></span>';
                $submenu = '<span id="spammenulink" class="dropbuttontip"></span>';
                $this->api->add_content(html::tag('span', array('class' => 'dropbutton', 'id' => 'dbjunk', 'style' => $display_junk), $button.$submenu), 'toolbar');
                //
				$button0 = $this->api->output->button(array('command' => 'plugin.markasjunk2.not_junk', 'type' => 'link', 'class' => 'button buttonPas markasnotjunk2 disabled', 'classact' => 'button markasnotjunk2', 'classsel' => 'button markasnotjunk2Sel', 'title' => 'markasjunk2.buttonnotjunk', 'label' => 'markasjunk2.notjunk', 'style' => $display_not_junk."background-color: lightyellow;"));
                //$submenu0 = html::span(array('id' => 'nospammenulink', 'class' => 'dropbuttontip', 'onclick' => "UI.show_popup('nospammenu');return false"), null);
                //$submenu0 = '<span id="nospammenulink" class="dropbuttontip" onclick="show_menu(\'nospammenu\', this);return false"></span>';;
                $submenu0 = '<span id="nospammenulink" class="dropbuttontip"></span>';;
                $this->api->add_content(html::tag('span', array('class' => 'dropbutton', 'id' => 'dbnotjunk', 'style' => $display_not_junk), $button0.$submenu0), 'toolbar');
                //
				$markjunk = $this->api->output->button(array('command' => 'plugin.markasjunk2.junk', 'label' => 'markasjunk2.markasjunk', 'classact' => 'active'));
                $attr = array('id' => 'nospammenu', 'class' => 'popupmenu');
                $li = html::tag('li', array(), $markjunk);
                $cont = html::tag('ul', array('class' => 'toolbarmenu'), $li);
                $this->api->add_content(html::div($attr, $cont), 'toolbar');
                //
				$marknotjunk = $this->api->output->button(array('command' => 'plugin.markasjunk2.not_junk', 'label' => 'markasjunk2.markasnotjunk', 'classact' => 'active'));
                $attr = array('id' => 'spammenu', 'class' => 'popupmenu');
                $li = html::tag('li', array(), $marknotjunk);
                $cont = html::tag('ul', array('class' => 'toolbarmenu'), $li);
                $this->api->add_content(html::div($attr, $cont), 'toolbar');
            }
=======
			if ($this->toolbar) {
				// add the buttons to the main toolbar
				$this->add_button(array('command' => 'plugin.markasjunk2.junk', 'type' => 'link', 'class' => 'button buttonPas markasjunk2 disabled', 'classact' => 'button markasjunk2', 'classsel' => 'button markasjunk2Sel', 'title' => 'markasjunk2.buttonjunk', 'label' => 'junk'), 'toolbar');
				$this->add_button(array('command' => 'plugin.markasjunk2.not_junk', 'type' => 'link', 'class' => 'button buttonPas markasnotjunk2 disabled', 'classact' => 'button markasnotjunk2', 'classsel' => 'button markasnotjunk2Sel', 'title' => 'markasjunk2.buttonnotjunk', 'label' => 'markasjunk2.notjunk'), 'toolbar');
			}
>>>>>>> 805939eb36bbd0cbb421ccc2cacc1d009b1c2620
			else {
				$markjunk = $this->api->output->button(array('command' => 'plugin.markasjunk2.junk', 'label' => 'markasjunk2.markasjunk', 'id' => 'markasjunk2', 'class' => 'icon markasjunk2', 'classact' => 'icon markasjunk2 active', 'innerclass' => 'icon markasjunk2'));
				$marknotjunk = $this->api->output->button(array('command' => 'plugin.markasjunk2.not_junk', 'label' => 'markasjunk2.markasnotjunk', 'id' => 'markasnotjunk2', 'class' => 'icon markasnotjunk2', 'classact' => 'icon markasnotjunk2 active', 'innerclass' => 'icon markasnotjunk2'));
<<<<<<< HEAD
				$this->api->add_content(html::tag('li', array('role' => 'menuitem'), $markjunk), 'markmenu');
				$this->api->add_content(html::tag('li', array('role' => 'menuitem'), $marknotjunk), 'markmenu');
			}

			// add markasjunk2 folder settings to the env for JS
=======
<<<<<<< HEAD
				$this->api->add_content(html::tag('li', array('style' => $display_junk), $markjunk), 'markmenu');
				$this->api->add_content(html::tag('li', array('style' => $display_not_junk), $marknotjunk), 'markmenu');
			}

			$this->api->output->set_env('markasjunk2_override', $mb_override);
=======
				$this->api->add_content(html::tag('li', array('role' => 'menuitem'), $markjunk), 'markmenu');
				$this->api->add_content(html::tag('li', array('role' => 'menuitem'), $marknotjunk), 'markmenu');
			}

			// add markasjunk2 folder settings to the env for JS
>>>>>>> 805939eb36bbd0cbb421ccc2cacc1d009b1c2620
>>>>>>> temp
			$this->api->output->set_env('markasjunk2_ham_mailbox', $this->ham_mbox);
			$this->api->output->set_env('markasjunk2_spam_mailbox', $this->spam_mbox);

			$this->api->output->set_env('markasjunk2_move_spam', $rcmail->config->get('markasjunk2_move_spam', false));
			$this->api->output->set_env('markasjunk2_move_ham', $rcmail->config->get('markasjunk2_move_ham', false));

			// check for init method from driver
			$this->_call_driver('init');
		}
	}

	function mark_junk()
	{
		$this->add_texts('localization');
		$this->_set_flags();

		$uids = rcube_utils::get_input_value('_uid', rcube_utils::INPUT_POST);
		$mbox = rcube_utils::get_input_value('_mbox', rcube_utils::INPUT_POST);

		if ($this->_spam($uids, $mbox, $this->spam_mbox))
			$this->api->output->command('display_message', $this->gettext('reportedasjunk'), 'confirmation');

		$this->api->output->send();
	}

	function mark_notjunk()
	{
		$this->add_texts('localization');
		$this->_set_flags();

		$uids = rcube_utils::get_input_value('_uid', rcube_utils::INPUT_POST);
		$mbox = rcube_utils::get_input_value('_mbox', rcube_utils::INPUT_POST);

		if ($this->_ham($uids, $mbox, $this->ham_mbox))
			$this->api->output->command('display_message', $this->gettext('reportedasnotjunk'), 'confirmation');

		$this->api->output->send();
	}

	private function _spam($uids, $mbox_name = NULL, $dest_mbox = NULL)
	{
		$rcmail = rcube::get_instance();
		$storage = $rcmail->storage;

		if ($rcmail->config->get('markasjunk2_learning_driver', false)) {
			$result = $this->_call_driver($uids, true);

<<<<<<< HEAD
			if (!$result)
				return false;
		}
=======
			if ($rcmail->config->get('markasjunk2_learning_driver', false)) {
				$result = $this->_call_driver('spam', $uids, $mbox);
<<<<<<< HEAD
=======
>>>>>>> 805939eb36bbd0cbb421ccc2cacc1d009b1c2620
>>>>>>> temp

		if ($rcmail->config->get('markasjunk2_read_spam', false))
			$storage->set_flag($uids, 'SEEN', $mbox_name);

		if ($rcmail->config->get('markasjunk2_spam_flag', false))
			$storage->set_flag($uids, $this->spam_flag, $mbox_name);

		if ($rcmail->config->get('markasjunk2_ham_flag', false))
			$storage->unset_flag($uids, $this->ham_flag, $mbox_name);

		if ($dest_mbox && $mbox_name != $dest_mbox)
			$this->api->output->command('rcmail_markasjunk2_move', $dest_mbox, $uids);
		else
			$this->api->output->command('command', 'list', $mbox_name);

		return true;
	}

	private function _ham($uids, $mbox_name = NULL, $dest_mbox = NULL)
	{
		$rcmail = rcube::get_instance();
		$storage = $rcmail->storage;

		if ($rcmail->config->get('markasjunk2_learning_driver', false)) {
			$result = $this->_call_driver($uids, false);

<<<<<<< HEAD
			if (!$result)
				return false;
		}
=======
			if ($rcmail->config->get('markasjunk2_learning_driver', false)) {
				$result = $this->_call_driver('ham', $uids, $mbox);
<<<<<<< HEAD
=======
>>>>>>> 805939eb36bbd0cbb421ccc2cacc1d009b1c2620
>>>>>>> temp

		if ($rcmail->config->get('markasjunk2_unread_ham', false))
			$storage->unset_flag($uids, 'SEEN', $mbox_name);

		if ($rcmail->config->get('markasjunk2_spam_flag', false))
			$storage->unset_flag($uids, $this->spam_flag, $mbox_name);

		if ($rcmail->config->get('markasjunk2_ham_flag', false))
			$storage->set_flag($uids, $this->ham_flag, $mbox_name);

		if ($dest_mbox && $mbox_name != $dest_mbox)
			$this->api->output->command('rcmail_markasjunk2_move', $dest_mbox, $uids);
		else
			$this->api->output->command('command', 'list', $mbox_name);

		return true;
	}

<<<<<<< HEAD
	private function _call_driver($action, &$uids = null, $mbox = null)
=======
<<<<<<< HEAD
	private function _call_driver(&$uids, $spam)
=======
	private function _call_driver($action, &$uids = null, $mbox = null)
>>>>>>> 805939eb36bbd0cbb421ccc2cacc1d009b1c2620
>>>>>>> temp
	{
		$driver = $this->home.'/drivers/'. rcube::get_instance()->config->get('markasjunk2_learning_driver', 'cmd_learn') .'.php';
		$class = 'markasjunk2_' . rcube::get_instance()->config->get('markasjunk2_learning_driver', 'cmd_learn');

		if (!is_readable($driver)) {
			rcube::raise_error(array(
				'code' => 600,
				'type' => 'php',
				'file' => __FILE__,
				'line' => __LINE__,
				'message' => "MarkasJunk2 plugin: Unable to open driver file $driver"
				), true, false);
		}

		include_once $driver;

		if (!class_exists($class, false) || !method_exists($class, 'spam') || !method_exists($class, 'ham')) {
			rcube::raise_error(array(
				'code' => 600,
				'type' => 'php',
				'file' => __FILE__,
				'line' => __LINE__,
				'message' => "MarkasJunk2 plugin: Broken driver: $driver"
				), true, false);
		}

		$object = new $class;
		if ($action == 'spam')
			$object->spam($uids, $mbox);
		elseif ($action == 'ham')
			$object->ham($uids, $mbox);
		elseif ($action == 'init' && method_exists($object, 'init')) // method_exists check here for backwards compatibility, init method added 20161127
			$object->init();

		return $object->is_error ? false : true;
	}

	private function _set_flags()
	{
		$rcmail = rcube::get_instance();

		if ($rcmail->config->get('markasjunk2_spam_flag', false)) {
			if ($flag = array_search($rcmail->config->get('markasjunk2_spam_flag'), $rcmail->storage->conn->flags))
				$this->spam_flag = $flag;
			else
				$rcmail->storage->conn->flags[$this->spam_flag] = $rcmail->config->get('markasjunk2_spam_flag');
		}

		if ($rcmail->config->get('markasjunk2_ham_flag', false)) {
			if ($flag = array_search($rcmail->config->get('markasjunk2_ham_flag'), $rcmail->storage->conn->flags))
				$this->ham_flag = $flag;
			else
				$rcmail->storage->conn->flags[$this->ham_flag] = $rcmail->config->get('markasjunk2_ham_flag');
		}
	}
}

?>
