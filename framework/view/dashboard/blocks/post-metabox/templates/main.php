<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<div
    id="your_namespace-<?php echo str_replace( '_', '-', $slug ); ?>__inner"
    class="your_namespace-metabox__inner"
>
    <?php echo $inner_content; ?>
</div>

<input type="hidden" name="your_namespace_<?php echo $slug; ?>[is_submitted]" value="1">