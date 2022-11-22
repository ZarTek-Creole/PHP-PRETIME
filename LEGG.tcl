# Script "Listen Eggdrop"
# Ecoute sur un port proteger par un password
# transmet des messages vers un salon IRC
# URL https://github.com/ZarTek-Creole/PHP-PRETIME

if { [info commands ::LEGG::uninstall] eq "::LEGG::uninstall" } { ::LEGG::uninstall }

namespace eval ::LEGG {
	array set CONF {
		"port"				"6666"
		"channel"			"#ZarTek-Creole"
		"password"			"MyPasswordLEgg"
	}
	array set SCRIPT {
		"name"				"Listen Eggdrop"
		"version"			"0.0.1"
		"auteur"			"ZarTek-Creole"
	}
}
proc ::LEGG::CORE { idx args } {
	variable CONF
	set arg			[join ${args}]
	set password	[lindex ${arg} 0]
	set message		[lrange ${arg} 1 end]
	if { [string match ${password} ${CONF(password)}] } {
		putquick "PRIVMSG ${CONF(channel)} :${message}"   
		putdcc ${idx}"OK: ${message}"
	}
	killdcc ${idx}
}

proc ::LEGG::slisten { idx } {
	putlog "LEGG: Connection ${idx}"
	putdcc ${idx} "Welcome"
    control ${idx} ::LEGG::CORE
}

proc ::LEGG::init {args} {
	variable CONF
	listen ${CONF(port)} script ::LEGG::slisten
	putlog [format "Chargement de LEGG (%s) version %s by %s" ${::LEGG::SCRIPT(name)} ${::LEGG::SCRIPT(version)} ${::LEGG::SCRIPT(auteur)}]
}

proc ::LEGG::uninstall {args} {
	putlog [format "Désallocation des ressources de \002%s\002..." ${::LEGG::SCRIPT(name)}];
	foreach binding [lsearch -inline -all -regexp [binds *[set ns [string range [namespace current] 2 end]]*] " \{?(::)?${ns}"] {
		unbind [lindex ${binding} 0] [lindex ${binding} 1] [lindex ${binding} 2] [lindex ${binding} 4];
	}
	namespace delete ::LEGG
}
::LEGG::init