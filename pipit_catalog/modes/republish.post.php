<?php
    echo $HTML->title_panel([
        'heading' => $Lang->get('Republishing Products'),
    ], $CurrentUser);

    include(__DIR__.'/list.smartbar.php');
    

    echo $Form->form_start();
    echo $HTML->info_block('Republish Products?', $Lang->get('Are you sure you wish to republish all Products?'));
    echo $HTML->submit_bar([
        'button' => $Form->submit('btnsubmit', 'Republish', 'button'),
        'cancel_link' => '/core/apps/content/'
        ]);
    echo $Form->form_end();