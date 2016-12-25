/**
 * MarkAsJunk2 plugin script
 *
 * @licstart  The following is the entire license notice for the
 * JavaScript code in this file.
 *
 * Copyright (C) 2009-2014 Philip Weir
 * Modified by Chi-Huy Trinh, 2016
 *
 * The JavaScript code in this page is free software: you can redistribute it
 * and/or modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * @licend  The above is the entire license notice
 * for the JavaScript code in this file.
 */

function rcmail_markasjunk2(prop) {
	if (!rcmail.env.uid && (!rcmail.message_list || !rcmail.message_list.get_selection().length))
		return;

	if (!prop || prop == 'markasjunk2')
		prop = 'junk';

	var prev_sel = null;

	// also select childs of (collapsed) threads
	if (rcmail.message_list) {
		if (rcmail.env.uid) {
			if (rcmail.message_list.rows[rcmail.env.uid].has_children && !rcmail.message_list.rows[rcmail.env.uid].expanded) {
				if (!rcmail.message_list.in_selection(rcmail.env.uid)) {
					prev_sel = rcmail.message_list.get_selection();
					rcmail.message_list.select_row(rcmail.env.uid);
				}

				rcmail.message_list.select_childs(rcmail.env.uid);
				rcmail.env.uid = null;
			}
			else if (rcmail.message_list.get_single_selection() == rcmail.env.uid) {
				rcmail.env.uid = null;
			}
		}
		else {
			selection = rcmail.message_list.get_selection();
			for (var i in selection) {
				if (rcmail.message_list.rows[selection[i]].has_children && !rcmail.message_list.rows[selection[i]].expanded)
					rcmail.message_list.select_childs(selection[i]);
			}
		}
	}

	var uids = rcmail.env.uid ? rcmail.env.uid : rcmail.message_list.get_selection().join(',');

	var lock = rcmail.set_busy(true, 'loading');
	rcmail.http_post('plugin.markasjunk2.' + prop, '_uid='+uids+'&_mbox='+urlencode(rcmail.env.mailbox), lock);

	if (prev_sel) {
		rcmail.message_list.clear_selection();

		for (var i in prev_sel)
			rcmail.message_list.select_row(prev_sel[i], CONTROL_KEY);
	}
}

function rcmail_markasjunk2_notjunk(prop) {
	rcmail_markasjunk2('not_junk');
}

rcube_webmail.prototype.rcmail_markasjunk2_move = function(mbox, uid) {
	var prev_uid = rcmail.env.uid;
	var prev_sel = null;
	var a_uids = uid.split(",");

	if (rcmail.message_list && a_uids.length == 1 && !rcmail.message_list.rows[a_uids[0]]) {
		rcmail.env.uid = a_uids[0];
	}
	else if (rcmail.message_list && a_uids.length == 1 && !rcmail.message_list.in_selection(a_uids[0]) && !rcmail.env.threading) {
		rcmail.env.uid = a_uids[0];
		rcmail.message_list.remove_row(rcmail.env.uid, false);
	}
	else if (rcmail.message_list && (!rcmail.message_list.in_selection(a_uids[0]) || a_uids.length != rcmail.message_list.selection.length)) {
		prev_sel = rcmail.message_list.get_selection();
		rcmail.message_list.clear_selection();

		for (var i in a_uids)
			rcmail.message_list.select_row(a_uids[i], CONTROL_KEY);
	}

	if (mbox)
		rcmail.move_messages(mbox);
	else
		rcmail.delete_messages();

	rcmail.env.uid = prev_uid;

	if (prev_sel) {
		rcmail.message_list.clear_selection();

		for (var i in prev_sel) {
			if (prev_sel[i] != uid)
				rcmail.message_list.select_row(prev_sel[i], CONTROL_KEY);
		}
	}
}

function rcmail_markasjunk2_init() {
	if (window.rcm_contextmenu_register_command) {
		rcm_contextmenu_register_command('markasjunk2', 'rcmail_markasjunk2', rcmail.gettext('markasjunk2.markasjunk'), 'reply', null, true, null, null, 'markmessage markasjunk2');
		rcm_contextmenu_register_command('markasnotjunk2', 'rcmail_markasjunk2_notjunk', rcmail.gettext('markasjunk2.markasnotjunk'), 'reply', null, true, null, null, 'markmessage markasnotjunk2');
		$('#rcmContextMenu li.unflagged').removeClass('separator_below');
		$('#rcmContextMenu li.reply').addClass('separator_above');
	}
}

function rcmail_markasjunk2_update() {
	var spamobj = $('#' + rcmail.buttons['plugin.markasjunk2.junk'][0].id);
	var hamobj = $('#' + rcmail.buttons['plugin.markasjunk2.not_junk'][0].id);

	if (spamobj.parent('li').length > 0) {
		spamobj = spamobj.parent();
		hamobj = hamobj.parent();
	}

<<<<<<< HEAD
	if (!rcmail.env.markasjunk2_override && rcmail.env.markasjunk2_spam_mailbox && rcmail.env.mailbox != rcmail.env.markasjunk2_spam_mailbox) {
		$('#spammenulink').parent().show();
		$('#nospammenulink').parent().hide();
		$('#rcmContextMenu li.markasjunk2').show();
		$('#rcmContextMenu li.markasnotjunk2').hide();
		spamobj.show();
		hamobj.hide();
	}
	else if (!rcmail.env.markasjunk2_override) {
		$('#spammenulink').parent().hide();
		$('#nospammenulink').parent().show();
		$('#rcmContextMenu li.markasjunk2').hide();
		$('#rcmContextMenu li.markasnotjunk2').show();
		spamobj.hide();
		hamobj.show();
=======
	var disp = {'spam': true, 'ham': true};
	if (!rcmail.is_multifolder_listing() && rcmail.env.markasjunk2_spam_mailbox) {
		if (rcmail.env.mailbox != rcmail.env.markasjunk2_spam_mailbox) {
			disp.ham = false;
		}
		else {
			disp.spam = false;
		}
>>>>>>> 805939eb36bbd0cbb421ccc2cacc1d009b1c2620
	}

	var evt_rtn = rcmail.triggerEvent('markasjunk2-update', {'objs': {'spamobj': spamobj, 'hamobj': hamobj}, 'disp': disp});
	if (evt_rtn && evt_rtn.abort)
		return;
	disp = evt_rtn ? evt_rtn.disp : disp;

	disp.spam ? spamobj.show() : spamobj.hide();
	disp.ham ? hamobj.show() : hamobj.hide();
}

function rcmail_markasjunk2_status(command) {
	switch (command) {
		case 'beforedelete':
			if (!rcmail.env.flag_for_deletion && rcmail.env.trash_mailbox &&
				rcmail.env.mailbox != rcmail.env.trash_mailbox &&
				(rcmail.message_list && !rcmail.message_list.shiftkey))
				rcmail.enable_command('plugin.markasjunk2.junk', 'plugin.markasjunk2.not_junk', false);

			break;
		case 'beforemove':
		case 'beforemoveto':
			rcmail.enable_command('plugin.markasjunk2.junk', 'plugin.markasjunk2.not_junk', false);
			break;
		case 'aftermove':
		case 'aftermoveto':
			if (rcmail.env.action == 'show')
				rcmail.enable_command('plugin.markasjunk2.junk', 'plugin.markasjunk2.not_junk', true);

			break;
		case 'afterpurge':
		case 'afterexpunge':
			if (!rcmail.env.messagecount && rcmail.task == 'mail')
				rcmail.enable_command('plugin.markasjunk2.junk', 'plugin.markasjunk2.not_junk', false);

			break;
	}
}

$(document).ready(function() {
	if (window.rcmail) {
		rcmail.addEventListener('init', function(evt) {
			// register command (directly enable in message view mode)
			rcmail.register_command('plugin.markasjunk2.junk', rcmail_markasjunk2, rcmail.env.uid);
			rcmail.register_command('plugin.markasjunk2.not_junk', rcmail_markasjunk2_notjunk, rcmail.env.uid);

			if (rcmail.message_list) {
				rcmail.message_list.addEventListener('select', function(list) {
					rcmail.enable_command('plugin.markasjunk2.junk', list.get_selection().length > 0);
					rcmail.enable_command('plugin.markasjunk2.not_junk', list.get_selection().length > 0);
				});
			}
		});

		rcmail.add_onload('rcmail_markasjunk2_init()');
		rcmail.addEventListener('listupdate', function(props) { rcmail_markasjunk2_update(); } );

		rcmail.addEventListener('beforemoveto', function(mbox) {
			if (mbox && typeof mbox === 'object')
				mbox = mbox.id;

			// check if destination mbox equals junk box (and we're not already in the junk box)
			if (rcmail.env.markasjunk2_move_spam && mbox && mbox == rcmail.env.markasjunk2_spam_mailbox && mbox != rcmail.env.mailbox) {
				rcmail_markasjunk2();
				return false;

			}
			// or if destination mbox equals ham box and we are in the junk box
			else if (rcmail.env.markasjunk2_move_ham && mbox && mbox == rcmail.env.markasjunk2_ham_mailbox && rcmail.env.mailbox == rcmail.env.markasjunk2_spam_mailbox) {
				rcmail_markasjunk2_notjunk();
				return false;
			}

			return;
		} );

		// update button activation after external events
		rcmail.addEventListener('beforedelete', function(props) { rcmail_markasjunk2_status('beforedelete'); } );
		rcmail.addEventListener('beforemove', function(props) { rcmail_markasjunk2_status('beforemove'); } );
		rcmail.addEventListener('beforemoveto', function(props) { rcmail_markasjunk2_status('beforemoveto'); } );
		rcmail.addEventListener('aftermove', function(props) { rcmail_markasjunk2_status('aftermove'); } );
		rcmail.addEventListener('aftermoveto', function(props) { rcmail_markasjunk2_status('aftermoveto'); } );
		rcmail.addEventListener('afterpurge', function(props) { rcmail_markasjunk2_status('afterpurge'); } );
		rcmail.addEventListener('afterexpunge', function(props) { rcmail_markasjunk2_status('afterexpunge'); } );
		$('#nospammenulink').click( function(e) {
            //var pos1 = $('#nospammenulink').offset();
            //var pos2 = $('#nospammenu').offset();
            //var posY = pos.top - $(window).scrollTop();
            //var posX = pos.left - $(window).scrollLeft(); 
            //var h = $('#nospammenu').height();
            //var w = $('#nospammenu').width();
            //pos.top = pos.top - $(window).scrollTop();//2 * h;
            //pos.left = pos.left - 2 * w;//$(window).scrollLeft();//pos.left - w;
            //$('#nospammenu').css( "left", posX );
            //$('#nospammenu').css( "top", posY );
            //$('#nospammenu').css( pos );
            //$('#nospammenu').css( "position", "absolute" );
            if (!$( '#nospammenu' ).is(":visible"))
                $( '#nospammenu' ).show();
            else
                $( '#nospammenu' ).hide();
                
        } );
		$('#spammenulink').click( function(e) {
            //var pos = $('#spammenulink').offset();
            //var h = $('#spammenu').height();
            //var w = $('#spammenu').width();
            //pos.top = 2 * h;
            //pos.left = pos.left - w;
            //$('#spammenu').css( pos );
            ////$('#spammenu').css( "position", "absolute" );
            if (!$( '#spammenu' ).is(":visible"))
                $( '#spammenu' ).show();
            else
                $( '#spammenu' ).hide();
        } );
        jQuery(document).click(function(e) {
            if (!$( '#spammenu' ).is(":visible") && !$( '#nospammenu' ).is(":visible"))
                return;
            var target = e.target; //target div recorded
            if (target.id != 'spammenulink') {
                jQuery('#spammenu').hide(); //if the click element is not the above id will hide
            }
            if (target.id != 'nospammenulink') {
                jQuery('#nospammenu').hide(); //if the click element is not the above id will hide
            }
        })
	}
});
