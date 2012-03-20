<?php
require_once('inc/ez_sql_core.php');
require_once('inc/ez_sql_mysql.php');
require_once('inc/config.inc.php');
require_once('core/baseModel.class.php');
require_once('core/user.class.php');
require_once('core/blog.class.php');
require_once('core/blogPost.class.php');
require_once('core/match.class.php');

require_once('inc/functions.php');
session_name("felix_varsity");
session_start();

if (array_key_exists('login', $_POST)) {
    // attempting to login
    if (!login($_POST['uname'], $_POST['pass'])) { ?>
        <div class="alert alert-error">Sorry, your account details were not accepted. Please try again.</div>
    <?php } else {
        $_SESSION['felix_varsity']['uname'] = strtolower($_POST['uname']);
        // Add redirect here if we need to
    }
}

$blog = new Blog('varsity');

if(isset($_POST['new-post']) && isloggedin()) {
    foreach($_POST as $key => $value) {
        switch($key) {
            case 'type':
                $type = mysql_real_escape_string($value);
                break;
            case 'content':
                $content = mysql_real_escape_string($value);
                break;
            case 'new-post':
                break;
            default:
                $meta[$key] = mysql_real_escape_string($value);
                break;
        }
    }
    $author = $_SESSION['felix_varsity']['uname'];

    publishpost($type, $content, $author, $meta, $blog);
}

if(isset($_POST['update-match']) && isloggedin()) {
    if($_POST['finished'] == 'on') {
        $finished = 1;
    } else {
        $finished = 0;
    }
    $score1 = mysql_real_escape_string($_POST['score1']);
    $score2 = mysql_real_escape_string($_POST['score2']);
    $match = mysql_real_escape_string($_POST['match']);
    $author = $_SESSION['felix_varsity']['uname'];

    $meta = array(
        'score1' => $score1,
        'score2' => $score2,
        'match' => $match
    );

    if($finished) { // post saying match has finished
        $type = 'matchfinish';
    } else { // post saying score has changed
        $type = 'matchupdate';
    }

    updateMatch($match, $meta, $finished);
    publishpost($type, '', $author, $meta, $blog);
}
?>
<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!-- Consider adding a manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="utf-8">

    <!-- Use the .htaccess and remove these lines to avoid edge case issues.
        More info: h5bp.com/i/378 -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <title>Varsity</title>
    <meta name="description" content="">

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width">

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

    <link rel="stylesheet/less" href="css/bootstrap.less">
    <link rel="stylesheet/less" href="css/responsive.less">
    <link rel="stylesheet/less" href="css/style.less">
    <script src="js/libs/less-1.3.0.min.js"></script>

    <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->
</head>
<body>
    <div class="container">
        <header>
            <div class="row">
                <div class="span12">
                    <h1>Varsity</h1>
                </div>
            </div>
        </header>
        <div role="main">
            <?php if (!isloggedin()) { // not logged in? display login form ?>
                <form method="post" id="loginForm" class="form-horizontal">
                    <legend>Please enter your username/password to continue</legend>
                    <fieldset class="control-group">
                        <label class="control-label" for="uname">IC Username:</label>
                        <div class="controls">
                            <input type="text" name="uname" id="uname" class="input-large" />
                        </div>
                    </fieldset>
                    <fieldset class="control-group">
                        <label class="control-label" for="pass">IC Password:</label>
                        <div class="controls">
                            <input type="password" name="pass" id="pass" class="input-large"/>
                        </div>
                    </fieldset>
                    <fieldset class="form-actions">
                        <input type="submit" value="Login" name="login" id="submitButton" class="btn btn-primary"/>
                    </fieldset>
                </form>
            <?php } else { ?>
                <div class="info">
                    <p>Logged in as <?php echo $_SESSION['felix_varsity']['uname']; ?></p>
                </div>
                <div class="add-new">
                    <form id="newpostform" class="form-horizontal" method="post" action=""> 
                        <fieldset>
                            <legend>Add new post</legend>
                            <div class="control-group">
                                <label class="control-label" for="type">Type</label>
                                <div class="controls">
                                    <select id="type" name="type">
                                        <option value="">Normal</option>
                                        <option value="twitter">Twitter</option>
                                        <option value="picture">Picture</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div id="meta"></div>
                            <div class="control-group">
                                <label class="control-label" for="content">Content</label>
                                <div class="controls">
                                    <textarea class="input-xlarge" id="content" name="content" rows="4"></textarea>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary" id="new-post" name="new-post">Submit</button>
                            </div>
                        </fieldset>
                    </form>
                </div>
                
                <!-- Matches -->
                <div class="row matches">
                    <legend class="span12">Matches</legend>
                    <?php
                        $sql = "SELECT id FROM varsity ORDER BY start ASC";
                        $matches = $db->get_results($sql);
                        foreach($matches as $key => $object) {
                            $match = new Match($object->id); ?>
                    <form id="match" class="form-horizontal span12" method="post" action="">
                        <fieldset>
                                <div class="match">
                                    <div class="row">
                                        <div class="span3">
                                            <?php echo $match->getTeam1(); ?>
                                        </div> 
                                        <div class="span3">
                                            <input type="text" class="input-mini" id="score1" name="score1" value="<?php echo $match->getScore1(); ?>"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="span3">
                                            <?php echo $match->getTeam2(); ?>
                                        </div> 
                                        <div class="span3">
                                            <input type="text" class="input-mini" id="score2" name="score2" value="<?php echo $match->getScore2(); ?>"/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="span1 time">
                                            <?php echo date('H:i', $match->getStart()); ?>
                                        </div>
                                        <div class="span2">
                                            <label class="checkbox">
                                                <input type="checkbox" name="finished" id="finished" <?php if($match->getFinished()) echo 'checked="yes"'; ?>> Finished
                                            </label>
                                        </div>
                                        <div class="span3">
                                            <button id="update-match" name="update-match" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="match" value="<?php echo $match->getId(); ?>"/>
                                </div>
                        </fieldset>
                    </form>
                    <?php } ?>
                </div>
                <!--
                <div class="feed">
                    <h3>Feed</h3>
                    <?php
                        //foreach($blog->getPosts() as $key => $post) { ?>
                        <div class="post">
                            <div class="row">
                                <div class="time span1">
                                    <?php //echo date('H:i', $post->getTimestamp()); ?>
                                </div>
                                <div class="content span4">
                                    <?php 
                                        //$meta = json_decode($post->getMeta()); 
                                        //echo $meta->tweetcontent->html;
                                    ?>
                                    <?php //echo $post->getContent(); ?>
                                </div>
                            </div>
                        </div>
                    <?php //} ?>
                </div>
                -->
            <?php } ?>
        </div>
        <footer>

        </footer>
    </div>

    <!-- JavaScript at the bottom for fast page loading -->

    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>

    <!-- Load TinyMCE -->
    <script type="text/javascript" src="js/tiny_mce/tiny_mce.js"></script>
    <!-- /TinyMCE -->
    <!-- scripts concatenated and minified via build script -->
    <script src="js/script.js"></script>
    <!-- end scripts -->

</body>
</html>
