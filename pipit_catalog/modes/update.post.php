<div class="inner">
  <div style="update-box">
    <div class="hd">
        <h1>Pipit Catalog Update</h1>
    </div>

    <div class="bd">
        <ul class="progress-list">
        <?php
            echo '<li class="progress-item progress-success">'.PerchUI::icon('core/circle-check').' Updated to version '.PIPIT_CATALOG_VERSION.'</li>';
        ?>
      </ul>
    </div>
    <div class="submit">
      <a href="<?php echo $API->app_path(); ?>" class="button button-simple action-success">Continue</a>
    </div>
  </div>
</div>