<?php
// set title
echo $view->header()->setAttribute('template', $T('NutUps_Title'));

// add simple panel
echo $view->panel()
    ->insert($view->selector('status'))
    ->insert($view->fieldset()->setAttribute('template',$T('Mode_label'))
        ->insert($view->fieldsetSwitch('Mode', 'master', $view::FIELDSETSWITCH_EXPANDABLE)
            ->insert($view->textInput('SearchModel'))
            ->insert($view->textInput('Model'))
            ->insert($view->selector('Device', $view::SELECTOR_DROPDOWN))
            ->insert($view->textLabel('Password')->setAttribute('template', $T('Slave_password_label')))
        )
        ->insert($view->fieldsetSwitch('Mode', 'slave', $view::FIELDSETSWITCH_EXPANDABLE)
            ->insert($view->textInput('Master'))
            ->insert($view->textInput('Password'))
        )
    )
;

// show submit and help buttons
echo $view->buttonList($view::BUTTON_SUBMIT | $view::BUTTON_HELP);
$models = '';
foreach ($view['models'] as $desc => $driver) {
    $desc = str_replace("","'",$desc);
    $driver = str_replace("","'",$driver);
    $models .= "{ label: \"$desc\", value: \"$driver\"},";
}
$desc_id = $view->getClientEventTarget('SearchModel');
$model_id = $view->getClientEventTarget('Model');
$view->includeJavascript("
(function ( $ ) {

    $(document).ready(function() {
        $('.$desc_id' ).autocomplete({
            source: [ $models ],
            select: function( event, ui ) {
                $('.$model_id').val(ui.item.value);
                $(this).val('');
                return false;
            }
        });
    });

} ( jQuery ));
");

