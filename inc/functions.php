<?php
	// Check to see if we have authenticated
	function isloggedin() {
		return (array_key_exists('felix_varsity', $_SESSION) && array_key_exists('uname', $_SESSION['felix_varsity']));
	}
	
	// Log in
	function login($uname, $pass) {
		if (LOCAL) {
			return true;
		}
		return pam_auth($uname, $pass);
	}

    function publishpost($type, $content, $author, $meta, $blog) {
        global $db;

        switch($type) {
            case 'twitter':
                $meta['tweetcontent'] = json_decode(getTweet($meta['tweeturl']));
                break;
        }

        // insert into database
        $sql = "INSERT INTO `blog_post`
                (
                    `id`, 
                    `blog`, 
                    `content`, 
                    `timestamp`, 
                    `author`, 
                    `type`, 
                    `meta`
                ) VALUES (
                    '',
                    ".$blog->getId().",
                    '".$content."',
                    NOW(),
                    '".$author."',
                    '".$type."',
                    '".json_encode($meta)."'
                )";

        $db->query($sql);

        // post to nodejs
        $url = "http://176.34.227.200:3000/newpost";
        $data = array(
            'api' => API_KEY,
            'new-post' => 1
        );

        $data_string = http_build_query($data);

        $ch = curl_init($url.'?'.$data_string);

        curl_setopt($ch, CURLOPT_NOBODY, true);

        $result = curl_exec($ch);
        curl_close($ch);
    }

    function getTweet($url) {
        $api = 'https://api.twitter.com/1/statuses/oembed.json?id=99530515043983360';
        $request = $api.'?url='.htmlentities($url).'&omit_script=true';

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$request);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
