<div
    class="wrap your_namespace-settings-page__section"
    id="<?php echo str_replace( '_', '-', $slug ); ?>-page"
>
    <h1>
        <?php echo esc_html( get_admin_page_title() ); ?>
    </h1>

    <div>
        <?php settings_errors( $slug ); ?>
    </div>

    <form action="options.php" method="post">
        <?php
        settings_fields( $slug );

        do_settings_sections( $slug );

        submit_button( 'Save','primary', 'submit', true, [
            ':disabled' => 'hasErrors'
        ]); ?>
    </form>
</div>
