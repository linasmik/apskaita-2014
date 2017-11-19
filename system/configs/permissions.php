<?php

// Jei nera konstantos metam errora
defined('WEB_INIT') or die('Error 403 - Forbidden');

// Vartotojo teises
$permissions = array(
	'change_settings'	=> 1,
	'change_avatar'		=> 2,
	'change_password'	=> 3,
	'change_email'		=> 4,
	'privmsg_read'		=> 5,
	'privmsg_write'		=> 6,
	'privmsg_delete'	=> 7,
	'forum_write_post'	=> 8,
	'forum_edit_post'	=> 9,
	'forum_create_topic'	=> 10,
	'forum_edit_topic'	=> 11,
	'forum_admin_lock_topic'	=> 12,
	'forum_admin_edit_topic'	=> 13,
	'forum_admin_delete_topic'	=> 14,
	'forum_admin_delete_post'	=> 15,
	'forum_admin_hide_post'		=> 16,
	'news_write'	=> 17,
	'news_edit'	=> 18,
	'news_hide'	=> 19,
	'news_delete'	=> 20,
	'poll_create'	=> 21,
	'poll_edit'	=> 22,
	'poll_delete'	=> 23,
	'poll_stop'	=> 24,
	'admin_edit_users'	=> 25,
	'admin_edit_user_forum_perm'	=> 26,
	'admin_edit_user_other_perm'	=> 27,
	'admin_edit_user_profile'	=> 28,
	'admin_edit_user_pm'		=> 29,
	'admin_block_ip'		=> 30,
	'super_admin'			=> 31
);

?>