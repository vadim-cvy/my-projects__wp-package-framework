<div class="your_namespace-custom-field your_namespace-custom-field_<?php echo str_replace( '_', '-', $setting_name ); ?>">
    <?php
    require $field_input_template_path;

    if ( ! empty( $description ) )
    { ?>
        <p class="description">
            <?php echo $description; ?>
        </p>
    <?php
    } ?>
</div>
