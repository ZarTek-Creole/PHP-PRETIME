- [PHP-PRETIME](#php-pretime)
- [Fonctionnalités](#fonctionnalités)
- [Explications](#explications)
- [Téléchargement](#téléchargement)
	- [Git](#git)
	- [Zip](#zip)
- [Configuration](#configuration)
	- [Renommer le fichier de config](#renommer-le-fichier-de-config)
	- [Éditer la configuration](#éditer-la-configuration)
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

PHP pour les PRETIME, multiserveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes

# Fonctionnalités

* FIND PRETIME      : Retourne le temps (unixtime/timestamp) de la release depuis le nom de la release.
* FIND WITH MULTI-SERVER (SELECT) : Recherche en cascade sur plusieurs serveurs MySQL, si nécessaire.
* ADD IF MISSING (INSERT)   : Ajout facultatif de la release dans la/les base(s) des données si release manquante.
* SEND TO EGGDROP (IF MISSING) : Envois facultatifs à un ou des Eggdrops en cas de release manquante

# Explications
Certains autotrades ont besoin du pretime de la release pour fonctionner correctement,
ce code lit une base de données mysql (non fournis) pour retourner la release et son pretime sous forme:
`rlsname rlstime - -`
Le plus de ce code est que si la première base de données MySQL ne contient pas les informations nécessaires, il peut consulter d'autres bases de données jusqu'à la trouver. 

Si toutefois, il ne repère sur aucun serveur, il peut réagir de différente manière configurable. 
Une configuration permet de fournir le temps actuel comme s’il avait trouvé pour que votre autotrade trade tout de même.
Il peut également afficher sur un salon IRC la release manquante pour faire un !newdir, !getold, simplement un message d'avertissement, etc. Plusieurs robots Eggdrop peut être paramétrés.

# Téléchargement
## Git

```git clone https://github.com/ZarTek-Creole/PHP-PRETIME.git /var/www/pretime```

pour télécharger dans /var/www/pretime directement prêt-à-être configurer
## Zip
```
wget https://github.com/ZarTek-Creole/PHP-PRETIME/archive/refs/heads/master.zip
unzip master.zip
```
# Configuration

## Renommer le fichier de config
Vous devez renommer le fichier nommé `config.example.php` en `config.php`
pour faire vous pouvez taper la commande dans votre terminal 

```mv config.example.php config.php```
## Éditer la configuration
dans votre terminal ouvrer le fichier `config.php` avec votre éditeur préférer 

```nano config.php```

---

### RELEASE_MISSING_IS_NOW

Si la valeur de `$RELEASE_MISSING_IS_NOW` vaut `1`: si la release est introuvable dans les bases de données, il va faire croire qu’elle vient d'être PRE en communiquant l'unixtime actuel

Si la valeur de `$RELEASE_MISSING_IS_NOW` vaut `0`: si la release est introuvable dans les bases de données, il ne va rien afficher

---

### RELEASE_MISSING_INSERT

Si la valeur de `$RELEASE_MISSING_INSERT` vaut `1`: si la release est introuvable dans les bases de données,
il va l'ajouter aux bases de données qui ont été `$cfg['MySQL'][$m]['insert']` renseigné. Voir plus bas.

Si la valeur de `$RELEASE_MISSING_INSERT` vaut `0`: si la release est introuvable dans les bases de données, il ne l'ajoute nulle part.

---

### RELEASE_MISSING_SECTION

Si la`$RELEASE_MISSING_INSERT` vaut 1, `$RELEASE_MISSING_SECTION` il contient le nom de la section avec laquelle il doit ajouter en SQL.

---
### $cfg['MySQL']
Les variables`$cfg['MySQL']`,  cela permet de configurer les bases de données. Cela fonctionne par bloc (ensemble) de variable `$cfg['MySQL']` par serveur vous devez avoir un bloc comme ceci: 

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
Cette variable doit contenir la requête SELECT de SQL. observer le point d'interrogation ? Celle si représente la position de la release dans la requête

#### $cfg['MySQL'][$m]['insert']
seul `$cfg['MySQL'][$m]['insert']` est facultatif. si `$RELEASE_MISSING_INSERT` vaut `1` et cette variable est renseignée, il va ajouter grâce à la requête SQL en base de données.
Remarquez les *quatres* points d'interrogation ?
Le premier, correspond à la colonne de temps
le deuxième représente à la colonne de la release
Le troisième représente à la colonne de la section
le quatrième équivaut à la colonne du nom de groupe de la release.
Notez bien : l'ordre est important et les *quatres* points d'interrogation doivent être fournis pour que cela fonctionne.

### $cfg['Eggdrop']
Les variables `$cfg['Eggdrop']` fonctionnent comme `$cfg['MySQL']` en bloc, chaque bloc représente la connexion à un Eggdrop
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
Cette variable permet de configurer le message qui sera affiché sur IRC et sera remplacé par l’appellation de la release. Ce que vous mettez à côté de `%rlsname%` vous appartient. Quelques exemples:
* '!getold %rlsname%'
* '!newdir %rlsname% -'
* '!addpre %rlsname% AUTOADD'
* 'Release %rlsname% introuvable'

L'explication de l'installation d’Eggdrop n'est pas détaillée ici. Par contre un script TCL nommer `LEGG.tcl` doit être chargé dans vos Eggdrops et édité.

# LEGG.tcl
Ce fichier se trouve avec les fichiers PHP. Il doit être chargé dans vos Eggdrops.
Éditez-le.
Modifier le bloc 
```
	array set CONF {
		"port"				"6666"
		"channel"			"#ZarTek-Creole"
		"password"			"MyPasswordLEgg"
	}
```

---

Choisissez un port libre, et n'oubliez pas de mettre le même dans `$cfg['Eggdrop'][$e]['port']`. La même chose pour le password avec `$cfg['Eggdrop'][$e]['port']` de votre `config.php`

---

Le `channel` peut-être un salon IRC où se trouve votre Eggdrop ou un pseudonyme, le votre par exemple pour recevoir les messages en privé.

# Bogues, idées, remarques
Pour faire évoluer le projet, vous pouvez créer [une issue ici](https://github.com/ZarTek-Creole/PHP-PRETIME/issues/new )

# Contribution

Toutes contributions sont les bienvenues. N'hésitez pas à fork le projet et proposer vos PR.

# Vous aimez ce script 
Vous pouvez me payer un café pour que j'ai encore de bonnes idées, et que j’ai de l'énergie pour améliorer les projets existants.

Trouvez les informations par ici : https://github.com/ZarTek-Creole/DONATE
