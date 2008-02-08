<?php
/*
Plugin Name: OERC Search
Description: Allows you to add a search box to any page for searching oercommons.org
Version: 0.5
Author: Nathan Kinkade
Author URI: http://creativecommons.org
*/

# Set some default option values when the plugin is first activated
register_activation_hook( __FILE__, 'oerc_pluginActivate' );

add_filter("the_content", "oerc_insertForm");
add_filter("the_content", "oerc_displayResults");
add_filter("template", "oerc_insertForm");
add_action("wp_head", "oerc_addCSS");
add_action('admin_menu', 'oerc_addOptionsPage');

## ---------------------------------------------------------- ##

/**
 * Add a sub-menu to the main Options menu
 */
function oerc_addOptionsPage() {
    # The directory where this plugin was installed
    $plugindir = dirname(__FILE__);
    add_options_page("OER Commons Search Options", "OERC Search", 10, __FILE__, "oerc_loadOptionsPage");
}

## ---------------------------------------------------------- ##

function oerc_displayResults($page_content) {

    # Only do any of this if someone searched
    if ( isset($_GET['oerc_doSearch']) ) {
        require(dirname(__FILE__) . "/oercommons_search.class.php");
        $oerc_Searcher = new OERCommonsSearch();
        if ( isset($_GET['oerc_doSearch']) && "" != trim($_GET['oerc_searchString']) ) {
            $oerc_searchString = trim($_GET['oerc_searchString']);
            $oerc_Searcher->searchOERC($oerc_searchString);
        }
        if ( ! $oerc_Searcher->result_error ) {
            if ( $oerc_Searcher->result_count > 0 ) {
                $results = $oerc_Searcher->prettifyResults();
                $search_results = "<div style='margin-top: 2ex;'>";
                $search_results .= "Displaying <strong>$oerc_Searcher->result_count</strong> of <strong>$oerc_Searcher->total_results</strong> results for a search on <strong>$oerc_Searcher->search_string</strong><br />\n";
                if ( $oerc_Searcher->result_count > 20 ) {
                    $search_results .= "View the rest of the results at <a href='http://oercommons.org/search?f.search=$oerc_Searcher->search_string&sort_order=asc&batch_start=20&batch_size=20'>oercommons.org</a><br />\n";
                $search_results .= "</div>";
                }
                foreach ( $results as $result ) {
                    $search_results .= "$result\n";
                }
            } else {
                if ( is_numeric($oerc_Searcher->result_count) ) {
                    $search_results = "<span class='oerc_msgError'><strong>Your search did not return any results.</strong></span>";
                }
            }
        } else {
            $search_results = $oerc_Searcher->result_error;
        }

        $replace_text = get_option("oerc_resultsReplaceText");
	if ( ! empty($replace_text) ) {
            # Add slashes before some common special chars that might
            # otherwise break the regular expression below.
            $replace_text = quotemeta($replace_text);
            $new_content = preg_replace("/$replace_text/", $search_results, $page_content);
            return $new_content;
	}
    }

    return $page_content;

}

## ---------------------------------------------------------- ##

/**
 * Adds CSS for styling the plugin and results to the <head>
 * section of each page
 */
function oerc_addCSS() {

    # Get contents of CSS file from the plugin's dir
    $oerc_CSS = oerc_getCSS();

    echo <<<HTML
    <!-- BEGIN headers added by oerc_search.php plugin -->
    <style>
        $oerc_CSS
    </style>
    <!-- END headers added by oerc_search.php plugin -->

HTML;

}

## ---------------------------------------------------------- ##

/**
 * Check to see if the user wanted to add the search form somewhere
 * in the template.
 */
function oerc_insertForm($content) {

    $replace_text = get_option("oerc_formReplaceText");
    $searchForm = oerc_getForm();

    if ( ! empty($replace_text) && ! empty($searchForm) ) {
        # Add slashes before some common special chars that might
        # otherwise break the regular expression below.
        $replace_text = quotemeta($replace_text);
        $new_content = preg_replace("/$replace_text/", $searchForm, $content);
        return $new_content;
    } else {
        return $content;
    }

}

## ---------------------------------------------------------- ##

/**
 * Sets the various options to some reasonable default values
 * when this plugin is activated.  If the values already
 * exist it leaves them there.
 */
function oerc_pluginActivate() {

    if ( ! get_option("oerc_formReplaceText") ) {
        update_option("oerc_formReplaceText", "<!--OERC_FORM-->", "This text will be replaced by an OERC search form");
    }

    if ( ! get_option("oerc_resultsReplaceText") ) {
        update_option("oerc_resultsReplaceText", "<!--OERC_RESULTS-->", "This text will be replaced by the results of the search");
    }

    if ( ! get_option("oerc_pageSlug") ) {
        update_option("oerc_pageSlug", "oerc_search", "WP slug of the page where this search will reside");
    }

}

## ---------------------------------------------------------- ##

/**
 * Displays and updates the Options page for this plugin
 * WP Admin -> Options -> OERC Search
 */
function oerc_loadOptionsPage() {

    if ( isset($_POST['oerc_doUpdateOptions']) ) {
        update_option('oerc_formReplaceText', trim($_POST['oerc_formReplaceText']));
        update_option('oerc_resultsReplaceText', trim($_POST['oerc_resultsReplaceText']));
        update_option('oerc_pageSlug', trim($_POST['oerc_pageSlug']));
        echo "<div class='updated fade'> <div><strong>Options saved.</strong></div></div>\n";
    }

    # Get current option value if it exist
    $oerc_formReplaceText = get_option("oerc_formReplaceText");
    $oerc_resultsReplaceText = get_option("oerc_resultsReplaceText");
    $oerc_pageSlug = get_option("oerc_pageSlug");

    echo <<<HTML
<div class="wrap">

    <h2>OER Search Options</h2>

    <form action="{$_SERVER['REQUEST_URI']}" method="post">
        <div>
            <strong>The page slug where all of this should be done</strong>:
            <input type="text" name="oerc_pageSlug" size="40" value="$oerc_pageSlug" />
        </div>
        <div>
            <strong>The search form will be added to the page wherever this text in encountered</strong>:
            <input type="text" name="oerc_formReplaceText" size="40" value="$oerc_formReplaceText" />
        </div>
        <div>
            <strong>The search result will be added to the page wherever this text in encountered</strong>:
            <input type="text" name="oerc_resultsReplaceText" size="40" value="$oerc_resultsReplaceText" />
        </div>
        <div class="submit">
            <input type="submit" name="oerc_doUpdateOptions" value="Update Options" />
        </div>
    </form>
</div>

HTML;

}

## ---------------------------------------------------------- ##

/**
 * This function will output a simple HTML form
 */
function oerc_getForm() {

    $blog_url = get_option("siteurl");
    $blog_url = rtrim($blog_url, "/");
    $page_slug = get_option("oerc_pageSlug");
    $form_action = "$blog_url/$page_slug";

    $oerc_Form = <<<HTML
<img src='http://learn-staging.creativecommons.org/images/oercommons_logo.jpg' alt="OERC" />
<form action='$form_action' method='get' id="oerc_searchForm">
    <input type='text' name='oerc_searchString' id="oerc_searchBox" />
    <input type='submit' value='Search' name='oerc_doSearch' id="oerc_submitButton" />
</form>
HTML;

    return $oerc_Form;

}

## ---------------------------------------------------------- ##

/**
 * This function will output any CSS for this plugin
 */
function oerc_getCSS() {

    $css = <<<HTML
.oerc_Result {
    margin-top: 2ex;
    margin-bottom: 2ex;
}

#oerc_searchBox {
    width: 50ex;
}
HTML;

    return $css;
   
}

## ---------------------------------------------------------- ##

?>
