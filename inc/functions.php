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

        if($uname != 'felix' && $uname != 'pk1811' && $uname != 'kmw13' && $uname != 'fsport') {
            return false;
        }

        return pam_auth($uname, $pass);
    }

    function insert_image($name, $title) {
        global $db;
        if ($db->query("INSERT INTO `image` (title,uri,user) VALUES ($title,'img/upload/'.$filename,'1')")) {
            $id = mysql_insert_id();
            return $id;
        } else {
            return false;
        }
    }

    function publishpost($type, $content, $author, $meta, $blog) {
        global $db;

        switch($type) {
            case 'twitter':
                $meta['tweetcontent'] = json_decode(getTweet($meta['tweetid']), true);
                break;
            case 'image':
                if(!empty($_FILES)) {
                    $tempFile = $_FILES['Filedata']['tmp_name'];
                    $targetPath = '/media/felix/img/upload/';

                    $filename = date('YmdHi').'-varsity-'.$_FILES['Filedata']['name'];

                    $targetFile =  '/website'.str_replace('//','/',$targetPath) . $filename;
                    $imgid = insert_image($filename,$_POST['user'],$title);

                    move_uploaded_file($tempFile,$targetFile);

                    // Replace any URL given with new URL
                    $meta['picurl'] = 'http://felixonline.co.uk/img/upload/'.$filename;
                }
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
        pingNode('matchupdate');
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
