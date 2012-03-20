<?php
/*
 * Varsity match class
 *
 * Fields:
 *      id:         -
 *      start:      -
 *      team1:      -
 *      team2:      -
 *      score1:     -
 *      score2:     - 
 *      duration:   - 
 *      finished:   -
 */
class Match extends BaseModel {
    protected $db;

    function __construct($id=NULL) {
        global $db;
        $this->db = $db;
        if($id !== NULL) {
            $sql = "SELECT
                        `id`,
                        UNIX_TIMESTAMP(start) as start,
                        `team1`,
                        `team2`,
                        `score1`,
                        `score2`,
                        `duration`,
                        `finished`
                    FROM `varsity`
                    WHERE id='".$id."'";
            parent::__construct($this->db->get_row($sql), 'Match', $slug);
            return $this;
        } else {
            // initialise new blog post
        }
    }
}
?>
