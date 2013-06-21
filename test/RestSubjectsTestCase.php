<?php
/*  
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * 
 */
?>
<?php
require_once dirname(__FILE__) . '/../../tao/test/TaoTestRunner.php';
include_once dirname(__FILE__) . '/../includes/raw_start.php';

/**
 * connects as a client agent on the rest controller
 * @author patrick
 * @package taoSubjects
 * @subpackage test
 */
class RestSubjectsTestCase extends UnitTestCase {
	

	
	private $host = ROOT_URL;
	private $userUri = "";
	private $login = "";
	private $password = "";
	/**
	 * tests initialization
	 */
	public function setUp(){		
		TaoTestRunner::initTest();
		    //creates a user using remote script from joel
		    $process = curl_init($this->host.'/tao/test/connector/setUp.php');
		    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		    $returnedData = curl_exec($process);
		    $data = json_decode($returnedData, true);
		    $this->assertNotNull($data);
		  
		    $this->login = $data['userData'][PROPERTY_USER_LOGIN];
		    $this->password = $data['userData'][PROPERTY_USER_PASSWORD];
		    $this->userUri			= $data['userUri'];
		    
	}
	public function tearDown(){
	    //removes the created user
		    $process = curl_init(ROOT_URL.'tao/test/connector/tearDown.php');
		    curl_setopt($process, CURLOPT_POSTFIELDS, array('uri' => $this->userUri));
		    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		     $data = curl_exec($process);
	}
	public function testAuth(){
	    
	    $url = $this->host.'taoSubjects/RestSubjects';
	    //HTTP Basic
	    $process = curl_init($url);
	     curl_setopt($process,CURLOPT_HTTPHEADER,array (
		 "Accept: application/json"
		 ));

	    //should return a 401
	    curl_setopt($process, CURLOPT_USERPWD, "dummy:dummy");
	    curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($process);
	    $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "401");
	    curl_close($process);

	    //should return a 200
	    $process = curl_init($url);
	     curl_setopt($process,CURLOPT_HTTPHEADER,array (
		 "Accept: application/json"
		 ));
	     
	     curl_setopt($process, CURLOPT_USERPWD, $this->login.":".$this->password);
	     curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
	    $data = curl_exec($process);
	    
	    $http_status = curl_getinfo($process, CURLINFO_HTTP_CODE);
	    $this->assertEqual($http_status, "200");

	    curl_close($process);
	}

}
?>