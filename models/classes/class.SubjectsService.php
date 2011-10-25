<?php

error_reporting(E_ALL);

/**
 * Service methods to manage the Subjects business models using the RDF API.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoSubjects
 * @subpackage models_classes
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/**
 * The Service class is an abstraction of each service instance. 
 * Used to centralize the behavior related to every servcie instances.
 *
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 */
require_once('tao/models/classes/class.GenerisService.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-constants end

/**
 * Service methods to manage the Subjects business models using the RDF API.
 *
 * @access public
 * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
 * @package taoSubjects
 * @subpackage models_classes
 */
class taoSubjects_models_classes_SubjectsService
    extends tao_models_classes_GenerisService
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The RDFS top level subject class
     *
     * @access protected
     * @var Class
     */
    protected $subjectClass = null;

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-45-69571c33:1239d9f7146:-8000:0000000000001896 begin
		
		parent::__construct();
		$this->subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);

        // section 10-13-1-45-69571c33:1239d9f7146:-8000:0000000000001896 end
    }

    /**
     * Short description of method getSubject
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getSubject($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001818 begin
		
		if(is_null($clazz) && $mode == 'uri'){
			try{
				$resource = new core_kernel_classes_Resource($identifier);
				foreach($resource->getType() as $type){
					if($type->uriResource != CLASS_ROLE_SUBJECT){
						$clazz = $type;
						break;
					}
				}
			}
			catch(Exception $e){
				// empty
			}
		}
		if(is_null($clazz)){
			$clazz = $this->subjectClass;
		}
		if($this->isSubjectClass($clazz)){
			$returnValue = $this->getOneInstanceBy($clazz, $identifier, $mode);
		}
		
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001818 end

        return $returnValue;
    }

    /**
     * Short description of method getSubjectClasses
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubjectClasses()
    {
        $returnValue = null;

        // section 10-13-1-45--7118a60:123a410cfcb:-8000:0000000000001895 begin
		
		$returnValue = $this->subjectClass->getSubClasses(true);
		
        // section 10-13-1-45--7118a60:123a410cfcb:-8000:0000000000001895 end

        return $returnValue;
    }

    /**
     * get a subject subclass by uri. 
     * If the uri is not set, it returns the subject class (the top level class.
     * If the uri don't reference a subject subclass, it returns null
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getSubjectClass($uri = '')
    {
        $returnValue = null;

        // section 10-13-1-45--4deb5f8d:123cd7d5aaa:-8000:0000000000001891 begin
		
		if(empty($uri) && !is_null($this->subjectClass)){
			$returnValue = $this->subjectClass;
		}
		else{
			$clazz = new core_kernel_classes_Class($uri);
			if($this->isSubjectClass($clazz)){
				$returnValue = $clazz;
			}
		}
		
        // section 10-13-1-45--4deb5f8d:123cd7d5aaa:-8000:0000000000001891 end

        return $returnValue;
    }

    /**
     * Create a new subject instance and link it to the tao subject role
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @return core_kernel_classes_Resource
     */
    public function createInstance( core_kernel_classes_Class $clazz, $label = '')
    {
        $returnValue = null;

        // section 127-0-1-1-56f8794f:129833f491d:-8000:0000000000002021 begin

        $returnValue = parent::createInstance($clazz, $label);
        
        $returnValue->setType(new core_kernel_classes_Class(CLASS_ROLE_SUBJECT));
        
        // section 127-0-1-1-56f8794f:129833f491d:-8000:0000000000002021 end

        return $returnValue;
    }

    /**
     * Short description of method createSubjectClass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createSubjectClass( core_kernel_classes_Class $clazz = null, $label = '', $properties = array())
    {
        $returnValue = null;

        // section 10-13-1-45--23b8408f:123a2bfe34c:-8000:0000000000001880 begin
		
		if(is_null($clazz)){
			$clazz = $this->subjectClass;
		}
		
		if($this->isSubjectClass($clazz)){
		
			$subjectClass = $this->createSubClass($clazz, $label);
			
			foreach($properties as $propertyName => $propertyValue){
				$myProperty = $subjectClass->createProperty(
					$propertyName,
					$propertyName . ' ' . $label .' subject property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
				);
				
				//@todo implement check if there is a widget key and/or a range key
			}
			$returnValue = $subjectClass;
		}
		
        // section 10-13-1-45--23b8408f:123a2bfe34c:-8000:0000000000001880 end

        return $returnValue;
    }

    /**
     * delete a subject instance
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource subject
     * @return boolean
     */
    public function deleteSubject( core_kernel_classes_Resource $subject)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45--2fb16c8f:12398b55d4e:-8000:000000000000179D begin
		
		if(!is_null($subject)){
				$returnValue = $subject->delete();
		}
		
        // section 10-13-1-45--2fb16c8f:12398b55d4e:-8000:000000000000179D end

        return (bool) $returnValue;
    }

    /**
     * delete a subject class or sublcass
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function deleteSubjectClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-6bd382c2:12495fe5af9:-8000:0000000000001AC2 begin
		
		if(!is_null($clazz)){
			if($this->isSubjectClass($clazz) && $clazz->uriResource != $this->subjectClass->uriResource){
				$returnValue = $clazz->delete();
			}
		}

        // section 127-0-1-1-6bd382c2:12495fe5af9:-8000:0000000000001AC2 end

        return (bool) $returnValue;
    }

    /**
     * Check if the Class in parameter is a subclass of Subject
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    public function isSubjectClass( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45--4deb5f8d:123cd7d5aaa:-8000:0000000000001895 begin
		
		if($clazz->uriResource == $this->subjectClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach( $this->subjectClass->getSubClasses(true) as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}
		
        // section 10-13-1-45--4deb5f8d:123cd7d5aaa:-8000:0000000000001895 end

        return (bool) $returnValue;
    }

    /**
     * retrieve the list of groups where the subject has been set
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource subject
     * @return array
     */
    public function getSubjectGroups( core_kernel_classes_Resource $subject)
    {
        $returnValue = array();

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D27 begin
		
		if(!is_null($subject)){
			$groupClass 		= new core_kernel_classes_Class(TAO_GROUP_CLASS);
			$membersProperty	= new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
			
			$groups = array();
			
			foreach($groupClass->getInstances(true) as $instance){
				foreach($instance->getPropertyValues($membersProperty) as $member){
					if($member == $subject->uriResource){
						$groups[] = $instance->uriResource;
						break;
					}
				}
			}
			
			if(count($groups) > 0){
				$groupSubClasses = array();
				foreach($groupClass->getSubClasses(true) as $groupSubClass){
					$groupSubClasses[] = $groupSubClass->uriResource;
				}
				foreach($groups as $groupUri){
					$clazz = $this->getClass(new core_kernel_classes_Resource($groupUri));
					if(!is_null($clazz)){
						if(in_array($clazz->uriResource, $groupSubClasses)){
							$returnValue[] = $clazz->uriResource;
						}
					}
					$returnValue[] = $groupUri;
				}
			}
			
		}

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D27 end

        return (array) $returnValue;
    }

    /**
     * set the list of groups where the subject is
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource subject
     * @param  array groups
     * @return boolean
     */
    public function setSubjectGroups( core_kernel_classes_Resource $subject, $groups = array())
    {
        $returnValue = (bool) false;

        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D2A begin
		
		if(!is_null($subject)){
			$groupClass 		= new core_kernel_classes_Class(TAO_GROUP_CLASS);
			$membersProperty	= new core_kernel_classes_Property(TAO_GROUP_MEMBERS_PROP);
			
			$done = 0;
			foreach($groupClass->getInstances(true) as $instance){
				$newMembers = array();
				$updateIt = false;
				foreach($instance->getPropertyValues($membersProperty) as $member){
					if($member == $subject->uriResource){
						$updateIt = true;
					}
					else{
						$newMembers[] = $member;
					}
				}
				if($updateIt){
					$instance->removePropertyValues($membersProperty);
					foreach($newMembers as $newMember){
						$instance->setPropertyValue($membersProperty, $newMember);
					}
				}
				if(in_array($instance->uriResource, $groups)){
					if($instance->setPropertyValue($membersProperty, $subject->uriResource)){
						$done++;
					}
				}
			}
			if($done == count($groups)){
				$returnValue = true;
			}
		}
		
        // section 127-0-1-1-3cab853e:12592221770:-8000:0000000000001D2A end

        return (bool) $returnValue;
    }

    /**
     * Short description of method cloneInstance
     *
     * @access public
     * @author Bertrand Chevrier, <bertrand.chevrier@tudor.lu>
     * @param  Resource instance
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function cloneInstance( core_kernel_classes_Resource $instance,  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 127-0-1-1-52f845f:12a853ab37b:-8000:000000000000249B begin
        
        $returnValue = parent::cloneInstance($instance, $clazz);
        
        $userService = tao_models_classes_ServiceFactory::get('tao_models_classes_UserService');
        $loginProperty = new core_kernel_classes_Property(PROPERTY_USER_LOGIN);
        try{
        	$login = $returnValue->getUniquePropertyValue($loginProperty);
        	while($userService->loginExist($login)){
        		$login .= (string) rand(0, 9); 
        	}
        	
        	$returnValue->editPropertyValues($loginProperty, $login);
        }
        catch(common_Exception $ce){
        	//empty
        }
        
        // section 127-0-1-1-52f845f:12a853ab37b:-8000:000000000000249B end

        return $returnValue;
    }

} /* end of class taoSubjects_models_classes_SubjectsService */

?>