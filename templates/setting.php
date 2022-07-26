
<link rel='stylesheet' href='http://localhost/tulsi_work/wp-queue/wp-admin/load-styles.php?c=1&amp;dir=ltr&amp;load%5Bchunk_0%5D=dashicons,admin-bar,site-health,common,forms,admin-menu,dashboard,list-tables,edit,revisions,media,themes,about,nav-menus,wp-poi&amp;load%5Bchunk_1%5D=nter,widgets,site-icon,l10n,buttons,wp-auth-check&amp;ver=6.0.1' media='all' />
<link rel='stylesheet' id='thickbox-css'  href='http://localhost/tulsi_work/wp-queue/wp-includes/js/thickbox/thickbox.css?ver=6.0.1' media='all' />

<style type="text/css">
    a.sm_button {
        padding: 4px;
        display: block;
        padding-left: 25px;
        background-repeat: no-repeat;
        background-position: 5px 50%;
        text-decoration: none;
        border: none;
    }

    .sm-padded .inside {
        margin: 12px !important;
    }

    .sm-padded .inside ul {
        margin: 6px 0 12px 0;
    }

    .sm-padded .inside input {
        padding: 1px;
        margin: 0;
    }
    #cat{
        width:300px
    }
</style>



<div class="wrap" id="sm_div" align="center">
    <h2>XML-Sitemap Generator</h2>
    by <strong>Tulsiram Kushwah</strong>
    
    <?php	if (isset($msg) && $msg !='') {	?>
        <div id="message" class="error">
            <p><strong><?php echo $msg; ?></strong></p>
        </div>
    <?php	}	?>

    
    <div style="clear:both" ;></div>
</div>



<div id="wpbody-content">

    <div class="wrap" id="sm_div">

        <div id="poststuff" class="metabox-holder has-right-sidebar">
            <div class="inner-sidebar">
                <div id="side-sortables" class="meta-box-sortabless ui-sortable" style="position:relative;">
                    <div id="sm_pnres" class="postbox">
                        <h3 class="hndle"><span>Info plugin:</span></h3>
                        <div class="inside">

                            Plugin: <a href="#." rel="noopener noreferrer">XML-Sitemap Generator</a>
                            <br/>

                            Author: <a target="_blank" rel="noopener noreferrer" href="https://facebook.com/ramp00786">Tulsiram Kushwah</a>
                            <br/>
                            
                            Version:
                                <a href="#.">1.0.0</a>
                            
                        </div>
                    </div>
                </div>
            </div>




            <div class="has-sidebar sm-padded">

                <div id="post-body-content" class="has-sidebar-content">

                    <div class="meta-box-sortabless">

                        <div id="sm_rebuild" class="postbox">
                            <h3 class="hndle"><span>Google News Sitemap settings</span></h3>
                            <div class="inside">

                                <form name="form1" method="post"
                                    action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                                    <input type="hidden" name="trk_submit" value="trk_submit" />
                                    <ul>
                                        <li>
                                            <label for="trk_news_active">
                                                <input name="trk_news_active" type="checkbox" id="trk_news_active"
                                                    value="1"
                                                    <?php echo $trk_news_active?'checked="checked"':''; ?> />
                                                Create news sitemap.
                                            </label>
                                        </li>
                                        <li><label for="trk_n_name">Your Google News Name: <input
                                                    name="trk_n_name" type="text" id="trk_n_name"
                                                    value="<?php echo $trk_n_name?>" /></label></li>
                                        <li><label for="trk_n_lang">Your Article Language (it, en, es...): <input
                                                    name="trk_n_lang" type="text" id="trk_n_lang"
                                                    value="<?php echo $trk_n_lang?>" /></label></li>
                                        <li>
                                            <label for="trk_n_genres">
                                                <input name="trk_n_genres" type="checkbox" id="trk_n_genres"
                                                    value="1" <?php echo $trk_n_genres?'checked="checked"':''; ?> />
                                                Show GENRES, if possible.
                                            </label>
                                        </li>
                                        <li>
                                            <label for="trk_n_genres_type">If GENRES is defined then select the type
                                                of it:
                                                <select name="trk_n_genres_type">
                                                    <option
                                                        <?php echo $trk_n_genres_type=="Blog"?'selected="selected"':'';?>
                                                        value="Blog">Blog</option>
                                                    <option
                                                        <?php echo $trk_n_genres_type=="PressReleases"?'selected="selected"':'';?>
                                                        value="PressReleases">PressReleases</option>
                                                    <option
                                                        <?php echo $trk_n_genres_type=="UserGenerated"?'selected="selected"':'';?>
                                                        value="UserGenerated">UserGenerated</option>
                                                    <option
                                                        <?php echo $trk_n_genres_type=="Satire"?'selected="selected"':'';?>
                                                        value="Satire">Satire</option>
                                                    <option
                                                        <?php echo $trk_n_genres_type=="OpEd"?'selected="selected"':'';?>
                                                        value="OpEd">OpEd</option>
                                                    <option
                                                        <?php echo $trk_n_genres_type=="Opinion"?'selected="selected"':'';?>
                                                        value="Opinion">Opinion</option>
                                                </select>
                                            </label>
                                        </li>
                                        <li>
                                            <label for="trk_n_access">
                                                <input name="trk_n_access" type="checkbox" id="trk_n_access"
                                                    value="1" <?php echo $trk_n_access?'checked="checked"':''; ?> />
                                                Enable limited access "Subscription" or "Registration".
                                            </label>
                                        </li>
                                        <li>
                                            <label for="trk_n_access_type">
                                                If ACCESS is defined then select the type of it:
                                                <select name="trk_n_access_type">
                                                    <option
                                                        <?php echo $trk_n_access_type=="Subscription"?'selected="selected"':'';?>
                                                        value="Subscription">Subscription</option>
                                                    <option
                                                        <?php echo $trk_n_access_type=="Registration"?'selected="selected"':'';?>
                                                        value="Registration">Registration</option>
                                                </select>
                                            </label>
                                        </li>
                                    </ul>
                                    <b>Advanced settings - default sitemap will be generated in
                                        http://yourdomain.com/sitemap-news.xml</b>
                                    <ul>
                                        <li>
                                            <label for="trk_path">
                                                Sitemap path (relatively to blog's home) (leave empty if not sure):
                                                <input name="trk_path" type="text" id="trk_path"
                                                    value="<?php echo $trk_path?>" />
                                            </label>
                                        </li>
                                    </ul>

                            </div>
                        </div>
                        <!-- Excluded Items -->

                        <div id="sm_excludes" class="postbox">
                            <h3 class="hndle"><span>Exclude elements for google news sitemap</span></h3>

                            <div class="inside">

                                <b>Exclude Category:</b>

                                <?php 
                                    $excludedCats = get_option('trk_n_excludecatlist');
                                    if (!is_array($excludedCats)) $excludedCats = array();
                                ?>


                                <div
                                    style="border-color:#CEE1EF; border-style:solid; border-width:2px; height:10em; margin:5px 0px 5px 40px; overflow:auto; padding:0.5em 0.5em;">
                                    <ul>
                                        <?php wp_category_checklist(0,0,$excludedCats,false); ?>
                                        <?php 
                                        // --- It will work for outsite admin
                                        // $select_cats = wp_dropdown_categories( array( 'echo' => 0 ) );
                                        // $select_cats = str_replace( "name='cat' id=", "name='cat[]' multiple='multiple' id=", $select_cats );
                                        // echo $select_cats;
                                        ?>

                                    </ul>

                                </div>

                                <b>Exlclude Articles:</b>
                                <div style="margin:5px 0 13px 40px;">
                                    <label for="trk_n_excludepost">Exclude the following articles or pages:
                                        <small>put comma separated ID (ex. 1,2,3)</small><br />
                                        <input name="trk_n_excludepostlist" id="trk_n_excludepostlist" type="text"
                                            style="width:400px;"
                                            value="<?php echo $trk_n_excludepostlist;?>" /></label><br />
                                </div>

                            </div>
                        </div>

                        <div class="postbox" style="padding: 10px 20px;">
                            <b>Sitemap-post: Post per page:</b>
                            <br/>
                            <br/>
                            <input style="width:400px; margin-left: 43px; height: 36px; margin-bottom:25px" type="text" name="trk_post_per_page" id="trk_post_per_page" value="<?php echo get_option('trk_post_per_page'); ?>">
                        </div>
                        <!-- Excluded -->
                        <p class="submit"> <input type="submit" value="Save &amp; Rebuild" /></p>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>