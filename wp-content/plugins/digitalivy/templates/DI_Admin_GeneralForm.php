<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br>
	</div>
	<h2>
		Triton Digital Ivy General Settings
	</h2>
	<form method="post" action="?page=diPluginSettings">
		<input type="hidden" name="action" value="update"> 
		<?php wp_nonce_field( 'update_triton_di', '_wpnonce' ) ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="org_short_code">Orgnazation Short Code</label>
				</th>
				<td>
					
					<input name="org_short_code" type="text" id="org_short_code" value="<?php echo get_option( TRITON_DI_OPTION_ORG_CODE ); ?>" class="regular-text">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="di_feed_url">DigitalIvy List Feed Url</label>
				</th>
				<td>
					<input name="di_feed_url" type="text" id="di_feed_url" value="<?php echo get_option( TRITON_DI_OPTION_DATA_FEED_URL ); ?>" class="regular-text code">
					<p class="description">
						Location of Triton DI Feed Endpoint
					</p>
				</td>
			</tr>
			
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
</div>