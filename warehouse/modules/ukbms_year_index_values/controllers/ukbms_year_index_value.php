<?php

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
 * Controller providing CRUD access to the ukbms year index values list.
 */
class Ukbms_year_index_value_Controller extends Gridview_Base_Controller {

  public function __construct() {
    parent::__construct('ukbms_year_index_value');
    $this->columns = array(
      'title'   => 'Survey title',
      'website' => '',
      'year'    => '',
      'name'    => 'Location',
      'taxon'   => ''
    );
    $this->pagetitle = "UKBMS Aggregated data: Year Index Value";
  }
    
  /**
   * You can only access the list of surveys if at least an editor of one website.
   */
  protected function page_authorised() {
    return $this->auth->logged_in('CoreAdmin');
  }
  
}
?>
