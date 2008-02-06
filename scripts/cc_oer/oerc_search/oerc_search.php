<?php
/*
Plugin Name: OER Commons Search
Description: Allows you to add a search box to any page for searching oercommons.org
Version: 0.5
Author: Nathan Kinkade
Author URI: http://creativecommons.org
*/

add_filter("the_content", "oerc_printResults");
add_action("wp_head", "oerc_addCSS");

function oerc_loadSearch() {

    # Only do any of this if the user is accessing an OER Commons Search page
    if ( preg_match("/^\/oersearch/", $_SERVER['REQUEST_URI']) ) {
        require(PLUGINDIR . "/oerc_search/oercommons_search.class.php");
        $oerc_Searcher = new OERCommonsSearch();
        if ( isset($_GET['oerc_doSearch']) && "" != trim($_GET['oerc_searchString']) ) {
            $oerc_searchString = trim($_GET['oerc_searchString']);
            $oerc_Searcher->searchOERC($oerc_searchString);
        }
    } else {
        return false;
    }

    return $oerc_Searcher;

}

function oerc_printResults($page_content) {

    $oerc_Searcher = oerc_loadSearch();

    if ( $oerc_Searcher ) {
        if ( ! $oerc_Searcher->result_error ) {
            if ( $oerc_Searcher->result_count > 0 ) {
                $results = $oerc_Searcher->prettifyResults();
                $search_results = "";
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
    }

    $new_content = preg_replace("/<!--oerc_results-->/", $search_results, $page_content);
    return $new_content;

}

function oerc_addCSS() {

    echo <<<HTML
    <!-- BEGIN headers added by oerc_search.php plugin -->
    <style>
        .result { margin-top: 2ex; margin-bottom: 2ex; }
    </style>
    <!-- END headers added by oerc_search.php plugin -->

HTML;

}
