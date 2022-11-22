- [PHP-PRETIME](#php-pretime)
- [Fonctionnalités](#fonctionnalités)
- [Explications](#explications)
- [Téléchargement](#téléchargement)
	- [Git](#git)
	- [Zip](#zip)
- [Configuration](#configuration)
	- [Renommer le fichier de config](#renommer-le-fichier-de-config)
	- [Éditez la configuration](#éditez-la-configuration)
		- [RELEASE\_MISSING\_IS\_NOW](#release_missing_is_now)
		- [RELEASE\_MISSING\_INSERT](#release_missing_insert)
		- [RELEASE\_MISSING\_SECTION](#release_missing_section)
		- [$cfg\['MySQL'\]](#cfgmysql)
			- [$cfg\['MySQL'\]\[$m\]\['select'\]](#cfgmysqlmselect)
			- [$cfg\['MySQL'\]\[$m\]\['insert'\]](#cfgmysqlminsert)
		- [$cfg\['Eggdrop'\]](#cfgeggdrop)
			- [$cfg\['Eggdrop'\]\[$e\]\['message'\]](#cfgeggdropemessage)
- [LEGG.tcl](#leggtcl)
- [Bogues, idées, remarques](#bogues-idées-remarques)
- [Contribution](#contribution)
- [Vous aimez ce script](#vous-aimez-ce-script)

# PHP-PRETIME

PHP PRETIME est un script PHP permetant avec un nom de release de fournir le pretime de celle-ci. 
Il est compatible MySQL/MariaDB et est multi-serveur.
Utile pour les autostrades comme slftp, ...

# Fonctionnalités

* FIND PRETIME      : Retourne le temps (unixtime/timestamp) de la release depuis le nom de la release.
* FIND WITH MULTI-SERVER (SELECT) : Recherche en cascade sur plusieurs serveurs MySQL, si nécessaire.
* ADD IF MISSING (INSERT)   : Ajout facultatif de la release dans la/les base(s) des données si release manquante.
* SEND TO EGGDROP (IF MISSING) : Envois facultatifs à un ou des Eggdrops en cas de release manquante

# Explications
Certains autotrades ont besoin du pretime de la release pour fonctionner correctement,
ce code lit une base de données MySQL/MariaDB (non fournis) pour retourner la release et sa pretime sous forme de `rlsname rlstime - -`

**Le plus de ce code** est que si la première base de données ne contient pas les informations nécessaires, il peut consultés d'autres bases de données jusqu'à les trouver. 

Si toute fois, il ne repère sur aucun des serveurs, une configuration permet de fournir le temps actuel comme s’il avait trouvé, pour que votre autotrade trade tout de même.

Il peut également afficher sur un salon IRC la release manquante. 
Vous pouvez faire un !newdir, !getold, simplement un message d'avertissement, etc. C'est également configurable et plusieurs robots Eggdrop peut-être paramétrés.

# Téléchargement
## Git

```git clone https://github.com/ZarTek-Creole/PHP-PRETIME.git /var/www/pretime```

Pour télécharger dans /var/www/pretime directement prêt-à-être configurer
## Zip
```
wget https://github.com/ZarTek-Creole/PHP-PRETIME/archive/refs/heads/master.zip
unzip master.zip
```
# Configuration

## Renommer le fichier de config
Vous devez renommer le fichier nommer `config.example.php` en `config.php`

Vous pouvez taper la commande dans votre terminal 

```mv config.example.php config.php```
## Éditez la configuration
Dans le terminal ouvrer le fichier `config.php` avec votre éditeur préférer 

```nano config.php```

---

### RELEASE_MISSING_IS_NOW

Si la valeur de `$RELEASE_MISSING_IS_NOW` vaut `1` et que la release est introuvable dans les bases de données, il va faire croire qu’elle vient d'être PRE en communiquant l'unixtime actuelle.

Si par contre la valeur de `$RELEASE_MISSING_IS_NOW` vaut `0` et que release est introuvable dans les bases de données, il ne va rien afficher du tout.

---

### RELEASE_MISSING_INSERT

Si la valeur de `$RELEASE_MISSING_INSERT` vaut `1` et que la release est introuvable dans les bases de données,
il va l'ajoute aux bases de données qui ont `$cfg['MySQL'][$m]['insert']` renseigné dans votre fichier config.php. Voir plus bas pour plus d'information.

Si la valeur de `$RELEASE_MISSING_INSERT` vaut `0` et que la release est introuvable dans les bases de données, il ne l'ajoutera nulle part.

---

### RELEASE_MISSING_SECTION

Si `$RELEASE_MISSING_INSERT` vaut 1, et que `$RELEASE_MISSING_SECTION` contient le nom de la section avec laquelle la release sera rajouter en SQL.

---
### $cfg['MySQL']
Les variables `$cfg['MySQL']`, permetent de configurer les bases de données. Cela fonctionne par bloc (ensemble) de variables `$cfg['MySQL']` et par serveur vous devez avoir un bloc qui ressemble comme ceci: 

```
    $m++;
	$cfg['MySQL'][$m]['host']		= 'localhost';
	$cfg['MySQL'][$m]['user']		= 'root';
	$cfg['MySQL'][$m]['password']   = '';
	$cfg['MySQL'][$m]['db'] 		= 'predb';
	$cfg['MySQL'][$m]['port'] 	    = '3306';
	$cfg['MySQL'][$m]['select'] 	= 'SELECT `rlsname`, `time` FROM `scene` WHERE `rlsname` = ? LIMIT 1;';
	$cfg['MySQL'][$m]['insert'] 	= "INSERT IGNORE INTO `scene` (`time`, `rlsname`, `section`, `grp`) VALUES (?, ?, ?, ?)";
```

#### $cfg['MySQL'][$m]['select']

Cette variable doit contenir la requête SELECT de SQL pour recuprer le pretime. Observer le point d'interrogation ? Celle si représente la position de la release dans la requête.

#### $cfg['MySQL'][$m]['insert']

Seul la variable `$cfg['MySQL'][$m]['insert']` est facultatif. si `$RELEASE_MISSING_INSERT` vaut `1` et que cette variable est renseigné, il va ajouter la release grâce à la requête SQL dans la ou les en base de données.
Remarquer les **quatres** points d'interrogation ?

Le premier, corresponds la colonne de temps.
Le deuxième, représente la colonne de la release.
Le troisième représente la colonne du nom de la section.
Le quatrième équivaut la colonne du nom de groupe de la release.µ

Notez bien : l'ordre est important et  les *quatres* points d'interrogation doivent être fournis pour que cela fonctionne.

### $cfg['Eggdrop']

Les variables `$cfg['Eggdrop']` fonctionnent comme `$cfg['MySQL']` en ensemble, chaque bloc de variables représente la connexion à un Eggdrop

```
    $e = 0;
    // Congiguration du/des serveur(s) Eggdrops

    /** Eggdrop LEGG 1 */ 
    $e++;
    $cfg['Eggdrop'][$e]['host']     = '127.0.0.1';
    $cfg['Eggdrop'][$e]['port']     = '6666';
    $cfg['Eggdrop'][$e]['password'] = 'MyPasswordLEgg';
    $cfg['Eggdrop'][$e]['message']  = '!addpre %rlsname% AUTOADD';
    $cfg['Eggdrop'][$e]['sleep']    = 1;
```

#### $cfg['Eggdrop'][$e]['message']

Cette variable permet de configurer le message qui sera affiché sur IRC et sera remplacé par l’appellation de la release. Ce que vous mettez à côté de `%rlsname%` vous appartient. Quelques exemples cependant:

* '!getold %rlsname%'
* '!newdir %rlsname% -'
* '!addpre %rlsname% AUTOADD'
* 'Release %rlsname% introuvable'

L'explication de l'installation d’Eggdrop n'est pas détaillée ici. Par contre un script TCL nommer `LEGG.tcl` doit être chargé dans vos Eggdrops et édité.

# LEGG.tcl
Ce fichier se trouve avec les fichiers PHP. Il doit être chargé dans vos Eggdrops.
```
source scripts/LEGG.tcl
```
Éditez-le et modifier le bloc 
```
	array set CONF {
		"port"				"6666"
		"channel"			"#ZarTek-Creole"
		"password"			"MyPasswordLEgg"
	}
```

---

Choisissez un port libre sans oublier pas de fournir le même dans la variable `$cfg['Eggdrop'][$e]['port']`. Le même proceder pour le password avec `$cfg['Eggdrop'][$e]['password']` dans le fichier `config.php` biensur.

---

Le `channel` peut-être un salon IRC ou un pseudonyme, le votre par exemple, pour recevoir les messages en privée sur le serveur de votre Eggdrop.

# Bogues, idées, remarques

Pour faire évoluer le projet, vous pouvez créer [un issue ici](https://github.com/ZarTek-Creole/PHP-PRETIME/issues/new )

# Contribution

Toutes contributions sont les bienvenues. N'hésitez pas à fork le projet et proposer vos PR.

# Vous aimez ce script 
Vous pouvez me payer un café pour que j'ai encore de bonnes idées, et que j’ai de l'énergie pour améliorer les projets existants.

Trouvez les informations par ici : https://github.com/ZarTek-Creole/DONATE
