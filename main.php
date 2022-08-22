<?php
/**  
   * Plugin name: XML-Sitemap Generator
   * Description: This plugin will generate sitemap-news.xml file in root directory each time when it call  from url, to call this plugin http://yourdomain.com/generate-xml, For setting of the    plugin please Go to <a href="options-general.php?page=main.php">Settings -> XML-Sitemap Generator</a> for setup.
   * Version: 1.0
   * Author: <a href="https://www.facebook.com/ramp00786" target="_blank"> Tulsiram Kushwah</a>
   * URL: https://prudour.com/

**/



define('SITE_URL', get_site_url());

//--Initializing the main function--
add_action('init' , 'XmlGenerator');
/* ----Modify old plugin--- */
$trk_sitemap_version = "1.0.0";
add_option('trk_post_per_page', 100);
// Aggiungiamo le opzioni di default
add_option('trk_news_active', true);
add_option('trk_active', true);
add_option('trk_tags', true);
add_option('trk_path', "./");
add_option('trk_last_ping', 0);
//add_option('trk_publication_name','<publication_name>');
add_option('trk_n_name',get_bloginfo( 'name' ));
add_option('trk_n_lang','it');
// Genere dei contenuti
add_option('trk_n_genres',false);
add_option('trk_n_genres_type','blog');
// Tipo di accesso dell'articolo - Facoltativo
add_option('trk_n_access',false);
add_option('trk_n_access_type','Subscription');
//add_option('trk_n_access_type','Registration');
//add_option('trk_n_excludecat',false);
add_option('trk_n_excludecatlist','');
add_option('trk_n_excludepostlist','');
//Controllo eliminazione, pubblicazione pagine post per rebuild
add_action('delete_post', 'trk_autobuild' ,9999,1);	
add_action('publish_post', 'trk_autobuild' ,9999,1);	
add_action('publish_page', 'trk_autobuild' ,9999,1);
// Carichiamo le opzioni
$trk_news_active = get_option('trk_news_active');
$trk_post_per_page = get_option('trk_post_per_page');
$trk_active = get_option('trk_active');
$trk_path = get_option('trk_path');
//$trk_publication_name = get_option('trk_publication_name','<publication_name>');
$trk_n_name = get_option('trk_n_name','<n:name>');
$trk_n_lang = get_option('trk_n_lang','<n:language>');
$trk_n_access = get_option('trk_n_access','<n:access>');
$trk_n_genres = get_option('trk_n_genres','<n:genres>');
// Aggiungiamo la pagina delle opzioni
add_action('admin_menu', 'trk_add_pages');	
//Aggiungo la pagina della configurazione
function trk_add_pages() {
    add_options_page("XML-Sitemap Generator", "XML-Sitemap Generator", 9, basename(__FILE__), "trk_admin_page");
}


function trk_escapexml($string) {
    return str_replace ( array ( '&', '"', "'", '<', '>'), array ( '&amp;' , '&quot;', '&apos;' , '&lt;' , '&gt;'), $string);
}

function trk_permissions($fileName) {

    $trk_permission = 0;
    $trk_news_active = get_option('trk_news_active');
    
    $trk_path = ABSPATH . get_option('trk_path');
    $trk_news_file_path = $trk_path . $fileName; // "sitemap-news.xml";
    
    
    if ($trk_news_active && is_file($trk_news_file_path) && is_writable($trk_news_file_path)) $trk_permission += 0;
    elseif ($trk_news_active && !is_file($trk_news_file_path) && is_writable($trk_path)) {
        $fp = fopen($trk_news_file_path, 'w');
        fwrite($fp, "<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap-news/0.963\" />");
        fclose($fp);
        if (is_file($trk_news_file_path) && is_writable($trk_news_file_path)) $trk_permission += 0;
        else $trk_permission += 2;
    }
    elseif ($trk_news_active) $trk_permission += 2;
    else $trk_permission += 0;

    return $trk_permission;
}
/*
    Auto Build sitemap
*/
function trk_autobuild($postID) {
    global $wp_version;
    $isScheduled = false;
    $lastPostID = 0;
    //Ricostruisce la sitemap una volta per post se non fa import
    if($lastPostID != $postID && (!defined('WP_IMPORTING') || WP_IMPORTING != true)) {
        
        //Costruisce la sitemap direttamente oppure fa un cron
        if(floatval($wp_version) >= 2.1) {
            if(!$isScheduled) {
                //Ogni 15 secondi.
                //Pulisce tutti gli hooks.
                wp_clear_scheduled_hook(trk_generate_sitemap());
                wp_schedule_single_event(time()+15,trk_generate_sitemap());
                $isScheduled = true;
            }
        } else {
            //Costruisce la sitemap una volta sola e mai in bulk mode
            if(!$lastPostID && (!isset($_GET["delete"]) || count((array) $_GET['delete'])<=0)) {
                trk_generate_sitemap();
            }
        }
        $lastPostID = $postID;
    }
}
//---Start from here-
function trk_generate_sitemap() {
    
    ?>  
        <div>
        <h1>XML Sitemap - News (Limit 1000)</h1>
        
        <div class="card" style="background-color:#c1c1c1; padding: 8px; display:inline-block; border-radius:10px;">
            <div class="card-body" style="text-shadow: 1px 1px 0px #e7e2e2;  color: #6a6868;">
                Plugin: Sitemap xml Generator <br/>
                Author: Tulsiram Kushwah<br/>
            </div>
        </div>

        <!-- Menu -->
        <ul style="list-style:none; display:flex">
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml"> Home </a></li>
            <li style="margin-right:5px; padding-top:2px" >></li>
            <li> news </a> </li>
        </ul>
        <!-- Menu -->
    <?php

        echo  "The xml file is generated with the name of sitemap-news.xml (<a target='_blank' href='".get_site_url()."/sitemap-news.xml'>".get_site_url()."/sitemap-news.xml</a>)";



    global $trk_sitemap_version, $table_prefix;
    global $wpdb;
    
    $t = $table_prefix;
    
    $trk_news_active = get_option('trk_news_active');
    $trk_active = get_option('trk_active');
    $trk_path = get_option('trk_path');
    //add_option('trk_publication_name','<publication_name>');
    $trk_n_name = get_option('trk_n_name');
    $trk_n_lang = get_option('trk_n_lang');
    // Genere dei contenuti
    $trk_n_genres = get_option('trk_n_genres');
    $trk_n_genres_type = get_option('trk_n_genres_type');
    // Tipo di accesso dell'articolo - Facoltativo
    $trk_n_access = get_option('trk_n_access');
    $trk_n_access_type = get_option('trk_n_access_type');
    //add_option('trk_n_access_type','Registration');
    //$trk_n_excludecat = get_option('trk_n_excludecat');
    $trk_n_excludecatlist = get_option('trk_n_excludecatlist');
    $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
    
    $includeMe = '';
    $includeNoCat = '';
    $includeNoPost = '';
    if ( $trk_n_excludecatlist <> NULL ) {
        $exPosts = get_objects_in_term($trk_n_excludecatlist,"category");
        $includeNoCat = ' AND `ID` NOT IN ('.implode(",",$exPosts).')';
        $ceck = implode(",",$exPosts);
        if ($ceck == '' || $ceck == ' ') $includeNoCat = '';
        }
    if ($trk_n_excludepostlist != ''){
        $includeNoPost = ' AND `ID` NOT IN ('.$trk_n_excludepostlist.')';
        $ceck = implode(",",$exPosts);
        if ($trk_n_excludepostlist == '' || $trk_n_excludepostlist == ' ') $includeNoPost = '';
        }
    
    $trk_permission = trk_permissions('sitemap-news.xml');
    if ($trk_permission > 2 || (!$trk_active && !$trk_news_active)) return;

    //mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    //mysql_query("SET NAMES '".DB_CHARSET."'");
    //mysql_select_db(DB_NAME);

    echo '
            <table cellpadding="5">
            <tbody>
                <tr style="background-color: whitesmoke;">
                    <th>#</th>
                    <th>XML Sitemap</th>
                    <th>Last Modified</th>
                </tr>
    ';



    $home = get_option('home') . "/";

    $xml_sitemap_google_news = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xml_sitemap_google_news .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap-news/0.9\">
    <!-- Generated by XML-Sitemap Generator ".$trk_sitemap_version." -->
    <!-- plugin by Tulsiram kushwah -->
    <!-- https://www.facebook.com/ramp00786 -->
    <!-- Created ".get_date_from_gmt(date('Y-m-d H:i:s'), 'F d, Y, H:i')." -->";

    $posts = $wpdb->get_results("SELECT * FROM ".$wpdb->posts." WHERE `post_status`='publish' 
    AND (`post_type`='page' OR `post_type`='post') ". $includeNoCat . ' ' . $includeNoPost." GROUP BY `ID` ORDER BY `post_modified_gmt` DESC LIMIT 1000");		
    
    $now = time();
    $twoDays = 2*24*60*60;
    $num = 0;
    foreach ($posts as $post) {
        if ($trk_news_active && $trk_permission != 2) {
            $postDate = strtotime($post->post_date);
            if ($now - $postDate < $twoDays) {

                $num++;

                

                if($num % 2){
                    $cls = "";
                }
                else{
                    $cls = "style='background-color: whitesmoke;'";
                }


                echo '<tr '.$cls.'>';

                    echo '<td>';
                    echo $num;
                    echo '</td>';

                    echo '<td>';
                    echo '<a href="'.trk_escapexml(get_permalink($post->ID)).'">';
                    echo trk_escapexml(get_permalink($post->ID));
                    echo '</a>';
                    echo '</td>';


                    echo '<td>';
                    echo str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z";
                    echo '</td>';

                echo '<tr>';



                $xml_sitemap_google_news .= "
                <url>
                    <loc>".trk_escapexml(get_permalink($post->ID))."</loc>
                    <n:news>
                        <n:publication>
                            <n:name>".$trk_n_name."</n:name>
                            <n:language>".$trk_n_lang."</n:language>
                        </n:publication>";
                            
                            // Se selzionato il genere allora lo aggiungo
                            if ($trk_n_genres == true) {
                                $xml_sitemap_google_news .= "
                                <n:genres>".$trk_n_genres_type."</n:genres>";
                                }
                            // Se selzionato il tipo di accesso allora lo aggiungo
                            if ($trk_n_access == true) {
                                $xml_sitemap_google_news .= "
                                <n:access>".$trk_n_access_type."</n:access>";
                                }	
                                
                            $xml_sitemap_google_news .= "	
                        <n:publication_date>".str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z"."</n:publication_date>
                        <n:title>".htmlEntityMaker($post->post_title)."</n:title>
                    </n:news>
                </url>";
            }
        }
    }


    echo '
            </tbody>
         </table>
    ';



    $xml_sitemap_google_news .= "\n</urlset>";
    
    
    if ($trk_news_active && $trk_permission != 2) {
        $fp = fopen(ABSPATH . $trk_path . "sitemap-news.xml", 'w');
        fwrite($fp, $xml_sitemap_google_news);
        fclose($fp);
    }
    

    $trk_last_ping = get_option('trk_last_ping');
    if ((time() - $trk_last_ping) > 60 * 60) {
        //get_headers("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap.xml"));	//PHP5+
        if(file_exists("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-news.xml"))){
            $fp = @fopen("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-news.xml"), 80);
            @fclose($fp);
            update_option('trk_last_ping', time());
        }
        
    }
}

function trk_generate_sitemap_with_posts() {
    
    ?>  
        <div>
        <h1>XML Sitemap - News (Limit 1000)</h1>
        
        <div class="card" style="background-color:#c1c1c1; padding: 8px; display:inline-block; border-radius:10px;">
            <div class="card-body" style="text-shadow: 1px 1px 0px #e7e2e2;  color: #6a6868;">
                Plugin: Sitemap xml Generator <br/>
                Author: Tulsiram Kushwah<br/>
            </div>
        </div>

        <!-- Menu -->
        <ul style="list-style:none; display:flex">
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml"> Home </a></li>
            <li style="margin-right:5px; padding-top:2px" >></li>
            <li> news </a> </li>
        </ul>
        <!-- Menu -->
    <?php

        echo  "The xml file is generated with the name of sitemap-news.xml (<a target='_blank' href='".get_site_url()."/sitemap-news.xml'>".get_site_url()."/sitemap-news.xml</a>)";

        echo "<br/>";
        echo "<br/>";

        echo  "The xml file is generated with the name of sitemap-post.xml (<a target='_blank' href='".get_site_url()."/sitemap-post.xml'>".get_site_url()."/sitemap-post.xml</a>)";



    global $trk_sitemap_version, $table_prefix;
    global $wpdb;
    
    $t = $table_prefix;
    
    $trk_news_active = get_option('trk_news_active');
    $trk_active = get_option('trk_active');
    $trk_path = get_option('trk_path');
    //add_option('trk_publication_name','<publication_name>');
    $trk_n_name = get_option('trk_n_name');
    $trk_n_lang = get_option('trk_n_lang');
    // Genere dei contenuti
    $trk_n_genres = get_option('trk_n_genres');
    $trk_n_genres_type = get_option('trk_n_genres_type');
    // Tipo di accesso dell'articolo - Facoltativo
    $trk_n_access = get_option('trk_n_access');
    $trk_n_access_type = get_option('trk_n_access_type');
    //add_option('trk_n_access_type','Registration');
    //$trk_n_excludecat = get_option('trk_n_excludecat');
    $trk_n_excludecatlist = get_option('trk_n_excludecatlist');
    $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
    
    $includeMe = '';
    $includeNoCat = '';
    $includeNoPost = '';
    if ( $trk_n_excludecatlist <> NULL ) {
        $exPosts = get_objects_in_term($trk_n_excludecatlist,"category");
        $includeNoCat = ' AND `ID` NOT IN ('.implode(",",$exPosts).')';
        $ceck = implode(",",$exPosts);
        if ($ceck == '' || $ceck == ' ') $includeNoCat = '';
        }
    if ($trk_n_excludepostlist != ''){
        $includeNoPost = ' AND `ID` NOT IN ('.$trk_n_excludepostlist.')';
        $ceck = implode(",",$exPosts);
        if ($trk_n_excludepostlist == '' || $trk_n_excludepostlist == ' ') $includeNoPost = '';
        }
    
    $trk_permission = trk_permissions('sitemap-news.xml');
    if ($trk_permission > 2 || (!$trk_active && !$trk_news_active)) return;

    $trk_permission_post = trk_permissions('sitemap-post.xml');
    if ($trk_permission_post > 2 || (!$trk_active && !$trk_news_active)) return;

    //mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    //mysql_query("SET NAMES '".DB_CHARSET."'");
    //mysql_select_db(DB_NAME);

    echo '
            <table cellpadding="5">
            <tbody>
                <tr style="background-color: whitesmoke;">
                    <th>#</th>
                    <th>XML Sitemap</th>
                    <th>Last Modified</th>
                </tr>
    ';



    $home = get_option('home') . "/";

    $xml_sitemap_google_news = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xml_sitemap_google_news .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap-news/0.9\">
    <!-- Generated by XML-Sitemap Generator ".$trk_sitemap_version." -->
    <!-- plugin by Tulsiram kushwah -->
    <!-- https://www.facebook.com/ramp00786 -->
    <!-- Created ".get_date_from_gmt(date('Y-m-d H:i:s'), 'F d, Y, H:i')." -->";

    $xml_sitemap_google_posts = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xml_sitemap_google_posts .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap-post.xml/0.9\">
    <!-- Generated by XML-Sitemap Generator ".$trk_sitemap_version." -->
    <!-- plugin by Tulsiram kushwah -->
    <!-- sitemap for posts limit 1000 -->
    <!-- https://www.facebook.com/ramp00786 -->
    <!-- Created ".get_date_from_gmt(date("Y-m-d H:i:s"), 'F d, Y, H:i')." -->";



    $posts = $wpdb->get_results("SELECT * FROM ".$wpdb->posts." WHERE `post_status`='publish' 
    AND (`post_type`='page' OR `post_type`='post') ". $includeNoCat . ' ' . $includeNoPost." GROUP BY `ID` ORDER BY `post_modified_gmt` DESC LIMIT 1000");		
    
    $now = time();
    $twoDays = 2*24*60*60;
    $num = 0;
    foreach ($posts as $post) {
        if ($trk_news_active && $trk_permission != 2) {
            $postDate = strtotime($post->post_date);
            if ($now - $postDate < $twoDays) {

                $num++;

                

                if($num % 2){
                    $cls = "";
                }
                else{
                    $cls = "style='background-color: whitesmoke;'";
                }


                echo '<tr '.$cls.'>';

                    echo '<td>';
                    echo $num;
                    echo '</td>';

                    echo '<td>';
                    echo '<a href="'.trk_escapexml(get_permalink($post->ID)).'">';
                    echo trk_escapexml(get_permalink($post->ID));
                    echo '</a>';
                    echo '</td>';


                    echo '<td>';
                    echo str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z";
                    echo '</td>';

                echo '<tr>';



                $xml_sitemap_google_news .= "
                <url>
                    <loc>".trk_escapexml(get_permalink($post->ID))."</loc>
                    <n:news>
                        <n:publication>
                            <n:name>".$trk_n_name."</n:name>
                            <n:language>".$trk_n_lang."</n:language>
                        </n:publication>";                            
                            // Se selzionato il genere allora lo aggiungo
                            if ($trk_n_genres == true) {
                                $xml_sitemap_google_news .= "
                                <n:genres>".$trk_n_genres_type."</n:genres>";
                                }
                            // Se selzionato il tipo di accesso allora lo aggiungo
                            if ($trk_n_access == true) {
                                $xml_sitemap_google_news .= "
                                <n:access>".$trk_n_access_type."</n:access>";
                                }	
                                
                            $xml_sitemap_google_news .= "	
                        <n:publication_date>".str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z"."</n:publication_date>
                        <n:title>".htmlEntityMaker($post->post_title)."</n:title>
                    </n:news>
                </url>";

                $xml_sitemap_google_posts .= "
                <url>
                    <loc>".trk_escapexml(get_permalink($post->ID))."</loc>
                    <lastmod>".str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z"."</lastmod>
                </url>";

            }
        }
    }


    echo '
            </tbody>
         </table>
    ';



    $xml_sitemap_google_news .= "\n</urlset>";

    $xml_sitemap_google_posts .= "\n</urlset>";
    
    
    if ($trk_news_active && $trk_permission != 2) {
        $fp = fopen(ABSPATH . $trk_path . "sitemap-news.xml", 'w');
        fwrite($fp, $xml_sitemap_google_news);
        fclose($fp);
    }

    if ($trk_news_active && $trk_permission_post != 2) {
        $fp23 = fopen(ABSPATH . $trk_path . "sitemap-post.xml", 'w');
        fwrite($fp23, $xml_sitemap_google_posts);
        fclose($fp23);
    }
    

    $trk_last_ping = get_option('trk_last_ping');
    if ((time() - $trk_last_ping) > 60 * 60) {
        //get_headers("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap.xml"));	//PHP5+
        if(file_exists("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-news.xml"))){
            $fp = @fopen("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-news.xml"), 80);
            @fclose($fp);
            update_option('trk_last_ping', time());
        }

        // if(file_exists("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-post.xml"))){
        //     $fp = @fopen("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-post.xml"), 80);
        //     @fclose($fp);
        //     update_option('trk_last_ping', time());
        // }
        
    }
}

function trk_generate_sitemap_page() {    
    ?>       
        
        <div id="xml-area" class="xml-area">
        <h1>XML Sitemap - Pages</h1>
        
        <div class="card" style="background-color:#c1c1c1; padding: 8px; display:inline-block; border-radius:10px;">
            <div class="card-body" style="text-shadow: 1px 1px 0px #e7e2e2;  color: #6a6868;">
                Plugin: Sitemap xml Generator <br/>
                Author: Tulsiram Kushwah<br/>
            </div>
        </div>

        <!-- Menu -->
        <ul style="list-style:none; display:flex">
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml"> Home </a></li>
            <li style="margin-right:5px; padding-top:2px" >></li>
            <li> pages </a> </li>
        </ul>
        <!-- Menu -->
        

    <?php

        echo  "The xml file is generated with the name of sitemap-page.xml (<a target='_blank' href='".get_site_url()."/sitemap-page.xml'>".get_site_url()."/sitemap-page.xml</a>)";



    global $trk_sitemap_version, $table_prefix;
    global $wpdb;
    
    $t = $table_prefix;
    
    $trk_news_active = get_option('trk_news_active');
    $trk_active = get_option('trk_active');
    $trk_path = get_option('trk_path');
    //add_option('trk_publication_name','<publication_name>');
    $trk_n_name = get_option('trk_n_name');
    $trk_n_lang = get_option('trk_n_lang');
    // Genere dei contenuti
    $trk_n_genres = get_option('trk_n_genres');
    $trk_n_genres_type = get_option('trk_n_genres_type');
    // Tipo di accesso dell'articolo - Facoltativo
    $trk_n_access = get_option('trk_n_access');
    $trk_n_access_type = get_option('trk_n_access_type');
    //add_option('trk_n_access_type','Registration');
    //$trk_n_excludecat = get_option('trk_n_excludecat');
    $trk_n_excludecatlist = get_option('trk_n_excludecatlist');
    $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
    
    $includeMe = '';
    $includeNoCat = '';
    $includeNoPost = '';
    if ( $trk_n_excludecatlist <> NULL ) {
        $exPosts = get_objects_in_term($trk_n_excludecatlist,"category");
        $includeNoCat = ' AND `ID` NOT IN ('.implode(",",$exPosts).')';
        $ceck = implode(",",$exPosts);
        if ($ceck == '' || $ceck == ' ') $includeNoCat = '';
        }
    if ($trk_n_excludepostlist != ''){
        $includeNoPost = ' AND `ID` NOT IN ('.$trk_n_excludepostlist.')';
        $ceck = implode(",",$exPosts);
        if ($trk_n_excludepostlist == '' || $trk_n_excludepostlist == ' ') $includeNoPost = '';
        }
    
    $trk_permission = trk_permissions('sitemap-page.xml');
    if ($trk_permission > 2 || (!$trk_active && !$trk_news_active)) return;

    //mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    //mysql_query("SET NAMES '".DB_CHARSET."'");
    //mysql_select_db(DB_NAME);

    echo '
            <table cellpadding="5">
            <tbody>
                <tr style="background-color: whitesmoke;">
                    <th>#</th>
                    <th>XML Sitemap</th>
                    <th>Last Modified</th>
                </tr>
    ';

    $home = get_option('home') . "/";

    $xml_sitemap_google_news = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xml_sitemap_google_news .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap-page/0.9\">
    <!-- Generated by XML-Sitemap Generator ".$trk_sitemap_version." -->
    <!-- plugin by Tulsiram kushwah -->
    <!-- sitemap for pages -->
    <!-- https://www.facebook.com/ramp00786 -->
    <!-- Created ".get_date_from_gmt(date('Y-m-d H:i:s'), 'F d, Y, H:i')." -->";

    $posts = $wpdb->get_results("SELECT * FROM ".$wpdb->posts." WHERE `post_status`='publish' 
    AND (`post_type`='page') ". $includeNoCat . ' ' . $includeNoPost." GROUP BY `ID` ORDER BY `post_modified_gmt` DESC");		
    
    $now = time();
    $twoDays = 2*24*60*60;
    $num = 0;
    foreach ($posts as $post) {
        if ($trk_news_active && $trk_permission != 2) {
            $postDate = strtotime($post->post_date);
            if (1) { //if ($now - $postDate < $twoDays) {
                $num++;

                if($num % 2){
                    $cls = "";
                }
                else{
                    $cls = "style='background-color: whitesmoke;'";
                }


                echo '<tr '.$cls.'>';

                    echo '<td>';
                    echo $num;
                    echo '</td>';

                    echo '<td>';
                    echo '<a href="'.trk_escapexml(get_permalink($post->ID)).'">';
                    echo trk_escapexml(get_permalink($post->ID));
                    echo '</a>';
                    echo '</td>';


                    echo '<td>';
                    echo str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z";
                    echo '</td>';

                echo '<tr>';

                $xml_sitemap_google_news .= "
                <url>
                    <loc>".trk_escapexml(get_permalink($post->ID))."</loc>
                    <lastmod>".str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z"."</lastmod>
                </url>";
            }
        }
    }

    echo '
            </tbody>
         </table>
    ';

    $xml_sitemap_google_news .= "\n</urlset>";
    
    
    if ($trk_news_active && $trk_permission != 2) {
        $fp = fopen(ABSPATH . $trk_path . "sitemap-page.xml", 'w');
        fwrite($fp, $xml_sitemap_google_news);
        fclose($fp);
    }
    

    $trk_last_ping = get_option('trk_last_ping');
    if ((time() - $trk_last_ping) > 60 * 60) {
        //get_headers("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap.xml"));	//PHP5+
        if(file_exists("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-page.xml"))){
            $fp = @fopen("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-page.xml"), 80);
            @fclose($fp);
            update_option('trk_last_ping', time());
        }
        
    }
}

function trk_generate_sitemap_post_pr_page($string) {

    

    parse_str($string, $get_array); 
    $trk_post_per_page = get_option('trk_post_per_page');
    $page = $get_array['page'];

    if($get_array['page'] == 1){
        $start = 0;
        $limit = $trk_post_per_page;
    }
    else{
        $start = $trk_post_per_page * ($get_array['page']-1);
        $limit = $trk_post_per_page;
    }


    ?>

        
        
        <div id="xml-area" class="xml-area">
        <h1>XML Sitemap - Posts: page <?php echo $page; ?></h1>
        
        <div class="card" style="background-color:#c1c1c1; padding: 8px; display:inline-block; border-radius:10px;">
            <div class="card-body" style="text-shadow: 1px 1px 0px #e7e2e2;  color: #6a6868;">
                Plugin: Sitemap xml Generator <br/>
                Author: Tulsiram Kushwah<br/>
            </div>
        </div>

        <!-- Menu -->
        <ul style="list-style:none; display:flex">
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml"> Home </a></li>
            <li style="margin-right:5px; padding-top:2px" >></li>
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml-post"> Posts </a></li>
            <li style="margin-right:5px; padding-top:2px" >></li>
            <li>Page <?php echo $page ?></li>
        </ul>
        <!-- Menu -->
        

    <?php

        echo  "The xml file is generated with the name of sitemap-post-page-".$page.".xml (<a target='_blank' href='".get_site_url()."/sitemap-post-page-".$page.".xml'>".get_site_url()."/sitemap-post-page-".$page.".xml</a>)";




    global $trk_sitemap_version, $table_prefix;
    global $wpdb;
    
    $t = $table_prefix;
    
    $trk_news_active = get_option('trk_news_active');
    $trk_active = get_option('trk_active');
    $trk_path = get_option('trk_path');
    //add_option('trk_publication_name','<publication_name>');
    $trk_n_name = get_option('trk_n_name');
    $trk_n_lang = get_option('trk_n_lang');
    // Genere dei contenuti
    $trk_n_genres = get_option('trk_n_genres');
    $trk_n_genres_type = get_option('trk_n_genres_type');
    // Tipo di accesso dell'articolo - Facoltativo
    $trk_n_access = get_option('trk_n_access');
    $trk_n_access_type = get_option('trk_n_access_type');
    //add_option('trk_n_access_type','Registration');
    //$trk_n_excludecat = get_option('trk_n_excludecat');
    $trk_n_excludecatlist = get_option('trk_n_excludecatlist');
    $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
    
    $includeMe = '';
    $includeNoCat = '';
    $includeNoPost = '';
    if ( $trk_n_excludecatlist <> NULL ) {
        $exPosts = get_objects_in_term($trk_n_excludecatlist,"category");
        $includeNoCat = ' AND `ID` NOT IN ('.implode(",",$exPosts).')';
        $ceck = implode(",",$exPosts);
        if ($ceck == '' || $ceck == ' ') $includeNoCat = '';
        }
    if ($trk_n_excludepostlist != ''){
        $includeNoPost = ' AND `ID` NOT IN ('.$trk_n_excludepostlist.')';
        $ceck = implode(",",$exPosts);
        if ($trk_n_excludepostlist == '' || $trk_n_excludepostlist == ' ') $includeNoPost = '';
        }
    
    $trk_permission = trk_permissions('sitemap-post-page-'.$page.'.xml');
    if ($trk_permission > 2 || (!$trk_active && !$trk_news_active)) return;

    //mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    //mysql_query("SET NAMES '".DB_CHARSET."'");
    //mysql_select_db(DB_NAME);

    $home = get_option('home') . "/";
    
    $xml_sitemap_google_news = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xml_sitemap_google_news .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap-post-page-".$page."/0.9\">
    <!-- Generated by XML-Sitemap Generator ".$trk_sitemap_version." -->
    <!-- plugin by Tulsiram kushwah -->
    <!-- sitemap for post page $page -->
    <!-- https://www.facebook.com/ramp00786 -->
    <!-- Created ".get_date_from_gmt(date("Y-m-d H:i:s"), 'F d, Y, H:i')." -->";

    //  echo "<br/>";
    //  echo "SELECT * FROM ".$wpdb->posts." WHERE `post_status`='publish' AND (`post_type`='post') GROUP BY `ID` ORDER BY `post_modified_gmt` DESC LIMIT $start, $limit";

    //  echo "<br/>";
    

    $posts = $wpdb->get_results("SELECT * FROM ".$wpdb->posts." WHERE `post_status`='publish' 
    AND (`post_type`='post') GROUP BY `ID` ORDER BY `post_modified_gmt` DESC LIMIT $start, $limit");		
    
    $now = time();
    $twoDays = 2*24*60*60;


    echo '
            <table cellpadding="5">
            <tbody>
                <tr style="background-color: whitesmoke;">
                    <th>#</th>
                    <th>XML Sitemap</th>
                    <th>Last Modified</th>
                </tr>
    ';


    $num = 0;
    foreach ($posts as $post) {
        if ($trk_news_active && $trk_permission != 2) {
            $postDate = strtotime($post->post_date);
            if (1) { //if ($now - $postDate < $twoDays) {
                $num++;
                if($num % 2){
                    $cls = "";
                }
                else{
                    $cls = "style='background-color: whitesmoke;'";
                }


                echo '<tr '.$cls.'>';

                    echo '<td>';
                    echo $num;
                    echo '</td>';

                    echo '<td>';
                    echo '<a href="'.trk_escapexml(get_permalink($post->ID)).'">';
                    echo trk_escapexml(get_permalink($post->ID));
                    echo '</a>';
                    echo '</td>';


                    echo '<td>';
                    echo str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z";
                    echo '</td>';

                echo '<tr>';


                $xml_sitemap_google_news .= "
                <url>
                    <loc>".trk_escapexml(get_permalink($post->ID))."</loc>
                    <lastmod>".str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z"."</lastmod>
                </url>";
            }
        }
    }


    echo '
            </tbody>
         </table>
    ';



    $xml_sitemap_google_news .= "\n</urlset>";
    
    
    if ($trk_news_active && $trk_permission != 2) {
        $fp = fopen(ABSPATH . $trk_path . "sitemap-post-page-".$page.".xml", 'w');
        fwrite($fp, $xml_sitemap_google_news);
        fclose($fp);
    }
    

    /* $trk_last_ping = get_option('trk_last_ping');
    if ((time() - $trk_last_ping) > 60 * 60) {
        //get_headers("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap.xml"));	//PHP5+
        if(file_exists("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-page.xml"))){
            $fp = @fopen("http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($home . $trk_path . "sitemap-page.xml"), 80);
            @fclose($fp);
            update_option('trk_last_ping', time());
        }
        
    } */

    //echo $msg = "The xml file is generated with the name of sitemap-post-page-".$get_array['page'].".xml (<a target='_blank' href='".get_site_url()."/sitemap-post-page-".$page.".xml'>".get_site_url()."/sitemap-post-page-".$get_array['page'].".xml</a>)";
}

function trk_generate_sitemap_post_all_in_one_page() {

    

    parse_str($string, $get_array); 
    $trk_post_per_page = get_option('trk_post_per_page');
    $page = $get_array['page'];

    if($get_array['page'] == 1){
        $start = 0;
        $limit = $trk_post_per_page;
    }
    else{
        $start = $trk_post_per_page * ($get_array['page']-1);
        $limit = $trk_post_per_page;
    }


    ?>

        
        
        <div id="xml-area" class="xml-area">
        <h1>XML Sitemap - Posts: limit 1000</h1>
        
        <div class="card" style="background-color:#c1c1c1; padding: 8px; display:inline-block; border-radius:10px;">
            <div class="card-body" style="text-shadow: 1px 1px 0px #e7e2e2;  color: #6a6868;">
                Plugin: Sitemap xml Generator <br/>
                Author: Tulsiram Kushwah<br/>
            </div>
        </div>

        <!-- Menu -->
        <ul style="list-style:none; display:flex">
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml"> Home </a></li>
            <li style="margin-right:5px; padding-top:2px" >></li>
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml-post"> Posts </a></li>
            <!-- <li style="margin-right:5px; padding-top:2px" >></li>
            <li>Page <?php //echo $page ?></li> -->
        </ul>
        <!-- Menu -->
        

    <?php

        echo  "The xml file is generated with the name of sitemap-post.xml (<a target='_blank' href='".get_site_url()."/sitemap-post.xml'>".get_site_url()."/sitemap-post.xml</a>)";




    global $trk_sitemap_version, $table_prefix;
    global $wpdb;
    
    $t = $table_prefix;
    
    $trk_news_active = get_option('trk_news_active');
    $trk_active = get_option('trk_active');
    $trk_path = get_option('trk_path');
    //add_option('trk_publication_name','<publication_name>');
    $trk_n_name = get_option('trk_n_name');
    $trk_n_lang = get_option('trk_n_lang');
    // Genere dei contenuti
    $trk_n_genres = get_option('trk_n_genres');
    $trk_n_genres_type = get_option('trk_n_genres_type');
    // Tipo di accesso dell'articolo - Facoltativo
    $trk_n_access = get_option('trk_n_access');
    $trk_n_access_type = get_option('trk_n_access_type');
    //add_option('trk_n_access_type','Registration');
    //$trk_n_excludecat = get_option('trk_n_excludecat');
    $trk_n_excludecatlist = get_option('trk_n_excludecatlist');
    $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
    
    $includeMe = '';
    $includeNoCat = '';
    $includeNoPost = '';
    if ( $trk_n_excludecatlist <> NULL ) {
        $exPosts = get_objects_in_term($trk_n_excludecatlist,"category");
        $includeNoCat = ' AND `ID` NOT IN ('.implode(",",$exPosts).')';
        $ceck = implode(",",$exPosts);
        if ($ceck == '' || $ceck == ' ') $includeNoCat = '';
        }
    if ($trk_n_excludepostlist != ''){
        $includeNoPost = ' AND `ID` NOT IN ('.$trk_n_excludepostlist.')';
        $ceck = implode(",",$exPosts);
        if ($trk_n_excludepostlist == '' || $trk_n_excludepostlist == ' ') $includeNoPost = '';
        }
    
    $trk_permission = trk_permissions('sitemap-post.xml');
    if ($trk_permission > 2 || (!$trk_active && !$trk_news_active)) return;

    //mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
    //mysql_query("SET NAMES '".DB_CHARSET."'");
    //mysql_select_db(DB_NAME);

    $home = get_option('home') . "/";
    
    $xml_sitemap_google_news = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xml_sitemap_google_news .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap-post.xml/0.9\">
    <!-- Generated by XML-Sitemap Generator ".$trk_sitemap_version." -->
    <!-- plugin by Tulsiram kushwah -->
    <!-- sitemap for posts limit 1000 -->
    <!-- https://www.facebook.com/ramp00786 -->
    <!-- Created ".get_date_from_gmt(date("Y-m-d H:i:s"), 'F d, Y, H:i')." -->";

    //  echo "<br/>";
    //  echo "SELECT * FROM ".$wpdb->posts." WHERE `post_status`='publish' AND (`post_type`='post') GROUP BY `ID` ORDER BY `post_modified_gmt` DESC LIMIT $start, $limit";

    //  echo "<br/>";
    

    $posts = $wpdb->get_results("SELECT * FROM ".$wpdb->posts." WHERE `post_status`='publish' 
    AND (`post_type`='post') GROUP BY `ID` ORDER BY `post_modified_gmt` DESC LIMIT 1, 1000");		
    
    $now = time();
    $twoDays = 2*24*60*60;


    echo '
            <table cellpadding="5">
            <tbody>
                <tr style="background-color: whitesmoke;">
                    <th>#</th>
                    <th>XML Sitemap</th>
                    <th>Last Modified</th>
                </tr>
    ';


    $num = 0;
    foreach ($posts as $post) {
        if ($trk_news_active && $trk_permission != 2) {
            $postDate = strtotime($post->post_date);
            if (1) { //if ($now - $postDate < $twoDays) {
                $num++;
                if($num % 2){
                    $cls = "";
                }
                else{
                    $cls = "style='background-color: whitesmoke;'";
                }


                echo '<tr '.$cls.'>';

                    echo '<td>';
                    echo $num;
                    echo '</td>';

                    echo '<td>';
                    echo '<a href="'.trk_escapexml(get_permalink($post->ID)).'">';
                    echo trk_escapexml(get_permalink($post->ID));
                    echo '</a>';
                    echo '</td>';


                    echo '<td>';
                    echo str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z";
                    echo '</td>';

                echo '<tr>';


                $xml_sitemap_google_news .= "
                <url>
                    <loc>".trk_escapexml(get_permalink($post->ID))."</loc>
                    <lastmod>".str_replace(" ", "T", get_date_from_gmt($post->post_modified_gmt))."Z"."</lastmod>
                </url>";
            }
        }
    }


    echo '
            </tbody>
         </table>
    ';



    $xml_sitemap_google_news .= "\n</urlset>";
    
    
    if ($trk_news_active && $trk_permission != 2) {
        $fp = fopen(ABSPATH . $trk_path . "sitemap-post.xml", 'w');
        fwrite($fp, $xml_sitemap_google_news);
        fclose($fp);
    }
    

    
}

function trk_generate_sitemap_category(){
    
    ?>  
        <div id="xml-area" class="xml-area">
        <h1>XML Sitemap - Categories</h1>
        
        <div class="card" style="background-color:#c1c1c1; padding: 8px; display:inline-block; border-radius:10px;">
            <div class="card-body" style="text-shadow: 1px 1px 0px #e7e2e2;  color: #6a6868;">
                Plugin: Sitemap xml Generator <br/>
                Author: Tulsiram Kushwah<br/>
            </div>
        </div>

        <!-- Menu -->
        <ul style="list-style:none; display:flex">
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml"> Home </a></li>
            <li style="margin-right:5px; padding-top:2px" >></li>
            <li> category </a> </li>
        </ul>
        <!-- Menu -->
    <?php

    echo  "The xml file is generated with the name of sitemap-category.xml (<a target='_blank' href='".get_site_url()."/sitemap-category.xml'>".get_site_url()."/sitemap-category.xml</a>)";

    echo '
        <table cellpadding="5">
            <tbody>
                <tr style="background-color: whitesmoke;">
                    <th>#</th>
                    <th>XML Sitemap</th>
                    
                </tr>
        ';
    $home = get_option('home') . "/";

    $trk_sitemap_version = get_option('trk_sitemap_version');

    $xml_sitemap_google_news = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
    $xml_sitemap_google_news .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap-category/0.9\">
    <!-- Generated by XML-Sitemap Generator ".$trk_sitemap_version." -->
    <!-- plugin by Tulsiram kushwah -->
    <!-- sitemap for category -->
    <!-- https://www.facebook.com/ramp00786 -->
    <!-- Created ".get_date_from_gmt(date('Y-m-d H:i'), "F d, Y, H:i")." -->";

    $num = 0;
    $categories = get_categories();
  
    foreach($categories as $category) {

        //echo '<div class="col-md-4"><a href="' . get_category_link($category->term_id) . '">' . $category->name . '</a></div>';

        $num++;
        if($num % 2){
            $cls = "";
        }
        else{
            $cls = "style='background-color: whitesmoke;'";
        }


        echo '<tr '.$cls.'>';

            echo '<td>';
            echo $num;
            echo '</td>';

            echo '<td>';
            echo '<a href="'.get_category_link($category->term_id).'">';
            echo get_category_link($category->term_id);
            echo '</a>';
            echo '</td>';


            // echo '<td>';
            // echo str_replace(" ", "T", $category->name)."Z";
            // echo '</td>';

        echo '<tr>';

        $xml_sitemap_google_news .= "
        <url>
            <loc>".get_category_link($category->term_id)."</loc>
        </url>";
        
    }


    echo '
            </tbody>
        </table>
        ';

    $xml_sitemap_google_news .= "\n</urlset>";

    $trk_path = get_option('trk_path');
    if (1) {
        $fp = fopen(ABSPATH . $trk_path . "sitemap-category.xml", 'w');
        fwrite($fp, $xml_sitemap_google_news);
        fclose($fp);
    }





}

//Config page
function trk_admin_page() {
    
    $msg = "";

    // Check form submission and update options
    if (isset($_POST['trk_submit']) ) {
        update_option('trk_news_active', $_POST['trk_news_active']);
        update_option('trk_n_name', $_POST['trk_n_name']);
        update_option('trk_n_lang', $_POST['trk_n_lang']);

        if(isset($_POST['trk_n_access'])){
            update_option('trk_n_access', $_POST['trk_n_access']);
        }

        if(isset($_POST['trk_n_genres'])){
            update_option('trk_n_genres', $_POST['trk_n_genres']);
        }

        if(isset($_POST['trk_n_excludecat'])){
            update_option('trk_n_excludecat', $_POST['trk_n_excludecat']);
        }
        

        update_option('trk_n_excludepostlist', $_POST['trk_n_excludepostlist']);

        update_option('trk_post_per_page', $_POST['trk_post_per_page']);

        
        
        $newPath = trim($_POST['trk_path']);
        if ($newPath == "" || $newPath == "/") $newPath = "./";
        elseif ($newPath[strlen($newPath)-1] != "/") $newPath .= "/";
        
        update_option('trk_path', $newPath);
        
        if ( $_POST['trk_n_genres_type']=="Blog" 
             || $_POST['trk_n_genres_type']=="PressReleases"
             || $_POST['trk_n_genres_type']=="UserGenerated" 
             || $_POST['trk_n_genres_type']=="Satire" 
             || $_POST['trk_n_genres_type']=="OpEd" 
             || $_POST['trk_n_genres_type']=="Opinion" ) {
            update_option('trk_n_genres_type', $_POST['trk_n_genres_type']);
        } else { 
            update_option('trk_n_genres_type', "blog"); 
        }
        
        if ($_POST['trk_n_access_type']=="Subscription" || $_POST['trk_n_access_type']=="Registration" ) update_option('trk_n_access_type', $_POST['trk_n_access_type']);
        else update_option('trk_n_access_type', "Subscription");
        
        // Excluded category
        $exCats = array();
        if(isset($_POST["post_category"])) {
            foreach((array) $_POST["post_category"] AS $vv) if(!empty($vv) && is_numeric($vv)) $exCats[] = intval($vv);
        }
        update_option('trk_n_excludecatlist', $exCats); 
        
        // Sitemap generation
        trk_generate_sitemap();

        $msg = "Setting saved and generated sitemap-news.xml (<a target='_blank' href='".get_site_url()."/sitemap-news.xml'>".get_site_url()."/sitemap-news.xml</a>)";
    }
    

    $trk_news_active = get_option('trk_news_active');
    $trk_path = get_option('trk_path');
    $trk_n_name = get_option('trk_n_name');
    $trk_n_lang = get_option('trk_n_lang');
    $trk_n_genres = get_option('trk_n_genres');
    $trk_n_genres_type = get_option('trk_n_genres_type');
    $trk_n_access = get_option('trk_n_access');
    $trk_n_access_type = get_option('trk_n_access_type');
    $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
    
    $trk_permission = trk_permissions('sitemap-news.xml');
    
    if ($trk_permission == 1) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable. <a href=\"http://www.andreapernici.com/wordpress/google-news-sitemap/\" target=\"_blank\" >For help see the plugin's homepage</a>.";
    elseif ($trk_permission == 2) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable. <a href=\"http://www.andreapernici.com/wordpress/google-news-sitemap/\" target=\"_blank\" >For help see the plugin's homepage</a>.";
    elseif ($trk_permission == 3) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable. <a href=\"http://www.andreapernici.com/wordpress/google-news-sitemap/\" target=\"_blank\" >For help see the plugin's homepage</a>.";

    

    $dir = plugin_dir_path( __FILE__ );
    include($dir."/templates/setting.php");
    
}

function trk_generate_sitemap_post_all(){

    

    $trk_post_per_page = get_option('trk_post_per_page');
    $total_posts =  wp_count_posts()->publish;
    $rows = $total_posts/$trk_post_per_page;

    if(is_float($rows)){
        $loop = round($rows, '0')+1;
    }
    else{
        $loop = $rows;
    }

    for($i = 1; $loop >= $i; $i++){
        //echo $i;
        trk_generate_sitemap_post_pr_page("page=".$i);
    }
}

function trk_generate_sitemap_root(){

    


    $msg = '';

    $trk_news_active = get_option('trk_news_active');
    $trk_path = get_option('trk_path');
    $trk_n_name = get_option('trk_n_name');
    $trk_n_lang = get_option('trk_n_lang');
    $trk_n_genres = get_option('trk_n_genres');
    $trk_n_genres_type = get_option('trk_n_genres_type');
    $trk_n_access = get_option('trk_n_access');
    $trk_n_access_type = get_option('trk_n_access_type');
    $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
    
    $trk_permission = trk_permissions('sitemap.xml');
    
    if ($trk_permission == 1) $msg = "Error: there is a problem with <em>sitemap.xml</em>. It doesn't exist or is not writable.";
    elseif ($trk_permission == 2) $msg = "Error: there is a problem with <em>sitemap.xml</em>. It doesn't exist or is not writable.";
    elseif ($trk_permission == 3) $msg = "Error: there is a problem with <em>sitemap.xml</em>. It doesn't exist or is not writable.";
    if($msg == ''){
        $trk_sitemap_version = get_option('trk_sitemap_version');
        $trk_path = get_option('trk_path');

        $xml_sitemap_google_news = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
        $xml_sitemap_google_news .= "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:n=\"http://www.google.com/schemas/sitemap/0.9\">
        <!-- Generated by XML-Sitemap Generator ".$trk_sitemap_version." -->
        <!-- plugin by Tulsiram kushwah -->
        <!-- sitemap for root -->
        <!-- https://www.facebook.com/ramp00786 -->
        <!-- Created ".date("F d, Y, H:i")." -->";

        $xml_sitemap_google_news .= "
            <url>
                <loc>".get_site_url()."/sitemap-post.xml</loc>
                <lastmod>".str_replace(" ", "T", get_date_from_gmt(date('Y-m-d H:i'), "Y-m-d H:s:i"))."Z"."</lastmod>
            </url>
            
            <url>
                <loc>".get_site_url()."/sitemap-page.xml</loc>
                <lastmod>".str_replace(" ", "T", get_date_from_gmt(date('Y-m-d H:i'), "Y-m-d H:s:i"))."Z"."</lastmod>
            </url>

            <url>
                <loc>".get_site_url()."/sitemap-news.xml</loc>
                <lastmod>".str_replace(" ", "T", get_date_from_gmt(date('Y-m-d H:i'), "Y-m-d H:s:i"))."Z"."</lastmod>
            </url>

            <url>
                <loc>".get_site_url()."/sitemap-category.xml</loc>
                <lastmod>".str_replace(" ", "T", get_date_from_gmt(date('Y-m-d H:i'), "Y-m-d H:s:i"))."Z"."</lastmod>
            </url>
            
            ";
            

        $xml_sitemap_google_news .= "\n</urlset>";


        if (1) {
            $fp = fopen(ABSPATH . $trk_path . "sitemap.xml", 'w');
            fwrite($fp, $xml_sitemap_google_news);
            fclose($fp);
        }
    }
    else{
        echo $msg;
    }
        
}

function htmlEntityMaker($str){

    

    $htmlCodes = [
        
        '&' => '&amp;',
        '"' => '&quot;',
        '<' => '&lt;',
        '>' => '&gt;',
        "'" => '&apos;',
    
    ];

    foreach($htmlCodes as $key => $val){
        if (strpos($str, $key) !== false) {
            $str = str_replace($key, $val, $str);
        }
    }
    
    //return 'hello';
    //$str = htmlentities($str);
    return htmlspecialchars($str);
}

//--Define the function for generate xml
function XmlGenerator(){

    
    //---Get url
    $current_url = home_url($_SERVER['REQUEST_URI']);
    $pageName = explode('/', $current_url);
    $subQstring = explode('?', end($pageName));    
    //--Check url if match with xml-generate
    

    if($subQstring[0] == 'generate-xml-root'){
        
    }

    if($subQstring[0] == 'generate-xml-news'){
        //--Generate xml with default setting
        $msg = '';

        $trk_news_active = get_option('trk_news_active');
        $trk_path = get_option('trk_path');
        $trk_n_name = get_option('trk_n_name');
        $trk_n_lang = get_option('trk_n_lang');
        $trk_n_genres = get_option('trk_n_genres');
        $trk_n_genres_type = get_option('trk_n_genres_type');
        $trk_n_access = get_option('trk_n_access');
        $trk_n_access_type = get_option('trk_n_access_type');
        $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
        
        $trk_permission = trk_permissions('sitemap-news.xml');
        
        if ($trk_permission == 1) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 2) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 3) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        if($msg == ''){
            //trk_generate_sitemap();
            trk_generate_sitemap_with_posts();
        }
        else{
            echo $msg;
        }
        

        die();
    }

    if($subQstring[0] == 'generate-xml-page'){
        //--Generate xml with default setting
        $msg = '';

        $trk_news_active = get_option('trk_news_active');
        $trk_path = get_option('trk_path');
        $trk_n_name = get_option('trk_n_name');
        $trk_n_lang = get_option('trk_n_lang');
        $trk_n_genres = get_option('trk_n_genres');
        $trk_n_genres_type = get_option('trk_n_genres_type');
        $trk_n_access = get_option('trk_n_access');
        $trk_n_access_type = get_option('trk_n_access_type');
        $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
        
        $trk_permission = trk_permissions('sitemap-page.xml');
        
        if ($trk_permission == 1) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 2) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 3) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        if($msg == ''){
            trk_generate_sitemap_page();
        }
        else{
            echo $msg;
        }
        

        die();
    }


    if($subQstring[0] == 'generate-xml-posts'){
        //--Generate xml with default setting
        $msg = '';

        $trk_news_active = get_option('trk_news_active');
        $trk_path = get_option('trk_path');
        $trk_n_name = get_option('trk_n_name');
        $trk_n_lang = get_option('trk_n_lang');
        $trk_n_genres = get_option('trk_n_genres');
        $trk_n_genres_type = get_option('trk_n_genres_type');
        $trk_n_access = get_option('trk_n_access');
        $trk_n_access_type = get_option('trk_n_access_type');
        $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
        
        $trk_permission = trk_permissions('sitemap-post.xml');
        
        if ($trk_permission == 1) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 2) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 3) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        if($msg == ''){
            trk_generate_sitemap_post_pr_page($subQstring[1]);
        }
        else{
            echo $msg;
        }
        

        die();
    }


    if($subQstring[0] == 'generate-xml-post'){
        //sitemap-post.xml
        //get_site_url() ."generate-xml-posts?page=". $i;

        //--Generate xml with default setting
        $msg = '';

        $trk_news_active = get_option('trk_news_active');
        $trk_path = get_option('trk_path');
        $trk_n_name = get_option('trk_n_name');
        $trk_n_lang = get_option('trk_n_lang');
        $trk_n_genres = get_option('trk_n_genres');
        $trk_n_genres_type = get_option('trk_n_genres_type');
        $trk_n_access = get_option('trk_n_access');
        $trk_n_access_type = get_option('trk_n_access_type');
        $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
        
        $trk_permission = trk_permissions('sitemap-post.xml');
        
        if ($trk_permission == 1) $msg = "Error: there is a problem with <em>sitemap-post.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 2) $msg = "Error: there is a problem with <em>sitemap-post.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 3) $msg = "Error: there is a problem with <em>sitemap-post.xml</em>. It doesn't exist or is not writable.";
        if($msg == ''){
            //trk_generate_sitemap_post_all_in_one_page();
            trk_generate_sitemap_with_posts();
        }
        else{
            echo $msg;
        }        

        die();

    }


    if($subQstring[0] == 'generate-xml-post_old'){
        //--Generate xml with default setting
        $msg = '';

        $trk_post_per_page = get_option('trk_post_per_page');
        $total_posts =  wp_count_posts()->publish;
        $rows = $total_posts/$trk_post_per_page;

        if(is_float($rows)){
            $loop = round($rows, '0')+1;
        }
        else{
            $loop = $rows;
        }

        ?>

        
        
        <div id="xml-area" class="xml-area">
        <h1>XML Sitemap - Posts</h1>
        
        <div class="card" style="background-color:#c1c1c1; padding: 8px; display:inline-block; border-radius:10px;">
            <div class="card-body" style="text-shadow: 1px 1px 0px #e7e2e2;  color: #6a6868;">
                Plugin: Sitemap xml Generator <br/>
                Author: Tulsiram Kushwah<br/>
            </div>
        </div>

        <!-- Menu -->
        <ul style="list-style:none; display:flex">
            <li style="margin-right:5px"> <a href="<?php echo get_site_url(); ?>/generate-xml"> Home </a></li>
            <li style="margin-right:5px; padding-top:2px" >></li>
            <li>Posts</li>
            
            
        </ul>
        <!-- Menu -->


        <p>The posts are split from <?php echo $trk_post_per_page; ?> for each page</p>

        <a href="generate-xml-all-post" onclick="return confirm('Are you sure to generate static xml files for all posts')" style="font-size:14px; color:#970505"> Generate xml files for all posts</a>
        <br/>
        <br/>

        <table cellpadding="5">
            <tbody>
                <tr style="background-color: whitesmoke;">
                    <th>#</th>
                    <th>XML Sitemap</th>
                    <th>Last Modified</th>
                </tr>

                <?php 
                
                for($i = 1; $loop >= $i; $i++){
                    //echo $i.'<br/>';
                    if($i % 2){
                        $cls = "style='background-color: whitesmoke;'";
                    }
                    else{
                        $cls = '';
                    }
                    


                ?>
                <tr <?php echo $cls; ?>>
                    <td>1</td>
                    <td>
                    
                        <a href="<?php echo get_site_url() ?>/generate-xml-posts?page=<?php echo $i; ?>"><?php echo get_site_url() ?>/sitemap-post-page-<?php echo $i; ?>.xml</a>
                    </td>
                    <td>
                        <?php 
                            if(file_exists(ABSPATH.'sitemap-post-page-'.$i.'.xml')){            
                                echo get_date_from_gmt(date('Y-m-d H:i:s', filemtime(ABSPATH.'sitemap-post-page-'.$i.'.xml')), "Y-m-d H:i:s")."(".wp_timezone_string().")";
                            }
                            else{
                                echo 'No created yet';
                            }
                        ?>
                    </td>
                </tr>

                <?php } ?>
              
            </tbody>
        </table>
        </div>


        <?php
        
        
        

        die();
    }

    if($subQstring[0] == 'generate-xml-category'){
        
        //--Generate xml with default setting
        $msg = '';

        $trk_news_active = get_option('trk_news_active');
        $trk_path = get_option('trk_path');
        $trk_n_name = get_option('trk_n_name');
        $trk_n_lang = get_option('trk_n_lang');
        $trk_n_genres = get_option('trk_n_genres');
        $trk_n_genres_type = get_option('trk_n_genres_type');
        $trk_n_access = get_option('trk_n_access');
        $trk_n_access_type = get_option('trk_n_access_type');
        $trk_n_excludepostlist = get_option('trk_n_excludepostlist');
        
        $trk_permission = trk_permissions('sitemap-category.xml');
        
        if ($trk_permission == 1) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 2) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        elseif ($trk_permission == 3) $msg = "Error: there is a problem with <em>sitemap-news.xml</em>. It doesn't exist or is not writable.";
        if($msg == ''){
            trk_generate_sitemap_category();
        }
        else{
            echo $msg;
        }        

        die();
    }
    
    if($subQstring[0] == 'generate-xml'){ 
        
        ?>
        
        <div>
        <h1>XML Sitemap - Index</h1>
        
        <div class="card" style="background-color:#c1c1c1; padding: 8px; display:inline-block; border-radius:10px;">
            <div class="card-body" style="text-shadow: 1px 1px 0px #e7e2e2;  color: #6a6868;">
                Plugin: Sitemap xml Generator <br/>
                Author: Tulsiram Kushwah<br/>
            </div>
        </div>
        <br/>
        <br/>
        
        <!-- Menu -->
        <ul style="list-style:none">
            <li> <a href="<?php echo get_site_url(); ?>/generate-xml"> Home </a> </li>
        </ul>
        <!-- Menu -->

        

        <p>This plugin will generate static xml file for root, posts, pages, google-news and category</p>
        
        <a href="generate-xml-all" onclick="return confirm('Are you sure to generate static xml files for root, posts, pages, google-news and category ')" style="font-size:14px; color:#970505"> Generate xml files for all</a>
        <br/>
        <br/>
        
        <?php 
        echo  "The xml file is generated with the name of sitemap.xml (<a target='_blank' href='".get_site_url()."/sitemap.xml'>".get_site_url()."/sitemap.xml</a>)";
        ?>

        <table cellpadding="5">
            <tbody>
                <tr style="background-color: whitesmoke;">
                    <th>#</th>
                    <th>XML Sitemap</th>
                    <th>Last Modified</th>
                </tr>
                <tr >
                    <td>1</td>
                    <td>
                        <a href="<?php echo get_site_url() ?>/generate-xml-post"><?php echo get_site_url() ?>/sitemap-post.xml</a>
                    </td>
                    <td>
                        <?php 
                            if(file_exists(ABSPATH.'sitemap-post.xml')){            
                                echo get_date_from_gmt(date("Y-m-d H:i:s ", filemtime(ABSPATH.'sitemap-post.xml')), 'Y-m-d H:i:s')."(".wp_timezone_string().")";
                            }
                            else{
                                echo 'No created yet';
                            }
                        ?>
                    </td>
                </tr>
                <tr style="background-color: whitesmoke;">
                    <td>2</td>
                    <td>
                        <a href="<?php echo get_site_url() ?>/generate-xml-page"><?php echo get_site_url() ?>/sitemap-page.xml</a>
                    </td>
                    <td>
                        <?php 
                            if(file_exists(ABSPATH.'sitemap-page.xml')){            
                                echo get_date_from_gmt(date("Y-m-d H:i:s ", filemtime(ABSPATH.'sitemap-page.xml')), 'Y-m-d H:i:s')."(".wp_timezone_string().")";
                            }
                            else{
                                echo 'No created yet';
                            }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>
                        <a href="<?php echo get_site_url() ?>/generate-xml-news"><?php echo get_site_url() ?>/sitemap-news.xml</a>
                    </td>
                    <td>
                        <?php 
                            if(file_exists(ABSPATH.'sitemap-news.xml')){            
                                echo get_date_from_gmt(date("Y-m-d H:i:s ", filemtime(ABSPATH.'sitemap-news.xml')), 'Y-m-d H:i:s')."(".wp_timezone_string().")";
                            }
                            else{
                                echo 'No created yet';
                            }
                        ?>
                    </td>
                </tr>

                <tr style="background-color: whitesmoke;">
                    <td>4</td>
                    <td>
                        <a href="<?php echo get_site_url() ?>/generate-xml-category"><?php echo get_site_url() ?>/sitemap-category.xml</a>
                    </td>
                    <td>
                        <?php 
                            if(file_exists(ABSPATH.'sitemap-category.xml')){            
                                echo get_date_from_gmt(date("Y-m-d H:i:s ", filemtime(ABSPATH.'sitemap-category.xml')), 'Y-m-d H:i:s')."(".wp_timezone_string().")";
                            }
                            else{
                                echo 'No created yet';
                            }
                        ?>
                    </td>
                </tr>


            </tbody>
        </table>
        </div>

        <?php
        trk_generate_sitemap_root();
        die();
    }

    if($subQstring[0] == 'generate-xml-all'){

        trk_generate_sitemap_root();
        trk_generate_sitemap();
        trk_generate_sitemap_page();
        trk_generate_sitemap_category();
        //trk_generate_sitemap_post_all();
        trk_generate_sitemap_post_all_in_one_page();

        die();
    }

    if($subQstring[0] == 'generate-xml-all-post'){

        trk_generate_sitemap_post_all();
        die();
    }

    
    
}


