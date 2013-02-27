<?php if ( ! empty( $template_args["errors"] ) && count( $template_args["errors"] ) > 0 ) { ?>
	<div class="error">
		Errors occurred while performing this action:
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
		<br />
	</div>
	<h2>
		Triton SEE Custom Actions
	</h2>

	<form method="post" action="?page=seePluginCustomActions">
		<?php $actions_list_table->display(); ?>
	</form>

	<p><h2>Add Custom Action</h2></o>

	<form method="post" action="?page=seePluginCustomActions">
		<fieldset>
			<input type="hidden" name="action" value="addAction"> 
			<?php wp_nonce_field( 'triton_add_custom_action', '_wpnonce' ); ?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						Trigger Event
					</th>
					<td>
						<div id="add_new_custom_action">
							<form>
								<fieldset>
									<legend class="screen-reader-text">Add Custom Action</legend>
									<label for="event_type_read"><input type="radio" name="event_type" id="event_type_read" value="read" /> When the user reads a post from the following category or categories:</label>
									<?php 
										wp_dropdown_categories( array( 
											"name"            => "event_type_category",
											"show_option_all" => "All Categories",
											"hide_empty"      => 0
										)); 
									?>
									<br />
									<label for="event_type_list"><input type="radio" name="event_type" id="event_type_list" value="list" /> When the following Wordpress action is triggered:</label>
										<select name="event_type_list_selection">
											<option value="wp_insert_comment">Comment</option>
											<option value="wp_login">Login</option>
											<option value="user_register">Register</option>
										</select>
										Or enter your action hook here if it is not listed:
										<input type="text" name="event_type_custom" value="" placeholder="your_custom_action" />
								</fieldset>
							</form>
						</div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						SEE Action
					</th>
					<td>
						<input type="text" name="event_see_action" id="event_see_action" />
					</td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Save Custom Action">
			</p>
		</fieldset>
	</form>	
</div>