<?php

/**
 * @file
 * Theme implementation for open graph comment.
 */
?>
<div class="open-graph-comment-outer">
  <div class="open-graph-comment-img">
    <img src='<?php print $og_data['img']; ?>'>
  </div>
    <h3 class="open-graph-comment-title"><a href="<?php print $og_data['url']; ?>" target="_blank"><?php print $og_data['title']; ?></a></h3>
    <p class="open-graph-comment-desc"><?php print $og_data['desc']; ?></p>
  <div>
  </div>
</div>
