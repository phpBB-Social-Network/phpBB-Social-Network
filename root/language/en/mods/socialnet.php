<?php
/**
 *
 * @package phpBB Social Network
 * @version 0.7.0
 * @copyright (c) phpBB Social Network Team 2010-2012 http://phpbbsocialnetwork.com
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (!isset($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	/**
	 * Edit these two lines write your own Welcome text for unregistered guests on Activity page.
	 */
	'SN_AP_WELCOME_TITLE'					 => 'Welcome to our website!',
	'SN_AP_WELCOME_TEXT'					 => 'Feel free to register and use all of the features of our website.<br /><br />Greetings,<br />the Administrator',

	'SN_MODULE_IM_NAME'						 => 'Instant Messenger',
	'SN_MODULE_USERSTATUS_NAME'				 => 'User Status',
	'SN_MODULE_APPROVAL_NAME'				 => 'Friends management system',

	'SN_IM_CHAT'							 => 'Chat',
	'SN_IM_NO_ONLINE_USER'					 => 'No user is online',
	'SN_IM_YOU_ARE_OFFLINE'					 => 'You are offline',
	'SN_IM_SOUND'							 => 'Sound',
	'SN_IM_SELECT_NAME'						 => 'Choose a sound',
	'SN_IM_NEW_MESSAGE'						 => 'New message',
	'SN_IM_LOGIN'							 => 'Online',
	'SN_IM_LOGOUT'							 => 'Offline',
	'SN_IM_PRESS_TO_CLOSE'					 => 'Press %1$s to close chat box',
	'SN_IM_PRESS_TO_SEND'					 => 'Press %1$s to send message',

	'SN_US_SHARE_STATUS'					 => 'Share',
	'SN_US_WHATS_ON_YOUR_MIND'				 => 'What is on your mind?',
	'SN_US_EMPTY_STATUS'					 => 'You can not submit an empty status',
	'SN_US_COMMENT'							 => 'Comment',
	'SN_US_EMPTY_COMMENT'					 => 'You can not submit an empty comment',
	'SN_US_USER_STATUS_WALL'				 => 'Activity',
	'SN_US_WRITE_COMMENT'					 => 'Write a comment...',
	'SN_US_COMMENT_STATUS'					 => 'Comment',
	'SN_US_HAS_NO_STATUS'					 => 'has no status',
	'SN_US_HAS_DELETED_STATUS'				 => 'This status has been deleted',
	'SN_STATUS_NOT_EXISTS'					 => 'This status does not exist',
	'SN_US_HAS_NO_ACTIVITY'					 => 'has no activity',
	'SN_US_SHARED_STATUS'					 => 'You have shared your status',
	'SN_US_DELETE_STATUS'					 => 'Delete',
	'SN_US_LOAD_MORE'						 => 'Older posts',
	'SN_US_VIEW'						 	 => 'View',
	'SN_US_LOAD_MORE_COMMENT'				 => 'more comment',
	'SN_US_LOAD_MORE_COMMENTS'				 => 'more comments',
	'SN_US_CONFIRM'							 => 'Confirm',
	'SN_US_CLOSE'							 => 'Close',
	'SN_US_CANCEL'							 => 'Cancel',
	'SN_US_SHARED_A'						 => 'shared a',
	'SN_US_LINK'							 => 'link',

	//
	// FETCH PAGE
	//
	'SN_US_FETCH_PAGE'						 => 'Fetch page',
	'SN_US_FETCH_CLEAR'						 => 'Clear loaded page',
	'SN_US_NO_VIDEO_THUMB'					 => 'No video preview',
	'LOADER'								 => 'Loader',
	'NEXT_IMAGE'							 => 'Next image',
	'PREVIOUS_IMAGE'						 => 'Previous image',
	'OF'									 => 'of',
	'SN_US_NO_IMG_THUMB'					 => 'No image preview',
	'SN_US_CHOOSE_THUMB'					 => 'images',
	'SN_CB_FETCH_ERROR'						 => 'An error was encountered when fetching the web page',

	'SN_AP_ACTIVITYPAGE'					 => 'Activity page',
	'SN_AP_AND'								 => 'and',
	'SN_AP_ARE_FRIENDS'						 => 'are now friends',
	'SN_AP_ADD_AS_FRIEND'					 => 'Add as a Friend',
	'SN_AP_PRIVATE_MESSAGE'					 => 'Messages',
	'SN_AP_MANAGE_PROFILE'					 => 'Edit My Profile',
	'SN_AP_VIEW_FRIENDS'					 => 'View My Friends',
	'SN_AP_VIEW_SUGGESTIONS'				 => 'People You May Know',
	'SN_AP_MANAGE_FRIENDS'					 => 'Manage Friends',
	'SN_AP_BOARD'							 => 'Discussion Board',
	'SN_AP_VIEW_MEMBERLIST'					 => 'View Members',
	'SN_AP_LOG_OUT'							 => 'Log Out',
	'SN_AP_LAST_POSTS'						 => 'Recent discussions',
	'SN_AP_TOTAL_FRIEND'					 => 'You have 1 friend',
	'SN_AP_TOTAL_FRIENDS'					 => 'You have %s friends',
	'SN_AP_FRIEND_SUGGESTIONS'				 => 'People you may know',
	'SN_AP_REQUESTS_LIST'					 => 'Requests',
	'SN_AP_ONLINE_FRIENDS'					 => 'Friends online',
	'SN_AP_NO_ONLINE_USER'					 => 'No users online',
	'SN_AP_NO_DISCUSSION'					 => 'No recent discussions',
	'SN_AP_NO_BIRTHDAY'					 	 => 'There are no birthdays coming up',
	'SN_AP_NO_ENTRY'						 => 'Nothing new here',
	'SN_AP_LOAD_NEWS'						 => 'Refresh',
	'SN_AP_SEE_ALL'							 => 'View All',
	'SN_AP_NO_FRIENDS'						 => 'You have no friends',
	'SN_AP_KEEP_LOGGEDIN'					 => 'Keep me logged in',
	'SN_AP_STATISTICS'						 => 'Statistics',
	'SN_AP_TOTAL_USERS'						 => '<strong>%d</strong> members',
	'SN_AP_TOTAL_POSTS'						 => '<strong>%d</strong> posts',
	'SN_AP_TOTAL_TOPICS'					 => '<strong>%d</strong> topics',
	'SN_AP_TOPICS_PER_DAY'					 => '<strong>%d</strong> topics per day',
	'SN_AP_POSTS_PER_DAY'					 => '<strong>%d</strong> posts per day',
	'SN_AP_USERS_PER_DAY'					 => '<strong>%d</strong> users per day',
	'SN_AP_BIRTHDAY'						 => 'Birthday',
	'SN_AP_BIRTHDAY_1'						 => 'birthday <span class="sn-ap-textNoWrap">%1$s</span>',
	'SN_AP_BIRTHDAY_2'						 => 'birthday on <span class="sn-ap-textNoWrap">%1$s</span>',
	'SN_AP_BIRTHDAY_USERNAME'				 => '%1$s\'s',
	'SN_AP_WELCOME'							 => 'Welcome',
	'SN_AP_VIEWING_ACTIVITYPAGE'			 => 'Viewing Activity page',
	'SN_AP_NO_SUGGESTIONS'					 => 'Currently, there are no friend suggestions for you',
	'SN_AP_SEARCH'							 => 'Search…',
	'SN_AP_CHANGED_PROFILE_HIS'				 => 'has updated his profile',
	'SN_AP_CHANGED_PROFILE_HER'				 => 'has updated her profile',
	'SN_AP_CHANGED_PROFILE_THEIR'			 => 'has updated their profile',
	'SN_UP_CHANGED_AVATAR_HIS'				 => 'has changed his profile picture',
	'SN_UP_CHANGED_AVATAR_HER'				 => 'has changed her profile picture',
	'SN_UP_CHANGED_AVATAR_THEIR'			 => 'has changed their profile picture',
	'SN_AP_ADDED_NEW_FAMILY_MEMBER_HIS'		 => 'has added %1$s (%2$s) as a new family member to his profile',
	'SN_AP_ADDED_NEW_FAMILY_MEMBER_HER'		 => 'has added %1$s (%2$s) as a new family member to her profile',
	'SN_AP_ADDED_NEW_FAMILY_MEMBER_THEIR'	 => 'has added %1$s (%2$s) as a new family member to their profile',
	'SN_AP_CHANGED_RELATIONSHIP_HIS'		 => 'has added a new relationship to his profile',
	'SN_AP_CHANGED_RELATIONSHIP_HER'		 => 'has added a new relationship to her profile',
	'SN_AP_CHANGED_RELATIONSHIP_THEIR'		 => 'has added a new relationship to their profile',
	'SN_UP_SEND_EMOTE'						 => 'has sent an emote to',

	'SN_PROFILE'							 => 'Profile',
	'SN_MYPROFILE'							 => 'My Profile',

	// User profile
	'SN_UP_PROFILE_UPDATED'					 => 'Your profile has been updated successfully.',
	'SN_UP_HOMETOWN'						 => 'Hometown',
	'SN_UP_SEX'								 => 'Sex',
	'SN_UP_INTERESTED_IN'					 => 'Interested in',
	'SN_UP_LANGUAGES'						 => 'Languages',
	'SN_UP_ABOUT_ME'						 => 'About me',
	'SN_UP_EMPLOYER'						 => 'Employer',
	'SN_UP_UNIVERSITY'						 => 'University',
	'SN_UP_HIGH_SCHOOL'						 => 'High school',
	'SN_UP_OCCUPATION'						 => 'Occupation',
	'SN_UP_RELIGION'						 => 'Religion',
	'SN_UP_POLITICAL_VIEWS'					 => 'Political views',
	'SN_UP_QUOTATIONS'						 => 'Favorite quotations',
	'SN_UP_INTERESTS'						 => 'Interests',
	'SN_UP_MUSIC'							 => 'Music',
	'SN_UP_BOOKS'							 => 'Books',
	'SN_UP_MOVIES'							 => 'Movies',
	'SN_UP_GAMES'							 => 'Games',
	'SN_UP_FOODS'							 => 'Foods',
	'SN_UP_SPORTS'							 => 'Sports you play',
	'SN_UP_SPORT_TEAMS'						 => 'Favorite sport teams',
	'SN_UP_ACTIVITIES'						 => 'Activities',
	'SN_UP_SKYPE'							 => 'Skype',
	'SN_UP_FACEBOOK'						 => 'Facebook',
	'SN_UP_TWITTER'							 => 'Twitter',
	'SN_UP_YOUTUBE'							 => 'Youtube',
	'SN_UP_USER_ICQ'						 => 'ICQ number',
	'SN_UP_USER_AIM'						 => 'AOL Instant Messenger',
	'SN_UP_USER_MSNM'						 => 'WL/MSN Messenger',
	'SN_UP_USER_YIM'						 => 'Yahoo Messenger',
	'SN_UP_USER_JABBER'						 => 'Jabber address',
	'SN_UP_USER_WEBSITE'					 => 'Website',
	'SN_UP_USER_FROM'						 => 'Location',
	'SN_UP_USER_INTERESTS'					 => 'Interests',
	'SN_UP_BDAY_MONTH'						 => 'Month of birth',
	'SN_UP_BDAY_DAY'						 => 'Day of birth',
	'SN_UP_BDAY_YEAR'						 => 'Year of birth',
	'SN_UP_USERNAME'						 => 'Username',
	'SN_UP_USER_EMAIL'						 => 'E-mail',
	'SN_UP_USER_BIRTHDAY'					 => 'Birthday',
	'SN_UP_USER_OCC'						 => 'Occupation',
	'SN_UP_USER_SIG'						 => 'Signature',
	'SN_UP_PROFILE_VIEWS'					 => 'Profile views',
	'SN_UP_X_TIMES'							 => 'x',
	'SN_UP_PROFILE_VISITORS'				 => 'Profile visitors',
	'SN_UP_LAST_CHANGE'						 => 'Last profile update',
	'SN_UP_MALE'							 => 'Male',
	'SN_UP_MALES'							 => 'Males',
	'SN_UP_FEMALE'							 => 'Female',
	'SN_UP_FEMALES'							 => 'Females',
	'SN_UP_BOTH'							 => 'Both',
	'SN_UP_RELATIONSHIP'					 => 'Relationship',
	'SN_UP_SINGLE'							 => 'Single',
	'SN_UP_IN_RELATIONSHIP'					 => 'In a relationship',
	'SN_UP_ENGAGED'							 => 'Engaged',
	'SN_UP_MARRIED'							 => 'Married',
	'SN_UP_ITS_COMPLICATED'					 => 'It\'s complicated',
	'SN_UP_OPEN_RELATIONSHIP'				 => 'In an open relationship',
	'SN_UP_WIDOWED'							 => 'Widowed',
	'SN_UP_SEPARATED'						 => 'Separated',
	'SN_UP_DIVORCED'						 => 'Divorced',
	'SN_UP_TO'								 => 'to',
	'SN_UP_WITH'							 => 'with',
	'SN_UP_ANNIVERSARY'						 => 'Anniversary',
	'SN_UP_ANNIVERSARY_ON'					 => 'Anniversary on',
	'SN_UP_BIRTHDAY'						 => 'Birthday',
	'SN_UP_SUNDAY'							 => 'Sunday',
	'SN_UP_MONDAY'							 => 'Monday',
	'SN_UP_TUESDAY'							 => 'Tuesday',
	'SN_UP_WEDNESDAY'						 => 'Wednesday',
	'SN_UP_THURSDAY'						 => 'Thursday',
	'SN_UP_FRIDAY'							 => 'Friday',
	'SN_UP_SATURDAY'						 => 'Saturday',
	'SN_UP_SUNDAY_MIN'						 => 'Su',
	'SN_UP_MONDAY_MIN'						 => 'Mo',
	'SN_UP_TUESDAY_MIN'						 => 'Tu',
	'SN_UP_WEDNESDAY_MIN'					 => 'We',
	'SN_UP_THURSDAY_MIN'					 => 'Th',
	'SN_UP_FRIDAY_MIN'						 => 'Fr',
	'SN_UP_SATURDAY_MIN'					 => 'Sa',
	'SN_UP_JANUARY_MIN'						 => 'Jan',
	'SN_UP_FEBRUARY_MIN'					 => 'Feb',
	'SN_UP_MARCH_MIN'						 => 'Mar',
	'SN_UP_APRIL_MIN'						 => 'Apr',
	'SN_UP_MAY_MIN'							 => 'May',
	'SN_UP_JUNE_MIN'						 => 'Jun',
	'SN_UP_JULY_MIN'						 => 'Jul',
	'SN_UP_AUGUST_MIN'						 => 'Aug',
	'SN_UP_SEPTEMBER_MIN'					 => 'Sep',
	'SN_UP_OCTOBER_MIN'						 => 'Oct',
	'SN_UP_NOVEMBER_MIN'					 => 'Nov',
	'SN_UP_DECEMBER_MIN'					 => 'Dec',
	'SN_UP_FAMILY'							 => 'Family',
	'SN_UP_SELECT_RELATIONSHIP'				 => 'Add a relationship',
	'SN_UP_SELECT_FAMILY_RELATION'			 => 'Add a family member',
	'SN_UP_SISTER'							 => 'Sister',
	'SN_UP_BROTHER'							 => 'Brother',
	'SN_UP_DAUGHTER'						 => 'Daughter',
	'SN_UP_SON'								 => 'Son',
	'SN_UP_MOTHER'							 => 'Mother',
	'SN_UP_FATHER'							 => 'Father',
	'SN_UP_AUNT'							 => 'Aunt',
	'SN_UP_UNCLE'							 => 'Uncle',
	'SN_UP_NIECE'							 => 'Niece',
	'SN_UP_NEPHEW'							 => 'Nephew',
	'SN_UP_COUSIN_FEMALE'					 => 'Cousin: Female',
	'SN_UP_COUSIN_MALE'						 => 'Cousin: Male',
	'SN_UP_GRANDDAUGHTER'					 => 'Granddaughter',
	'SN_UP_GRANDSON'						 => 'Grandson',
	'SN_UP_GRANDMOTHER'						 => 'Grandmother',
	'SN_UP_GRANDFATHER'						 => 'Grandfather',
	'SN_UP_SISTER_IN_LAW'					 => 'Sister-in-law',
	'SN_UP_BROTHER_IN_LAW'					 => 'Brother-in-law',
	'SN_UP_MOTHER_IN_LAW'					 => 'Mother-in-law',
	'SN_UP_FATHER_IN_LAW'					 => 'Father-in-law',
	'SN_UP_DAUGHTER_IN_LAW'					 => 'Daughter-in-law',
	'SN_UP_SON_IN_LAW'						 => 'Son-in-law',
	'SN_UP_ADD_FAMILY_MEMBER'				 => 'Add family member',
	'SN_UP_ADD_FAMILY_ERR_MEMBER_EMPTY'		 => 'Empty family member name',
	'SN_UP_APPROVE'							 => 'Approve',
	'SN_UP_IGNORE'							 => 'Ignore',
	'SN_UP_APPROVE_RELATION_SUBJECT'		 => '%1$s has created a relationship with you',
	'SN_UP_APPROVE_RELATION_TEXT'			 => '%2$s has created the relationship with you: <strong>%3$s %2$s</strong>.<br /><br />%1$sYou can approve this relationship here%4$s',
	'SN_UP_APPROVE_RELATION_CONFIRM'		 => 'Are you sure you want to approve this relationship?',
	'SN_UP_REFUSE_RELATION_CONFIRM'			 => 'Are you sure you want to refuse this relationship?',
	'SN_UP_APPROVE_RELATION_ERROR_CANCELED'	 => 'This relationship has been canceled',
	'SN_UP_APPROVE_RELATION_ERROR_MYSELF'	 => 'You can not create a relationship with yourself',
	'SN_UP_APPROVE_RELATION_ERROR_APPROVED'	 => 'This relationship has already been approved',
	'SN_UP_APPROVE_RELATION_ERROR_REFUSED'	 => 'This relationship has already been refused',
	'SN_UP_APPROVE_RELATION_VICE_VERSA'		 => 'I want to add this relationship to my profile too.',
	'SN_UP_DELETE_RELATIONSHIP_CONFIRM'		 => 'Are you sure you want to delete this relationship?',
	'SN_UP_APPROVE_RELATION_NO_RELATIONSHIP' => 'No relationship status',
	'SN_UP_APPROVE_FAMILY_SUBJECT'			 => '%1$s has added you as a %2$s',
	'SN_UP_APPROVE_FAMILY_TEXT'				 => '%2$s has added you as a <strong>%3$s</strong>.<br /><br />%1$sYou can approve this relationship here%4$s',
	'SN_UP_APPROVE_FAMILY_CONFIRM'			 => 'Are you sure you want to approve this relationship?',
	'SN_UP_REFUSE_FAMILY_CONFIRM'			 => 'Are you sure you want to refuse this relationship?',
	'SN_UP_APPROVE_FAMILY_ERROR_CANCELED'	 => 'This relationship has been canceled',
	'SN_UP_APPROVE_FAMILY_ERROR_MYSELF'		 => 'You can not add yourself as your family member',
	'SN_UP_APPROVE_FAMILY_ERROR_APPROVED'	 => 'This relationship has already been approved.',
	'SN_UP_APPROVE_FAMILY_ERROR_REFUSED'	 => 'This relationship has already been refused.',
	'SN_UP_APPROVE_FAMILY_ERROR_EXIST'		 => '%1$s has already been added to your family',
	'SN_UP_APPROVE_FAMILY_VICE_VERSA'		 => 'I want to add %1$s to my family members too.',
	'SN_UP_APPROVE_FAMILY_USERNAME'			 => '%1$s is my',
	'SN_UP_APPROVE_NO_FAMILY_MEMBER'		 => 'No family member',
	'SN_UP_DELETE_FAMILY_CONFIRM'			 => 'Are you sure you want to delete <strong>%1$s</strong> from your family members?',
	'SN_UP_USERNAME_NOT_EXIST'				 => 'This username you entered does not exist',
	'SN_UP_NOT_APPROVED'					 => 'not approved yet',
	'SN_UP_RELATION_REFUSED'				 => 'refused',
	'SN_UP_RELATION_REQUESTS'				 => 'Requests',
	'SN_UP_APPROVE_REQUESTS'				 => 'Approve relationship',
	'WRONG_DATA_FACEBOOK'					 => 'The Facebook address has to be a valid URL, including the protocol. For example http://www.facebook.com/facebook/.',
	'WRONG_DATA_TWITTER'					 => 'The Twitter address has to be a valid URL, including the protocol. For example http://twitter.com/#!/twitter/.',
	'WRONG_DATA_YOUTUBE'					 => 'The Youtube address has to be a valid URL, including the protocol. For example http://www.youtube.com/user/youtube/.',
	'WRONG_DATA_FAMILY_USER'				 => 'One of family usernames you entered doesn\'t exist',
	'WRONG_DATA_RELATION_USER'				 => 'The relationship username you entered doesn\'t exist',
	'WRONG_DATA_ANNIVERSARY'				 => 'The anniversary has to be a valid date in the form dd-mm-yyyy. For example 01-12-2011',
	'TOO_SHORT_FACEBOOK'					 => 'The Facebook address you entered is too short, a minimum of 12 characters is required.',
	'TOO_SHORT_TWITTER'						 => 'The Twitter address you entered is too short, a minimum of 12 characters is required.',
	'TOO_SHORT_YOUTUBE'						 => 'The Youtube address you entered is too short, a minimum of 12 characters is required.',
	'TOO_SHORT_ANNIVERSARY'					 => 'The Anniversary you entered is too short, a minimum of 8 characters is required.',
	'TOO_SHORT_SKYPE'						 => 'The Skype name you entered is too short, a minimum of 6 characters is required.',
	'SN_UP_WALL'							 => 'Activity',
	'SN_UP_INFO'							 => 'Info',
	'SN_UP_FRIENDS'							 => 'Friends',
	'SN_UP_STATS'							 => 'Statistics',
	'SN_UP_BASIC_INFO'						 => 'Basic Info',
	'SN_UP_EDU_WORK'						 => 'Education and Work',
	'SN_UP_PHILOSOPHY'						 => 'Philosophy',
	'SN_UP_ENT_ACT'							 => 'Entertainment and Activities',
	'SN_UP_CONTACT_INFO'					 => 'Contact Info',
	'SN_UP_OTHER_INFO'						 => 'Other Info',
	'SN_UP_LAST_VISITORS'					 => 'Last profile visitors',
	'SN_UP_PROFILE_VIEWED'					 => 'Profile viewed',
	'SN_UP_ADD_FRIEND'						 => 'Add friend',
	'SN_UP_ADD_FRIEND_TO_GROUP'				 => 'Add to group',
	'SN_UP_EDIT_PROFILE'					 => 'Edit profile',
	'SN_UP_EDIT_FRIENDS'					 => 'Manage friends',
	'SN_UP_EDIT_RELATIONS'					 => 'Manage relations',
	'SN_UP_REPORT_PROFILE'					 => 'Report user',
	'SN_UP_EMPTY_REPORT'					 => 'You have to choose the report reason',
	'SN_UP_REPORT_SUCCESS'					 => 'User has been reported successfully',
	'SN_UP_CAN_LEAVE_BLANK'					 => 'This can be left blank.',
	'SN_UP_MORE_INFO'						 => 'Further information',
	'SN_UP_RETURN_TO_PROFILE'				 => '%1$sReturn to profile%2$s',
	'SN_UP_TABS_SPINNER'					 => '<em>Loading&#8230;<\/em>',
	'SN_UP_EMOTES'							 => 'Send Emote',

	'SN_UP_PROFILE_VALUE_DELETED'			 => '<em>Removed</em>',

	'SN_NTF_EMOTE_CB_TITLE'					 => 'Emote sent',
	'SN_NTF_EMOTE_CB_TEXT'					 => 'Emote %2$s %3$s has been sent successfully to %1$s',

	'SN_IN'									 => 'in',

	'AVATAR'								 => 'Avatar',

	'FOES'									 => 'Foes',
	'MUTUAL'								 => 'Mutual friends',
	'SUGGESTIONS'							 => 'Suggestions',

	/**
	 * CONFIRM BOXES
	 */
	'SN_CB_DELETE_STATUS_TITLE'				 => 'Delete status',
	'SN_CB_DELETE_STATUS_TEXT'				 => 'Are you sure you want to delete this status?',
	'SN_CB_DELETE_COMMENT_TITLE'			 => 'Delete comment',
	'SN_CB_DELETE_COMMENT_TEXT'				 => 'Are you sure you want to delete this comment?',
	'SN_CB_DELETE_ACTIVITY_TITLE'			 => 'Delete activity',
	'SN_CB_DELETE_ACTIVITY_TEXT'			 => 'Are you sure you want to delete this activity?',

	/**
	 * SOCIALNET TIME AGO
	 */
	'SN_TIME_AGO'							 => '%1$u %2$s ago',
	'SN_TIME_FROM_NOW'						 => '%1$u %2$s from now',
	'SN_TIME_PERIODS'						 => array(
		'SECOND'	 => 'second',
		'SECONDS'	 => 'seconds',
		'MINUTE'	 => 'minute',
		'MINUTES'	 => 'minutes',
		'HOUR'		 => 'hour',
		'HOURS'		 => 'hours',
		'DAY'		 => 'day',
		'DAYS'		 => 'days',
		'WEEK'		 => 'week',
		'WEEKS'		 => 'weeks',
		'MONTH'		 => 'month',
		'MONTHS'	 => 'months',
		'YEAR'		 => 'year',
		'YEARS'		 => 'years',
		'DECADE'	 => 'decade',
		'DECADES'	 => 'decades',
	)
));

// UCP
$lang = array_merge($lang, array(
	// UCP
	'UCP_SOCIALNET'							 => 'Social Network',
	'UCP_SOCIALNET_SETTINGS'				 => 'Social Network settings',
	'UCP_SN_IM'								 => 'Instant Messenger settings',
	'UCP_SN_IM_SETTINGS'					 => 'Instant Messenger settings',
	'UCP_SN_IM_HISTORY'						 => 'Instant Messenger history',
	'UCP_SN_APPROVAL_UFG'					 => 'Friends groups',
	'UCP_SOCIALNET_IM_PURGE_MESSAGES'		 => 'Instant Messenger purge messages',
	'UCP_SOCIALNET_USERSTATUS'				 => 'User Status settings',
	'UCP_SN_PROFILE'						 => 'Edit personal info',
	'UCP_SN_PROFILE_RELATIONS'				 => 'Relationships &amp; Family relations',

	// Instant Messenger
	'IM_ONLINE'								 => 'I am Online',
	'IM_ONLINE_EXPLAIN'						 => 'If yes, friends will see you in the online list and they can chat with you.',

	'IM_HISTORY_PURGED_AT'					 => 'Instant Messenger history has been deleted by administrator on %1$s',
	'IM_NO_HISTORY'							 => 'You have no instant messages',
	//'IM_HISTORY_WITH'						 => 'History with',
	'IM_MSG_TOTAL'							 => '1 message',
	'IM_MSGS_TOTAL'							 => '%1$s messages',
	'IM_CONVERSATION_TOTAL'					 => '1 conversation',
	'IM_CONVERSATIONS_TOTAL'				 => '%1$s conversations',
	'IM_SOUND_SELECT_NAME'					 => 'Select sound',
	'EXPORT_IM_HISTORY'						 => 'Export conversation with %s',
	//'IM_HISTORY_SELECT_USER'				 => 'Select user',
	'IM_GROUP_UNDECIDED'					 => 'No category',

	// Friends approval
	'ADD_FRIEND'							 => 'Add new friend',
	'ACCEPT_FRIEND'							 => 'Accept friend request',

	'SN_APPROVAL_FRIENDS'					 => 'Approve friendship',
	'SN_APPROVALS_FRIENDS_EXPLAIN'			 => 'Here you can approve users who have requested to be your friend.',

	'SN_APPROVE'							 => 'Accept',
	'SN_NO_APPROVE'							 => 'Deny',
	'SN_REFUSE'								 => 'Refuse',

	'SN_APPROVAL_REQUESTS'					 => 'Your requests',
	'SN_APPROVAL_REQUESTS_EXPLAIN'			 => 'Here you can cancel requests which you have sent.',

	'SN_VIEW_PROFILE'						 => 'View profile',

	'SN_CANCEL_REQUEST'						 => 'Cancel request',

	'SN_REMOVE_FRIEND'						 => 'Remove friend',
	'SN_REMOVE_FRIENDS'						 => 'Your friends',
	'SN_REMOVE_FRIENDS_EXPLAIN'				 => 'Here you can see all your friends and remove them from your friend list',

	'SN_USING_AVATARS_1_EXPLAIN'			 => 'Click on the users to select them, then confirm the operation',

	'FRIENDS_APPROVALS_SUCCESS'				 => ' has been added to your friendlist',
	'FRIENDS_APPROVALS_REQUEST_EXIST'		 => 'You have already sent the request to',
	'FRIENDS_APPROVALS_DENY'				 => 'The friend request has been canceled',
	'FRIENDS_APPROVALS_REMOVE'				 => 'The friend has been removed successfully',
	'FRIENDS_APPROVALS_ADDED'				 => 'The friend request has been send successfully',

	'SN_FAS_FRIEND_LIST'					 => 'Friend list',
	'SN_FAS_COMMON_FRIEND_LIST'				 => 'Mutual friends',
	'SN_FAS_REMOVE'							 => 'Friend removed',

	'FAS_FRIEND_TOTAL'						 => 'One friend',
	'FAS_FRIENDS_TOTAL'						 => '%1$s friends',
	'FAS_FRIEND_NO_TOTAL'					 => 'No friends',
	'FAS_FRIENDGROUP_TOTAL'					 => 'One friend',
	'FAS_FRIENDGROUPS_TOTAL'				 => '%1$s friends',
	'FAS_FRIENDGROUP_NO_TOTAL'				 => 'No friends',
	'FAS_APPROVE_TOTAL'						 => 'One approve',
	'FAS_APPROVES_TOTAL'					 => '%1$s approves',
	'FAS_APPROVE_NO_TOTAL'					 => 'No approves',
	'FAS_CANCEL_TOTAL'						 => 'One request',
	'FAS_CANCELS_TOTAL'						 => '%1$s requests',
	'FAS_CANCEL_NO_TOTAL'					 => 'No requests',
	'FAS_COMMON_TOTAL'						 => 'One mutual friend',
	'FAS_COMMONS_TOTAL'						 => '%1$s mutual friends',
	'FAS_COMMON_NO_TOTAL'					 => 'No mutual friend',
	'FAS_MUTUAL_NO_TOTAL'					 => 'No mutual friend',
	'FAS_MUTUAL_TOTAL'						 => 'One mutual friend',
	'FAS_MUTUALS_TOTAL'						 => '%1$s mutual friends',
	'FAS_SUGGESTION_NO_TOTAL'				 => 'No suggestion',
	'FAS_SUGGESTION_TOTAL'					 => 'One suggestion',
	'FAS_SUGGESTIONS_TOTAL'					 => '%1$s suggestions',

	'SN_FAS_NOT_ADDED_FRIENDS_IN_APPROVAL'	 => 'User has already been added',
	'SN_FAS_NOT_ADDED_FRIENDS_IN_FOES'		 => 'User is already a foe',
	'SN_FAS_NOT_ADDED_FRIENDS_IN_FRIENDS'	 => 'User is already a friend',

	// Friends groups
	'UFG_CREATE'							 => 'Create a new friends group',
	'UFG_NAME'								 => 'Friends group name',
	'UFG_CREATE_EXPLAIN'					 => 'You can create a friends group here to divide your friends into groups.',
	'UFG_MANAGE'							 => 'Friends groups',
	'UFG_DRAG_FRIENDS_INTO_UFG'				 => 'Drag and drop users into the friends group',
	'SN_CREATE_NEW_GROUP'					 => 'Create new group',
	//'CONFIRM_CREATE_UFG'					 => 'Are you sure you want to create <strong>%1$s</strong> friends group?',
	'CONFIRM_DELETE_UFG'					 => 'Are you sure you want to delete <strong>%1$s</strong> friends group?',
	'FMS_DELETE_UFG'						 => 'Delete friend user group',
	'FMS_DELETE_UFG_TEXT'					 => 'Are you sure you want to delete this friend user group?',

	'ADD_FRIEND_TO_GROUP'					 => 'Add friend to friends group',
	'ERROR_GROUP_EMPTY_NAME'				 => 'Empty group name',
	'ERROR_GROUP_ALREADY_EXISTS'			 => 'You have already created this group',
));

// NTF MESSAGE TITLES FOR PMs
$lang = array_merge($lang, array(
	'SN_NTF_FRIENDSHIP_REQUEST_PM_TITLE'		=> '%1$s sent you a friendship request',
	'SN_NTF_FRIENDSHIP_CANCEL_PM_TITLE'			=> '%1$s canceled your friendship request',
	'SN_NTF_FRIENDSHIP_DENY_PM_TITLE'				=> '%1$s denied your friendship request',
	'SN_NTF_FRIENDSHIP_ACCEPT_PM_TITLE'			=> '%1$s accepted your friendship request',

	'SN_NTF_STATUS_FRIEND_WALL_PM_TITLE'	 	=> '%1$s has left a message on your Profile page',
	'SN_NTF_STATUS_USER_COMMENT_PM_TITLE'	 	=> '%1$s has commented on %2$s\'s status',
	'SN_NTF_STATUS_AUTHOR_COMMENT_PM_TITLE'	=> '%1$s has commented on your status',

	'SN_NTF_APPROVE_FAMILY_PM_TITLE'				=> '%1$s has added you as a %2$s',
	'SN_NTF_APPROVE_RELATIONSHIP_PM_TITLE'	=> '%1$s has created a relationship with you',

	'SN_NTF_EMOTE_PM_TITLE'					 				=> '%1$s has sent you an emote',

	'SN_NTF_RELATIONSHIP_APPROVED_PM_TITLE'	=> '%1$s has confirmed a relationship with you',
	'SN_NTF_FAMILY_APPROVED_PM_TITLE'		 		=> '%1$s has confirmed a family relation with you',

	'SN_NTF_RELATIONSHIP_REFUSED_PM_TITLE'	=> '%1$s has refused a relationship with you',
	'SN_NTF_FAMILY_REFUSED_PM_TITLE'				=> '%1$s has refused a family relation with you',

	'SN_NTF_STATUS_FRIEND_MENTION_PM_TITLE' => '%1$s has mentioned you in his status',
));

// MCP
$lang = array_merge($lang, array(
	'MCP_SOCIALNET'					 => 'Social Network',
	'MCP_SN_REPORTUSER'				 => 'Reported users',

	'POSTS_IN_QUEUE'				 => 'Posts in queue',

	'SN_UP_REPORTED_USER'			 => 'Reported user',
	'SN_UP_REPORT_TEXT'				 => 'Details',
	'SN_UP_REASON'					 => 'Reason',
	'SN_UP_VIEW_REPORTS'			 => 'View reports',
	'SN_UP_CLOSE_REPORT_CONFIRM'	 => 'Are you sure you want to close this report?',
	'SN_UP_CLOSE_REPORTS_CONFIRM'	 => 'Are you sure you want to close these reports?',
	'SN_UP_CLOSE_REPORT_SUCCESS'	 => 'Report has been closed successfully.',
	'SN_UP_CLOSE_REPORTS_SUCCESS'	 => 'Reports have been closed successfully.',
	'SN_UP_DELETE_REPORT_CONFIRM'	 => 'Are you sure you want to delete this report?',
	'SN_UP_DELETE_REPORTS_CONFIRM'	 => 'Are you sure you want to delete these reports?',
	'SN_UP_DELETE_REPORT_SUCCESS'	 => 'Report has been deleted successfully.',
	'SN_UP_DELETE_REPORTS_SUCCESS'	 => 'Reports have been deleted successfully.',
));

// NOTIFY
$lang = array_merge($lang, array(
	'SN_AP_NOTIFY'					 => 'Notifications',
	'SN_NO_NOTIFY'					 => 'You have no notification',
	'SN_NTF_FRIENDSHIP_ACCEPT'		 => '%1$s accepted your <a href="%2$s">friendship request</a>',
	'SN_NTF_FRIENDSHIP_DENY'		 => '%1$s denied your <a href="%2$s">friendship request</a>',
	'SN_NTF_FRIENDSHIP_REQUEST'		 => '%1$s sent you a <a href="%2$s">friendship request</a>',
	'SN_NTF_FRIENDSHIP_CANCEL'		 => '%1$s canceled your <a href="%2$s">friendship request</a>',

	'SN_NTF_STATUS_AUTHOR_COMMENT'	 => '%1$s has commented on <a href="%2$s">your status</a>',
	'SN_NTF_STATUS_USER_COMMENT'		 => '%1$s has commented on <a href="%3$s">%2$s\'s status</a>',
	'SN_NTF_STATUS_FRIEND_WALL'		 	=> '%1$s has left a message on <a href="%2$s">your Profile page</a>',

	'SN_NTF_APPROVE_FAMILY'			 => '%1$s has added you as a %2$s. You can <a href="%3$s">approve this relationship here</a>',
	'SN_NTF_APPROVE_RELATIONSHIP'	 => '%1$s has created a relationship with you. You can <a href="%2$s">approve this relationship here</a>',

	'SN_NTF_EMOTE'					 => '%1$s has sent you an emote: %2$s %3$s',

	'SN_NTF_RELATIONSHIP_APPROVED'	 => '%1$s has confirmed <a href="%2$s">relationship</a> with you',
	'SN_NTF_FAMILY_APPROVED'		 => '%1$s has confirmed <a href="%2$s">family relation</a> with you',

	'SN_NTF_RELATIONSHIP_VICEVERSA'	 => '%1$s has confirmed <a href="%2$s">relationship</a> with you and has added it to their profile too',
	'SN_NTF_FAMILY_VICEVERSA'		 => '%1$s has confirmed <a href="%2$s">family relation</a> with you and has added it to their profile too',

	'SN_NTF_RELATIONSHIP_REFUSED'	 => '%1$s has refused <a href="%2$s">relationship</a> with you',
	'SN_NTF_FAMILY_REFUSED'			 => '%1$s has refused <a href="%2$s">family relation</a> with you',

	'SN_NTF_STATUS_FRIEND_MENTION' => '%1$s has mentioned you in <a href="%2$s">his status</a>',
));

// EMOTES
$lang = array_merge($lang, array(
	'SN_UP_EMOTES_USER'	 => 'Emotes',
));

// EXPANDER
$lang = array_merge($lang, array(
	'SN_EXPANDER_READ_MORE'	 => 'See More',
	'SN_EXPANDER_READ_LESS'	 => 'Close',
));

// OUTDATED BROWSER
$lang = array_merge($lang, array(
  'BROWSER_OUTDATED_TITLE'	 => 'Your browser is outdated',
	'BROWSER_OUTDATED'	 => 'Some of the features will not work on your browser. We highly recommend you to update it.',
));

?>