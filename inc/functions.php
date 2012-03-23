<?php
	// Check to see if we have authenticated
	function isloggedin() {
		return (array_key_exists('felix_tedx', $_SESSION) && array_key_exists('uname', $_SESSION['felix_tedx']));
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
                $meta['tweetcontent'] = json_decode(getTweet($meta['tweetid']), true);
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
                    '".$db->escape(serialize($meta))."'
                )";

        $db->query($sql);

        // post to nodejs
        pingNode('newpost');
    }

    function getTweet($id) {
        $hashbang = '#!/';
        $pos = strpos($id, $hashbang);
        if($pos !== false) {
            $id = str_replace("#!/", "", $id);
        }
        $api = 'https://api.twitter.com/1/statuses/oembed.json';
        $request = $api.'?url='.$id.'';

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch,CURLOPT_URL,$request);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function pingNode($type) {
        // post to nodejs
        $url = NODE_URL."/".$type;
        $data = array(
            'api' => API_KEY,
            $type => 1
        );

        $data_string = http_build_query($data);

        $ch = curl_init($url.'?'.$data_string);

        curl_setopt($ch, CURLOPT_NOBODY, true);

        $result = curl_exec($ch);
        curl_close($ch);
    }
