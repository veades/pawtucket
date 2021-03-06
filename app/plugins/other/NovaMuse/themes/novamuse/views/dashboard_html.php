<?php
/* ----------------------------------------------------------------------
 * app/plugins/NovaStory/themes/novastory/views/dashboard_html.php : 
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
?> 
<div id='pageBody'>
	<div class="imageRightCol">	
		<div id="sideNav">
			<ul>
				<li><?php print caNavLink($this->request, _t("What is NovaMuse?"), "", "", "About", "Index"); ?></li>
				<li><?php print caNavLink($this->request, _t("Background"), "", "", "About", "Background"); ?></li>
				<li><?php print caNavLink($this->request, _t("Contributors"), "", "", "About", "Participants"); ?></li>
				<li><?php print caNavLink($this->request, _t("FAQ"), "", "", "About", "FAQ"); ?></li>
				<li><?php print caNavLink($this->request, _t("Site Stats"), "", "NovaStory", "Dashboard", "Index"); ?></li>
				<li><?php print caNavLink($this->request, _t("Sponsors"), "", "", "About", "Sponsors"); ?></li>
				<li><?php print caNavLink($this->request, _t("Terms of Use"), "", "", "About", "Policy"); ?></li>
			</ul>
		</div><!-- end sideNav -->
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/novamuse/FortPointMuseum2.jpg">
		<div class="caption">Fort Point Museum</div><br/>
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/novamuse/jostHouse.jpg">
		<div class="caption">Jost House</div><br/>
		<img src="<?php print $this->request->getThemeUrlPath(); ?>/graphics/novamuse/WallaceAndAreaMuseum.jpg">
		<div class="caption">Wallace and Area Museum</div>
	</div>	
	<h1><?php print _t("Site Stats"); ?></H1>
	<div class="textContent">
		<div>
			How many contributors? How many records? How many images? So many questions! NovaMuse updates on a daily basis, so here are the current numbers:
		</div>
		<div class="dashboardStats">
			<div><?php print "Number of contributing institutions: ".$this->getVar("num_members"); ?></div>
			<div><?php print "Number of entities: ".$this->getVar("num_entities"); ?></div>
			<div><?php print "Number of objects: ".$this->getVar("num_objects"); ?></div>
			<div><?php print "Number of images: ".$this->getVar("num_reps"); ?></div>
			<div><?php print "Oldest item: ".$this->getVar("oldest_date"); ?></div>
			<div><?php print "Average date: ".$this->getVar("median_date"); ?></div>
			<div><?php print "Number of new items in the last 60 days: ".$this->getVar("createdLast60Days"); ?></div>
			<div><?php print "Top 5 themes by number of objects: ".join(", ", $this->getVar("topThemes")); ?></div>
		</div>
		<div style="clear:left;"></div>
		<!--<div><?php print "Average date: ".$this->getVar("average_date"); ?></div>-->
<?php
		$va_most_popular = $this->getVar("most_popular");
		if(is_array($va_most_popular) && sizeof($va_most_popular)){
?>
			<div>
				<b><?php print _t("Most Popular Items"); ?></b>
				<div>
<?php
				foreach($va_most_popular as $vn_most_popular_id => $va_most_popular_info){
					print "<div class='mostPopular'>".caNavLink($this->request, $va_most_popular_info["image"], "", "Detail", "Object", "Show", array("object_id" => $vn_most_popular_id))."<br/>".caNavLink($this->request, $va_most_popular_info["label"], "", "Detail", "Object", "Show", array("object_id" => $vn_most_popular_id))."</div>";
				}
?>
				</div>
			</div>
<?php
		}
		$va_recently_added = $this->getVar("recently_added");
		if(is_array($va_recently_added) && sizeof($va_recently_added)){
?>
			<div style="clear:left;">
				<b><?php print _t("Recently Added Items"); ?></b>
				<div>
<?php
				foreach($va_recently_added as $vn_recently_added_id => $va_recently_added_info){
					print "<div class='mostPopular'>".caNavLink($this->request, $va_recently_added_info["image"], "", "Detail", "Object", "Show", array("object_id" => $vn_recently_added_id))."<br/>".caNavLink($this->request, $va_recently_added_info["label"], "", "Detail", "Object", "Show", array("object_id" => $vn_recently_added_id))."</div>";
				}
?>
				</div>
			</div>
<?php
		}
?>
	</div>
</div>