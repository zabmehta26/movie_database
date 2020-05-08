<?php

namespace Drupal\movie_database\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;

/**
 * [the class contains the function for the actor's list and movie list page]
 */
class testing extends ControllerBase{

 /**
  * [the function below returns the movie list array]
  * @return [array] [the array is of the movie list]
  */
  public function test() {
    $bundle = 'movies';
    $movie = NULL;
    if (empty($movie)){
    $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', $bundle);
    }
    else{
    $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', $bundle)
        ->condition('title', $movie, 'CONTAINS');
    }
    $nids = $query->execute();
    if (empty($nids)){
      $data = array("#markup" => "No Results Found");
    }
    else{
      $nodes = entity_load_multiple('node', $nids);
      $items = array();
      //kint($items);
      foreach($nodes as $node){
        $node_title = $node->title->value;
        $node_body = $node->get('body')->value; //can use getString() getValue() instead of value
        $node_image_fid = $node->get('field_movie_poster')->target_id;
        if ( !is_null($node_image_fid) ){
          $image_entity = \Drupal\file\Entity\File::load($node_image_fid);
          $image_entity_url = $image_entity->url();
        }
        else{
          $image_entity_url = "/sites/default/files/default_images/obama.jpg";
        }
        $target_id = $node->get('field_paragraph')->target_id;
        $paragraph = Paragraph::load($target_id);
        $values = array();
        $array = $paragraph->field_actor->getValue();
        foreach ($array as $value) {
          if (isset($value['target_id'])) {
            $values[] = $value['target_id'];
          }
        }
        $actor = array();
        foreach ($values as $value) {
          $node_details = Node::load($value);
          $actor[] = $node_details->title->value;
        }
        $actors = implode(", ", $actor);
        $items[] = array($node_title, '<img src="'.$image_entity_url.'" >', $node_body, $actors);
        //kint($items);
      }
      $items['table'] = [
        '#type' => 'table',
        '#caption' => t('movie_database'),
        '#header' => [t('Movie_name'), 'movie_poster', t('Moive_poster'), t('cast')],
        '#rows' => $items,
      ];

      return $this->render(
        array(
          '#theme' => 'temp',
          '#items' => $items,
          '#title' => 'our article list',
        )
      );
  }
}


/**
   * This function is used to return list of movies of a particular actor.
   * @param  $type type of id       $node  id of a node.
   * @return mixed
*/
  public function actorMovie (NodeInterface $node) {

    // Get Name of the actor whose id is passed.
    $title = $node->title->value;
    // Get nid of the current node.
    $nid = $node->nid->value;
    // Query for fetching out all movies in which actor has worked.
    $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type','movie')
        ->condition('field_actor_role.entity:paragraph.field_actor.target_id',$nid);
        $nids = $query->execute();
    // check if query returned something or not.
    if (empty($nids)){
      $data = array("#markup" => "No Results Found");
      }
    else{
      $movie_nodes = entity_load_multiple('node', $nids);
      $items = array();
      foreach ($movie_nodes as $node) {
        $ratings = $node->field_rating->getValue ();
        $rating = $ratings[0]['rating'];
        $rating = $rating/20;
        $rating = $rating."/"."5";
        $node_title = $node->title->value;
        $node_id = $node->nid->value;
        $node_des = $node->field_s->value;
        $node_image_fid = $node->field_poster->target_id;
        if ( !is_null($node_image_fid) ) {
          $image_entity = \Drupal\file\Entity\File::load($node_image_fid);
          $node_poster = $image_entity->url();
        }
        else {
               $image_entity_url = "/sites/default/files/default_images/obama.jpg";
        }

        $target_id = array();
        $target_id = $node ->field_actor_role->getValue();
        $actors = array();
        $j = 0;
        $coactors = array ();
        foreach ($target_id as $value) {
          $paragraph = Paragraph::load($value['target_id']);
          $actor_id = $paragraph->field_actor->target_id;

          $actor = Node::load($actor_id);
          $actors[$j]['name'] = $actor->title->value;
          $actors[$j]['nid'] = $actor->nid->value;
          if ($actor_id != $nid ) {
            $coactors[$j]['name'] = $actor->title->value;
            $coactors[$j]['nid'] = $actor->nid->value;
          }
          $j++;
        }
        // kint ($coactors);

        $items[] = [
          'name' => $node_title,
          'nid' => $node_id,
          'des' => $node_des,
          'poster' => $node_poster,
          'actors' =>$actors,
          'ratings' =>$rating,
          'costars' => $coactors
        ];
      }
      $mTitle = 'List of '.$title.' movies';
      return array (
        '#theme' => 'actormovie_list',
        '#items' => $items,
        '#title' => $mTitle,
      );
    }
  }

  /**
     * This function is used to return JsonResponse to popup a box for costar.
     * @param  $movie=Null,$nid=Null Default parameters for movie and actor node id.
     * @return mixed
  */

      public function costar($movie=NULL, $nid=NULL) {
      $node = Node::load($movie);
      $target_id = array();
      $target_id = $node->field_actor_role->getValue();
      foreach ($target_id as $value) {
        $paragraph = Paragraph::load($value['target_id']);
        $actor_id = $paragraph->field_actor->target_id;
        if($actor_id == $nid) {
          $role = $paragraph->field_role->value;
          $actor = Node::load($actor_id);
          $node_image_fid = $actor->field_dp->target_id;
          if ( !is_null($node_image_fid) ){
            $image_entity = \Drupal\file\Entity\File::load($node_image_fid);
            $image_entity_url = $image_entity->url();
          }
          else{
            $image_entity_url = "/sites/default/files/default_images/obama.jpg";
          }
          $node_title = $actor->title->value;
          $actors['nid'] = $actor->nid->value;
        }
      }
      $items = [
         'name' => $node_title,
         'image' => $image_entity_url,
         'role' => $role,
       ];
      return new JsonResponse($items);
    }


    /**
       * This function is used to return actor list data array.
       * @return mixed
    */
      public function getActors () {
          $query = \Drupal::entityQuery('node')
          ->condition('status', 1)
          ->condition('type', 'actors');
          $nids = $query->execute();
          if (empty($nids)){
            $data = array("#markup" => "No Results Found");
          }
          else {
            $items = array();
            $actor_nodes = entity_load_multiple('node', $nids);
            foreach ($actor_nodes as $node) {
              $name = $node->title->value;
              $nid = $node->nid->value;
              $items[] = [
                'name' => $name,
                'nid' => $nid,
              ];
            }
            return array(
              '#theme' => 'actors_list',
              '#items' => $items,
              '#title' => t('List of all actors'),
            );

          }
        }


}

?>
