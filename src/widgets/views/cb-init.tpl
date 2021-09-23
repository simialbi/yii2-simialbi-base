{use class="rmrevin\yii\fontawesome\FAS"}
{use class="rmrevin\yii\fontawesome\FAL"}
{use class="rmrevin\yii\fontawesome\FAR"}

<div id="cb-{$id}">
    {$input}
</div>

{registerJs}
    // init builder
    var conditionBuilder = $('#cb-{$id}').queryBuilder({$options});

    // on form submit: put json in hidden input and submit
    $('#cb-{$id}').closest('form').on('submit', function() {
        if (conditionBuilder.queryBuilder('validate')) {
            let rules = conditionBuilder.queryBuilder("getRules");
            $('input[name="{$inputName}"]').val( JSON.stringify(rules) );
            return true;
        }
        else {
            return false;
        }
    });
{/registerJs}
