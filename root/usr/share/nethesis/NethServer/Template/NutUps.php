<?php
// set title
echo $view->header()->setAttribute('template', $T('NutUps_Title'));

// add simple panel
echo $view->panel()
    ->insert($view->selector('status'))
    ->insert($view->fieldset()->setAttribute('template',$T('Mode_label'))
        ->insert($view->fieldsetSwitch('Mode', 'master', $view::FIELDSETSWITCH_EXPANDABLE)
            ->insert($view->selector('Model', $view::SELECTOR_DROPDOWN))
            ->insert($view->textInput('Device'))
        )
        ->insert($view->fieldsetSwitch('Mode', 'slave', $view::FIELDSETSWITCH_EXPANDABLE)
            ->insert($view->textInput('Master'))
        )
    )
       
    ->insert($view->textInput('Password'))
;

// show submit and help buttons
echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
