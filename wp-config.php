<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'dbfitactive' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'root' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', '' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'k2iF/)?y{#Z`BsK7`hDCm<nLJU|iz49FsD&7B)l?aQX-a-3P<kP0)5gp0HbiXtw&' );
define( 'SECURE_AUTH_KEY',  '.R}<-L||0DgNEDB)LG7g+01vYz-^F2^V-yp-FN2d&*.TvfN[pXw[@Et<x!ZowG5f' );
define( 'LOGGED_IN_KEY',    'a(#4JT*%OXN5gl(NVCk-C3[r)4HDehi#2gR@QnL&PKf!_HR>(gNOScq[RwJE<[60' );
define( 'NONCE_KEY',        'pVRgVJg@by]l+3R$] vg$2;.Ia6UW[(Jc%RpXfT5Z/#r+)RJRKA[s`/,6@Qj}39y' );
define( 'AUTH_SALT',        'QQr$/+<rG:J,h30SG5FT$4!sqD#}o-uzx*!;.Qg-5>Miy[>JLe{4u0}586?-ubRd' );
define( 'SECURE_AUTH_SALT', 'y!eCC/Jy1}#z5MrrImw181(jSL[uD7>QJ;9J,5|xa=C6B<)%8;l>BR<TG/|mD/W`' );
define( 'LOGGED_IN_SALT',   '03IkBNb.,`b+K)f#mRuY6_>]NKX?ZhMAVU*!K5sm7YDj@3OY{dG}q}Yf<s.[t][u' );
define( 'NONCE_SALT',       'K%otD*r|KRGOxmOGPo.Ne44>/EyKCZJ[QziXcR=0e8bMb!wMXbJuWz*u?D~Q5V>k' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
