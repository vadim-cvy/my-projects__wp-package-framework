<?php

namespace YOUR_NAMESPACE\framework\inc\user_input;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class aUser_Input
{
    protected $value;

    protected $context;

    protected $value_label;

    public function __construct( $value, string $context = '', string $value_label = '' )
    {
        $this->value = $value;
        $this->context = $context;

        $this->set_value_label( $value_label );
    }

    protected function set_value_label( string $value_label ) : void
    {
        if ( empty( $value_label ) )
        {
            $value_label = ! empty( $this->context ) ? $this->context : 'value';
        }

        $this->value_label = $value_label;
    }

    abstract public function is_empty() : bool;

    abstract public function get_normalized();

    public function validate_not_empty() : aUser_Input
    {
        if ( $this->is_empty( $this->value ) )
        {
            $this->throw_error( 'Value must not be empty.' );
        }

        return $this;
    }

    public function throw_unexpected_value_error( array $expected_values, string $message = '' ) : void
    {
        $value_str =
            is_array( $this->value ) ?
            implode( '", "', $this->value ) :
            $this->value;

        $this->throw_value_error(
            'Unexpected ' . $this->value_label . '. ' .
            $message .
            'Expected values are: "' . implode( '", "', $expected_values ) . '".'
        );
    }

    public function throw_value_error( string $message ) : void
    {
        $passed_value_str =
            is_array( $this->value ) ?
            implode( '", "', $this->value ) :
            $this->value;

        $message .= ' Passed value: "' . $passed_value_str . '".';

        $this->throw_error( $message );
    }

    public function throw_error( string $message )
    {
        throw new User_Input_Validation_Error( $this->context . ': ' . $message );
    }
}