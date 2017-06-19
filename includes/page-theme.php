<?php
/**
 * @package WPGlobus Translate Options.
 * @since 1.5.0
 */
$theme 			= wp_get_theme();
$theme_caption 	= 'Active theme'; 
$params = array( 
			'Name', 
			'ThemeURI', 
			'Description', 
			'Author', 
			'AuthorURI', 
			'Version', 
			'Template', 
			#'Status', 
			#'Tags', 
			'TextDomain', 
			'DomainPath' 
		);
		
if ( ! empty( $theme->get('Template') ) ) {
	$parent_theme = wp_get_theme( get_template() );
	$theme_caption .= ' (child theme)'; 
	$parent_theme_caption = 'Parent theme'; 
}	
?>
<table class="active_theme" cellspacing="10">
	<caption><h2><?php echo $theme_caption; ?></h2></caption>
	<thead>
		<tr>
			<th>Parameter</th>
			<th>Value</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><b>Theme option</b></td>
			<td><?php echo 'theme_mods_' . get_stylesheet(); ?></td>
		</tr>	<?php
		foreach( $params as $param ) :	?>
			<tr>
				<td><b><?php echo $param; ?></b></td>
				<td><?php echo $theme->get($param);  ?></td>
			</tr>	<?php
		endforeach;	?>	
	</tbody>
</table>
<?php 
if ( ! empty( $parent_theme ) ) {
	/**
	 * Show parent theme.
	 */
	?>
	<table class="parent_theme" cellspacing="10">
		<caption><h2><?php echo $parent_theme_caption; ?></h2></caption>
		<thead>
			<tr>
				<th>Parameter</th>
				<th>Value</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><b>Theme option</b></td>
				<td><?php echo 'theme_mods_' . get_template(); ?></td>
			</tr>	<?php		
			foreach( $params as $param ) :	?>
				<tr>
					<td><b><?php echo $param; ?></b></td>
					<td><?php echo $parent_theme->get($param);  ?></td>
				</tr>	<?php
			endforeach;	?>	
		</tbody>
	</table>
	<?php 
}
