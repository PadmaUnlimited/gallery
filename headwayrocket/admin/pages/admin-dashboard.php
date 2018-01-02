<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/
?>

<div class="wrap hwr-dashboard hwr-page">
	<h2 class="dashboard-title" data-container="hwr-content-wrap">Dashboard <a href="<?php echo add_query_arg( array( 'hwr-action' => 'refresh' ), $this->model->url ); ?>" class="add-new-h2" data-action="refresh-data">Refresh data</a></h2>
	<div class="toolbar">
		<ul>
			<li><span class="last-update">Last update: <?php echo $this->model->get_last_update(); ?></span></li>
		</ul>		
	</div>
	<ul class="uk-tab" data-uk-switcher="{connect:'#switcher'}">
		<li class="uk-active"><a href="#">Dashboard</a></li>
		<li><a href="#">Settings</a></li>		
	</ul>
	<div id="switcher" class="uk-switcher">
		<div><!-- dashboard -->
			<div class="uk-grid"><!-- grid -->
				<div class="uk-width-large-2-3 btr-panel">
					<h4 class="btr-panel-title">Your installed components</h4>
					<div class="btr-panel-content">
						<?php if ( !$installed_components = $this->model->get_installed_components() ) : ?>
							<p>You don't have any component installed!</p>
						<?php else : ?>
							<table class="btr-panel-table">
								<thead>
									<tr>
										<th>Title</th>
										<th>Installed Version</th>
										<th>Current Version</th>
										<th>Ressources</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $installed_components as $component ) : ?>
										<tr>
											<td><?php echo $component['name']; ?></td>
											<td>
												<span class="indicator-<?php echo $this->model->plugins[$component['filepath']]['Version'] < $component['version'] ? 'orange' : 'green'; ?>"></span><?php echo $this->model->plugins[$component['filepath']]['Version']; ?>
												<?php if ( $this->model->plugins[$component['filepath']]['Version'] < $component['version'] ) : ?>
													<a href="<?php echo admin_url(); ?>update-core.php" class="action-link">Update</a>
												<?php endif; ?>
											</td>
											<td><?php echo $component['version']; ?></td>
											<td class="link-list">
												<a href="<?php echo $component['doc_url']; ?>" target="_blank" title="<?php echo $component['name']; ?> guide">Guide</a>
												<a href="<?php echo $component['support_url']; ?>" target="_blank" title="<?php echo $component['name']; ?> support">Support</a>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php endif; ?>
					</div>
				</div>
				<div class="uk-width-large-1-3 btr-panel sign-up">
					<h4 class="btr-panel-title">Get email updates</h4>
					<div class="btr-panel-content">
						<p>Sign up to get notified when we release new Headway goodies and product updates.</p>
						<form action="http://headwayrocket.us5.list-manage.com/subscribe/post?u=5634a864e7429fd035bd71b21&amp;id=3a9b303fe3" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
							<input type="email" name="EMAIL" class="required input email" id="mce-EMAIL" value="Enter your email address" onblur="if(this.value=='') this.value='Enter your email address';" onfocus="if(this.value=='Enter your email address') this.value='';">
							<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">
						</form>
					</div>
				</div>
			</div><!-- close grid -->
			
			<div class="hwr-add-ons-title-wrap"><!-- filter section -->
				<h3 class="subtitle">Add-ons</h3>
				<ul class="subsubsub hwr-add-ons-filters">
					<li class="all"><a href="<?php echo $this->model->url; ?>" class="<?php if ( !butler_get( 'addons_filter' ) ) echo 'current';?>">All <span class="count">(<?php echo count( $this->model->components ); ?>)</span></a></li>
					<li class="inactive"><a href="<?php echo add_query_arg( array( 'addons_filter' => 'installed' ), $this->model->url ); ?>" class="hwr-separator-left<?php if ( butler_get( 'addons_filter' ) == 'installed' ) echo ' active';?>">Installed <span class="count">(<?php echo count( $this->model->get_installed_components() ); ?>)</span></a></li>
					<li class="active"><a href="<?php echo add_query_arg( array( 'addons_filter' => 'active' ), $this->model->url ); ?>" class="hwr-separator-left<?php if ( butler_get( 'addons_filter' ) == 'active' ) echo ' active';?>">Active <span class="count">(<?php echo count( $this->model->get_active_components() ); ?>)</span></a></li>
				</ul>
			</div><!-- close filter section -->
		
			<?php if ( $components = $this->model->get_filtered_components( butler_get( 'addons_filter' ) ) ) : ?> 
				<ul class="components uk-grid" data-uk-grid-match="{target:'.component'}">
					<?php foreach ( $components as $component ) : ?>
						<li class="uk-width-large-1-4">				
							<div class="component" data-component="<?php echo htmlspecialchars( json_encode( $component ) ); ?>" data-container>	
								<div class="component-content">
									<h4 class="component-title"><?php echo $component['name']; ?></h4>
									<?php echo $this->model->get_badge( $component['slug'] ); ?>
									<p><?php echo $component['description']; ?></p>
								</div>
								<div class="component-cta">
									<?php echo $this->model->get_action_button( $component['slug'] ); ?>
									<div class="link-list">
										<a href="<?php echo $component['product_url']; ?>" target="_blank" title="<?php echo $component['name']; ?> details">Details</a>
										<a href="<?php echo $component['demo_url']; ?>" target="_blank" title="<?php echo $component['name']; ?> demo">Demo</a>
									</div>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div><!-- close dashboard -->

		<div> <!-- settings -->
			<?php butler_options_meta_boxes( HEADWAYROCKET_PARENT_MENU ) ?>
		</div><!-- close settings -->
	</div>
</div>