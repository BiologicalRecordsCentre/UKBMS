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

require_once(DOCROOT.'client_helpers/data_entry_helper.php');
if (isset($_POST))
  data_entry_helper::dump_errors(array('errors'=>$this->model->getAllErrors()));
echo html::script(array(
  'media/js/jquery.ajaxQueue.js',
  'media/js/jquery.bgiframe.min.js',
  'media/js/jquery.autocomplete.js'
), FALSE); 
?>
<script type="text/javascript" >
$(document).ready(function() {
  $("input#location").autocomplete("<?php echo url::site() ?>services/data/location", {
    minChars : 2,
	mustMatch : true,
	extraParams : {
	  orderby : "name",
      mode : "json",
      view : "detail",
      deleted : 'false',
      qfield: "name"
    },
    parse: function(data) {
      var results = [];
      $.each(data, function(i, item) {
	    item.full = item.name+" ("+item.id+"/"+item.location_type_id+")";
        results[results.length] = {
          'data' : item,
          'value' : item.id,
          'result' : item.full };
	  });
      return results;
    },
    formatItem: function(item) {
      return item.full;
    },
    formatResult: function(item) {
      return item.id;
    }
  });
  $("input#location").css('width','500px').result(function(event, data){
    $("input[name$=location_id]").attr('value', data.id);
  });
  $("input#taxon").autocomplete("<?php echo url::site() ?>services/data/taxa_taxon_list", {
    minChars : 3,
    mustMatch : true,
    extraParams : {
      orderby : "taxon",
      mode : "json",
      deleted : 'false',
      qfield: "taxon"
    },
    parse: function(data) {
      var results = [];
      $.each(data, function(i, item) {
        item.full = item.taxon+" ("+item.id+")";
        if(item.taxon != item.preferred_taxon)
        	item.full = item.full+" <em>"+item.taxon+"</em>";
        if(item.taxon_group != "" && item.taxon_group != null)
            item.full = item.full+" ["+item.taxon_group+"]";
        item.full = item.full+" "+item.taxon_list;
        results[results.length] = {
          'data' : item,
          'value' : item.id,
          'result' : item.full };
      });
      return results;
    },
    formatItem: function(item) {
      return item.full;
    },
    formatResult: function(item) {
      return item.id;
    }
  });
  $("input#taxon").css('width','500px').result(function(event, data){
    $("input[name$=taxa_taxon_list_id]").attr('value', data.id);
  });
});
</script>
<form class="iform" action="<?php echo url::site(); ?>ukbms_year_index_value/save" method="post" id="entry-form"">
<fieldset>
<legend>UKBMS Year Index Value</legend>
<?php
data_entry_helper::link_default_stylesheet();
data_entry_helper::enable_validation('entry-form');
if (isset($values['ukbms_year_index_value:id'])) :
	print form::hidden('ukbms_year_index_value:id', html::initial_value($values, 'ukbms_year_index_value:id'));
	print form::hidden('ukbms_year_index_value:survey_id', html::initial_value($values, 'ukbms_year_index_value:survey_id'));
	$survey = new Survey_Model($values['ukbms_year_index_value:survey_id']);
	echo data_entry_helper::text_input(array(
		'label'=>'Survey Title',
		'fieldname'=>'survey:title',
		'default'=>$survey->title,
		'disabled' => 'disabled'
	));
else :
  $arr=array();
  $surveys = $this->db
        ->select('s.id, s.title, websites.title as website')
        ->from('surveys s')
        ->join('websites', 'websites.id', 's.website_id')
        ->where(array('s.deleted' => 'f', 'websites.deleted' => 'f'))
        ->orderby(array('s.title'=>'ASC'))
        ->get();
    foreach ($surveys as $survey) {
      $arr[$survey->id] = $survey->title . ' (' . $survey->website . ')';
    }
    echo data_entry_helper::select(array(
    	'label' => 'Survey',
    	'fieldname' => 'ukbms_year_index_value:survey_id',
    	'default' => html::initial_value($values, 'ukbms_year_index_value:survey_id'),
    	'lookupValues' => $arr,
    	'blankText' => '<Please select>',
		'validation' => array('required')
    ));
    echo html::error_message($model->getError('ukbms_year_index_value:survey_id'));
endif;

echo data_entry_helper::text_input(array(
		'label' => 'Year',
		'fieldname' => 'ukbms_year_index_value:year',
		'default' => html::initial_value($values, 'ukbms_year_index_value:year'),
		'validation' => array('required','integer','minimum[1900]')
));
echo html::error_message($model->getError('ukbms_year_index_value:year'));

echo "<label for='location'>Location:</label>";
print form::input('location', $model->location->name);
echo data_entry_helper::text_input(array(
	'label'=>'Location ID',
	'fieldname'=>'ukbms_year_index_value:location_id',
	'default'=>html::initial_value($values, 'ukbms_year_index_value:location_id'),
	'readonly' => 'readonly'
));
echo html::error_message($model->getError('ukbms_year_index_value:location_id'));

echo "<label for='taxon'>Taxon:</label>";
print form::input('taxon', $model->taxa_taxon_list->taxon->taxon);
echo data_entry_helper::text_input(array(
	'label'=>'Taxon ID',
	'fieldname'=>'ukbms_year_index_value:taxa_taxon_list_id',
	'default'=>html::initial_value($values, 'ukbms_year_index_value:taxa_taxon_list_id'),
	'readonly' => 'readonly'
));
echo html::error_message($model->getError('ukbms_year_index_value:taxa_taxon_list_id'));

echo data_entry_helper::text_input(array(
		'label' => 'Index',
		'fieldname' => 'ukbms_year_index_value:index',
		'default' => html::initial_value($values, 'ukbms_year_index_value:index'),
		'validation' => array('required','integer','minimum[0]')
));
echo html::error_message($model->getError('ukbms_year_index_value:index'));

echo html::form_buttons((isset($values['ukbms_year_index_value:id'])), false, false);
data_entry_helper::$dumped_resources[] = 'jquery';
data_entry_helper::$dumped_resources[] = 'jquery_ui';
data_entry_helper::$dumped_resources[] = 'fancybox';
echo data_entry_helper::dump_javascript();
?>
</fieldset>
</form>