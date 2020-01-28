<?php ${name} = get_field( '{name}' ); ?>
<?php if ( ${name} ) : ?>
	<a class="" href="<?php echo esc_url_raw( ${name}['url'] ); ?>" target="<?php echo esc_attr( ${name}['target'] ? ${name}['target'] : '_self' ); ?>"><?php echo esc_html( ${name}['title'] ); ?></a>
<?php endif; ?>
