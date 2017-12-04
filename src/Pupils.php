<?php
namespace PupilManagement;

use DBAL\Database;
use UKCounties\Counties;

class Pupils {
    /**
     * This should be the instance of the database object
     * @var object Need to be an instance of DBAL\Database
     */
    protected $db;
    
    /**
     * This should be the table name where the pupils can be found
     * @var string This is the pupil table name
     */
    protected $pupil_table = 'pupils';

    /**
     * Constructor
     * @param Database $db This should be an instance of the DBAL\Database
     */
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    /**
     * Set the pupils table
     * @param string $table This should be the pupil table if something other than that set as default
     * @return $this Returns this object for chaining
     */
    public function setPupilTable($table) {
        if(!empty(trim($table))) {
            $this->pupil_table = trim($table);
        }
        return $this;
    }
    
    /**
     * Returns the table name where the pupils can be found
     * @return string This will be the table name
     */
    public function getPupilTable() {
        return $this->pupil_table;
    }
    
    /**
     * List all of the pupils for a given instructor with a particular status
     * @param int $fino This should be the instructors unique fino
     * @param int $status The status of the pupils that you are listing (0 = 'deleted, 1 = active, 2 = archived)
     * @return array|false If any pupils exist they will be returned as an array else false will be returned
     */
    public function listPupils($fino, $status = 1) {
        if(is_numeric($fino) && is_numeric($status)){
            return $this->db->selectAll($this->getPupilTable(), array('fino_stud' => intval($fino), 'student' => intval($status)));
        }
        return false;
    }
    
    /**
     * Returns the total number of pupils for an instructor with a certain status
     * @param int $fino This should be the instructors fino
     * @param int $status The status of the pupils that you are listing (0 = 'deleted, 1 = active, 2 = archived)
     * @return int The total number of pupils for that instructor with the given status will be returned
     */
    public function getNumPupils($fino, $status = 1){
        return $this->countPupils($fino, '', 0, $status);
    }
    
    /**
     * Counts the number of pupils matching the given criteria
     * @param int $fino This should be the instructors fino
     * @param string $search Any search value that has been entered to narrow down the pupil list
     * @param int|boolean $headOffice If the user looking is a head office user set to (1 or true) else set to (0 or false)
     * @param int $status The status of the pupils that you are listing (0 = 'deleted, 1 = active, 2 = archived)
     * @return int Returns the number of pupils matching the given criteria
     */
    public function countPupils($fino, $search = '', $headOffice = 0, $status = 1) {
        $this->getPupils($fino, 0, $search, $headOffice, $status);
        return intval($this->db->numRows());
    }
    
    /**
     * Returns an array of the given instructors pupils
     * @param int $fino This should be the instructors fino you wish to get the pupils for
     * @param int $limit The maximum number for pupils too show
     * @param int $status The current status for the pupil and if they are active
     * @return array|boolean Returns an array of the pupils if any exist else returns false 
     */
    public function getPupils($fino, $limit = 100, $search = '', $headOffice = 0, $status = 1, $order = 'firstname', $orderDir = 'ASC') {
        if(is_numeric($fino)){
            if($headOffice){$wheresql = "`student_type` = ?"; $fino = 1; $wherearray = array('student' => 1, 'student_type' => 1);}
            else{$wheresql = "`fino_stud` = ?"; $wherearray = array('student' => $status, 'fino_stud' => $fino);}
            if(!empty(trim($search))){
                $pupils = $this->db->query("SELECT * FROM `{$this->getPupilTable()}` WHERE `student` = ? AND ".$wheresql." AND (`firstname` LIKE :SEARCH OR `surname` LIKE :SEARCH OR `address1` LIKE :SEARCH OR `postcode` LIKE :SEARCH OR `pupilno` LIKE ?) ORDER BY `".$order."` ".$orderDir.(is_array($limit) ? sprintf(" LIMIT %d, %d", key($limit), $limit[key($limit)]) : " LIMIT ".intval($limit)).";",
                    array($status, $fino, ':SEARCH' => '%'.filter_var(trim($search), FILTER_SANITIZE_SPECIAL_CHARS).'%', filter_var(trim($search), FILTER_SANITIZE_SPECIAL_CHARS))
                );
            }
            else{$pupils = $this->db->selectAll($this->getPupilTable(), $wherearray, '*', array($order => $orderDir), $limit);}
            
            foreach($pupils as $i => $pupil){
                $pupils[$i]['county'] = Counties::getCountyName($pupil['county']);
            }
            return $pupils;
        }
        return false;
    }
    
    /**
     * Returns all of the information for a particular pupils based on their unique ID
     * @param int $pupilID This should be the pupils unique ID number
     * @return array|false If the pupil exists the information will be returned as an array else will return false
     */
    public function getPupilInfo($pupilID) {
        return $this->db->select($this->getPupilTable(), array('cust_id' => intval($pupilID), 'student' => array('>=', 1)));
    }
}
