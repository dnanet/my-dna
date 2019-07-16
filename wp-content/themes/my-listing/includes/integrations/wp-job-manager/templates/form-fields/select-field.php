<?php
/**
 * Shows the `select` form field on add listing forms.
 *
 * @since 1.0
 * @var   array $field
 */

$attrs = [];
$attrs[] = sprintf( 'name="%s"', esc_attr( isset( $field['name'] ) ? $field['name'] : $key ) );
$attrs[] = sprintf( 'id="%s"', esc_attr( $key ) );
$attrs[] = ! empty( $field['required'] ) ? 'required' : '';
$attrs[] = ! empty( $field['placeholder'] ) ? sprintf( 'data-placeholder="%s"', esc_attr( $field['placeholder'] ) ) : '';
?>

<select <?php echo join( ' ', $attrs ) ?>>
	<?php foreach ( $field['options'] as $key => $value ):
		$selected = '';
		if ( isset( $field['value'] ) || isset( $field['default'] ) ) {
			$selected = selected( isset( $field['value'] ) ? $field['value'] : $field['default'], $key, false );
		}
		?>
		<option value="<?php echo esc_attr( $key ); ?>" <?php echo $selected ?> >
			<?php echo esc_html( $value ); ?>
		</option>
	<?php endforeach ?>
</select>
