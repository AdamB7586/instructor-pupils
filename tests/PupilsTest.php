<?php
namespace PupilManagement\Tests;

use PupilManagement\Pupils;
use DBAL\Database;
use PHPUnit\Framework\TestCase;

class PupilsTest extends TestCase{
    
    protected $db;
    protected $pupils;
    
    public function setUp() {
        $this->db = new Database('localhost', 'username', 'password', 'test_db', false, false, true, 'sqlite');
        if(!$this->db->isConnected()){
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        else{
            // Create the database if it doesn't exists here
            $this->pupils = new Pupils($this->db);
        }
    }
    
    public function tearDown() {
        $this->db = null;
        $this->pupils = null;
    }
    
    public function testChangeDatabaseTable(){
        $this->pupils->setPupilTable('new_table');
        $this->assertEquals('new_table', $this->pupils->getPupilTable());
        $this->pupils->setPupilTable('pupils');
        $this->assertNotEquals('new_table', $this->pupils->getPupilTable());
    }
    
    public function testListPupils(){
        $this->markTestIncomplete('Test has not yet been completed');
    }
    
    public function testGetNumPupils(){
        $this->markTestIncomplete('Test has not yet been completed');
    }
    
    public function testSearchForPupils(){
        $this->markTestIncomplete('Test has not yet been completed');
    }
    
    public function testGetPupilInformation(){
        $this->markTestIncomplete('Test has not yet been completed');
    }
}
