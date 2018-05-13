<?php

include_once 'vendor/autoload.php';

use Tracy\Debugger;
Debugger::enable();

/**
 * INIT
 * - get token from https://api.slack.com/methods/im.history/test
 * - get channel name from https://api.slack.com/methods/im.list/test
 */
 $channelName = '<CHANNEL-NAME>';
 $token = '<GENERATED-TOKEN>';
$botID = '<BOT-ID>';
$deletedCountInOneCall = 9000;
$messagesCount = 100;

// slack can get only 100 last messages in one call
for($i = 1; $i <= ($deletedCountInOneCall / $messagesCount); $i++)
{
	// delete messages from channel
	$messagesUrl = "https://slack.com/api/channels.history?token=$token&channel=$channelName&count=$messagesCount&pretty=1";

	// delete messages from chats
	// $messagesUrl = "https://slack.com/api/im.history?token=$token&channel=$channelName&count=$messagesCount&pretty=1";

    // get messages
    $messagesJson = file_get_contents($messagesUrl);
    $messages = json_decode($messagesJson);

    // get message timestamps
    $ts = [];
    if (isset($messages->messages)) {
        foreach($messages->messages as $m) {
			if ( $botID !== $m->bot_id ) {
				continue;
			}
            $ts[] = $m->ts;
        }
    }

    // delete URL
    $deleteUrl = "https://slack.com/api/chat.delete?token=$token&channel=$channelName&pretty=1&ts=";

    // delete all messages
	$ts_count = 0;
    foreach ($ts as $t) {
		$ts_count++;
        $r = file_get_contents($deleteUrl . $t);
		if ( ! $r ) {
			var_dump($ts_count . ' sleep');
			sleep(3);
			$r = file_get_contents($deleteUrl . $t);
		}
		if ( $r ) {
			var_dump('ok');
		} else {
			var_dump($deleteUrl . $t);
		}

    }
	var_dump('batch sleep');
	sleep(3);
    echo "deleted" . sizeof($ts) . " messages" . "\n";
}

exit("end");
