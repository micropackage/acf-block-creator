( function( $ ) {

	var $block_option_fields = $( '#acf-field-group-options .acf-fields' ).find( '[data-name="block_name"], [data-name="block_slug"], [data-name="block_category"], [data-name="block_align"], [data-name="block_container_class"], [data-name="inner_blocks"]' );

	$block_option_fields.hide();

	$( '#title' ).keyup( function() {
		var title = $( this ).val();
		if ( 0 === title.indexOf( 'Block:' ) ) {
			$( '#acf_field_group-block_name' ).val( title.replace( 'Block: ', '' ) ).trigger( 'change' );
			if ( ! $( '#acf_field_group-create_gutenberg_block' ).prop( 'checked' ) ) {
				$( '#acf_field_group-create_gutenberg_block' ).trigger( 'click' );
			}
		}
	} );

	$( '#acf_field_group-create_gutenberg_block' ).change( function() {
		if ( this.checked ) {
			$block_option_fields.show();
		} else {
			$block_option_fields.hide();
		}
	} );

	$( '#acf_field_group-block_name ' ).change( function() {
		var block_slug = $( this ).val().toLowerCase()
			.replace( /\s+/g, '-' )
			.replace( /[^\w\-]+/g, '' )
			.replace( /\-\-+/g, '-' )
			.replace( /^-+/, '' )
			.replace( /-+$/, '' );
		$( '#acf_field_group-block_slug' ).val( block_slug );
	} );

} )( jQuery );
