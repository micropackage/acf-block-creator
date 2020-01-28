<?php if ( have_rows( '{name}' ) ): ?>
	<?php while ( have_rows( '{name}' ) ) : ?>
		<?php the_row(); ?>

{subfields}

	<?php endwhile; ?>
<?php endif; ?>
