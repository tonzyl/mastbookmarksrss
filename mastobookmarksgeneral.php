<?php
// Code created with assistance of GitHub co-pilot    
// Function to fetch bookmarks
    function get_bookmarks($api_url, $access_token) {
        $headers = array(
            'Authorization: Bearer ' . $access_token
        );
        $ch = curl_init($api_url . '/api/v1/bookmarks'); /* this loads at most 20 bookmarks by default. you can load at most 40 by using /api/v1/bookmarks?limit=40 */
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        /* should there be more bookmarks than the 20 or 40 limit in the api call the link header of the response will contain pagination
        see https://docs.joinmastodon.org/methods/bookmarks/#query-parameters for that. parsing the Link header, which this code does not do,
        would allow you to do repeated calls to the api for bookmarks. This is only useful for your old bookmarks should you have many. Once you run this
        script moving forward it will be complete (provided you do not bookmark more than 20 to 40 messages in the interval your feedreader
        uses to refresh feeds.
        */        
        if ($http_code == 200) {
            $bookmarks = json_decode($response, true);
            return $bookmarks;
        } else {
            return null;
        }
    }
    // Function to generate RSS feed
    function generate_rss_feed($bookmarks) {
        $rss = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"></rss>');
        $channel = $rss->addChild('channel');
        $channel->addChild('title', 'My Mastodon Bookmarks');
        $channel->addChild('link', 'https://yourinstanceurl/bookmarks');
        $channel->addChild('description', 'Bookmarks from my Mastodon profile');

        foreach ($bookmarks as $bookmark) {
            $item = $channel->addChild('item');
            $item->addChild('title', $bookmark['uri']);
            $item->addChild('link', $bookmark['url']);
            $item->addChild('pubDate', date('r', strtotime($bookmark['created_at'])));
            $item->addChild('description', $bookmark['content']);
        }

        return $rss->asXML();
    }

    // fetch bookmarks and output as rss
    $api_url = 'https://yourinstanceurl';
    $access_token = 'youraccesstoken';
    $bookmarks = get_bookmarks($api_url, $access_token);
    if ($bookmarks !== null) {
        $rss_feed = generate_rss_feed($bookmarks);
        echo $rss_feed;
    } else {
        echo 'Failed to retrieve bookmarks.';
    }
    
?>
