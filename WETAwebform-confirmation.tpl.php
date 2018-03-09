<?php

/**
 * @file
 * Customize confirmation screen after successful submission.
 *
 * This file may be renamed "webform-confirmation-[nid].tpl.php" to target a
 * specific webform e-mail on your site. Or you can leave it
 * "webform-confirmation.tpl.php" to affect all webform confirmations on your
 * site.
 *
 * Available variables:
 * - $node: The node object for this webform.
 * - $confirmation_message: The confirmation message input by the webform author.
 * - $sid: The unique submission ID of this submission.
 */
?>

<?php


if ($node->type == 'quiz_trivia') {
  // Get the proper view mode display.
  $node_display = node_view($node, 'trivia_quiz_results');
  if (isset($sid)) {

    $mapping = _webform_weta_webform_component_mapping($node);
    $submission = webform_get_submission($node->nid, $sid);
    $result = $submission->data[$mapping['result']][0];


    // Get the possible minimum scores and store them in an array.
    $min_scores = array();
    foreach ($node->field_trivia_buckets['und'] as $i => $bucket) {
      $bucket_info = field_collection_item_load($bucket['value']);
      $min_scores[] = $bucket_info->field_quiz_min_score['und'][0]['value'];
    }

    // Sort them in ascending order, just in case something went wacky on input.
    sort($min_scores);

    // Figure out which bucket the result belongs to.
    foreach ($min_scores as $min_score) {
      if ($result >= $min_score) {
        $score_arg = $min_score;
      }
    }

    // How many questions?
    $quiz_items = _webform_weta_get_quiz_component($node);
    $total_quiz_items = count($quiz_items);

    // Construct URL.
    $alias = drupal_lookup_path('alias', 'node/' . $node->nid);
    $url = $GLOBALS['base_url'] . '/' . $alias;
    $short_url = custom_get_bitly_short_url($url);

    $tweet_title = $node->title;
    if (isset($node->field_quiz_tweet['und'][0]['safe_value'])) {
      $tweet_title = $node->field_quiz_tweet['und'][0]['safe_value'];
    }
    // Construct Tweet.
    $tweet = 'I got ' . $result . ' out of ' . $total_quiz_items . ' correct on the ' . $tweet_title . '! Find out your score: ';

    // Construct widget.
    $twitter_widget = '<div class="span_2 col"><a href="http://twitter.com/share?url=' . $short_url . '" class="twitter-share-button" data-count="vertical" data-text="' . $tweet . '" data-url="' . $short_url . '" data-counturl="' . $url . '" data-via="wetatvfm">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script></div>';


    // Facebook share button.
    $fb = '<div class="fb-share-button span_10 col" data-href="' . $url . '" data-layout="box_count"></div>';

    $buttons = $twitter_widget . $fb;
    $share_title = '<h3>Share your results</h3>';
    $share = $share_title . $buttons;


    // Get the view.
    $result_view = views_get_view('trivia_quiz_result');
    // Set the arguments.
    $result_view_args = array($node->nid, $score_arg);
    // Choose the display.
    $display_id = 'panel_pane_1 ';
    // Compose the view.
    $result_display = $result_view->execute_display($display_id, $result_view_args);

    $quiz_items = _webform_weta_get_quiz_component($node);
    $components = array();
    foreach ($quiz_items as $quiz_item) {
      $components[$quiz_item] = $node->webform['components'][$quiz_item];
    }

    $results = webform_weta_trivia_results_display($node, $components, $submission);
    $results = '<div class="span_12 col">' . $results . '</div>';

    print drupal_render($node_display);
    print $result_display;
    print $share;
    print $results;
  }
  else {
    $link = l(t('Try again!'), 'node/' . $node->nid);
    $error = '<div class="span_12 col"><p>Uh oh. We weren\'t able to score your quiz. ' . $link . '</p></div>';
    print $error;
  }
}

else {
?>

<div class="webform-confirmation">
  <?php if ($confirmation_message): ?>
    <?php print $confirmation_message ?>
  <?php else: ?>
    <p><?php print t('Thank you, your submission has been received.'); ?></p>
  <?php endif; ?>
</div>

<?php
}
?>
