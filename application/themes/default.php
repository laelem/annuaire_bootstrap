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
		<div id="page">
			<div id="col_gauche">
				<div id="logo">
					<img src="https://blob-s-docs.googlegroups.com/docs/NwAAAORd1ldGeoB-Ps82C5fEFxviRTUuofPjGa6DAO-PW8efCXvxk37WtE3KOehwHte04CwNfQqYYfo_LM4kgiyJ_nYA15jOjCDxDa0uIv3AQm43OV_VD2Rm262Q" title="logo" alt="logo" />
				</div>
				<?php
					if(!empty($profil)){
						echo '<nav>';
							if($statuts_dispos[$profil['statut']] == 'admin'){
								echo '<h2>'.$this->lang->line('menu_admin').'</h2>';
								echo '<p><a href="'.site_url('/users').'">'.$this->lang->line('menu_section_users').'</a></p>';
								echo '<p><a href="'.site_url('/fonctions').'">'.$this->lang->line('menu_section_fonctions').'</a></p>';
							}
							else{
								echo '<h2>'.$this->lang->line('menu_client').'</h2>';
							}
							echo '<p><a href="'.site_url('/annuaire').'">'.$this->lang->line('menu_section_annuaire').'</a></p>';
							echo '<p><a href="'.site_url('/deconnexion').'">'.$this->lang->line('menu_section_deconnexion').'</a></p>';
						echo '</nav>';
					}
				?>
			</div>
			<div id="main">
				<?php
					if(!empty($profil)){
						echo '<header><table><tr>';
							echo '<td><h2>'.$rubrique.'</h2></td>';
							$user = $profil['prenom'] ? $profil['prenom'].' '.$profil['nom'] : $profil['nom'];
							echo '<td class="user">'.$user.'</td>';
							$jours = $this->config->item('jours');
							$mois = $this->config->item('mois');
							echo '<td class="date_heure">'.$jours[date('N')].' '.(date('j') == '1' ? date('j').'er' : date('j')).' '.$mois[date('n')].' '.date('Y').' - '.date('H').'h '.date('i').'min</td>';
						echo '</tr></table></header>';
					}
					echo $output; 
				?>
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