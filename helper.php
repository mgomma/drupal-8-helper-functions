<?php
/**
 * Build and return entities in drupal 8 to be ready for display in twig.
 * Currently supports menu, vocabulary, block.
 * Takes type: menu, vocabulary, block.
 * Other attributes in the second parameter as an array.
 * @todo support all entities attributes in the second parameter
 * @author Mohammed Gomma <mgomma90@gmail.com>
 */
function get_renderable_entities($type, $arr = FALSE){
  if(!$arr){
    \Drupal::logger('theme')
      ->notice('TTrying to build non exist menu');
    
    return $menu_name;
  }
  switch ($type) {
    case 'menu':

      $menu_tree = \Drupal::menuTree();
      $parameters = $menu_tree->getCurrentRouteMenuTreeParameters($arr[0]);
  
      $tree = $menu_tree->load($arr[0], $parameters);
      $manipulators = array(
    
// Only show links that are accessible for the current user.
        array('callable' => 'menu.default_tree_manipulators:checkAccess'),
// Use the default sorting of menu links.
        array('callable' => 'menu.default_tree_manipulators:generateIndexAndSort'),
        );

      $tree = $menu_tree->transform($tree, $manipulators);
      $menu = $menu_tree->build($tree);

      return $menu;
      break;

    case 'vocabulary':
      $container = \Drupal::getContainer();
      
      return $container->get('entity.manager')->getStorage('taxonomy_term')->loadTree($arr[0]);
      break;
    
    case 'block':
      $block = \Drupal\block_content\Entity\BlockContent::load($arr[0]);
      
      return \Drupal::entityManager()->getViewBuilder('block_content')->view($block);
      break;
  }
}
