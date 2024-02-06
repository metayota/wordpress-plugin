<?php namespace Metayota; include_once('../metayota/library.php');   include_once('../format-text/format-text.php');  ?><div class="editor-welcome">   
    <h1>Metayota</h1>
    <div>
        <?php if(getConfig('wordpress')): ?>
            <p><?= translate('wp_welcome_text') ?></p>

            <h2><?= translate('wp_welcome_features') ?></h2>
            <p><?= formatText(translate('wp_welcome_features_text')) ?></p>
            <h2><?= translate('wp_welcome_documentation_title') ?></h2>
            <p><?= formatText(translate('wp_welcome_documentation_text')) ?></p>
            
        <?php else: ?>


            <h2><?= translate('wp_welcome_features') ?></h2>
            <p><?= formatText(translate('wp_welcome_features_text')) ?></p>
            <h2><?= translate('wp_welcome_documentation_title') ?></h2>
            <p><?= formatText(translate('wp_welcome_documentation_text')) ?></p>




        <editor.server.login ></editor.server.login>
        <?php endif; ?>
    </div>
</div>