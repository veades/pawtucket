<?php
/* ----------------------------------------------------------------------
 * themes/default/views/ca_occurrences_full_html.php :
 * 		full search results
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2010 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
 	
$vo_result 				= $this->getVar('result');
$vn_items_per_page		= $this->getVar('current_items_per_page');
$t_occurrence = new ca_occurrences();
$t_lists = new ca_lists();
$t_list_items = new ca_list_items();

if($vo_result) {
	print '<div id="occurrenceResults">';
	
	$vn_item_count = 0;
	$va_tooltips = array();
	$t_list = new ca_lists();
	$vn_i = 0;
	while(($vn_i < $vn_items_per_page) && ($vo_result->nextHit())) {
		$vs_idno = $vo_result->get('ca_occurrences.idno');
		$vs_class = "";
		$vn_item_count++;
		if($vn_item_count == 2){
			$vs_class = "resultBg";
			$vn_item_count = 0;
		}
		
		$vn_occurrence_id = $vo_result->get('ca_occurrences.occurrence_id');
		
		
		$va_labels = $vo_result->getDisplayLabels($this->request);
		print "<div".(($vs_class) ? " class='$vs_class'" : "")." style='clear:both;'>";
		$t_occurrence->load($vn_occurrence_id);
		$vs_padding = 0;
		$va_related_objects_links = $t_occurrence->get("ca_objects_x_occurrences.relation_id", array("returnAsArray" => true));
		if(sizeof($va_related_objects_links)){
			$t_objects_x_occurrences = new ca_objects_x_occurrences();
			foreach($va_related_objects_links as $vn_relation_id){
				$t_objects_x_occurrences->load($vn_relation_id);
				$va_reps = $t_objects_x_occurrences->get("ca_objects_x_occurrences.representation_list", array("returnAsArray" => true, 'idsOnly' => true));
				if(is_array($va_reps)) {
					foreach($va_reps as $vn_relation_id => $va_attr) {
						$t_rep = new ca_object_representations($va_attr['representation_list']);
						$va_info = $t_rep->getMediaInfo("media");
						$vs_padding = round($va_info["small"]["HEIGHT"]/2) - 7;
						print "<div class='occThumb'>".$t_rep->getMediaTag('media', 'small')."</div><!-- end occThumb -->";
						break;
					}
				}
				break;
			}
		} else {
			print "<div class='occThumb'></div><!-- end occThumb -->";
		
		}
		print "<div class='searchFullText'>".caNavLink($this->request, join($va_labels, "; "), '', 'Detail', 'Occurrence', 'Show', array('occurrence_id' => $vn_occurrence_id));
#		if($vo_result->get('ca_occurrences.blankWorksheet')){
		if($vo_result->get('ca_occurrences.blankWorksheet')==671){  #671 is the item_id number for "yes" in the ca_list_items table
			print "<br/><br/><strong>Lesson Type:</strong> <span class='blankWorksheet' style='text-decoration:underline;'>Blank Worksheet*</span>";
		} else {
			print "<br/><br/><strong>Lesson Type:</strong> ".$vo_result->get('ca_occurrences.type_id', array("convertCodesToDisplayText" => true));
		}
			
		$myDisplayItem = $vo_result->get('ca_occurrences.gradelevel', array("delimiter" => ', ', "convertCodesToDisplayText" => true));
		if (strlen($myDisplayItem) > 0) {
			print "<br/><br/><strong>Grade Level:</strong> ".$myDisplayItem;			
		}
		
		$myDisplayItem = $vo_result->get('ca_occurrences.lessonTopic', array("delimiter" => '<br/>', "convertCodesToDisplayText" => false));
		if (strlen($myDisplayItem) > 0) {

					if($va_values = $t_occurrence->get("ca_occurrences.lessonTopic", array("convertCodesToDisplayText" => false, "returnAsArray" => true))){
						$va_output_parts = array();
		
						$loop_count = 0;
						foreach($va_values as $k => $va_value){
								# --- display hierarchy path for "lessonTopic"
									$vs_tmp = "";
									$vs_url = "";
												$loop_count++;
									$va_hierarchy_ancestors = $t_list_items->getHierarchyAncestors($va_value["lessonTopic"], array("idsOnly" => true, "includeSelf" => true));
									if(is_array($va_hierarchy_ancestors) && sizeof($va_hierarchy_ancestors)){
										# --- remove the root - we don't want to display it
										$va_root = array_pop($va_hierarchy_ancestors);
										if(is_array($va_hierarchy_ancestors) && sizeof($va_hierarchy_ancestors)){
											foreach($va_hierarchy_ancestors as $vni => $vn_list_item_id){
													$vs_tmp = $t_lists->getItemForDisplayByItemID($vn_list_item_id).(($vni > 0) ? " > ".$vs_tmp : "");
											}
											if ($loop_count > 1) { $va_output_parts[] = "<br/>".$vs_tmp;}
											else { $va_output_parts[] = $vs_tmp; }
										}
									}
						}
					}
									


			print "<br/><br/><strong>Lesson Topic:</strong> ";
			print join(", ", $va_output_parts);

			}

		print "</div><div class='divide' style='clear:left;'><!-- empty --></div></div>\n";
		$vn_i++;
		
	}
	TooltipManager::add(
		".blankWorksheet", "Blank worksheets are shells of lessons included here so you can customize and download them for classroom use."
	);
	print "</div>\n";
	
}


?>

