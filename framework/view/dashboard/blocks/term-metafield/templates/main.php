<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<tr class="form-field your_namespace-metafield__<?php echo $slug; ?>">
    <th scope="row" valign="top">
        <label for="<?php echo $slug; ?>">
            <?php echo $title; ?>
        </label>
    </th>

    <td>
        <?php echo $inner_content; ?>

        <input type="hidden" name="your_namespace_<?php echo $slug; ?>[is_submitted]" value="1">
    </td>
</tr>