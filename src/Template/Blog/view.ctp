<?php $this->Html->css('article', ['block' => true]); ?>
<h1><?= h($article->title) ?></h1>
<p><small>Por <a href="https://www.fondosafp.com" title="Fondos AFP">FondosAFP</a> - <?= $article->created->format('Y-m-d H:i:s') ?></small></p>

<div class="share-post">
  <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.fondosafp.com<?php echo $this->request->here; ?>" class="facebook" target="_blank">
    Facebook
  </a>
  <a href="https://twitter.com/intent/tweet?url=https://www.fondosafp.com<?php echo $this->request->here; ?>&text=<?php echo $article->title ?>%20por%20@FondosAFP" class="twitter" target="_blank">
    Twitter
  </a>
  <a href="https://plus.google.com/share?url=https://www.fondosafp.com<?php echo $this->request->here; ?>" class="googleplus" target="_blank">
    Google+
  </a>
</div>

<div class="row">
  <div class="col-md-8">
    <?= $article->body; ?>
  </div>
  <div class="col-md-4">
      
  </div>
</div>

<?php echo $this->element('comments', ['page' => $this->request->here]); ?>