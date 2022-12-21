<?php

namespace YOUR_NAMESPACE\framework\inc\user_input;

use \Exception;

if ( ! defined( 'ABSPATH' ) ) exit;

class File_Upload_Validator
{
    protected $input_name;

    protected $allowed_extensions;

    protected $saved_file_data = null;

    public function __construct( string $input_name, array $allowed_extensions = [] )
    {
        $this->input_name         = $input_name;
        $this->allowed_extensions = $allowed_extensions;
    }

    public function validate() : void
    {
        (new User_Input__Array())->validate_not_empty( $this->get_upload_data() );

        $this->validate_extension();
    }

    public function validate_and_save( string $file_custom_name = '' ) : array
    {
        if ( ! isset( $this->saved_file_data ) )
        {
            $this->validate();

            $this->save( $file_custom_name );
        }

        return $this->saved_file_data;
    }

    protected function save( string $file_custom_name = '' ) : void
    {
        $upload_data = $this->get_upload_data();

        if ( ! empty( $file_custom_name ) )
        {
            $upload_data['name'] =
                preg_replace( '~^[^.]+~', $file_custom_name, $upload_data['name'] );
        }

        $saved_file_data = wp_handle_upload( $upload_data, [
            'upload_error_handler' => [ $this, '_on_save_error' ],
            'test_form'            => false,
        ]);

        $this->saved_file_data = $saved_file_data;
    }

    public function _on_save_error( $file, $error_message ) : void
    {
        $error_message = 'An error occured during the file saving: ' . $error_message;

        $this->throw_error( $error_message );
    }

    protected function get_upload_data() : array
    {
        return ! empty( $_FILES[ $this->get_input_name() ] ) ?
            $_FILES[ $this->get_input_name() ] :
            [];
    }

    protected function validate_extension() : void
    {
        if ( ! empty( $this->allowed_extensions ) )
        {
            $submitted_extension =
                strtolower( pathinfo( $this->get_upload_data()['name'], PATHINFO_EXTENSION ) );

            (new User_Input__Text())->validate_is_value_in( '',
                $submitted_extension,
                $this->allowed_extensions,
                'file extension'
            );
        }
    }
}
