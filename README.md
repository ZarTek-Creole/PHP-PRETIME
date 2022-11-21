# PHP-PRETIME

PHP pour les PRETIME, multi-serveurs, utile par exemple pour les autotrades comme slftp afin de fournir les pretimes

# Fonctionalitées

* FIND PRETIME      : Retourne le temps (unixtime/timestamp) de la release depuis son le nom de release.
* FIND WITH MULTI-SERVER (SELECT) : Recherche en cascade sur plusieurs serveurs MySQL, si necessaire.
* ADD IF MISSING (INSERT)   : Ajout facultatif de la releases dans la/les base(s) des données si release manquante.
* SEND TO EGGDROP (IF MISSING) : Envois facultatif a un ou des eggdrops en cas de release manquante

# Explications
Certains autotrades necesite le pretime de la release pour fonctionner correctement,
ce code consulte une base de données mysql (non fournis) pour retourner la release et son pretime sous forme:
`rlsname rlstime - -`
Le plus de ce code est que si la premiere base de données MySQL ne contient pas les informations necessaire, il peu consultés d'autres base de données jusqu'à la trouver. 

Si toute fois, il ne trouve sur aucun serveur il peu reagir de differentes maniere configurable. 
Une configuration permet de fournir le temps actuelle comme si il avais trouver pour que votre autotrade trade tout de même.
Il peut également afficher sur un salon IRC la release manquante pour faire un !newdir, !getold, simplement un message d'avertisemment, etc. Plusieurs robot eggdrop peut-être configurer.

# Téléchargement
## Git

```git clone https://github.com/ZarTek-Creole/PHP-PRETIME.git /var/www/pretime```

pour télécharger dans /var/www/pretime directement pret a être configurer
## Zip
```
wget https://github.com/ZarTek-Creole/PHP-PRETIME/archive/refs/heads/master.zip
unzip master.zip
```
# Configuration

## Renommer le fichier config
Vous devez renommer le fichier nommer `config.example.php` en `config.php`
Pour faire vous pouvez taper la commande dans votre terminal 

```mv config.example.php config.php```
## Editez la configuration
dans votre terminal ouvrer le fichier `config.php` avec votre editeur preferer 

```nano config.php```

---

### RELEASE_MISSING_IS_NOW

Si la valeur de `$RELEASE_MISSING_IS_NOW` vaut `1`: si la release est introuvable dans les bases de données, il va faire croire que elle vient d'être PRE en donnant l'unixtime actuelle

Si la valeur de `$RELEASE_MISSING_IS_NOW` vaut `0`: si la release est introuvable dans les bases de données, il ne va rien afficher

---

### RELEASE_MISSING_INSERT

Si la valeur de `$RELEASE_MISSING_INSERT` vaut `1`: si la release est introuvable dans les bases de données,
il va l'ajoute aux bases de données qui ont `$cfg['MySQL'][$m]['insert']` renseigner. Voir plus bas.

Si la valeur de `$RELEASE_MISSING_INSERT` vaut `0`: si la release est introuvable dans les bases de données, il ne l'ajoute nulle part.

---

### RELEASE_MISSING_SECTION

Si `$RELEASE_MISSING_INSERT` vaut 1, `$RELEASE_MISSING_SECTION` contient le nom de la section avec le quel il doit ajouter en SQL.

---
### $cfg['MySQL']
Les variables `$cfg['MySQL']`, permet de configurer les bases de données. Cela fonction par bloc (ensemble) de variable `$cfg['MySQL']` par serveur vous devez avoir un bloc comme ceci: 

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
Cette variable doit contenir la requette SELECT de SQL. Remarquer le point d'interrogation ? Celle si represente la position de la release dans la requette

#### $cfg['MySQL'][$m]['insert']
seul `$cfg['MySQL'][$m]['insert']` est facultatif. si `$RELEASE_MISSING_INSERT` vaut `1` et cette variable est renseigner, il va ajouter grace a la requette SQL enn base de données.
Remarquer les *quatres* points d'interrogation ?
Le premier represente la colonne de temps
le deuxieme represente la colone de la release
Le troisieme represente la colonne de la section
le quatrieme represente la colonne du nom de group de la release.
Notez bien : L'ordre est important et  les *quatres* points d'interrogations doivent être fournis pour que cella fonctionne.

### $cfg['Eggdrop']
Les variables `$cfg['Eggdrop']` fonctionne comme `$cfg['MySQL']` en bloc, chaque bloc represente la connection a un eggdrop
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
Cette variable permet de configurer le message qui sera affcher sur IRC. `%rlsname%` sera remplacer par le nom de la release. ce que vous mettez a coter de `%rlsname%` vous appartiens. Quelque exemples:
* '!getold %rlsname%'
* '!newdir %rlsname% -'
* '!addpre %rlsname% AUTOADD'
* 'Release %rlsname% introuvable'

L'explication de l'installation de eggdrop n'ai pas detailler ici. Par contre un script TCL nommer `LEGG.tcl` doit etre charger dans vos eggdrops et editer.

# LEGG.tcl
Ce fichier ce trouve avec les fichiers php. il doit être charger dans vos eggdrops.
Editez-le.
Modifier le bloc 
```
	array set CONF {
		"port"				"6666"
		"channel"			"#ZarTek-Creole"
		"password"			"MyPasswordLEgg"
	}
```

---

Choisisez un port libre, et n'oublié pas de mettre le meme dans `$cfg['Eggdrop'][$e]['port']`. La meme chose pour le password avec `$cfg['Eggdrop'][$e]['port']` de votre `config.php`

---

Le `channel` peut-être un salon IRC où ce trouve votre eggdrop ou un pseudonyme, le votre par exemple pour recevoir les message en privée.

# Bogues, idées, remarques
Pour faire évoluer le projet, vous pouvez créer [un issue ici](https://github.com/ZarTek-Creole/PHP-PRETIME/issues/new )

# Contribution

Toutes contributions sont les bienvenues. N'hesitez pas a fork le projet et proposer vos PR.

# Vous aimez ce script 
Vous pouvez me payer un café pour que j'ai encore de bonnes idées, et, que jai de l'ernergie pour ameliorer les projets existant.

Trouvez les informations par ici : https://github.com/ZarTek-Creole/DONATE