<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $titre; ?></title>
		<meta charset="<?php echo $charset; ?>" />
        <meta name="description" content="<?php echo $desc; ?>" />
		<link rel="shortcut icon" href="<?php echo base_url(); ?>favicon.ico">
		<?php 
			// Inclusion des feuilles de style
			foreach($css as $url){
				echo '<link rel="stylesheet" href="'.$url.'" />';
			}
		?>
	</head>

	<body>
		<script>
			// Ajout des balises HTML5 pour IE
			document.createElement('header');
			document.createElement('nav');
			document.createElement('section');
		</script>
		<header id="titre_site">
			<?php
				echo '<h1>'.$this->lang->line('global_app_titre').'</h1>';
			?>
		</header>
		<nav id="fil_ariane">
			<ul class="breadcrumb">
				<?php
					if($rubrique == $this->lang->line('rubrique_accueil'))
						echo '<li class="active">'.$rubrique.'</li>';
					else{
						echo '<li>';
							echo '<a href="'.site_url('/').'">'.$this->lang->line('rubrique_accueil').'</a> <span class="divider">/</span>';
						echo '</li>';
						if(in_array($action, array('ajouter', 'modifier'))){
							echo '<li>';
								echo '<a href="'.site_url('/'.$menu_item).'">'.$rubrique.'</a> <span class="divider">/</span>';
							echo '</li>';
							echo '<li class="active">'.$str_action.'</li>';
						}
						else{
							echo '<li class="active">'.$rubrique.'</li>';
						}
					}
				?>
			</ul>
		</nav>
		<?php
		if(!empty($profil)){
			echo '<section id="infos" class="pull-right">';
				$user = $profil['prenom'] ? $profil['prenom'].' '.$profil['nom'] : $profil['nom'];
				$jours = $this->config->item('jours');
				$mois = $this->config->item('mois');
				echo '<span id="user">'.$user.'</span> - ';
				echo '<span id="date">'.$jours[date('N')].' '.(date('j') == '1' ? date('j').'er' : date('j')).' '.$mois[date('n')].' '.date('Y').' - '.date('H').'h '.date('i').'min</span>';
			echo '</section>';
			echo '<div class="clear"></div>';
		}
		?>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span2" id="sidebar">
					<section class="well">
						<div id="logo">
							<img src="https://blob-s-docs.googlegroups.com/docs/NwAAAORd1ldGeoB-Ps82C5fEFxviRTUuofPjGa6DAO-PW8efCXvxk37WtE3KOehwHte04CwNfQqYYfo_LM4kgiyJ_nYA15jOjCDxDa0uIv3AQm43OV_VD2Rm262Q" title="logo" alt="logo" />
						</div>
						<?php
							if(!empty($profil)){
								echo '<nav id="menu">';
									echo '<ul class="nav nav-pills nav-stacked">';
										if($statuts_dispos[$profil['statut']] == 'admin'){
											echo '<li class="nav-header">'.$this->lang->line('menu_admin').'</li>';
											if($menu_item == 'users')
												echo '<li class="active"><a href="'.site_url('/users').'"><i class="icon-white icon-user"></i> '.$this->lang->line('menu_section_users').'</a></li>';
											else
												echo '<li><a href="'.site_url('/users').'"><i class="icon-user"></i> '.$this->lang->line('menu_section_users').'</a></li>';
											if($menu_item == 'fonctions')
												echo '<li class="active"><a href="'.site_url('/fonctions').'"><i class="icon-white icon-th-list"></i> '.$this->lang->line('menu_section_fonctions').'</a></li>';
											else
												echo '<li><a href="'.site_url('/fonctions').'"><i class="icon-th-list"></i> '.$this->lang->line('menu_section_fonctions').'</a></li>';
										}
										else{
											echo '<li class="nav-header">'.$this->lang->line('menu_client').'</li>';
										}
										if($menu_item == 'annuaire')
											echo '<li class="active"><a href="'.site_url('/annuaire').'"><i class="icon-white icon-envelope"></i> '.$this->lang->line('menu_section_annuaire').'</a></li>';
										else
											echo '<li><a href="'.site_url('/annuaire').'"><i class="icon-envelope"></i> '.$this->lang->line('menu_section_annuaire').'</a></li>';
										echo '<li><a href="'.site_url('/deconnexion').'"><i class="icon-off"></i> '.$this->lang->line('menu_section_deconnexion').'</a></li>';
									echo '</ul>';
								echo '</nav>';
							}
						?>	
					</section>
				</div>
				<div class="span10" id="main">
					<?php
						if(!empty($str_action))
							echo '<h2 id="titre_action">'.$str_action.'</h2>';
						echo $output; 
					?>
				</div>
			</div>
		</div>
		<?php 
			// Inclusion des fichiers js
			foreach($js as $url){
				echo '<script src="'.$url.'"></script>';
			}
		?>
	</body>

</html>