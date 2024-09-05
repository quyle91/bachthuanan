<?php
function adminz_form_field($args){
    $a = new \Adminz\Helper\OptionField($args);
    return $a->init();
}

function adminz_copy($text){
    return <<<HTML
    <small class="adminz_click_to_copy" data-text="{$text}">{$text}</small>
    HTML;
}