<h1><?php _e('Settings Page', self::$text_domain); ?></h1>

<form method="post">

	<h3><?php _e('General Section', self::$text_domain); ?></h3>

	<table class="form-table">
		<tbody>

			<!-- Text -->

			<tr>
				<th>
					<label><?php _e('Text', self::$text_domain); ?></label>
					<td>
						<input id="<?php echo self::$prefix; ?>text" name="<?php echo self::$prefix; ?>text" type="text" size="50" value="<?php echo isset($settings['text']) ? esc_attr($settings['text']) : ''; ?>">
					</td>
				</th>
			</tr>

			<!-- TextArea -->

			<tr>
				<th>
					<label><?php _e('TextArea', self::$text_domain); ?></label>
					<td>
						<textarea rows="10" cols="50" id="<?php echo self::$prefix; ?>textarea" name="<?php echo self::$prefix; ?>textarea"><?php echo isset($settings['textarea']) ? esc_attr($settings['textarea']) : ''; ?></textarea>
					</td>
				</th>
			</tr>

			<!-- Checkbox -->

			<tr>
				<th>
					<label><?php _e('Checkbox', self::$text_domain); ?></label>
					<td>
						<input type="checkbox" id="<?php echo self::$prefix; ?>checkbox" name="<?php echo self::$prefix; ?>checkbox" <?php echo isset($settings['checkbox']) && $settings['checkbox'] ? 'checked="checked"' : ''; ?>/>
					</td>
				</th>
			</tr>

			<!-- Select -->

			<tr>
				<th>
					<label><?php _e('Select', self::$text_domain); ?></label>
					<td>
						<select id="<?php echo self::$prefix; ?>select" name="<?php echo self::$prefix; ?>select">
							<option value="small" <?php echo isset($settings['select']) && $settings['select'] == 'small' ? 'selected' : ''; ?>><?php _e('small', self::$text_domain); ?></option>
							<option value="medium" <?php echo isset($settings['select']) && $settings['select'] == 'medium' ? 'selected' : ''; ?>><?php _e('medium', self::$text_domain); ?></option>
							<option value="large" <?php echo isset($settings['select']) && $settings['select'] == 'large' ? 'selected' : ''; ?>><?php _e('large', self::$text_domain); ?></option>
						</select>
					</td>
				</th>
			</tr>

			<!-- Radio -->

			<tr>
				<th>
					<label><?php _e('Radio', self::$text_domain); ?></label>
					<td>
						<input type="radio" name="<?php echo self::$prefix; ?>radio" value="start" <?php echo isset($settings['radio']) && $settings['radio'] == 'start' ? 'checked="checked"' : ''; ?>/><label><?php _e('start', self::$text_domain); ?></label><br/>
						<input type="radio" name="<?php echo self::$prefix; ?>radio" value="middle" <?php echo isset($settings['radio']) && $settings['radio'] == 'middle' ? 'checked="checked"' : ''; ?>/><label><?php _e('middle', self::$text_domain); ?></label><br/>
						<input type="radio" name="<?php echo self::$prefix; ?>radio" value="end" <?php echo isset($settings['radio']) && $settings['radio'] == 'end' ? 'checked="checked"' : ''; ?>/><label><?php _e('end', self::$text_domain); ?></label><br/>
					</td>
				</th>
			</tr>

		</tbody>
	</table>

	<h3><?php _e('Other Section', self::$text_domain); ?></h3>

	<table>
		<tbody>

			<!-- URL -->

			<tr>
				<th class="general_admin_table_th">
					<label><?php _e('URL', self::$text_domain); ?></label>
					<td class="general_admin_table_td">
						<input id="<?php echo self::$prefix; ?>url" name="<?php echo self::$prefix; ?>url" type="url" size="50" value="<?php echo isset($settings['url']) ? esc_url($settings['url']) : ''; ?>">
					</td>
				</th>
			</tr>

		</tbody>
	</table>

<?php wp_nonce_field(self::$prefix . 'admin_settings'); ?>

<?php submit_button(); ?>

</form>