<?php if ( ! empty( $template_args["errors"] ) && count( $template_args["errors"] ) > 0 ) { ?>
	<div class="error">
		Errors occurred while saving settings:
		<ul>
			<?php 
				foreach( $template_args["errors"] as $error ) {
					echo "<li>{$error}</li>";
				}
			?>
		</ul>
	</div>
<?php } ?>

<?php if ( ! empty( $template_args["alerts"] ) && count( $template_args["alerts"] ) > 0 ) { ?>
	<div class="updated">
		<ul>
			<?php 
				foreach( $template_args["alerts"] as $alert ) {
					echo "<li>{$alert}</li>";
				}
			?>
		</ul>
	</div>
<?php } ?>

<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br>
	</div>
	<h2>
		Triton SEE General Settings
	</h2>
	<form method="post" action="?page=seePluginSettings">
		<input type="hidden" name="action" value="update"> 
		<?php wp_nonce_field( 'update_triton_see', '_wpnonce' ) ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="tenant_id">Tenant</label>
				</th>
				<td>
					<input name="tenant_id" type="text" id="tenant_id" value="<?php echo get_option( TRITON_SEE_OPTION_NAME_TENANT_ID ); ?>" class="regular-text">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="see_url">SEE Address (URL)</label>
				</th>
				<td>
					<input name="see_url" type="text" id="see_url" value="<?php echo get_option( TRITON_SEE_OPTION_NAME_URL ); ?>" class="regular-text code">
					<p class="description">
						Location of Triton Server
					</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="public_key">Public API Key</label>
				</th>
				<td>
					<input name="public_key" type="text" id="public_key" value="<?php echo get_option( TRITON_SEE_OPTION_NAME_PUBLIC_KEY ); ?>" class="regular-text">
				</td>
			</tr>
			<tr valign="top">
				<td></td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="private_key">Private API Key</label>
				</th>
				<td>
					<input name="private_key" type="text" id="private_key" value="<?php echo get_option( TRITON_SEE_OPTION_NAME_PRIVATE_KEY ); ?>" class="regular-text">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Use SSL
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>Membership</span></legend><label for="use_ssl"><input name="use_ssl" type="checkbox" id="use_ssl" value="1" <?php if (get_option( TRITON_SEE_OPTION_NAME_USE_SSL ) == '1') { echo ' checked="checked"'; } ?>> Pre-append https before SEE Address (URL)</label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Debug
				</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>Debug</span></legend><label for="use_sessions"><input name="debug" type="checkbox" id="debug" value="1" <?php if (get_option( TRITON_SEE_OPTION_NAME_DEBUG ) == '1') { echo ' checked="checked"'; } ?>> Enable debugging for the Triton SEE plugin</label>
					</fieldset>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					Custom Post/Page Views
				</th>
				<td>
					<table id="action_table2">
						<?php
							$post_page_views = get_option( TRITON_SEE_OPTION_NAME_POST_PAGE_VIEWS );
							$i = 1;
							foreach ($post_page_views as $action) {
								echo '<tr>';
								echo '<td>' . $i . '.</td>';
								echo '<td><input type="text" name="post_page_views[]" value="'. $action .'" /></td>';
								echo '</tr>';
								$i++;
							}
						?>
						<tr>
							<td>
								New:
							</td>
							<td>
								<input type="text" name="post_page_views[]" value="">
							</td>
						</tr>
					</table>
					<p class="description">
						Leave unwanted actions blank
					</p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
		</p>
	</form>
</div>