<?php
namespace Drupal\movie_database\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\m3dule\Controller\PhpController;
use Drupal\node\NodeInterface;
/**
 * Provides a 'Movie' Block.
 *
 * @Block(
 *   id = "movie_list",
 *   admin_label = @Translation("Movie List"),
 *   category = @Translation("Movies of an actor"),
 * )
 */
class MovieList extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function actor_movies_list($nid=NULL) {
    // $node = \Drupal::routeMatch()->getParameter('node');
    // $nid = $node->id();
    // kint($nid);
    // $obj = new PhpController ();
    // $arr = $obj->actor_movieslist('actor', $nid);
    // kint($arr);
    $node_details = Node::load($nid);
    $list_title = $node_details->title->value;
    $list_title = 'listed movies of '.$list_title;
    $nid = $node_details->nid->value;
    $bundle = 'movie';
    $query = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type', $bundle)
        ->condition('field_paragraph.entity:paragraph.field_actor.target_id',$nid)
        ->sort('field_release_date', 'DESC');
    $m_ids = $query->execute();
    if(empty($m_ids)) {
      $data = array("#markup" => "No Results Found");
    }
    else {
      $nodes = entity_load_multiple('node', $m_ids);
      $items = array();
      $actor = array();
      foreach($nodes as $node) {
        $mid = $node->id();
        $node_title = $node->title->value;
        $node_image_fid = $node->get('field_movie_poster')->target_id;
        if(!is_null($node_image_fid)) {
          $image_entity = \Drupal\file\Entity\File::load($node_image_fid);
          $image_entity_url = $image_entity->url();
        }
        else {
          $image_entity_url = "/sites/default/files/default_images/obama.jpg";
        }
        $target_id = $node->get('field_paragraph')->target_id;
        $paragraph = Paragraph::load($target_id);
        $values = array();
        $array = $paragraph->field_actor->getValue();
        foreach($array as $value) {
          if(isset($value['target_id'])) {
            $values[] = $value['target_id'];
          }
        }
        $j = 0;
        $no = 0;
        $actor = array();
        $url = array();
        // Fetched costar name and role of actor in particular movie.
        foreach($values as $value) {
          $node_details = Node::load($value);
          /* If value of nid($value) of actor equal to $nid,will get role of that
             Actor in that movie.
          */
          if($value == $nid) {
            $roles = $paragraph->field_role->getValue();
            $actor_role = $roles[$no]['value'];
          }
          // Else fetched costar of movie with its id.
          else {
            $actor[$j]['name'] = $node_details->title->value;
            $actor[$j]['actor_id'] = $value;
            $j++;
          }
          $no++;
        }
        $items[] = [
          'movie_name' => $node_title,
          'image_path' => $image_entity_url,
          'actor_role' => $actor_role,
          'costars' => $actor,
          'movie_url' =>  $mid,
        ];
      }
      return array(
        'theme' => 'actor_movies_list',
        'items' => $items,
        'title' => $list_title,
      );
    }
  }

  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = $node->id();
    $data=$this->actor_movies_list($nid);
    return array(
      '#theme' => $data['theme'],
      '#items' => $data['items'],
      '#title' => $data['title'],
      '#cache' => [
      'max-age' => 0,
      ],
    );
  }
}
