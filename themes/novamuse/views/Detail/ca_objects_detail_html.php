<?php
/* ----------------------------------------------------------------------
 * pawtucket2/themes/default/views/Detail/ca_objects_detail_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2009-2011 Whirl-i-Gig
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
 	JavascriptLoadManager::register('cycle');
 	
	$t_object = 						$this->getVar('t_item');
	$vn_object_id = 					$t_object->get('object_id');
	$vs_title = 						$this->getVar('label');
	
	$va_access_values = 				$this->getVar('access_values');
	$t_rep = 							$this->getVar('t_primary_rep');
	$vn_num_reps = 						$t_object->getRepresentationCount(array("return_with_access" => $va_access_values));
	$vs_display_version =				$this->getVar('primary_rep_display_version');
	$va_display_options =				$this->getVar('primary_rep_display_options');
	
	# --- get similar items by category
	$va_categories = explode(",", $t_object->get('ns_category'));
	$va_sim_items = array();
	if(sizeof($va_categories)){
		$vn_category = trim($va_categories[0]);
		require_once(__CA_LIB_DIR__.'/ca/Browse/ObjectBrowse.php');
		$o_browse = new ObjectBrowse();
		$o_browse->removeAllCriteria();
		$o_browse->addCriteria("category_facet", $vn_category);
		$o_browse->addCriteria("has_media_facet", 1);
		$o_browse->execute(array('checkAccess' => $va_access_values));
		$qr_sim_items = $o_browse->getResults();
		if($qr_sim_items->numHits()){
			# --- grab the first 100 items and shuffle array to randomize a bit
			$i = 0;
			while($qr_sim_items->nextHit() && $i < 100){
				$va_labels = array();
				$va_labels = $qr_sim_items->getDisplayLabels($this->request);
				$vs_label = join("; ", $va_labels);
				$va_media_info = array();
				$va_media_info = $qr_sim_items->getMediaInfo('ca_object_representations.media', 'icon', null, array('checkAccess' => $va_access_values));
				$vn_padding_top_bottom =  ((120 - $va_media_info["HEIGHT"]) / 2);
				$va_sim_items[] = array("object_id" => $qr_sim_items->get("ca_objects.object_id"), "label" => $vs_label, "media" => $qr_sim_items->getMediaTag('ca_object_representations.media', 'icon', array('checkAccess' => $va_access_values)), "idno" => $qr_sim_items->get("ca_objects.idno"), "padding" => $vn_padding_top_bottom);	
				$i++;
			}
			# -- shuffle array
			shuffle($va_sim_items);
			# --- grab first 6 values in array
			$va_sim_items = array_slice($va_sim_items, 0, 5);
		}
	}

?>	
	<div id="contentcontainer">
		<div id="objectcontainer">
			<div class="objecttitle titletext"><?php print $vs_title; ?></div>
<?php
		if ($t_rep && $t_rep->getPrimaryKey()) {
?>
  			<div class="objectslides">
 <?php
			if($va_display_options['no_overlay']){
				print $t_rep->getMediaTag('media', $vs_display_version, $this->getVar('primary_rep_display_options'));
			}else{
				$va_opts = array('display' => 'detail', 'object_id' => $vn_object_id, 'containerID' => 'cont');
				print "<div id='cont'><a href='#' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_object->get("object_id"), 'representation_id' => $t_rep->getPrimaryKey()))."\"); return false;' >".$t_rep->getRepresentationViewerHTMLBundle($this->request, $va_opts)."</a></div>";
			} 
 ?>
  			</div>
  			<!--<div class="objectnav">1 of 2</div>-->
    		
<?php

			if ((!$this->request->config->get('dont_allow_registration_and_login')) && (!$this->request->config->get('disable_my_collections'))) {
				print '<div class="nav1">';
				if($this->request->isLoggedIn()){
					print caNavLink($this->request, _t("Add to Lightbox"), 'shareButton', '', 'Sets', 'addItem', array('object_id' => $vn_object_id));
				}else{
					print caNavLink($this->request, _t("Add to Lightbox"), 'shareButton', '', 'LoginReg', 'form', array('site_last_page' => 'Sets', 'object_id' => $vn_object_id));
				}
				print '</div>';
			}
			print "<div class='nav2'><a href='#' class='shareButton' onclick='caMediaPanel.showPanel(\"".caNavUrl($this->request, 'Detail', 'Object', 'GetRepresentationInfo', array('object_id' => $t_object->get("object_id"), 'representation_id' => $t_rep->getPrimaryKey()))."\"); return false;' >".(($vn_num_reps > 1) ? _t("Zoom/more media") : _t("Zoom"))."</a></div>";
		}
?>
			
    		<div id="shareToggle"><a href="#" onclick="$('#shareWidgetsContainer').slideToggle(); return false;" class="shareButton">Share</a></div>
			<div class='clear'></div>
<?php
			if($this->getVar("ranking")){
				$vn_numRankings = $this->getVar("numRankings");
				$like_message = "<span class='likebk' style='float:right;'>"._t("%1 %2 liked this object", $vn_numRankings, ($vn_numRankings == 1) ? "person" : "people")."</span>";
			}
			print "<p id='likeThis'>".caNavLink($this->request, "<img src='".$this->request->getThemeUrlPath()."/graphics/like.gif' border='0' style='margin-top:-2px;'>".$like_message, '', 'Detail', 'Object', 'saveCommentRanking', array('object_id' => $vn_object_id, 'rank' => 5));

			print "</p>";
			
?>
			<!-- AddThis Button BEGIN -->
			<div id="shareWidgetsContainer">
				<div class="addthis_toolbox addthis_default_style " style="padding-left:50px;">
					<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
					<a class="addthis_button_tweet"></a>
					<a class="addthis_button_google_plusone" g:plusone:size="medium"></a>
					<a class="addthis_counter addthis_pill_style"></a>
				</div>
				<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4fb28b34285e728d"></script>
			</div>
			<!-- AddThis Button END -->
			
    	</div><!--end objectcontainer-->
	</div><!--end contentcontainer-->

	<div id="subcontentcontainer">
<?php
		$vs_text_theme = $t_object->get("novastory_category", array("convertCodesToDisplayText" => true, "delimiter" => "<br/>"));
		$va_theme_info = array();
		$va_themes = caExtractValuesByUserLocale($t_object->get("novastory_category", array("returnAsArray" => true)));
		print "<div class='object-typeContainer'>";
		if(sizeof($va_themes)){
			$t_list_item = new ca_list_items();
			foreach($va_themes as $k => $vs_list_item_id){
				$t_list_item->load($vs_list_item_id);
				$va_theme_info["name"] = $t_list_item->getLabelForDisplay();
				if(file_exists($this->request->getThemeDirectoryPath()."/graphics/novamuse/".$t_list_item->get("idno").".png")){
					$va_theme_info["icon"] = $this->request->getThemeUrlPath()."/graphics/novamuse/".$t_list_item->get("idno").".png";
				}
				$va_themes_info[] = $va_theme_info;
			}
		
			print "<div id='objTypeCycle'>";
			foreach($va_themes_info as $k => $va_theme_display_info){
				print '<div class="object-type">';
				if($va_theme_display_info["icon"]){
					print "<img src='".$va_theme_display_info["icon"]."' alt='".$va_theme_display_info["name"]."' />";
				}
				print "<div class='object-type-name'>".$va_theme_display_info["name"]."</div>";
				print "</div>";
			}
			print "</div><!-- end objTypeCycle -->";
			if(sizeof($va_theme_info) > 1){
	?>
				<script type="text/javascript">
					$(document).ready(function() {
					   $('#objTypeCycle').cycle({
								   fx: 'fade', // choose your transition type, ex: fade, scrollUp, shuffle, etc...
								   speed:  1000,
								   timeout: 4000
						   });
					});
				</script>
	<?php
			}
		}
		print "</div><!-- end object-typeContainer -->";
?>
		
		<div class="collection-badge">from the collection of<br/><span class="titletextcaps"><?php print $t_object->get('source_id', array('convertCodesToDisplayText' => true)); ?></span></div>

		<div class="clear"></div>
<?php
	if($t_object->get("description") || $t_object->get("narrative")){
?>
		<div class="detail-col1">
<?php
	}
	if($t_object->get("narrative")){
?>

			<p><span class="subtitletextcaps"><?php print _t("Narrative"); ?>:</span></p>
			<p class="narrativeText"><?php print $t_object->get("narrative", array("convertHTMLBreaks" => true)); ?></p>
<?php
	}
	if($t_object->get("description")){
?>
			<p><span class="subtitletextcaps"><?php print _t("Description"); ?>:</span></p>
			<p><?php print $t_object->get("description", array("convertHTMLBreaks" => true)); ?></p>
<?php
	}
	if($t_object->get("description") || $t_object->get("narrative")){
?>
		</div><!-- end detail-col1 -->
<?php
	}
?>
		<div class="detail-col2">
			<p class="subtitletextcaps">
<?php
				# --- identifier
				if($t_object->get('idno')){
					print _t("Accession number").": ".$t_object->get('idno')."<br/>";
				}
				if($va_alt_name = $t_object->get('ca_objects.nonpreferred_labels', array('delimiter' => '<br/>'))){
					print _t("Alternate Title").": ".$va_alt_name."<br/>";
				}
				# --- parent hierarchy info
				if($t_object->get('parent_id')){
					print _t("Part Of").": ".caNavLink($this->request, $t_object->get("ca_objects.parent.preferred_labels.name"), '', 'Detail', 'Object', 'Show', array('object_id' => $t_object->get('parent_id')))."</div>";
				}
				# --- category
				if($t_object->get('ns_category')){
					print _t("Category").": ".$t_object->get('ns_category', array('convertCodesToDisplayText' => true))."<br/>";
				}
?>
			</p>

			<p class="subtitletextcaps">
<?php
				# --- attributes
				$va_attributes = $this->request->config->get('ca_objects_detail_display_attributes');
				if(is_array($va_attributes) && (sizeof($va_attributes) > 0)){
					foreach($va_attributes as $vs_attribute_code){
						if($vs_value = $t_object->get("ca_objects.{$vs_attribute_code}", array('convertCodesToDisplayText' => true))){
							print $t_object->getDisplayLabel("ca_objects.{$vs_attribute_code}").": {$vs_value}<br/>";
						}
					}
				}
?>
			</p>
			
			<p class="subtitletextcaps">
<?php
				# --- entities
				$va_entities = $t_object->get("ca_entities", array("excludeRelationshipTypes" => array("repository", "source"), "returnAsArray" => 1, "checkAccess" => $va_access_values, "sort" => "surname"));
				if(sizeof($va_entities) > 0){	
					print "<p class='subtitletextcaps'>";
					foreach($va_entities as $va_entity) {
						print $va_entity['relationship_typename'].": ".(($this->request->config->get('allow_detail_for_ca_entities')) ? caNavLink($this->request, $va_entity["label"], '', 'Detail', 'Entity', 'Show', array('entity_id' => $va_entity["entity_id"])) : $va_entity["label"])."<br/>";
					}
				}

				# --- map
				if($this->request->config->get('ca_objects_map_attribute') && $t_object->get($this->request->config->get('ca_objects_map_attribute'))){
					$o_map = new GeographicMap(200, 200, 'map');
					$o_map->mapFrom($t_object, $this->request->config->get('ca_objects_map_attribute'));
					print "<div class='unit'>".$o_map->render('HTML')."</div>";
				}
?>
			</p>

		</div><!--end detail-col2-->

		<div class="clear"></div>

		

<?php
			# --- icons for similar items
			if(sizeof($va_sim_items)){
				print '<div class="thumbs-col1"><p class="subtitletextcaps">'._t("Similar Items").'</p>';
				foreach($va_sim_items as $va_similar_item){
					print "<div class='thumb'>".caNavLink($this->request, $va_similar_item["media"], "", "Detail", "Object", "Show", array("object_id" => $va_similar_item["object_id"]))."</div>";
				}
				print '<div class="clear"></div></div>';
			}
			
			# --- next, previous, back navigation
			if (($this->getVar('is_in_result_list')) && ($vs_back_link = ResultContext::getResultsLinkForLastFind($this->request, 'ca_objects', _t("Back"), ''))) {
				print "<div class='itemNav'>";
				if ($this->getVar('previous_id')) {
					print "<div class='nav1'>".caNavLink($this->request, "&larr; "._t("Previous Entry"), '', 'Detail', 'Object', 'Show', array('object_id' => $this->getVar('previous_id')), array('id' => 'previous'))."</div>";
				}else{
					# --- this is white text on white background - acts as spacer so back button will center properly
					print "<div class='nav1'>&larr; "._t("Previous Entry")."</div>";
				}
				print $vs_back_link;
				if ($this->getVar('next_id') > 0) {
					print "<div class='nav2'>".caNavLink($this->request, _t("Next Entry")." &rarr;", '', 'Detail', 'Object', 'Show', array('object_id' => $this->getVar('next_id')), array('id' => 'next'))."</div>";
				}else{
					# --- this is white text on white background - acts as spacer so back button will center properly
					print "<div class='nav2'>"._t("Next Entry")." &rarr;</div>";
				}
				print "</div>";
			}
?>
<div class="clear"></div>	
<?php
if (!$this->request->config->get('dont_allow_registration_and_login')) {
		# --- user data --- comments - ranking - tagging
		$va_comments = $this->getVar("comments");
		$va_tags = $this->getVar("tags_array");
		if((is_array($va_tags) && (sizeof($va_tags) > 0)) || (is_array($va_comments) && (sizeof($va_comments) > 0))){	
			print '<div id="objUserData">';
		}
?>			
				
<?php
			if(is_array($va_tags) && sizeof($va_tags) > 0){
				$va_tag_links = array();
				foreach($va_tags as $vs_tag){
					$va_tag_links[] = caNavLink($this->request, $vs_tag, '', '', 'Search', 'Index', array('search' => $vs_tag));
				}
?>
				<H2><?php print _t("Tags"); ?></H2>
				<div id="tags">
					<?php print implode($va_tag_links, ", "); ?>
				</div>
<?php
			}
			if((is_array($va_tags) && (sizeof($va_tags) > 0)) && (is_array($va_comments) && (sizeof($va_comments) > 0))){	
				print "<br/>";
			}
			if(is_array($va_comments) && (sizeof($va_comments) > 0)){
?>
				
				<H2><div id="numComments">(<?php print sizeof($va_comments)." ".((sizeof($va_comments) > 1) ? _t("comments") : _t("comment")); ?>)</div><?php print _t("User Comments"); ?></H2>
<?php
				foreach($va_comments as $va_comment){
					if($va_comment["media1"]){
?>
						<div class="commentImage" id="commentMedia<?php print $va_comment["comment_id"]; ?>">
							<?php print $va_comment["media1"]["tiny"]["TAG"]; ?>							
						</div><!-- end commentImage -->
<?php
						TooltipManager::add(
							"#commentMedia".$va_comment["comment_id"], $va_comment["media1"]["large_preview"]["TAG"]
						);
					}
					if($va_comment["comment"]){
?>					
					<div class="comment">
						<?php print $va_comment["comment"]; ?>
					</div>
<?php
					}
?>					
					<div class="byLine">
						<?php print $va_comment["author"].", ".$va_comment["date"]; ?>
					</div>
<?php
				}
			}else{
				if(!$vs_tags && !$this->getVar("ranking")){
					$vs_login_message = _t("Login/register to be the first to comment on this object!");
				}
			}
			if((is_array($va_tags) && (sizeof($va_tags) > 0)) || (is_array($va_comments) && (sizeof($va_comments) > 0))){
?>
				</div><!-- end objUserData -->
<?php			
			}
		if($this->request->isLoggedIn()){
?>
			<form class="appnitro" method="post" action="<?php print caNavUrl($this->request, 'Detail', 'Object', 'saveCommentRanking', array('object_id' => $vn_object_id)); ?>" name="comment" enctype='multipart/form-data'>
				<div class="form_description">
					<h2>Leave your comments</h2>
					<p>You are logged in as <?php print trim($this->request->user->get("fname")." ".$this->request->user->get("lname"));?>.</p>
				</div>						
				<ul>
					<li id="li_1" >
						<label class="description" for="element_1">Tags </label>
						<div>
							<input id="element_1" name="tags" class="element text medium" type="text" maxlength="255" value=""/> 
						</div> 
					</li>
					<li id="li_2" >
						<label class="description" for="element_2">Comments </label>
						<div>
							<textarea id="element_2" name="comment" class="element textarea medium"></textarea> 
						</div> 
					</li>
					<li id="li_3" >
						<label class="description" for="element_3">Upload a File </label>
						<div>
							<input id="element_3" name="media1" class="element file" type="file"/> 
						</div>  
					</li>
					<li class="buttons">
						<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
					</li>
				</ul>
			</form>
<?php
		}else{
			if (!$this->request->config->get('dont_allow_registration_and_login')) {
				print "<p style='text-align:center;'>".caNavLink($this->request, (($vs_login_message) ? $vs_login_message : _t("Please login/register to comment on this item.")), "", "", "LoginReg", "form", array('site_last_page' => 'ObjectDetail', 'object_id' => $vn_object_id))."</p>";
			}
		}
	}
?>
	</div><!--end subcontentcontainer-->
	
	
<?php
	require_once(__CA_LIB_DIR__.'/core/Parsers/COinS.php');
	
	print COinS::getTags($t_object);
	
	
?>
