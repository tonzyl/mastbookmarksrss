<?php
    // Function to fetch bookmarks
    function get_bookmarks($api_url, $access_token) {
        $headers = array(
            'Authorization: Bearer ' . $access_token
        );
        $ch = curl_init($api_url . '/api/v1/bookmarks');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

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
