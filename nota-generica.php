<?php
 /*
 * 
 * @return mixed
 */
function get_editor_recommendations_queue_items() {
    $items = array();
    if ($cache = cache_get('sidebar_recommended_nodes')) {
        $items = $cache->data;
    } else {

        $cache_expiration_time = time() + variable_get( 'imx_sidebar_queue_expiration', 10800 );

        $result = get_editor_recommendations_queue_query();

        $style = variable_get('imx_sidebar_queue_style', 'alert_fav');

        foreach ( $result as $key => $node ) {

              $nid = $node-> nid;
              $tid = $node-> workbench_access;                         
             
             $category = ( count( $tid ) > 0 ) ? get_name_of_taxonomy( $tid[ key( $tid ) ] ) : "Secciones" ;

            $image = entity_metadata_wrapper( 'node', $node )-> field_image_portada_grande-> value();

            if ( count( $image[ 0 ] ) > 1) {
                 $image = array_shift($image);
              }

            $items[] = array(
                'title' => $node-> title,
                'image' => image_style_url( $style, $image[ 'uri' ] ),
                'url' => url( 'node/' . $node-> nid, array( 'absolute' => TRUE ) ),
                'category' => $category,
            );

            unset($node, $image, $nid, $tid, $category);
        }//foreach

       cache_set('sidebar_recommended_nodes', $items, 'cache', $cache_expiration_time);
     }
    return $items;
}//get_editor_recommendations_queue_items

/**
 * 
 */
function get_editor_recommendations_queue_query() {

    $queue = variable_get('imx_sidebar_queue_title', 'recomendaciones_editor');
    $result = array();
    
    if ( $queue ) {

           $nq = nodequeue_load_queue_by_name( $queue );
           $sq = nodequeue_load_subqueues_by_queue( $nq-> qid );
     $sqobject = array_shift( $sq );

       $result = nodequeue_load_nodes ( $sqobject-> sqid, FALSE, 0, 30, TRUE );
      }

    return $result;
}
?>