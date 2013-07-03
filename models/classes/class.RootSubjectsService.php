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
  * 
 */

/**
 * Service methods to manage the Subjects business models using the RDF API.
 *
 * @access public
 * @author Joel Bout, <joel.bout@tudor.lu>
 * @package taoSubjects
 * @subpackage models_classes
 */
abstract class taoSubjects_models_classes_RootSubjectsService
    extends tao_models_classes_ClassService
{
  
    protected $subjectClass = null;

    public function __construct()
    {
        // section 10-13-1-45-69571c33:1239d9f7146:-8000:0000000000001896 begin
		
		parent::__construct();
		$this->subjectClass = new core_kernel_classes_Class(TAO_SUBJECT_CLASS);

        // section 10-13-1-45-69571c33:1239d9f7146:-8000:0000000000001896 end
    }

    public function getRootClass() {
		return $this->subjectClass;
	}

} /* end of class taoSubjects_models_classes_SubjectsService */

?>
