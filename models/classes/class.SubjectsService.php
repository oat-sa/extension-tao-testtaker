<?php

error_reporting(E_ALL);

/**
 * Generis Object Oriented API -
 *
 * $Id$
 *
 * This file is part of Generis Object Oriented API.
 *
 * Automatically generated on 21.09.2009, 14:23:16 with ArgoUML PHP module 
 * (last revised $Date: 2008-04-19 08:22:08 +0200 (Sat, 19 Apr 2008) $)
 *
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
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
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 */
require_once('tao/models/classes/class.Service.php');

/* user defined includes */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-includes begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-includes end

/* user defined constants */
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-constants begin
// section 10-13-1-45-792423e0:12398d13f24:-8000:00000000000017A5-constants end

/**
 * Short description of class taoSubjects_models_classes_SubjectsService
 *
 * @access public
 * @author Bertrand Chevrier, <taosupport@tudor.lu>
 * @package taoSubjects
 * @subpackage models_classes
 */
class taoSubjects_models_classes_SubjectsService
    extends tao_models_classes_Service
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute subjectClass
     *
     * @access protected
     * @var Class
     */
    protected $subjectClass = null;

    /**
     * Short description of attribute localNamspace
     *
     * @access protected
     * @var string
     */
    protected $localNamspace = '';

    /**
     * Short description of attribute subjectsOntologies
     *
     * @access protected
     * @var array
     */
    protected $subjectsOntologies = array('http://www.tao.lu/Ontologies/TAOSubject.rdf#');

    // --- OPERATIONS ---

    /**
     * Short description of method __construct
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @return mixed
     */
    public function __construct()
    {
        // section 10-13-1-45-69571c33:1239d9f7146:-8000:0000000000001896 begin
		
		parent::__construct();
		
		$this->subjectClass 	= new core_kernel_classes_Class( TAO_SUBJECT_CLASS );
		$this->localNamespace	= LOCAL_NAMESPACE;

		$ontologies = array_merge($this->ontologies, $this->subjectsOntologies);
		if(count($ontologies) > 0){
			$session = core_kernel_classes_Session::singleton();
			foreach($ontologies as $ontology){
				$session->model->loadModel($ontology);
			}
		}
		
        // section 10-13-1-45-69571c33:1239d9f7146:-8000:0000000000001896 end
    }

    /**
     * Short description of method getSubjects
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  Class clazz
     * @param  array options
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubjects( core_kernel_classes_Class $clazz = null, $options = array())
    {
        $returnValue = null;

        // section 10-13-1-45--2fb16c8f:12398b55d4e:-8000:0000000000001797 begin
		
		if(is_null($clazz)){
			$clazz = $this->subjectClass;
		}
		
		//verify the class type
		if( $clazz->uriResource != $this->subjectClass->uriResource ){
			if( ! $clazz->isSubClassOf($this->subjectClass) ){
				throw new Exception("your clazz argument must referr to a Subject or Subject's subclass in your ontology ");
			}
		}
		
		$instances = $clazz->getInstances();
		if($instances->count() > 0){
			
			//paginate options
			//@todo implements
			if(count($options) > 0){
			
				$sequence = $instances->sequence;
				
				if(isset($options['order'])){
					//order sequence by $options['order']
				}
				if(isset($options['start'])){
					//return sequence from $options['start'] index
				}
				if(isset($options['offset'])){
					//return  $options['offset'] elements of the sequence
				}
			
				$returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
				$returnValue->sequence = $sequence;
			}
			else{
				$returnValue = $instances;
			}
		}
		
        // section 10-13-1-45--2fb16c8f:12398b55d4e:-8000:0000000000001797 end

        return $returnValue;
    }

    /**
     * Short description of method getSubject
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  string identifier
     * @param  string mode
     * @param  Class clazz
     * @return core_kernel_classes_Resource
     */
    public function getSubject($identifier, $mode = 'uri',  core_kernel_classes_Class $clazz = null)
    {
        $returnValue = null;

        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001818 begin
		
		if(is_null($clazz)){
			$clazz = $this->subjectClass;
		}
		if($this->isASubjectModel($clazz)){
			$returnValue = $this->getOneInstanceBy( $clazz, $identifier, $mode);
		}
		
		
        // section 10-13-1-45-792423e0:12398d13f24:-8000:0000000000001818 end

        return $returnValue;
    }

    /**
     * Short description of method getSubjectModels
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubjectModels()
    {
        $returnValue = null;

        // section 10-13-1-45--7118a60:123a410cfcb:-8000:0000000000001895 begin
		
		$returnValue = $this->subjectClass->getSubClasses();
		
        // section 10-13-1-45--7118a60:123a410cfcb:-8000:0000000000001895 end

        return $returnValue;
    }

    /**
     * Short description of method getSubjectModel
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  string uri
     * @return core_kernel_classes_Class
     */
    public function getSubjectModel($uri)
    {
        $returnValue = null;

        // section 10-13-1-45--4deb5f8d:123cd7d5aaa:-8000:0000000000001891 begin

		$clazz = new core_kernel_classes_Class($uri);
		if($this->isASubjectModel($clazz)){
			$returnValue = $clazz;
		}

        // section 10-13-1-45--4deb5f8d:123cd7d5aaa:-8000:0000000000001891 end

        return $returnValue;
    }

    /**
     * Short description of method getSubjectClassProperties
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @return core_kernel_classes_ContainerCollection
     */
    public function getSubjectClassProperties()
    {
        $returnValue = null;

        // section 10-13-1-45-3a1b83be:123dbe69b5e:-8000:00000000000018B8 begin
		
		$pattern = "/^".str_replace('/', '\/', preg_quote(TAO_SUBJECT_NAMESPACE))."/";
		
		$returnValue = new core_kernel_classes_ContainerCollection(new core_kernel_classes_Container(__METHOD__),__METHOD__);
		
		$taoObjectClass = new core_kernel_classes_Class(TAO_OBJECT_CLASS);
		foreach($taoObjectClass->getSubClasses()->getIterator() as $subClass){
			
			if( $subClass->uriResource != $this->subjectClass->uriResource && 
				preg_match($pattern, $subClass->uriResource)){
				$returnValue->add($subClass);
			}
		}
		
        // section 10-13-1-45-3a1b83be:123dbe69b5e:-8000:00000000000018B8 end

        return $returnValue;
    }

    /**
     * Short description of method createSubjectModel
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  string label
     * @param  array properties
     * @return core_kernel_classes_Class
     */
    public function createSubjectModel($label, $properties = array())
    {
        $returnValue = null;

        // section 10-13-1-45--23b8408f:123a2bfe34c:-8000:0000000000001880 begin
		
		$subjectModelClass = $this->subjectClass->createSubClass(
			$label,
			$label . ' subject created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
		);
		
		foreach($properties as $propertyName => $propertyValue){
			$myProperty = $subjectModelClass->createProperty(
				$propertyName,
				$propertyName . ' ' . $label .' subject property created from ' . get_class($this) . ' the '. date('Y-m-d h:i:s') 
			);
			
			//@todo implement check if there is a widget key and/or a range key
		}
		$returnValue = $subjectModelClass;
		
        // section 10-13-1-45--23b8408f:123a2bfe34c:-8000:0000000000001880 end

        return $returnValue;
    }

    /**
     * Short description of method deleteSubject
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  Resource subject
     * @return boolean
     */
    public function deleteSubject( core_kernel_classes_Resource $subject)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45--2fb16c8f:12398b55d4e:-8000:000000000000179D begin
		
		if(!is_null($subject)){
			if($this->isCustom($subject)){
				$returnValue = $subject->delete();
			}
		}
		
        // section 10-13-1-45--2fb16c8f:12398b55d4e:-8000:000000000000179D end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isCustom
     *
     * @access public
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  Resource resource
     * @return boolean
     */
    public function isCustom( core_kernel_classes_Resource $resource)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:00000000000018B9 begin
		
		if(strpos($resource->uriResource, '#') === 0){
			//for a short, the namespace is always the local namespace 
			return true;
		}
		
		$resourceTokens = explode('#',  $resource->uriResource);
		if(count($resourceTokens) != 2){
			throw new Exception("The uri {$resource->uriResource} isn't well formated");
		}
		
		if( trim($resourceTokens[0]) == $this->localNamespace ){
			$returnValue = true;
		}
		
        // section 10-13-1-45--135fece8:123b76cb3ff:-8000:00000000000018B9 end

        return (bool) $returnValue;
    }

    /**
     * Short description of method isASubjectModel
     *
     * @access protected
     * @author Bertrand Chevrier, <taosupport@tudor.lu>
     * @param  Class clazz
     * @return boolean
     */
    protected function isASubjectModel( core_kernel_classes_Class $clazz)
    {
        $returnValue = (bool) false;

        // section 10-13-1-45--4deb5f8d:123cd7d5aaa:-8000:0000000000001895 begin
		if($clazz->uriResource == $this->subjectClass->uriResource){
			$returnValue = true;	
		}
		else{
			foreach($this->getSubjectModels()->getIterator() as $subclass){
				if($clazz->uriResource == $subclass->uriResource){
					$returnValue = true;
					break;	
				}
			}
		}
        // section 10-13-1-45--4deb5f8d:123cd7d5aaa:-8000:0000000000001895 end

        return (bool) $returnValue;
    }

} /* end of class taoSubjects_models_classes_SubjectsService */

?>