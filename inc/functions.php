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
        $url = "http://localhost:3000";
        $data = array(
            'api' => API_KEY,
            'new-post' => 1
        );

        $data_string = http_build_query($data);
        //open connection
        $ch = curl_init();
        
        //set the url, number of POST vars, POST data
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,count($data));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data_string);

        //execute post
        $result = curl_exec($ch);
        
        //close connection
        curl_close($ch);
    }
