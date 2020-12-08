<?php $this->Html->css('article', ['block' => true]); ?>
<?php foreach ($articles as $article) : ?>
<a href="/blog/view/<?php echo $article->id; ?>">
<h2><?= h($article->title) ?></h2>
</a>
<p><small>Por <a href="https://www.fondosafp.com" title="Fondos AFP">FondosAFP</a> - <?= $article->created->format('Y-m-d H:i:s') ?></small></p>

<div class="share-post">
  <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.fondosafp.com/blog/view/<?php echo $article->id; ?>" class="facebook" target="_blank">
    Facebook
  </a>
  <a href="https://twitter.com/intent/tweet?url=https://www.fondosafp.com/blog/view/<?php echo $article->id; ?>&text=<?php echo $article->title ?>%20por%20@FondosAFP" class="twitter" target="_blank">
    Twitter
  </a>
  <a href="https://plus.google.com/share?url=https://www.fondosafp.com/blog/view/<?php echo $article->id; ?>" class="googleplus" target="_blank">
    Google+
  </a>
</div>

<div class="row">
  <div class="col-md-8">
      <h2>Fondos AFP ahora en Github!</h2>
      <p>Asi es!, ahora el código de fondosafp.com está disponible en github.com</p>
      <p>Puedes verlo en https://github.com/NaNtrack/fondosafp/website</p>
      <p>Se aceptan pull requests!</p>
    <!--
    <?= $article->body; ?>
    -->
  </div>
  <div class="col-md-4">

  </div>
</div>
<?php endforeach; ?>
