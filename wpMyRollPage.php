<?php
/*
Plugin Name: wpMyRollPage
Plugin URI: http://www.abristolgeek.co.uk/wordpress-bits/homebrew-plugins/homebrew-wpmyrollpage/
Version: 1.2
Author: <a href="http://www.abristolgeek.com/">jamesakadamingo</a>
Description: wpMyRollPage displays your your links (blogroll and the such) on a page of your choosing, the output can be formatted in various ways.

You can:
+ Specify which link categories are displayed.
+ Specify if the category headers are shown, and in what size and style.
+ Specify if the links ratings are shown.
+ Specify how the links ratings are shown (many different icons to choose, from stars and medals to hearts!)
+ Specify if the links pictures should be shown.
+ Specify if the links descriptions should be shown.

The icons used in this plugin for link ratings come from the FamFamFam Silk icons set that is provided
under the Creative Commons Attribution licence, you can find more of the FamFamFam collection over on
its website http://www.famfamfam.com/lab/icons/silk/
*/


if ( ! class_exists("wpMyRollPage") )
{
    class wpMyRollPage
    {
        var $adminOptionsName = "wpMyRollPage_adminOptions";

        function wpMyRollPage() {
            //Constructor
        }

        function init() {
            $this->getAdminOptions();
        }

        function myPath() {
            (string) $pluginPath = '/wp-content/plugins';
            if (defined('PLUGINDIR')) {
                $pluginPath = PLUGINDIR;
            }
            return get_option('siteurl').'/'.$pluginPath.'/wpmyrollpage';

        }

        function getAdminOptions() {
            $wpMyRollPage_adminOptions = array (
                'wpmr_showTag' => '[wpMyRollPage]',
                'wpmr_linkTypes' => '',
                'wpmr_credit' => '1',
                'wpmr_headers' => '2',
                'wpmr_showImages' => TRUE,
                'wpmr_showDescription' => TRUE,
                'wpmr_showRating' => TRUE,
                'wpmr_headerStyle' => '',
                'wpmr_asList' => TRUE,
                'wpmr_ratingIcon' => 'star.png'

            );

            $myOptions = get_option($this->adminOptionsName);

            if ( !empty($myOptions) )
            {
                foreach ( $myOptions as $key => $option )
                    $wpMyRollPage_adminOptions[$key] = $option;
            }

            update_option($this->adminOptionsName,$wpMyRollPage_adminOptions);

            return $wpMyRollPage_adminOptions;
        }

        function showAdminPanel() {
            global $wpdb;
            $myOptions = $this->getAdminOptions();


            if( isset($_POST['update_wpMyRollPageOptions']) ) {

                if ( !check_admin_referer('wpMyRollPage_update') ) {
                    echo '<div class="error">Your request was not completed, a security check failed (NONCE_ERR).</div>';
                } else {
                    $err = "";

                    if ( isset($_POST['wpmr_linkTypes']) ) {
                        $myOptions['wpmr_linkTypes'] = $_POST['wpmr_linkTypes']."|";
                    } else {
                        $err .= "<li>You must select at least one link category</li>";
                    }

                    if ( isset($_POST['wpmr_headers']) ) {
                        $myOptions['wpmr_headers'] = $_POST['wpmr_headers'];
                    } else {
                        $err .= "<li>You must select a header size</li>";
                    }

                    if ( isset($_POST['wpmr_headerStyle']) ) {
                        $myOptions['wpmr_headerStyle'] = $_POST['wpmr_headerStyle'];
                    } else {
                        $myOptions['wpmr_headerStyle'] = "";
                    }

                    if ( isset($_POST['wpmr_asList']) ) {
                        if ($_POST['wpmr_asList'] == "0") {
                            $myOptions['wpmr_asList'] = FALSE;
                        } else {
                            $myOptions['wpmr_asList'] = TRUE;
                        }
                    } else {
                        $err .= "<li>You must choose either list view or no list view</li>";
                    }

                    if ( isset($_POST['wpmr_showDescription']) ) {
                        if ($_POST['wpmr_showDescription'] == "0") {
                            $myOptions['wpmr_showDescription'] = FALSE;
                        } else {
                            $myOptions['wpmr_showDescription'] = TRUE;
                        }
                    } else {
                        $err .= "<li>You must choose if discriptions should be shown</li>";
                    }

                    if ( isset($_POST['wpmr_showImages']) ) {
                        if ($_POST['wpmr_showImages'] == "0") {
                            $myOptions['wpmr_showImages'] = FALSE;
                        } else {
                            $myOptions['wpmr_showImages'] = TRUE;
                        }
                    } else {
                        $err .= "<li>You must choose if images should be shown</li>";
                    }

                    if ( isset($_POST['wpmr_showRating']) ) {
                        if ($_POST['wpmr_showRating'] == "0") {
                            $myOptions['wpmr_showRating'] = FALSE;
                        } else {
                            $myOptions['wpmr_showRating'] = TRUE;
                        }
                    } else {
                        $err .= "<li>You must choose if images should be shown</li>";
                    }

                    if ( isset($_POST['wpmr_ratingIcon'])) {
                        $myOptions['wpmr_ratingIcon'] = $_POST['wpmr_ratingIcon'];
                    } else {
                        $myOptions['wpmr_ratingIcon'] = 'star.png';
                    }

                    if ( isset($_POST['wpmr_credit']) ) {
                        if ($_POST['wpmr_credit'] == "0") {
                            $myOptions['wpmr_credit'] = FALSE;
                        } else {
                            $myOptions['wpmr_credit'] = TRUE;
                        }
                    } else {
                        $err .= "<li>Please choose if you wish to give me credit!</li>";
                    }

                    if (isset($_POST['wpmr_ratingIcon'])) {
                        $myOptions['wpmr_ratingIcon'] = $_POST['wpmr_ratingIcon'];
                    }


                    if ($err !== '') {
                        echo '<div class="error"><p><strong>Errors occured: </strong><ul>'.$err.'</ul></p></div>';
                    } else {
                        //Do the update
                        update_option($this->adminOptionsName,$myOptions);
			echo '<div class="updated"><p><strong>Settings Updated</strong></p></div>';

                    }


                }

            }

            //Start of admin form
            ?>
                <form id="wpmr_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
                    <?php if ( function_exists('wp_nonce_field') ) wp_nonce_field('wpMyRollPage_update'); ?>
                    <h2>wpMyRollPage Settings</h2>
                    <p>
                       wpMyRollPage creates a dynamic list of links that you have entered into your WordPress blog
                       these links are then displayed in a place of your choosing by putting the special tag
                       [wpMyRollPage] into the body of a post or page.  It is recommended that you use a specific
                       page for your links as this will make it easier for your visitors to find them!
                    </p>
                    <p>
                        You can customise the look and feel of your "Roll Page" by changing these settings.
                    </p>
                    <p>
                       <label>Select the link categories that you wish to display on your "Roll Page".</label><br/>
                       <table id="wpmr_catTable">
            	       <?php
                            $sql = "SELECT DISTINCT(t.name) as 'name' FROM ".$wpdb->terms." t
                                    INNER JOIN ".$wpdb->term_taxonomy." x ON t.term_id = x.term_id
                                    WHERE x.Taxonomy='link_category'";

                            $cats = $wpdb->get_results($sql);

                            $i = 1;
			    $endTr = FALSE;
                            foreach ($cats as $cat) {
                               if ($i == 1) {
                                   echo '<tr>';
				   $endTr = TRUE;
                               }

                               $class = "";
                               if ((strpos($myOptions['wpmr_linkTypes'],$cat->name.'|') === 0) | (strpos($myOptions['wpmr_linkTypes'],$cat->name) > 0) ) {
                                   $class = 'class="wpmr_SelectedCat"';
                               }

                               $catlink = '<a href="#" onclick="javascript:wpmr_UpdateCat(\''.$cat->name.'\');return false;">'.$cat->name.'</a>';

                               echo '<td '.$class.' id="'.str_replace(' ','_',$cat->name).'">'.$catlink.'</td>';

                                if ($i == 3) {
                                    $i = 0;
				    $endTr = FALSE;
                                    echo '</tr>';
                                } else {
                                    $i++;
                                }
                            }
			    if ($endTr === TRUE) { echo '</tr>'; }

                       ?>
                       </table>

                       <input type="hidden" id="wpmr_linkTypes" name="wpmr_linkTypes" value="<?php _e(apply_filters('format_to_edit',$myOptions['wpmr_linkTypes']), 'wpMyRollPage') ?>"/>
                    </p>
                    <p>
                       <label>What size headers should be used? (html Header tag sizes, the higher the number the smaller the text): </label>
                       <select id="wpmr_headers" name="wpmr_headers">
                           <option <?php if((int) $myOptions['wpmr_headers'] === 0) { echo 'selected="selected"';} ?> value="0">No Headers</option>
                           <option <?php if((int) $myOptions['wpmr_headers'] === 1) { echo 'selected="selected"';} ?>  value="1">h1</option>
                           <option <?php if((int) $myOptions['wpmr_headers'] === 2) { echo 'selected="selected"';} ?>  value="2">h2</option>
                           <option <?php if((int) $myOptions['wpmr_headers'] === 3) { echo 'selected="selected"';} ?>  value="3">h3</option>
                           <option <?php if((int) $myOptions['wpmr_headers'] === 4) { echo 'selected="selected"';} ?>  value="4">h4</option>
                           <option <?php if((int) $myOptions['wpmr_headers'] === 5) { echo 'selected="selected"';} ?>  value="5">h5</option>
                           <option <?php if((int) $myOptions['wpmr_headers'] === 6) { echo 'selected="selected"';} ?>  value="6">h6</option>
                       </select>
                    </p>
                    <p>
                       <label>What CSS styles should be applied to the headers? (Leave blank for none or if you don't understand!): </label><br/>
            	       <input size="100" type="text" id="wpmr_headerStyle" name="wpmr_headerStyle" value="<?php _e(apply_filters('format_to_edit',$myOptions['wpmr_headerStyle']), 'wpMyRollPage') ?>"/>
                    </p>
                    <p>
                       <label class="w250">Should the links be displayed as a bulleted list? </label>
                       <select id="wpmr_asList" name="wpmr_asList">
                           <option <?php if($myOptions['wpmr_asList'] === TRUE) { echo 'selected="selected"'; } ?> value="1">Yes</option>
                           <option <?php if($myOptions['wpmr_asList'] === FALSE) { echo 'selected="selected"'; } ?> value="0">No</option>
                       </select>
                    </p>
                    <p>
                       <label>Should the link descriptions be shown? </label>
                       <select id="wpmr_showDescription" name="wpmr_showDescription">
                           <option <?php if($myOptions['wpmr_showDescription'] === TRUE) { echo 'selected="selected"'; } ?> value="1">Yes</option>
                           <option <?php if($myOptions['wpmr_showDescription'] === FALSE) { echo 'selected="selected"'; } ?> value="0">No</option>
                       </select>
                    </p>
                    <p>
                       <label class="w250">Should the link pictures be shown? </label>
                       <select id="wpmr_showImages" name="wpmr_showImages">
                           <option <?php if($myOptions['wpmr_showImages'] === TRUE) { echo 'selected="selected"'; } ?> value="1">Yes</option>
                           <option <?php if($myOptions['wpmr_showImages'] === FALSE) { echo 'selected="selected"'; } ?> value="0">No</option>
                       </select>
                    </p>
                    <p>
                       <label class="w250">Should the link ratings be shown? </label>
                       <select id="wpmr_showRating" name="wpmr_showRating">
                           <option <?php if($myOptions['wpmr_showRating'] === TRUE) { echo 'selected="selected"'; } ?> value="1">Yes</option>
                           <option <?php if($myOptions['wpmr_showRating'] === FALSE) { echo 'selected="selected"'; } ?> value="0">No</option>
                       </select>
                    </p>
                    <p>
                        <label>What image should be used to show the ratings?</label>
                        <table style="width:50%;text-align:center;">
                            <tr>
                                <td id="bell" <?php if ($myOptions['wpmr_ratingIcon'] == "bell.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('bell.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>bell.png" alt="Bell"/></a></td>
                                <td id="emoticon_grin" <?php if ($myOptions['wpmr_ratingIcon'] == "emoticon_grin.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('emoticon_grin.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>emoticon_grin.png" alt="Grin"/></a></td>
                                <td id="emoticon_smile" <?php if ($myOptions['wpmr_ratingIcon'] == "emoticon_smile.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('emoticon_smile.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>emoticon_smile.png" alt="Smile"/></a></td>
                                <td id="flag_blue" <?php if ($myOptions['wpmr_ratingIcon'] == "flag_blue.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('flag_blue.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>flag_blue.png" alt="Flag Blue"/></a></td>
                                <td id="flag_green" <?php if ($myOptions['wpmr_ratingIcon'] == "flag_green.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('flag_green.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>flag_green.png" alt="Flag Green"/></a></td>
                            </tr>
                            <tr>
                                <td id="flag_orange" <?php if ($myOptions['wpmr_ratingIcon'] == "flag_orange.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('flag_orange.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>flag_orange.png" alt="Flag Orange"/></a></td>
                                <td id="flag_pink" <?php if ($myOptions['wpmr_ratingIcon'] == "flag_pink.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('flag_pink.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>flag_pink.png" alt="Flag Pink"/></a></td>
                                <td id="flag_purple" <?php if ($myOptions['wpmr_ratingIcon'] == "flag_purple.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('flag_purple.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>flag_purple.png" alt="Flag Purple"/></a></td>
                                <td id="flag_red" <?php if ($myOptions['wpmr_ratingIcon'] == "flag_red.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('flag_red.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>flag_red.png" alt="Flag Red"/></a></td>
                                <td id="flag_yellow" <?php if ($myOptions['wpmr_ratingIcon'] == "flag_yellow.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('flag_yellow.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>flag_yellow.png" alt="Flag Yellow"/></a></td>
                            </tr>
                            <tr>
                                <td id="heart" <?php if ($myOptions['wpmr_ratingIcon'] == "heart.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('heart.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>heart.png" alt="Heart"/></a></td>
                                <td id="lightbulb" <?php if ($myOptions['wpmr_ratingIcon'] == "lightbulb.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('lightbulb.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>lightbulb.png" alt="Lightbulb"/></a></td>
                                <td id="medal_bronze_1" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_bronze_1.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_bronze_1.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_bronze_1.png" alt="Medal Bronze Red"/></a></td>
                                <td id="medal_bronze_2" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_bronze_2.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_bronze_2.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_bronze_2.png" alt="Medal Bronze Green"/></a></td>
                                <td id="medal_bronze_3" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_bronze_3.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_bronze_3.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_bronze_3.png" alt="Medal Bronze Blue"/></a></td>
                            </tr>
                            <tr>
                                <td id="medal_gold_1" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_gold_1.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_gold_1.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_gold_1.png" alt="Medal Gold Red"/></a></td>
                                <td id="medal_gold_2" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_gold_2.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_gold_2.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_gold_2.png" alt="Medal Gold Green"/></a></td>
                                <td id="medal_gold_3" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_gold_3.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_gold_3.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_gold_3.png" alt="Medal Gold Blue"/></a></td>
                                <td id="medal_silver_1" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_silver_1.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_silver_1.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_silver_1.png" alt="Medal Silver Red"/></a></td>
                                <td id="medal_silver_2" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_silver_2.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_silver_2.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_silver_2.png" alt="Medal Silver Green"/></a></td>
                            </tr>
                            <tr>
                                <td id="medal_silver_3" <?php if ($myOptions['wpmr_ratingIcon'] == "medal_silver_3.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('medal_silver_3.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>medal_silver_3.png" alt="Medal Silver Blue"/></a></td>
                                <td id="money" <?php if ($myOptions['wpmr_ratingIcon'] == "money.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('money.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>money.png" alt="Money"/></a></td>
                                <td id="ruby" <?php if ($myOptions['wpmr_ratingIcon'] == "ruby.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('ruby.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>ruby.png" alt="Ruby"/></a></td>
                                <td id="shield" <?php if ($myOptions['wpmr_ratingIcon'] == "shield.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('shield.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>shield.png" alt="Shield"/></a></td>
                                <td id="star" <?php if ($myOptions['wpmr_ratingIcon'] == "star.png" ) { echo 'class="wpmr_adminImgSelection" '; } ?> ><a href="#" onclick="javascript:wpmr_setImage('star.png','<?php echo $myOptions['wpmr_ratingIcon']; ?>');return false;"><img src="<?php echo $this->myPath().'/images/' ?>star.png" alt="A Star"/></a></td>
                            </tr>
                        </table>

                    <input type="hidden" id="wpmr_ratingIcon" name="wpmr_ratingIcon" value="<?php echo $myOptions['wpmr_ratingIcon']; ?>"/>
                    <input type="hidden" id="wpmr_ratingIconHolding" name="wpmr_ratingIconHolding" value=""/>
                    </p>
                    <p>
                       <label>Would you like to give wpMyRollPage credit for creating your links page? (in really small text at the end!): </label><br/>
                       <select id="wpmr_credit" name="wpmr_credit">
                           <option <?php if($myOptions['wpmr_credit'] === TRUE) { echo 'selected="selected"'; } ?> value="1">Yes</option>
                           <option <?php if($myOptions['wpmr_credit'] === FALSE) { echo 'selected="selected"'; } ?> value="0">No</option>
                       </select>
                    </p>
                    <p>
                        <input type="submit" id="update_wpMyRollPageOptions" name="update_wpMyRollPageOptions" value="<?php _e('Update Settings', 'wpMyRollPage') ?>"/>
                    </p>
                </form>
            <?php
        }

        function addContent($content = '') {

            $myOptions = $this->getAdminOptions();

            if ( strpos($content,$myOptions['wpmr_showTag']) === 0 | strpos($content,$myOptions['wpmr_showTag']) > 0 ) {
                (bool) $showHeaders = FALSE;
                (string) $linkData = '';
		
                $linksToFetch = explode("|", $myOptions['wpmr_linkTypes']);

				sort($linksToFetch);
				
                if ( $myOptions['wpmr_headers'] > 0 ) {
                    $showHeaders = TRUE;
                }

                foreach ($linksToFetch as $linkType) {

                    $thisLinkset = $this->getLinks($linkType,$myOptions['wpmr_asList'],
                            $showHeaders,$myOptions['wpmr_headerStyle'],
                            $myOptions['wpmr_headers'],$myOptions['wpmr_showDescription']
				,$myOptions['wpmr_showRating'],$myOptions['wpmr_ratingIcon']);

                    if (!str_replace(" ","",$thisLinkset) == "") {
                        $linkData .= $thisLinkset.'<br/>';

                    }
                }

                if ( $myOptions['wpmr_credit'] == '1' ) {
                    $content .= '<div><br/><small><a href="http://www.abristolgeek.co.uk/wordpress-bits/homebrew-plugins/wpmyrollpage" target="_blank" title="wpMyRollPage @ abristolgeek.co.uk">wpMyRollPage - A Blogroll and Links page plugin for WordPress!</a></small></div>';
                }

                $content = str_replace($myOptions['wpmr_showTag'],$linkData,$content);


            }

            return $content;

        }

        function getLinks($linkName,$list = false,$getHeader = true,$headerStyle='',
                $headerSize = 2,$showDescription = TRUE,$showRating = FALSE,$icon = 'star.png') {

            global $wpdb;
            $returnValue = '';
            $header = '';

            if ( (bool) $getHeader === true ) {
                    $header = '<h'.$headerSize.' class="wprp_header"'.$headerStyle.'>'.$linkName.'</h'.$headerSize.'>';
            }

            $sql = "SELECT ts.name,ts.slug,l.link_url,l.link_name,l.link_target,l.link_description,l.link_rel,l.link_rating
                    FROM ".$wpdb->term_taxonomy." tt
                    INNER JOIN ".$wpdb->terms." ts ON tt.term_id = ts.term_id
                    INNER JOIN ".$wpdb->term_relationships." tr ON tt.term_taxonomy_id = tr.term_taxonomy_id
                    INNER JOIN ".$wpdb->links." l ON tr.object_id = l.link_id
                    WHERE tt.taxonomy = 'link_category' AND ts.name = '".$linkName."' AND l.link_visible='Y'
                    ORDER BY ts.name ASC,l.link_name ASC";

            $blogRoll = $wpdb->get_results($sql);

            foreach ($blogRoll as $link) {

                $thisLink = '';

                if ( (bool) $list === TRUE) {
                    $thisLink = '<li>';
                }

                $thisLink .= '<a class="wprp_link" href="';
                $thisLink .= $link->link_url.'" target="';
                $thisLink .= $link->link_target.'" title="';
                $thisLink .= $link->link_description.'" rel="';
                $thisLink .= $link->link_rel.'">';
                $thisLink .= $link->link_name.'</a>';

                if ( (bool) $showRating === TRUE ) {
                    for($i=1; $i <= $link->link_rating; $i++){
                        $thisLink .= '<img class="wprp_star" src="'.$this->myPath().'/images/'.$icon.'" alt="*"/>';
                    }
                }

                if ( (bool) $showDescription === TRUE ) {
                    $thisLink .= '<br/><span class="wprp_description">'.$link->link_description.'</span>';
                }

                if ( (bool) $list === TRUE) {
                    $thisLink .= '</li>';
                } else {
                    $thisLink .= '<br/>';
                }

                $returnValue .= $thisLink;
            }

            $rv = $header.$returnValue;

            if ( $returnValue === '' ) {
                $rv = "";
            }
	    return $rv;
        }

    }

}

if (class_exists("wpMyRollPage"))
{
	$wpMyRollPage = new wpMyRollPage();
}

//Initialize the admin panel
if (!function_exists("wpMyRollPage_ap")) {
	function wpMyRollPage_ap() {
		global $wpMyRollPage;
		if (!isset($wpMyRollPage)) {
			return;
		}
		if (function_exists('add_options_page')) {
			add_options_page('wpMyRollPage Options', 'wpMyRollPage', 9, basename(__FILE__), array(&$wpMyRollPage, 'showAdminPanel'));
		}
	}
}


if (isset($wpMyRollPage))
{
    add_filter('the_posts', 'conditionally_add_scripts_and_styles');
    add_action('wpMyRollPage/wpMyRollPage.php',  array(&$wpMyRollPage, 'init'));
    add_action('the_content',array(&$wpMyRollPage,'addContent'),1);
    add_action('admin_menu', 'wpMyRollPage_ap');
    add_action('admin_enqueue_scripts','wpmr_adminScript');
    function wpmr_adminScript(){
        (string) $pluginPath = '/wp-content/plugins';
        if (defined('PLUGINDIR')) {
            $pluginPath = PLUGINDIR;
        }
        $pluginPath =  get_option('siteurl').'/'.$pluginPath.'/wpmyrollpage';

        //wp_enqueue_script('wpMtRollPage_AdminScript',$pluginPath.'/js/wpMyRollPage.js');
        echo '<script type="text/javascript">
              function wpmr_setImage(imageName,oldImage) {
                document.getElementById(\'wpmr_ratingIcon\').value=imageName;
                oldImage = oldImage.substring(0,oldImage.length - 4);
                document.getElementById(oldImage).className=\'\';
                imageName = imageName.substring(0,imageName.length - 4);
                document.getElementById(imageName).className=\'wpmr_adminImgSelection\';
                var oldOldImage = document.getElementById(\'wpmr_ratingIconHolding\').value;
                if(oldOldImage) {
                    document.getElementById(oldOldImage).className=\'\';
                }
                document.getElementById(\'wpmr_ratingIconHolding\').value=imageName;
              }
              function wpmr_UpdateCat(catName) {
                    var miniCat = catName.replace(/\s/g,"_");

		    if (document.getElementById(miniCat).className == "" ) {
                    document.getElementById(catName.replace(/\s/g,"_")).className = "wpmr_SelectedCat";
                    var newValue = document.getElementById(\'wpmr_linkTypes\').value;
                    newValue = newValue.concat(catName);
                    newValue = newValue.concat("|");
                    document.getElementById(\'wpmr_linkTypes\').value = newValue;

                } else {
                    document.getElementById(catName.replace(/\s/g,"_")).className = "";
                    var currentText = document.getElementById(\'wpmr_linkTypes\').value;
                    currentText = currentText.replace(catName + "|","");
                    document.getElementById(\'wpmr_linkTypes\').value = currentText;
                }
            }
              </script>';
        echo '<style>
              .wpmr_adminImgSelection{background:grey;}
	      #wpmr_catTable{border-top:1px solid #000 !important;border-right:1px solid #000 !important;border-bottom:1px solid #000 !important;border-left:1px solid #000 !important;padding:1px;width:50%;}
	      .wpmr_SelectedCat{background-color:grey !important;color:red !important;}
	      #wpmr_catTable td{width:20%;text-align:center;}
	      .wpmr_SelectedCat a{color:#FFF !important;}
	      #wpmr_form select{width:100px;}
	      #wpmr_form .w250{width:250px;display:inline-block;float:left;}
              </style>';
    }

    function conditionally_add_scripts_and_styles($posts){
        if (empty($posts)) return $posts;

        (bool) $shortcode_found = FALSE;
        foreach ($posts as $post) {
            if ( ( strpos($post->post_content, '[wpMyRollPage]') === 0 ) |
                 ( strpos($post->post_content,'[wpMyRollPage]') > 0 ) )
                {
                    $shortcode_found = true;
                    break;
                }
        }

        if ( (bool) $shortcode_found === TRUE ) {

            (string) $pluginPath = '/wp-content/plugins';
            if (defined('PLUGINDIR')) {
                $pluginPath = PLUGINDIR;
            }
            $pluginPath =  get_option('siteurl').'/'.$pluginPath.'/wpmyrollpage';

            wp_enqueue_style('wpMyRollPage_Style', $pluginPath.'/css/wpMyRollPage.css');
        }

        return $posts;
    }
}
?>