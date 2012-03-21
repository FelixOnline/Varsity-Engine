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
                $meta['tweetcontent'] = json_decode(getTweet($meta['tweetid']), true);
                /*
                foreach($meta['tweetcontent'] as $key => $value) {
                    $meta['tweetcontent'][$key] = $db->escape($value);
                }
                 */
                break;
        }

        echo serialize($meta);

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
        $db->debug();

        // post to nodejs
        $url = NODE_URL."/newpost";
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

    function updateMatch($match, $meta, $finished) {
        global $db;

        $sql = "UPDATE `varsity`
                SET 
                    score1 = ".$meta['score1'].",
                    score2 = ".$meta['score2'].",
                    finished = ".$finished."
                WHERE
                    id = ".$match."";
        $db->query($sql);
        
        // post to nodejs
        $url = NODE_URL."/matchupdate";
        $data = array(
            'api' => API_KEY,
            'matchupdate' => 1
        );

        $data_string = http_build_query($data);

        $ch = curl_init($url.'?'.$data_string);

        curl_setopt($ch, CURLOPT_NOBODY, true);

        $result = curl_exec($ch);
        curl_close($ch);
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
