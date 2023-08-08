<div
	class="jet-reviews-settings-page jet-reviews-settings-page__integration"
>
	<cx-vui-switcher
		name="captcha-enable"
		label="<?php _e( 'Enable reCAPTCHA v3', 'jet-reviews' ); ?>"
		description="<?php _e( 'Use reCAPTCHA v3 form verification', 'jet-reviews' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:return-true="true"
		:return-false="false"
		v-model="pageOptions['captcha']['enable']"
	>
	</cx-vui-switcher>

	<cx-vui-component-wrapper
		:wrapper-css="[ 'fullwidth-control' ]"
		:conditions="[
			{
				input: pageOptions['captcha']['enable'],
				compare: 'equal',
				value: true,
			}
		]"
	>
		<cx-vui-input
			name="captcha-site-key"
			label="<?php _e( 'Site Key:', 'jet-reviews' ); ?>"
			description="<?php _e( 'Register reCAPTCHA v3 keys here.', 'jet-reviews' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="pageOptions['captcha']['site_key']"
		>
		</cx-vui-input>

		<cx-vui-input
			name="captcha-secret-key"
			label="<?php _e( 'Secret Key:', 'jet-reviews' ); ?>"
			description="<?php _e( 'Register reCAPTCHA v3 keys here.', 'jet-reviews' ); ?>"
			:wrapper-css="[ 'equalwidth' ]"
			size="fullwidth"
			v-model="pageOptions['captcha']['secret_key']"
		>
		</cx-vui-input>

	</cx-vui-component-wrapper>
</div>
