<div class="sui-box-settings-row">

	<div class="sui-box-settings-col-1">
		<span class="sui-settings-label"><?php esc_html_e( 'Social Services', 'wordpress-popup' ); ?></span>
		<span class="sui-description"><?php esc_html_e( 'Choose the social services which you want to display.', 'wordpress-popup' ); ?></span>
	</div>

	<div class="sui-box-settings-col-2">

		<div class="sui-box-builder">

			<?php
			// Builder Header ?>
			<div class="sui-box-builder-header">

				<button class="sui-button sui-button-purple hustle-choose-platforms">
					<i class="sui-icon-plus" aria-hidden="true"></i>
					<?php esc_html_e( 'Add Platform', 'wordpress-popup' ); ?>
				</button>

			</div>

			<?php
			// Builder Content ?>
			<div class="sui-box-builder-body">

				<div id="hustle-social-services" class="sui-builder-fields sui-accordion"></div>

				<button class="sui-button sui-button-dashed hustle-choose-platforms">
					<i class="sui-icon-plus" aria-hidden="true"></i>
					<?php esc_html_e( 'Add Platform', 'wordpress-popup' ); ?>
				</button>

			</div>

		</div>

		<span class="sui-description"><?php esc_html_e( 'You can re-arrange the order of social services by dragging and dropping.', 'wordpress-popup' ); ?></span>

	</div>

</div>
