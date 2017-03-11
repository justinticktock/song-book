<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
// filter is added in the template when needed and removed after
//add_filter('term_link', 'song_term_link_filter', 10, 3);

function song_term_link_filter( $url, $term, $taxonomy ) {
    
    //die(var_dump( $term->slug ));
    // $taxonomy = "ccli-song"
    //$termlink = "http://localhost/dev/blog/ccli-song/6666/
     
             
            
    
    if ( $taxonomy === "ccli-song" ) {
        $url = 'http://uk.search.ccli.com/songs/' . $term->slug ;
    }
    
    
    
 
    
    return $url;

}