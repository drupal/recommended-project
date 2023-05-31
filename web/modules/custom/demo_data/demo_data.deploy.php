<?php

use Drupal\menu_link_content\Entity\MenuLinkContent;

function demo_data_deploy_10001() {
  $nid = 2;
  $parent = MenuLinkContent::create([
    'title' => 'About',
    'link' => [['uri' => 'internal:/node/' . $nid]],
    'menu_name' => 'main',
  ]);
  $parent->save();
  $child1 = MenuLinkContent::create([
    'title' => 'About the Programme',
    'link' => [['uri' => 'internal:/node/2']],
    'menu_name' => 'main',
    'parent' => 'menu_link_content:' . $parent->uuid(),
  ]);
  $child1->save();
}
