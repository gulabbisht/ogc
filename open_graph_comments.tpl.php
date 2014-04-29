<?php

/**
 * @file
 * Theme implementation for open graph comment.
 */
?>
<div class="open-graph-comment-outer">
  <div class="open-graph-comment-img">
    <img src='<?php print $img; ?>'>
  </div>
    <h3 class="open-graph-comment-title"><a href="<?php print $url; ?>" target="_blank"><?php print $title; ?></a></h3>
    <p class="open-graph-comment-desc"><?php print $desc ?></p>
  <div>
  </div>
</div>
