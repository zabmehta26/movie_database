<?php

namespace Drupal\movie_database\Controller;
//use Drupal\taxonomy\Entity\Term;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\node\Entity\Node;
use Drupal\Core\Controller\ControllerBase;


class PhpController extends ControllerBase{

  public function addterms() {
    // $items['element_one'] = [
    //   '#markup' => '<p>' . t("Hello World") . '</p>',
    // '#id' => 'archit',   // Doesn't work
    // '#access' => FALSE,  // Works
    // ];
    // $items['element_2'] = [
    // '#plain_text' => t("Hello World"),
    // ];
    // $items['item_list'] = [
    //   '#theme' => 'item_list',
    //   '#items' => ['i1', 'i2'],
    // ];
    // $items['table'] = [
    //   '#type' => 'table',
    //   '#caption' => t('Our favorite colors.'),
    //   '#header' => [t('Name'), t('Favorite color')],
    //   '#rows' => [
    //    [t('Amber'), t('teal')],
    //    [t('Addi'), t('green')],
    //    [t('Blake'), t('#063')],
    //    [t('Enid'), t('indigo')],
    //    [t('Joe'), t('green')],
    //   ],
  // ];
    // $items = array(
    //   array('name' => 'article one'),
    //   array('name' => 'article 2'),
    //   array('name' => 'article 3'),
    //   array('name' => 'article 4'),
    // );
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
      //kint($items);
      return $this->render(
        array(
          '#theme' => 'article_list',
          '#items' => $items,
          '#title' => 'our article list',
        )
      );
  }
}


}

?>
