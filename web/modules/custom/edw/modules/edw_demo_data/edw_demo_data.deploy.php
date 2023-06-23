<?php

/**
 * @file
 * Deploy hooks for demo data.
 */

// phpcs:ignoreFile

/**
 * Create demo data.
 *
 * @SuppressWarnings(PHPMD)
 */
function edw_demo_data_deploy_10001() {
  $homepage = \Drupal::entityTypeManager()->getStorage('node')->create([
    'type' => 'page',
    'title' => 'Homepage',
  ]);
  $homepage->save();

  $homepage->id();
  $about = \Drupal::entityTypeManager()->getStorage('node')->create([
    'type' => 'page',
    'title' => 'Who we are',
  ]);
  $about->save();

  $about->id();
  $knowledge = \Drupal::entityTypeManager()->getStorage('node')->create([
    'type' => 'page',
    'title' => 'What we do',
  ]);
  $knowledge->save();

  $knowledge->id();
  $contact = \Drupal::entityTypeManager()->getStorage('node')->create([
    'type' => 'page',
    'title' => 'Contact Us',
  ]);
  $contact->save();

  $contact->id();
  $news = \Drupal::entityTypeManager()->getStorage('node')->create([
    'type' => 'page',
    'title' => 'News',
  ]);
  $news->save();

  $news->id();
  $policy = \Drupal::entityTypeManager()->getStorage('node')->create([
    'type' => 'page',
    'title' => 'Privacy/Site Policy',
  ]);
  $policy->save();

  $policy->id();
  $child = \Drupal::entityTypeManager()->getStorage('node')->create([
    'type' => 'page',
    'title' => 'Our approach',
  ]);
  $child->save();

  $child->id();
  $aboutMenu = \Drupal::entityTypeManager()
    ->getStorage('menu_link_content')
    ->create([
      'title' => 'Who we are',
      'link' => [['uri' => 'internal:/node/' . $about->id()]],
      'menu_name' => 'main',
      'bundle' => 'menu_link_content',
    ]);
  $aboutMenu->save();

  $knowledgeMenu = \Drupal::entityTypeManager()
    ->getStorage('menu_link_content')
    ->create([
      'title' => 'What we do',
      'link' => [['uri' => 'internal:/node/' . $knowledge->id()]],
      'menu_name' => 'main',
      'bundle' => 'menu_link_content',
    ]);
  $knowledgeMenu->save();

  $newsMenu = \Drupal::entityTypeManager()
    ->getStorage('menu_link_content')
    ->create([
      'title' => 'News',
      'link' => [['uri' => 'internal:/node/' . $knowledge->id()]],
      'menu_name' => 'main',
      'bundle' => 'menu_link_content',
    ]);

  $newsMenu->save();
  $childMenu = \Drupal::entityTypeManager()
    ->getStorage('menu_link_content')
    ->create([
      'title' => 'Our approach',
      'link' => [['uri' => 'internal:/node/' . $child->id()]],
      'menu_name' => 'main',
      'bundle' => 'menu_link_content',
      'parent' => 'menu_link_content:' . $knowledge->uuid(),
    ]);
  $childMenu->save();

  $contactMenu = \Drupal::entityTypeManager()
    ->getStorage('menu_link_content')
    ->create([
      'title' => 'Contact Us',
      'link' => [['uri' => 'internal:/node/' . $contact->id()]],
      'menu_name' => 'footer',
    ]);

  $contactMenu->save();
  $policyMenu = \Drupal::entityTypeManager()
    ->getStorage('menu_link_content')
    ->create([
      'title' => 'Privacy/Site Policy',
      'link' => [['uri' => 'internal:/node/' . $policy->id()]],
      'menu_name' => 'footer',
    ]);
  $policyMenu->save();
}
