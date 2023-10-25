<?php

/*
 *	Wordpress Github Repos Class
 */

class WP_My_Github_Repos extends WP_Widget{

	// Create Widget
	function __construct(){
		parent::__construct(
			'my_github_repos', // Base ID
			__('My Github Repos', 'mgr_domain'),
			array('description' => __('A Github repository widget', 'mgr_domain'))
		);
	}

	// Frontend Display
	public function widget($args, $instance){
		// get values
		$title = apply_filters('widget_title', $instance['title']);
		$username = esc_attr($instance['username']);
		$count = esc_attr($instance['count']);

		echo $args['before_widget'];

		if(!empty($title)){
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo $this->showRepos($title, $username, $count);

		echo $args['after_widget'];
	}

	// Backend Form
	public function form($instance){
		// Get Title
		if(isset($instance['title'])){
			$title = $instance['title'];
		} else {
			$title = __('Latest Github Repos', 'mgr_domain');
		}

		// Get Username
		if(isset($instance['username'])){
			$username = $instance['username'];
		} else {
			$username = __('bradtraversy', 'mgr_domain');
		}

		// Get Count
		if(isset($instance['count'])){
			$count = $instance['count'];
		} else {
			$count = 5;
		}
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'mgr_domain'); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_html($title); ?>">
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username', 'mgr_domain'); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" value="<?php echo esc_html($username); ?>">
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Count', 'mgr_domain'); ?></label>
				<input type="text" class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" value="<?php echo esc_html($count); ?>">
			</p>
		<?php
	}

	// Update Widget Values
	public function update($new_instance, $old_instance){
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['username'] = (!empty($new_instance['username'])) ? strip_tags($new_instance['username']) : '';
		$instance['count'] = (!empty($new_instance['count'])) ? strip_tags($new_instance['count']) : '';

		return $instance;
	}

	// Show Repositories
	public function showRepos($title, $username, $count){
		// Access the Github API ...
		$url = 'https://api.github.com/users/'.$username.'/repos?sort=created&per_page='.$count;
		$options = array('http' => array('user_agent' => $_SERVER['HTTP_USER_AGENT']));
		$context = stream_context_create($options);
		$response = file_get_contents($url, false, $context);
		$repos = json_decode($response);

		// Build Output
		$output = '<ul class="repos">';

		foreach($repos as $repo){
			$output .= '<li>
							<div class="repo-title">'.$repo->name.'</div>
							<div class="repo-desc">'.$repo->description.'</div>
							<a target="_blank" href="'.$repo->html_url.'">View on Github</a>
						</li>';
		}

		$output .= '</ul>';

		return $output;
	}
}
