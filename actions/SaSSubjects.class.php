<?php
/**
 * SaSSubjects Controller provide process services
 * 
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage actions
 * @license GPLv2  http://www.opensource.org/licenses/gpl-2.0.php
 */
class SaSSubjects extends Subjects {

    
    
    /**
     * @see Subjects::__construct()
     */
    public function __construct() {
        $this->setSessionAttribute('currentExtension', 'taoSubjects');
		tao_helpers_form_GenerisFormFactory::setMode(tao_helpers_form_GenerisFormFactory::MODE_STANDALONE);
		parent::__construct();
    }
    	
		
	/**
     * @see TaoModule::setView()
     */
    public function setView($identifier, $useMetaExtensionView = false) {
		if($useMetaExtensionView){
			$this->setData('includedView', $identifier);
		}
		else{
			$this->setData('includedView', BASE_PATH . '/' . DIR_VIEWS . $GLOBALS['dir_theme'] . $identifier);
		}
		parent::setView('sas.tpl', true);
    }

	/**
	 * Render the tree form to add a subject to groups
	 * @return void 
	 */
	public function addToGroup(){
		$this->setData('uri', $this->getRequestParameter('uri'));
		$this->setData('classUri', $this->getRequestParameter('classUri'));
		$this->setData('subjectGroups', json_encode(array_map("tao_helpers_Uri::encode", $this->service->getSubjectGroups($this->getCurrentInstance()))));
		$this->setView('groups.tpl');
	}	
}
?>