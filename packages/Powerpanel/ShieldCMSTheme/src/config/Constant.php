<?php

use Jenssegers\Agent\Agent;

$agent = new Agent;
$device = 'pc';

if ($agent->isMobile()) {
    $device = 'mobile';
} elseif ($agent->is('iPad')) {
    $device = 'iPad';
}

$returnArray = array(
    'ENV_APP_URL' => env('APP_URL'),
    'DEVICE' => $device,
    'SITE_ID' => '38',
    'TEST_MODE' => 'Y',
    'CHRSearchRank' => 'Y',
    'CHRContentScheduling' => 'Y',
    'SITE_TOCKEN' => 'de3fe1e9991d5f0686f057e1583119be',
    'MAIL_API_ID' => 'NetcluesAdmin',
    'MAIL_API_PSW' => 'NetcluesAdm!N$123#',
    'MAIL_API_URL' => 'https://www.cluesconnect.com/emailservice/email',
    'BUCKET_ENABLED' => false,
    'BUCKET_FRONT_URL' => '',
    'BUCKET_NAME' => '',
    'S3_MEDIA_BUCKET_PATH' => 'assets/images',
    'S3_MEDIA_BUCKET_DOCUMENT_PATH' => 'documents',
    'S3_MEDIA_BUCKET_AUDIO_PATH' => 'audios',
    'S3_MEDIA_BUCKET_VIDEO_PATH' => 'assets/videos',
    'S3_MEDIA_BUCKET_GENERAL_PATH' => 'generalfiles',
    'S3_MEDIA_BUCKET_XML_PATH' => 'xmlupload',
    'DRAFT_LIST' => '<img src="'.env('APP_URL').'assets/images/Draft_img.png'.'">',
    'APPROVAL_LIST' => '<img src="'.env('APP_URL').'assets/images/Appoval_img.png'.'">',
    'ARCHIVE_LIST' => '<img src="'.env('APP_URL').'assets/images/Archive_img.png'.'">',
    'RECOMMANDED_IMAGE_SIZES' => '600*600,400*600,600*400',
    
    
    'Authorization_Key' => 'AAAAlrs1h28:APA91bGQhSCaszETmSF1f-q8RBDVQ3pv-R903P-gRIQWQ4rFg9I_HmZVpfGuHJRWoeA1yYvoC2MssjXWoVb-DSuLBXQIXA88bEMlqIph0AMZn0L-eaDBRDBKquJ-GunQJnd_nlTj28al',
    'messagingSenderId' => '647385941871',
    'apiKey' => 'AIzaSyBpey0qKFF2ViikmtqpHoaJzMLAzTjgmvU',
    'projectId' => 'notification-80209',
    'appId' => '1:647385941871:web:7adc22cf765a0c1a860a9a',
);

if ($returnArray['BUCKET_ENABLED'] == true) {
    $returnArray['CDN_PATH'] = 'https://' . $returnArray['BUCKET_FRONT_URL'];
} else {
    $returnArray['CDN_PATH'] = env('APP_URL');
}

/* constants for log values */
$logconstants = array(
	'UPDATE_DRAFT'=>'Update Draft',
	'TRASH_RECORD_RESTORE'=>'Trash Record Restore',
	'TRASH_RECORD_MOVE_TO_FAVORITE'=> 'Trash Record Move To Favorite',
	'SENT_FOR_APPROVAL'=> 'Sent for Approval',
	'ROLLBACK_RECORD'=> 'RollBack Record',	
	'RECORD_RESTORE'=> 'Record Restore',
	'RECORD_UNARCHIVE'=> 'Record UnArchive',
	'RECORD_APPROVED'=> 'Record Approved', 
	'QUICK_EDIT_TO_PRIMARY_RECORD'=> 'Quick edit To Record',
	'QUICK_EDIT_TO_FAVORITE_RECORD'=> 'Quick edit To Favorite Record', 
	'QUICK_EDIT_TO_DRAFT_RECORD'=> 'Quick edit To Draft Record',
	'QUICK_EDIT_TO_APPROVE_RECORD'=> 'Quick edit To Approve Record',
	'PRIMARY_MOVE_TO_TRASH'=> 'Record Move to Trash',
	'PRIMARY_RECORD_MOVE_TO_FAVORITE'=> 'Record Move To Favorite',
	'PRIMARY_RECORD_MOVE_TO_ARCHIVE'=> 'Record Move To Archive',
	'PRIMARY_RECORD_COPY'=> 'Record Copy',
	'FAVORITE_RECORD_MOVE_TO_PRIMARY'=> 'Favorite Record Move',
	'FAVORITE_RECORD_MOVE_TO_DRAFT'=> 'Favorite Record Move To Draft',
	'FAVORITE_RECORD_MOVE_TO_APPROVE'=> 'Favorite Record Move To Approve',
	'FAVORITE_RECORD_MOVE'=> 'Favorite Record Move',
	'DRAFT_SENT_FOR_APPROVAL'=> 'Draft Sent for Approval',
	'DRAFT_RECORD_MOVE_TO_FAVORITE'=> 'Draft Record Move To Favorite',
	'DRAFT_RECORD_APPROVED'=> 'Draft Record Approved',
	'DELETE_TRASH_RECORD'=> 'Delete Trash Record',
	'DELETE_RECORD'=> 'Delete Record',
	'DELETE_DRAFT_RECORD'=> 'Delete Draft Record', 
	'DELETE_APPROVED'=> 'Delete Approved Record', 
	'COMMENT_ADDED'=> 'Comment Added',
	'APPROVE_RECORD_MOVE_TO_FAVORITE'=> 'Approve Record Move To Favorite',
	'ADDED_DRAFT'=> 'Added Draft', 
	'FAVORITE_RECORD_MOVE_TO_TRASH' => 'Favorite Record Move To Trash',
	'APPROVE_RECORD_MOVE_TO_TRASH' => 'Approval Record Move To Trash',
	'DRAFT_RECORD_MOVE_TO_TRASH' => 'Draft Record Move To Trash',
	'ARCHIVE_RECORD_MOVE_TO_TRASH' => 'Archive Record Move To Trash',
	'NOTIFICATION_RECORD_DELETE' => 'Notification Record Delete',
	'REPLIED_COMMENT_ADDED' => 'Replied on comment',
	'AUTO_SAVED_DARFT' => 'Auto Saved as a Draft',
	'LOCKED_RECORD' => 'Locked Record',
	'UNLOCKED_RECORD' => 'UnLocked Record'
);

$returnArray = array_merge($returnArray,$logconstants);

return $returnArray;