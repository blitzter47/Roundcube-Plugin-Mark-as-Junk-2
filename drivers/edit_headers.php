<?php

/**
 * Edit headers
 * @version 1.0
 * @author Philip Weir
 * Modified by Chi-Huy Trinh, 2016
 *
 * Copyright (C) 2012-2014 Philip Weir
 *
 * This driver is part of the MarkASJunk2 plugin for Roundcube.
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

class markasjunk2_edit_headers
{
	public function spam(&$uids, $mbox)
	{
		$this->_edit_headers($uids, true);
	}

	public function ham(&$uids, $mbox)
	{
		$this->_edit_headers($uids, false);
	}

	private function _edit_headers(&$uids, $spam, $mbox=null)
	{
		$rcmail = rcube::get_instance();
		$args = $spam ? $rcmail->config->get('markasjunk2_spam_patterns') : $rcmail->config->get('markasjunk2_ham_patterns');
		$args2 = $spam ? $rcmail->config->get('markasjunk2_spam_patterns') : $rcmail->config->get('markasjunk2_ham_patterns2');

		if (sizeof($args['patterns']) == 0)
			return;
		if (sizeof($args2['patterns']) == 0)
			return;

		$new_uids = array();
        $uids_arr = explode(',',$uids);
		foreach ($uids_arr as $uid) {
			$raw_message = $rcmail->storage->get_raw_body($uid);
			$raw_headers = $rcmail->storage->get_raw_headers($uid);

			$updated_headers = preg_replace($args['patterns'], $args['replacements'], $raw_headers);
			$updated_headers = preg_replace($args2['patterns'], $args2['replacements'], $updated_headers);
			$raw_message = str_replace($raw_headers, $updated_headers, $raw_message);
			$saved = $rcmail->storage->save_message($mbox, $raw_message);

			if ($saved !== false) {
				//$rcmail->output->command('rcmail_markasjunk2_move', null, $uid);
                //$spam_mbox = $rcmail->config->get('markasjunk2_spam_mbox', $rcmail->config->get('junk_mbox', null));
				$rcmail->storage->delete_message($uid);
                //if ($spam === true)
                    //$rcmail->storage->move_message($saved, $spam_mbox);
                //else
                    //$rcmail->storage->move_message($saved, 'INBOX');
				array_push($new_uids, $saved);
			}
		}

		if (sizeof($new_uids) > 0)
			$uids = implode(',', $new_uids);//$new_uids;
        //$rcmail->output->command('checkmail'); //refresh page
	}
}

?>
