<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL
 */

/**
 * Model class for the Year_index_value table.
 */
class Year_index_value_Model extends ORM {
  public $search_field='id';

  protected $belongs_to = array('survey', 'location', 'taxa_taxon_list');
  protected $has_and_belongs_to_many = array();

  public function validate(Validation $array, $save = FALSE) {
    // uses PHP trim() to remove whitespace from beginning and end of all fields before validation
    $array->pre_filter('trim'); 
    $array->add_rules('survey_id', 'required');
    $array->add_rules('year', 'required', 'integer', 'minimum[1900]');
    $array->add_rules('location_id', 'required');
    $array->add_rules('taxa_taxon_list_id', 'required');
    $array->add_rules('index', 'required', 'integer', 'minimum[0]');
    // No unvalidated fields
    return parent::validate($array, $save);
  }

  // Declare additional fields required when posting via CSV.
  protected $additional_csv_fields=array(
  		// extra lookup options
  		'year_index_value:fk_location:code' => 'Location Code',
  		'year_index_value:fk_location:external_key' => 'Location external key',
  );

  /**
   * Define a form that is used to capture a set of predetermined values that apply to every record during an import.
   * @param array $options Model specific options, including
   */
  public function fixed_values_form($options=array()) {
  
  	$location_types = array(":No filter");
  	$terms = $this->db->select('id, term')->from('list_termlists_terms')->where('termlist_external_key', 'indicia:location_types')->orderby('term', 'asc')->get()->result();
  	foreach ($terms as $term)
  		$location_types[] = str_replace(array(',',':'), array('&#44', '&#56'), $term->id) .
  		":".
  		str_replace(array(',',':'), array('&#44', '&#56'), $term->term);
  
  	return array(
  			'website_id' => array(
  					'display'=>'Website',
  					'description'=>'Select the website to import records into.',
  					'datatype'=>'lookup',
  					'population_call'=>'direct:website:id:title' ,
  					'filterIncludesNulls'=>true
  			),
  			'survey_id' => array(
  					'display'=>'Survey',
  					'description'=>'Select the survey to import records into.',
  					'datatype'=>'lookup',
  					'population_call'=>'direct:survey:id:title',
  					'linked_to'=>'website_id',
  					'linked_filter_field'=>'website_id'
  			),
  			'fkFilter:taxa_taxon_list:taxon_list_id'=>array(
  					'display' => 'Species list',
  					'description'=>'Select the species checklist which will be used when attempting to match species names.',
  					'datatype'=>'lookup',
  					'population_call'=>'direct:taxon_list:id:title',
  					'linked_to'=>'website_id',
  					'linked_filter_field'=>'website_id',
  					'filterIncludesNulls'=>true
  			),
  			'fkFilter:location:location_type_id' => array(
  						'display'=>'Location Type',
  						'description'=>'Restrict the type of locations looked up by setting this location type. '.
  										'It is not currently possible to use a column in the file to do this on a row by row basis.',
  						'datatype'=>'lookup',
  						'lookup_values'=>implode(',', $location_types)
  			));
  }
  
}
